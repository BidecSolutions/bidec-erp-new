<?php

namespace App\Http\Controllers\Store;

use App\Helpers\CommonHelper;
use App\Helpers\PurchaseHelper;
use App\Helpers\ReuseableCode;
use App\Http\Controllers\Controller;

use App\Http\Requests;
use Illuminate\Http\Request;
use Auth;
use DB;
use Config;
use App\Models\Department;
use App\Models\Category;
use App\Models\Fara;
use App\Models\PurchaseOrder;
use App\Models\PurchaseRequestData;
use App\Models\Recipe;
use App\Models\Product;
use App\Models\Subitem;
use App\ProductionChecklist;
use App\ProductionStage;
use Cache;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Input;

class StoreController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['auth']);
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function toDayActivity()
    {
        return view('Store.toDayActivity');
    }

    

    public function viewPettyCashList(Request $request){
        if($request->ajax()){
            $locationId = $request->input('filterLocationId');
            $subDepartmentId = $request->input('filterSubDepartmentId');
            $projectId = $request->input('filterProjectId');
            $supplierId = $request->input('filterSupplierId');
            $fromDate = date("Y-m-d", strtotime($request->get('fromDate')));
            $toDate = date("Y-m-d", strtotime($request->get('toDate')));
            $query = DB::table('purchase_order as po')
                ->select('po.id', 'po.purchase_order_no', 'po.purchase_order_date', 'po.purchase_request_no', 'po.purchase_request_date', 'po.qoutation_no', 'po.qoutation_date', 'po.payment_type_rate', 'po.paymentType', 'po.custom_tax_percent','po.invoice_type','po.batch_no','po.finance_status','po.submited_date', DB::raw('SUM(pdap.amount) as paidAmount'), 's.name as supplierName', 'p.project_name', DB::raw('SUM(pod.sub_total) as invoiceAmount'))
                ->join('payment_data_against_po as pdap', 'pdap.po_id', '=', 'po.id')
                ->join('supplier as s', 'po.supplier_id', '=', 's.id')
                ->join('project as p', 'po.project_id', '=', 'p.id')
                ->join('purchase_order_data as pod', 'po.purchase_order_no', '=', 'pod.purchase_order_no')
                ->when($locationId != '', function ($q) use ($locationId) {
                    return $q->where('po.location_id','=',$locationId);
                })
                ->when($subDepartmentId != '', function ($q) use ($subDepartmentId) {
                    return $q->where('po.sub_department_id','=',$subDepartmentId);
                })
                ->when($projectId != '', function ($q) use ($projectId) {
                    return $q->where('po.project_id','=',$projectId);
                })
                ->when($supplierId != '', function ($q) use ($supplierId) {
                    return $q->where('po.supplier_id','=',$supplierId);
                })
                ->whereBetween('po.purchase_order_date', [$fromDate, $toDate])
                ->groupBy('po.id')->get();
            return view('Store.viewPettyCashListAjax',compact('query'));    
        }
        return view('Store.viewPettyCashList');
    }

    public function viewInvoiceSubmissionList(Request $request){
        if($request->ajax()){
            $locationId = $request->input('filterLocationId');
            $subDepartmentId = $request->input('filterSubDepartmentId');
            $projectId = $request->input('filterProjectId');
            $supplierId = $request->input('filterSupplierId');
            $fromDate = date("Y-m-d", strtotime($request->get('fromDate')));
            $toDate = date("Y-m-d", strtotime($request->get('toDate')));
            $query = DB::table('purchase_order as po')
                ->select('po.id', 'po.purchase_order_no', 'po.purchase_order_date', 'po.purchase_request_no', 'po.purchase_request_date', 'po.qoutation_no', 'po.qoutation_date', 'po.payment_type_rate', 'po.paymentType', 'po.custom_tax_percent','po.invoice_type','po.batch_no','po.finance_status','po.submited_date', DB::raw('SUM(pdap.amount) as paidAmount'), 's.name as supplierName', 'p.project_name', DB::raw('SUM(pod.sub_total) as invoiceAmount'))
                ->leftJoin('payment_data_against_po as pdap', 'pdap.po_id', '=', 'po.id')
                ->join('supplier as s', 'po.supplier_id', '=', 's.id')
                ->join('project as p', 'po.project_id', '=', 'p.id')
                ->join('purchase_order_data as pod', 'po.purchase_order_no', '=', 'pod.purchase_order_no')
                ->when($locationId != '', function ($q) use ($locationId) {
                    return $q->where('po.location_id','=',$locationId);
                })
                ->when($subDepartmentId != '', function ($q) use ($subDepartmentId) {
                    return $q->where('po.sub_department_id','=',$subDepartmentId);
                })
                ->when($projectId != '', function ($q) use ($projectId) {
                    return $q->where('po.project_id','=',$projectId);
                })
                ->when($supplierId != '', function ($q) use ($supplierId) {
                    return $q->where('po.supplier_id','=',$supplierId);
                })
                ->whereBetween('po.purchase_order_date', [$fromDate, $toDate])
                ->groupBy('po.id')->get();
            return view('Store.viewInvoiceSubmissionListAjax',compact('query'));    
        }
        return view('Store.viewInvoiceSubmissionList');
    }


    public function addMaterialRequestForm()
    {
        $jsonFiles = [
            'products' => storage_path('app/json_files/products.json'),
            'product_variants' => storage_path('app/json_files/product_variants.json'),
            'categories' => storage_path('app/json_files/categories.json'),
            'brands' => storage_path('app/json_files/brands.json'),
            'sizes' => storage_path('app/json_files/sizes.json'),
            'departments' => storage_path('app/json_files/departments.json'),
        ];

        foreach ($jsonFiles as $key => $path) {
            if (!file_exists($path)) {
                generate_json($key);
            }
        }

        $data = collect($jsonFiles)->map(fn($p) => json_decode(file_get_contents($p), true));
        extract($data->toArray());

        $categoryMap = collect($categories)->pluck('name', 'id');
        $brandMap = collect($brands)->pluck('name', 'id');
        $sizeMap = collect($sizes)->pluck('name', 'id');

        $products = collect($products)
            ->map(function ($product) use ($product_variants, $categoryMap, $brandMap, $sizeMap) {
                $product['variants'] = array_filter($product_variants, fn($v) => $v['product_id'] == $product['id']);
                $product['category_name'] = $categoryMap[$product['category_id']] ?? '-';
                $product['brand_name'] = $brandMap[$product['brand_id']] ?? '-';
                foreach ($product['variants'] as &$variant) {
                    $variant['size_name'] = $sizeMap[$variant['size_id']] ?? '-';
                }
                return $product;
            })
            ->where('status', 1)
            ->values()
            ->toArray();

        return view('Store.addMaterialRequestForm', compact('products', 'departments'));
    }

    public  function addMaterialRequestFormTwo()
    {

        $m = Session::get('company_id');
        $checkPermission =  CommonHelper::checkUserPermissionForSingleOption($m, Auth::user()->id, Auth::user()->emp_id, $request->input('pageType'), $request->input('parentCode'), 'Store', Auth::user()->acc_type);
        if ($checkPermission != 1) {
            return view('dontPermissionForPage');
        }
        $departments = Cache::rememberForever('cacheDepartment_' . $m . '', function () use ($m) {
            return DB::select("select * from department where company_id = " . $m . "");
        });
        $recipes = Recipe::all();
        return view('Store.addMaterialRequestFormTwo', compact('departments', 'recipes'));
    }

    


    public  function viewMaterialRequestList()
    {
       
        // $checkPermission =  CommonHelper::checkUserPermissionForSingleOption(Session::get('company_id'), Auth::user()->id, Auth::user()->emp_id, $_GET['pageType'], $_GET['parentCode'], 'Store', Auth::user()->acc_type);
        // if ($checkPermission != 1) {
        //     return view('dontPermissionForPage');
        // }
        $companyId = session::get('company_id');
       return view('Store.viewMaterialRequestList', compact('companyId'));
    }

    public  function addStoreChallanForm()
    {
        $checkPermission =  CommonHelper::checkUserPermissionForSingleOption(Session::get('company_id'), Auth::user()->id, Auth::user()->emp_id, $_GET['pageType'], $_GET['parentCode'], 'Store', Auth::user()->acc_type);
        if ($checkPermission != 1) {
            return view('dontPermissionForPage');
        }

        $materialRequestDatas = DB::table('material_request_data')
            ->select(
                'material_request.id',
                'material_request_data.material_request_no',
                'material_request_data.material_request_date',
                'material_request.sub_department_id',
                'material_request.required_date',
                'material_request.username',
                'location.location_name',
                'sub_department.sub_department_name',
                'department.department_name'
            )
            ->join('material_request', 'material_request_data.material_request_no', '=', 'material_request.material_request_no')
            ->join('location', 'material_request.location_id', '=', 'location.id')
            ->join('sub_department', 'material_request.sub_department_id', '=', 'sub_department.id')
            ->join('department', 'sub_department.department_id', '=', 'department.id')
            ->where('material_request_data.store_challan_status', '=', '1')
            ->where('material_request_data.material_request_status', '=', '2')
            ->groupBy('material_request_data.material_request_no')
            ->get();
        return view('Store.addStoreChallanForm', compact('materialRequestDatas'));
    }

    public  function viewStoreChallanList()
    {
        $checkPermission =  CommonHelper::checkUserPermissionForSingleOption(Session::get('company_id'), Auth::user()->id, Auth::user()->emp_id, $_GET['pageType'], $_GET['parentCode'], 'Store', Auth::user()->acc_type);
        if ($checkPermission != 1) {
            return view('dontPermissionForPage');
        }
        return view('Store.viewStoreChallanList');
    }

    public  function addStoreChallanReturnForm()
    {
        $checkPermission =  CommonHelper::checkUserPermissionForSingleOption(Session::get('company_id'), Auth::user()->id, Auth::user()->emp_id, $_GET['pageType'], $_GET['parentCode'], 'Store', Auth::user()->acc_type);
        if ($checkPermission != 1) {
            return view('dontPermissionForPage');
        }

        $challanRequestDatas = DB::table('store_challan_data')
            ->select(
                'store_challan.id',
                'store_challan_data.store_challan_no',
                'store_challan_data.store_challan_date',
                'store_challan.sub_department_id',
                'store_challan.material_request_date',
                'store_challan.username',
                'location.location_name',
                'sub_department.sub_department_name',
                'department.department_name'
            )
            ->join('store_challan', 'store_challan_data.store_challan_no', '=', 'store_challan.store_challan_no')
            ->join('location', 'store_challan.location_id', '=', 'location.id')
            ->join('sub_department', 'store_challan.sub_department_id', '=', 'sub_department.id')
            ->join('department', 'sub_department.department_id', '=', 'department.id')
            ->where('store_challan_data.store_challan_status', '=', '2')
            ->groupBy('store_challan_data.store_challan_no')
            ->get();
        // dd($challanRequestDatas);
        return view('Store.addStoreChallanReturnForm', compact('challanRequestDatas'));
    }

    public  function viewStoreChallanReturnList()
    {
        $checkPermission =  CommonHelper::checkUserPermissionForSingleOption(Session::get('company_id'), Auth::user()->id, Auth::user()->emp_id, $_GET['pageType'], $_GET['parentCode'], 'Store', Auth::user()->acc_type);
        if ($checkPermission != 1) {
            return view('dontPermissionForPage');
        }
        return view('Store.viewStoreChallanReturnList');
    }







    public  function viewPurchaseRequestList()
    {
        $checkPermission =  CommonHelper::checkUserPermissionForSingleOption(Session::get('company_id'), Auth::user()->id, Auth::user()->emp_id, $_GET['pageType'], $_GET['parentCode'], 'Store', Auth::user()->acc_type);
        if ($checkPermission != 1) {
            return view('dontPermissionForPage');
        }
        return view('Store.viewPurchaseRequestList');
    }

    public function createStoreChallanForm()
    {
        $checkPermission =  CommonHelper::checkUserPermissionForSingleOption(Session::get('company_id'), Auth::user()->id, Auth::user()->emp_id, $_GET['pageType'], $_GET['parentCode'], 'Store', Auth::user()->acc_type);
        if ($checkPermission != 1) {
            return view('dontPermissionForPage');
        }
        $departments = new Department;
        $departments = $departments::where([['company_id', '=', Session::get('company_id')], ['status', '=', '1'],])->orderBy('id')->get();
        return view('Store.createStoreChallanForm', compact('departments'));
    }



    public  function editStoreChallanVoucherForm()
    {
        $checkPermission =  CommonHelper::checkUserPermissionForSingleOption(Session::get('company_id'), Auth::user()->id, Auth::user()->emp_id, 'edit', $_GET['parentCode'], 'Store', Auth::user()->acc_type);
        if ($checkPermission != 1) {
            return view('dontPermissionForModal');
        }
        $departments = new Department;
        $departments = $departments::where([['company_id', '=', Session::get('company_id')], ['status', '=', '1'],])->orderBy('id')->get();
        return view('Store.AjaxPages.editStoreChallanVoucherForm', compact('departments'));
    }

    public function createPurchaseOrderForm()
    {
        $checkPermission =  CommonHelper::checkUserPermissionForSingleOption(Session::get('company_id'), Auth::user()->id, Auth::user()->emp_id, $_GET['pageType'], $_GET['parentCode'], 'Store', Auth::user()->acc_type);
        if ($checkPermission != 1) {
            return view('dontPermissionForPage');
        }

        $PurchaseRequestDatas = DB::table('purchase_request_data')
            ->select(
                'purchase_request.id',
                'purchase_request_data.purchase_request_no',
                'purchase_request_data.purchase_request_date',
                'purchase_request.sub_department_id',
                'purchase_request.required_date',
                'purchase_request.username',
                'location.location_name',
                'sub_department.sub_department_name',
                'department.department_name'
            )
            ->join('purchase_request', 'purchase_request_data.purchase_request_no', '=', 'purchase_request.purchase_request_no')
            ->join('location', 'purchase_request.location_id', '=', 'location.id')
            ->join('sub_department', 'purchase_request.sub_department_id', '=', 'sub_department.id')
            ->join('department', 'sub_department.department_id', '=', 'department.id')
            ->where('purchase_request_data.purchase_order_status', '=', '1')
            ->where('purchase_request_data.purchase_request_status', '=', '2')
            ->groupBy('purchase_request_data.purchase_request_no')
            ->get();
        return view('Store.createPurchaseOrderForm', compact('PurchaseRequestDatas'));
    }
    public function createDirectPurchaseOrderForm()
    {
        $m = getSessionCompanyId();
        $accounts = DB::table("accounts")->select('*')        
            ->whereIn('code', ['1-2-2-2-31'])
            ->where('status','=','1')        
            ->get();
        $departments =  DB::select("select * from department where company_id = ".$m."");        
        return view('Store.createDirectPurchaseOrderForm', compact('accounts', 'departments'));
    }
    public function UpdateMaterialRequestVoucherForm(Request $request)
    {   
        // dd($request);   
        $tableMaterialRequest = DB::table('material_request')
            ->where('material_request_no', $request->main_ID)->first();
        // dd($tableMaterialRequest->sub_department_id, $tableMaterialRequest->department_id);
        $depart = explode('<*>', $request->department_id_1 );
        
        $SubDepart = isset($depart[0]) ? $depart[0] : $tableMaterialRequest->sub_department_id;
        $Depart = isset($depart[1]) ? $depart[1] : $tableMaterialRequest->department_id;
        $update = DB::table('material_request')
            ->where('material_request_no', $request->main_ID)
            ->update([
                'material_request_date' => "$request->material_request_date_1",
                'location_id' => "$request->location_id_1",
                'sub_department_id' => "$request->subdepartments",
                'department_id' => "$Depart",
                'sub_department_id' => "$SubDepart]",
                'project_id' => "$request->project_id_1",
                'description' => $request->description_1,
                'material_request_status' => '2'
            ]);

        $materialData = $request->materialRequestDataSection;
        $mrData = DB::table('material_request_data')
        ->where('material_request_no', $request->main_ID)->pluck('id')->toArray();
        // dd($materialData, $mrData);

        foreach ($materialData as $key => $value) {
            // $count = DB::table('material_request_data')
            // ->where('id', $value)->first();
            if (in_array($value, $mrData)) {
                DB::table('material_request_data')
                    ->where('id', $value)
                    ->update([
                        'category_id' => $request->category_id[$key],
                        'sub_item_id' => $request->sub_item_id[$key],
                        'qty' => $request->qty[$key],
                        'sub_description' => $request->sub_description[$key],
                    ]);
                    // $index = array_search($value, $mrData);
                    // if ($index !== false) {
                    //     echo "Value exists in the array. Removing it...\n";
                    //     unset($mrData[$index]);
                    // }
                    // dd($mrData);
            }else{
                // dd($materialData);
                DB::table('material_request_data')
                    ->insert([
                        'material_request_no' => $request->main_ID,
                        'material_request_date' => date("Y-m-d"),
                        'required_date' => date("Y-m-d"),
                        'approx_cost' => 0,
                        'approx_sub_total' => 0,
                        'status' => 1,
                        'date' => date("Y-m-d"),
                        'time' => date("H:i:s"),
                        'username' => Auth::user()->name,
                        'user_id' => Auth::user()->id,
                        'approve_username' => Auth::user()->name,
                        'accounting_year' => Session::get('accountYear'),
                        'category_id' => $request->category_id[$key],
                        'sub_item_id' => $request->sub_item_id[$key],
                        'qty' => $request->qty[$key],
                        'sub_description' => $request->sub_description[$key],
                    ]);
            }
        }
        // $materialData = $request->main_ID;
        // foreach($materialData as $key => $row) :
        //     $sub_meterial = new material_request_data();
        //     $sub_meterial->category_id = $request->input('category_id_1_1')[$key];
        //     $sub_meterial->sub_item_id = $request->input('sub_item_id_1_1')[$key];
        //     $sub_meterial->qty = $request->input('qty_1_1')[$key];
        //     $sub_meterial->sub_description = $request->input('sub_description_1_1')[$key];
        //     $sub_meterial->save();
        // endforeach;
        return view('Store.viewMaterialRequestList');
    }
    public function InvoicePO($id)
    {
       // dd($id);
        $purchaseOrderData = PurchaseOrder::where('purchase_order_no', $id)
        ->with(['purchaseOrderData', 'purchaseRequest', 'purchaseRequestData', 'location', 'department', 'subDepartment', 'project'])
        ->first();
        return view('Store.invoice', compact('purchaseOrderData'));
    }

    
    public function veiw_Checklist($id)
    {
        //dd($id);
        // $purchaseOrderData = DB::table('production_checklists')
        // ->join('production_stages', 'production_checklists.id', '=', 'production_stages.id')
        // ->first();
         $dataChecklist = ProductionChecklist::where('id', $id)->first();
        return view('Store.Edit_Stages', compact('purchaseOrderData'));
    }
    public function editPurchaseOrderForm($id)
    {
        // dd($id);
        $checkPermission =  CommonHelper::checkUserPermissionForSingleOption(Session::get('company_id'), Auth::user()->id, Auth::user()->emp_id, $_GET['pageType'], $_GET['parentCode'], 'Store', Auth::user()->acc_type);
        if ($checkPermission != 1) {
            return view('dontPermissionForPage');
        }
        $purchaseOrderData = PurchaseOrder::where('purchase_order_no', $id)
            ->with(['purchaseOrderData', 'purchaseRequest', 'purchaseRequestData', 'location', 'department', 'subDepartment', 'project'])
            ->first();
        $paymentTypeList = DB::connection('tenant')->table('payment_type')->get();
        return view('Store.editPurchaseOrderForm', compact('purchaseOrderData','paymentTypeList'));
    }

    public  function viewPurchaseOrderList()
    {
        $checkPermission =  CommonHelper::checkUserPermissionForSingleOption(Session::get('company_id'), Auth::user()->id, Auth::user()->emp_id, $_GET['pageType'], $_GET['parentCode'], 'Store', Auth::user()->acc_type);
        if ($checkPermission != 1) {
            return view('dontPermissionForPage');
        }
        return view('Store.viewPurchaseOrderList');
    }

    public  function editPurchaseOrderVoucherForm()
    {
        $checkPermission =  CommonHelper::checkUserPermissionForSingleOption(Session::get('company_id'), Auth::user()->id, Auth::user()->emp_id, 'edit', $_GET['parentCode'], 'Store', Auth::user()->acc_type);
        if ($checkPermission != 1) {
            return view('dontPermissionForModal');
        }
        $departments = new Department;
        $departments = $departments::where([['company_id', '=', Session::get('company_id')], ['status', '=', '1'],])->orderBy('id')->get();
        return view('Store.AjaxPages.editPurchaseOrderVoucherForm', compact('departments'));
    }


    public function createPurchaseOrderSaleForm()
    {
        $checkPermission =  CommonHelper::checkUserPermissionForSingleOption(Session::get('company_id'), Auth::user()->id, Auth::user()->emp_id, $_GET['pageType'], $_GET['parentCode'], 'Store', Auth::user()->acc_type);
        if ($checkPermission != 1) {
            return view('dontPermissionForPage');
        }
        $departments = new Department;
        $departments = $departments::where([['company_id', '=', Session::get('company_id')], ['status', '=', '1'],])->orderBy('id')->get();
        return view('Store.createPurchaseOrderSaleForm', compact('departments'));
    }

    public  function viewPurchaseOrderSaleList()
    {
        $checkPermission =  CommonHelper::checkUserPermissionForSingleOption(Session::get('company_id'), Auth::user()->id, Auth::user()->emp_id, $_GET['pageType'], $_GET['parentCode'], 'Store', Auth::user()->acc_type);
        if ($checkPermission != 1) {
            return view('dontPermissionForPage');
        }
        return view('Store.viewPurchaseOrderSaleList');
    }

    public  function editPurchaseOrderSaleVoucherForm()
    {
        $checkPermission =  CommonHelper::checkUserPermissionForSingleOption(Session::get('company_id'), Auth::user()->id, Auth::user()->emp_id, 'edit', $_GET['parentCode'], 'Store', Auth::user()->acc_type);
        if ($checkPermission != 1) {
            return view('dontPermissionForModal');
        }
        $departments = new Department;
        $departments = $departments::where([['company_id', '=', Session::get('company_id')], ['status', '=', '1'],])->orderBy('id')->get();
        return view('Store.AjaxPages.editPurchaseOrderSaleVoucherForm', compact('departments'));
    }



    public function createStoreChallanReturnForm()
    {
        $checkPermission =  CommonHelper::checkUserPermissionForSingleOption(Session::get('company_id'), Auth::user()->id, Auth::user()->emp_id, $_GET['pageType'], $_GET['parentCode'], 'Store', Auth::user()->acc_type);
        if ($checkPermission != 1) {
            return view('dontPermissionForPage');
        }
        $departments = new Department;
        $departments = $departments::where([['company_id', '=', Session::get('company_id')], ['status', '=', '1'],])->orderBy('id')->get();
        return view('Store.createStoreChallanReturnForm', compact('departments'));
    }



    public  function editStoreChallanReturnForm()
    {
        $checkPermission =  CommonHelper::checkUserPermissionForSingleOption(Session::get('company_id'), Auth::user()->id, Auth::user()->emp_id, 'edit', $_GET['parentCode'], 'Store', Auth::user()->acc_type);
        if ($checkPermission != 1) {
            return view('dontPermissionForModal');
        }
        $departments = new Department;
        $departments = $departments::where([['company_id', '=', Session::get('company_id')], ['status', '=', '1'],])->orderBy('id')->get();
        return view('Store.AjaxPages.editStoreChallanReturnForm', compact('departments'));
    }

    public function viewDateWiseStockInventoryReport()
    {
        $checkPermission =  CommonHelper::checkUserPermissionForSingleOption(Session::get('company_id'), Auth::user()->id, Auth::user()->emp_id, $_GET['pageType'], $_GET['parentCode'], 'Store', Auth::user()->acc_type);
        if ($checkPermission != 1) {
            return view('dontPermissionForPage');
        }
        $categorys = new Category;
        $categorys = $categorys::where([['company_id', '=', Session::get('company_id')], ['status', '=', '1'],])->orderBy('id')->get();
        return view('Store.viewDateWiseStockInventoryReport', compact('categorys'));
    }

    public  function stock_transfer_form(){
        return view('Store.stock_transfer_form');
    }
    public  function stock_trash_form(){
        return view('Store.stock_trash_form');
    }
    public  function stock_transfer_form_production(){
        return view('Store.stock_transfer_form_production');
    }
    public function viewStockTransferDetail(){
        return view('Store.viewStockTransferDetail');
    }
    public function viewStockTrashDetail(){
        return view('Store.viewStockTrashDetail');
    }
    public  function get_iot_products(Request $request){
        $sub_ic_id = $request->item;        
       // Define the subquery to get the latest records for each iot group
       $results = CommonHelper::AvailableSingleUnitStockDetail($sub_ic_id, 'tenant');
        // $subquery = DB::table('fara as f1')
        // ->select(DB::raw('MAX(`date`) as max_date, MAX(`time`) as max_time, iot, MAX(`id`) as fara_id'))
        // ->where('sub_ic_id', $sub_ic_id)
        // ->whereIn('action', [11, 12])
        // ->where('action', '!=', 15)
        // ->where('action', '!=', 13)
        // ->whereNotNull('iot')
        // ->where('company_id', getSessionCompanyId())
        // ->where('status', 1)
        // ->groupBy('iot')->pluck('fara_id')->toArray();

        // // Fetch the latest records with unique iot values using the main query with a LEFT JOIN and a WHERE condition with the subquery
        // $results = Fara::select('fara.id as id', 'fara.serial_no', 'fara.iot', 'fara.imei', 'fara.sim', 'fara.location_id', 'location_name')
        // ->join('location', 'location.id', '=', 'fara.location_id')
        // ->whereIn('fara.id', $subquery)->get()->toArray();
        // // dd($subquery, $results );

        return $results;
    }
    public  function stock_transfer_list(Request $request){
        if($request->ajax()){
            $fromDate = $request->fromDate ? date('Y-m-d', strtotime($request->fromDate)) : date("Y-m-d", mktime(0, 0, 0, date("m"), 1));
            $toDate = $request->toDate ? date('Y-m-d', strtotime($request->toDate)) : date("Y-m-d", mktime(0, 0, 0, date("m"), 0));
            $startRecordNo = $request->input('startRecordNo');
		    $endRecordNo = $request->input('endRecordNo');
            $voucherType = $request->input('voucherType');
            if(empty($voucherType)){
                $voucherCondition = '';
            }else{
                $voucherCondition = ' and type = '.$voucherType.'';
            }

            $params['startRecordNo'] = $startRecordNo;
            $params['endRecordNo'] = $endRecordNo;
            $params['m'] = $request->m;

            $countGetDetail = DB::select("select * from stock_transfers where status = 1 and tr_date BETWEEN '".$fromDate."' AND  '".$toDate."'".$voucherCondition."");
            $getDetail = DB::select("select * from stock_transfers where status = 1 and tr_date BETWEEN '".$fromDate."' AND  '".$toDate."'".$voucherCondition." order by id LIMIT ".$startRecordNo.",".$endRecordNo."");
            
            //DB::table('stock_transfers')->where('status', '=', 1)->orderBy('id', 'desc')->get();
            
            return view('Store.AjaxPages.getStockTransferDataAjax',compact('getDetail','countGetDetail','params'));
        }
        return view('Store.stock_transfer_list');
    }
    public  function stock_trash_list(){
        return view('Store.stock_trash_list');
    }

    public function addStockTrash(Request $request)
    {
        // dd($request);
        $m = getSessionCompanyId();
        DB::Connection('tenant')->beginTransaction();
        $uniq=PurchaseHelper::get_unique_no_trash(date('y'),date('m'));
        try {
            $data=array
                (
                    'tr_no'=>$uniq,
                    'company_id'=> $m,
                    'tr_date'=>$request->tr_date,
                    'description'=>$request->description,
                    'status'=>1,
                    'tr_status'=>1,
                    'created_by'=>Auth::user()->id,
                );
            $master_id = DB::Connection('tenant')->table('stock_trashes')->insertGetId($data);
            $data1=$request->item_id;
            foreach($data1 as $key=>$row){
                $locationId = $request->input('warehouse_from')[$key];
                $data2=array
                (
                    'master_id'=>$master_id,
                    'tr_no'=>$uniq,
                    'item_id'=>$row,
                    'warehouse_from'=>$locationId,
                    'qty'=>$request->input('qty')[$key] ?? 1,
                    'desc'=>$request->input('des')[$key] ?? '',
                    'status'=>1,
                );
                $master_data_id= DB::Connection('tenant')->table('stock_trash_datas')->insertGetId($data2);
                $subitem = Subitem::find($row);
                ReuseableCode::postStock($master_id, $master_data_id, $uniq, date('Y-m-d'), 16, 0, $subitem->id, '', $request->input('qty')[$key], $locationId, $subitem->main_ic_id);
                DB::Connection('tenant')->commit();
                dd('done');
            }
        } catch (\Exception $e) {
            DB::Connection('tenant')->rollback();
            dd('out', $e->getMessage());
        }
    }
    public function addStockTransferTwo(Request $request){
        // dd($request->item_id);
        $m = getSessionCompanyId();
        // echo "<pre>";
        // print_r($request->all());
        DB::Connection('tenant')->beginTransaction();
        $uniq = PurchaseHelper::get_unique_no_transfer(date('y'),date('m'));
        try {

            $data=array
                (
                    'tr_no'=>$uniq,
                    'type'=> $request->voucher_type ?? 1,
                    'company_id'=> $m,
                    'tr_date'=>$request->tr_date,
                    'description'=>$request->description,
                    'status'=>1,
                    'tr_status'=>1,
                    // 'date'=>$request->tr_date,
                    'created_by'=>Auth::user()->id,
                );
            $master_id = DB::Connection('tenant')->table('stock_transfers')->insertGetId($data);

            $data1 = $request->item_id;
            $trasnferArray = $request->input('trasnferArray');
            $TotAmount = 0;
            foreach($trasnferArray as $key => $row):

                $itemId = $request->input('item_id_'.$row.'');
                $trasnferDataArray = $request->input('trasnferDataArray_'.$row.'');
                foreach($trasnferDataArray as $row1){
                    $data2 = array
                    (
                        'master_id' => $master_id,
                        'tr_no' => $uniq,
                        'item_id' => $itemId,
                        'iot' => $request->input('iot_'.$row.'_'.$row1.''),
                        'warehouse_from' => $request->input('warehouse_from_'.$row.'_'.$row1.''),
                        'warehouse_to' => $request->input('warehouse_to_'.$row.''),
                        'qty' => $request->input('qty') ?? 1,
                        'rate' => $request->input('rate_'.$row.'') ?? 0,
                        'amount' => $request->input('amount_'.$row.'') ?? 0,
                        'batch_code' => $request->input('batch_code') ?? '',
                        'desc' => $request->input('des_'.$row.'') ?? '',
                        'status' => 1,
                    );
                    $master_data_id= DB::Connection('tenant')->table('stock_transfer_datas')->insertGetId($data2);

                    $subitem = Subitem::find($itemId);
                    $stock = array(
                        'main_id'=>$master_id,
                        'master_id'=>$master_data_id,
                        'voucher_no'=>$uniq,
                        'voucher_date'=>$request->tr_date,
                        'supp_id'=>0,
                        'action'=>13,
                        'price'=>$request->input('rate_'.$row.'') ?? 0,
                        'sub_ic_id'=>$itemId,
                        'main_ic_id'=>$subitem->main_ic_id,
                        'qty'=>$request->input('qty') ?? 0,
                        'value'=>$request->input('amount_'.$row.'') ?? 0,
                        'status'=>1,
                        'location_id'=>$request->input('warehouse_from_'.$row.'_'.$row1.''),
                        'warehouse_from_id'=>$request->input('warehouse_from_'.$row.'_'.$row1.''),
                        'warehouse_to_id'=>$request->input('warehouse_to_'.$row.''),
                        'accounting_year' => Session::get('accountYear'),                  
                        // 'transfer_status'=>1,
                        // 'description'=>'Transfer',
                        'username'=>Auth::user()->username,
                        'date'=>date('Y-m-d'),
                        'time' => date("H:i:s"),
                        'company_id' => getSessionCompanyId()
                    );
                        $fara = Fara::where('id', $request->input('fara_row_id_'.$row.'_'.$row1.''))->first();                    
                        $stock['main_id'] = $fara->main_id;
                        $stock['master_id'] = $fara->master_id;
                        $stock['unit_type'] = $fara->unit_type;
                        $stock['iot'] = $fara->iot ?? '';
                        $stock['imei'] = $fara->imei ?? '';
                        $stock['serial_no'] = $fara->serial_no ?? '';
                        $stock['sim'] = $fara->sim ?? '';
                        $stock['batch'] = $fara->batch ?? '';
                        $stock['process_id'] = $fara->process_id ?? '';
                        DB::Connection('tenant')->table('fara')->insert($stock);

                
                    $stock1=array
                    (
                        'main_id'=>$master_id,
                        'master_id'=>$master_data_id,
                        'voucher_no'=>$uniq,
                        'voucher_date'=>$request->tr_date,
                        'supp_id'=>0,
                        'action'=>12,
                        'price'=>$request->input('rate_'.$row.'') ?? 0,
                        // 'sub_ic_id'=>$row,
                        'sub_ic_id'=>$itemId,
                        'main_ic_id'=>$subitem->main_ic_id,
                        'qty'=>$request->input('qty') ?? 0,
                        'value'=>$request->input('amount_'.$row.'') ?? 0,
                        'status'=>1,
                        'location_id'=>$request->input('warehouse_to_'.$row.''),
                        'warehouse_from_id'=>$request->input('warehouse_from_'.$row.'_'.$row1.''),
                        'warehouse_to_id'=>$request->input('warehouse_to_'.$row.''),
                        'accounting_year' => Session::get('accountYear'),
                        // 'transfer_status'=>1,
                        // 'description'=>'Transfer',
                        'username'=>Auth::user()->username,
                        'date'=>date('Y-m-d'),
                        'time' => date("H:i:s"),  
                        'company_id' => getSessionCompanyId()
    
                    );
                    if($request->voucher_type == 2){
                        $fara = Fara::where('id', $request->input('fara_row_id_'.$row.'_'.$row1.''))->first();                    
                        $stock1['main_id'] = $fara->main_id;
                        $stock1['master_id'] = $fara->master_id;
                        $stock1['unit_type'] = $fara->unit_type;
                        $stock1['iot'] = $fara->iot ?? '';
                        $stock1['imei'] = $fara->imei ?? '';
                        $stock1['serial_no'] = $fara->serial_no ?? '';
                        $stock1['sim'] = $fara->sim ?? '';
                        $stock1['batch'] = $fara->batch ?? '';
                        $stock1['process_id'] = $fara->process_id ?? '';
                    }
                    
                DB::Connection('tenant')->table('fara')->insert($stock1);
                }

            endforeach;

            // CommonHelper::inventory_activity($uniq,$request->tr_date,$TotAmount,6,'Insert');


            DB::Connection('tenant')->commit();
        }
        catch(\Exception $e)
        {
            DB::Connection('tenant')->rollback();
            // echo "EROOR"; //die();
            // echo $e->getTraceAsString();
            dd($e->getMessage());
        }

        Session::flash('dataInsert', 'Stock Transfer Successfully Saved.');

        return Redirect::to('store/stock_transfer_list?pageType=view&&parentCode=95&&m=' . $m);
    }
    public function addStockTransfer(Request $request)
    {
        // dd($request->item_id);
        $m = getSessionCompanyId();
        DB::Connection('tenant')->beginTransaction();
        $uniq=PurchaseHelper::get_unique_no_transfer(date('y'),date('m'));
        try {

            $data=array
                (
                    'tr_no'=>$uniq,
                    'type'=> $request->voucher_type ?? 1,
                    'company_id'=> $m,
                    'tr_date'=>$request->tr_date,
                    'description'=>$request->description,
                    'status'=>1,
                    'tr_status'=>1,
                    // 'date'=>$request->tr_date,
                    'created_by'=>Auth::user()->id,
                );
            $master_id = DB::Connection('tenant')->table('stock_transfers')->insertGetId($data);

            $data1=$request->item_id;
            $TotAmount = 0;
            foreach($data1 as $key=>$row):




                // dd($request, $request->input('qty')[$key], $key);
                $data2=array
                (
                    'master_id'=>$master_id,
                    'tr_no'=>$uniq,
                    'item_id'=>$row,
                    'iot'=>$request->input('iot')[$key] ?? '',
                    'warehouse_from'=>$request->input('warehouse_from')[$key],
                    'warehouse_to'=>$request->input('warehouse_to')[$key],
                    'qty'=>$request->input('qty')[$key] ?? 1,
                    'rate'=>$request->input('rate')[$key] ?? 0,
                    'amount'=>$request->input('amount')[$key] ?? 0,
                    'batch_code'=>$request->input('batch_code')[$key] ?? '',
                    'desc'=>$request->input('des')[$key] ?? '',
                    'status'=>1,
                );

                // $TotAmount+=$request->input('amount')[$key];


               $master_data_id= DB::Connection('tenant')->table('stock_transfer_datas')->insertGetId($data2);
                $subitem = Subitem::find($row);
                $stock=array
                (
                    'main_id'=>$master_id,
                    'master_id'=>$master_data_id,
                    'voucher_no'=>$uniq,
                    'voucher_date'=>$request->tr_date,
                    'supp_id'=>0,
                    'action'=>13,
                    'price'=>$request->input('rate')[$key] ?? 0,
                    'sub_ic_id'=>$row,
                    'main_ic_id'=>$subitem->main_ic_id,
                    'qty'=>$request->input('qty')[$key] ?? 0,
                    'value'=>$request->input('amount')[$key] ?? 0,
                    'status'=>1,
                    'location_id'=>$request->input('warehouse_from')[$key],
                    'warehouse_from_id'=>$request->input('warehouse_from')[$key],
                    'warehouse_to_id'=>$request->input('warehouse_to')[$key],
                    'accounting_year' => Session::get('accountYear'),                  
                    // 'transfer_status'=>1,
                    // 'description'=>'Transfer',
                    'username'=>Auth::user()->username,
                    'date'=>date('Y-m-d'),
                    'time' => date("H:i:s"),
                    'company_id' => getSessionCompanyId()
                );
                if($request->voucher_type == 2){
                    $fara = Fara::where('id', $request->input('fara_row_id')[$key])->first();                    
                    $stock['main_id'] = $fara->main_id;
                    $stock['master_id'] = $fara->master_id;
                    $stock['unit_type'] = $fara->unit_type;
                    $stock['iot'] = $fara->iot ?? '';
                    $stock['imei'] = $fara->imei ?? '';
                    $stock['serial_no'] = $fara->serial_no ?? '';
                    $stock['sim'] = $fara->sim ?? '';
                    $stock['batch'] = $fara->batch ?? '';
                    $stock['process_id'] = $fara->process_id ?? '';
                }
                // dd($stock, $request->input('fara_row_id')[$key]);
               DB::Connection('tenant')->table('fara')->insert($stock);

               
                $stock1=array
                (
                    'main_id'=>$master_id,
                    'master_id'=>$master_data_id,
                    'voucher_no'=>$uniq,
                    'voucher_date'=>$request->tr_date,
                    'supp_id'=>0,
                    'action'=>12,
                    'price'=>$request->input('rate')[$key] ?? 0,
                    'sub_ic_id'=>$row,
                    'main_ic_id'=>$subitem->main_ic_id,
                    'qty'=>$request->input('qty')[$key] ?? 0,
                    'value'=>$request->input('amount')[$key] ?? 0,
                    'status'=>1,
                    'location_id'=>$request->input('warehouse_to')[$key],
                    'warehouse_from_id'=>$request->input('warehouse_from')[$key],
                    'warehouse_to_id'=>$request->input('warehouse_to')[$key],
                    'accounting_year' => Session::get('accountYear'),
                    // 'transfer_status'=>1,
                    // 'description'=>'Transfer',
                    'username'=>Auth::user()->username,
                    'date'=>date('Y-m-d'),
                    'time' => date("H:i:s"),  
                    'company_id' => getSessionCompanyId()
 
                );
                if($request->voucher_type == 2){
                    $fara = Fara::where('id', $request->input('fara_row_id')[$key])->first();                    
                    $stock1['main_id'] = $fara->main_id;
                    $stock1['master_id'] = $fara->master_id;
                    $stock1['unit_type'] = $fara->unit_type;
                    $stock1['iot'] = $fara->iot ?? '';
                    $stock1['imei'] = $fara->imei ?? '';
                    $stock1['serial_no'] = $fara->serial_no ?? '';
                    $stock1['sim'] = $fara->sim ?? '';
                    $stock1['batch'] = $fara->batch ?? '';
                    $stock1['process_id'] = $fara->process_id ?? '';
                }
                
               DB::Connection('tenant')->table('fara')->insert($stock1);


            endforeach;

            // CommonHelper::inventory_activity($uniq,$request->tr_date,$TotAmount,6,'Insert');


            DB::Connection('tenant')->commit();
        }
        catch(\Exception $e)
        {
            DB::Connection('tenant')->rollback();
            echo "EROOR"; //die();
            echo $e->getTraceAsString();
            dd($e->getMessage());
        }

        Session::flash('dataInsert', 'Stock Transfer Successfully Saved.');

        return Redirect::to('store/stock_transfer_list?pageType=view&&parentCode=95&&m=' . $m);

    }
}
