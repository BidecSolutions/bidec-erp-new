<?php

namespace App\Http\Controllers\Store;
use App\Http\Controllers\Controller;

use App\Http\Requests;
use App\Models\Location;
use Illuminate\Http\Request;
use CommonFacades;
use Input;
use DB;
use Config;
use PurchaseFacades;
use Session;
class StoreMakeFormAjaxLoadController extends Controller
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

    

    public function addMoreMaterialRequestsDetailRows(Request $request)
    {
        $counter = $request->input('counter');
        $id = $request->input('id');

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
                foreach ($product['variants'] as &$variant) {
                    $variant['size_name'] = $sizeMap[$variant['size_id']] ?? '-';
                }
                return $product;
            })
            ->where('status', 1)
            ->values()
            ->toArray();

        // Return partial view
        return view('Store.partials.material_request_row', compact('products', 'id', 'counter'));
    }

    public function makeFormPurchaseOrderDetailByPRNo(){
        $m = CommonFacades::getSessionCompanyId();
        $makeGetValue = explode('<*>',$_GET['prNo']);
        $prNo = $makeGetValue[0];
        $prDate = $makeGetValue[1];
        
        
        $getPurchaseRequestDetail = DB::selectOne("select 
            purchase_request.id,
            purchase_request.accounting_year,
            purchase_request.company_id,
            purchase_request.purchase_request_type,
            purchase_request.purchase_request_no,
            purchase_request.purchase_request_date,
            purchase_request.department_id,
            purchase_request.sub_department_id,
            purchase_request.location_id,
            purchase_request.project_id,
            purchase_request.required_date,
            purchase_request.description,
            purchase_request.purchase_request_status,
            purchase_request.status,
            purchase_request.date,
            purchase_request.time,
            purchase_request.username,
            purchase_request.user_id,
            purchase_request.approve_username,
            purchase_request.approve_date,
            purchase_request.delete_username,
            purchase_request.packing_list_status,
            purchase_request.purchase_request_type,
            sub_department.sub_department_name,
            location.location_name,
            project.project_name,
            department.department_name
        from purchase_request 
        INNER JOIN sub_department ON purchase_request.sub_department_id = sub_department.id
        INNER JOIN department ON sub_department.department_id = department.id 
        INNER JOIN location ON purchase_request.location_id = location.id
        INNER JOIN project ON purchase_request.project_id = project.id 
        where purchase_request.purchase_request_no = '".$prNo."'");
        $getPurchaseRequestDataDetail = DB::select("select
            purchase_request_data.id,
            purchase_request_data.accounting_year,
            purchase_request_data.company_id,
            purchase_request_data.purchase_request_no,
            purchase_request_data.purchase_request_date,
            purchase_request_data.required_date,
            purchase_request_data.qty,
            purchase_request_data.approx_cost,
            purchase_request_data.approx_sub_total,
            purchase_request_data.purchase_request_status,
            purchase_request_data.store_challan_status,
            purchase_request_data.purchase_order_status,
            purchase_request_data.goods_forward_status,
            purchase_request_data.purchase_request_send_type,
            purchase_request_data.status,
            purchase_request_data.date,
            purchase_request_data.time,
            purchase_request_data.username,
            purchase_request_data.user_id,
            purchase_request_data.approve_username,
            purchase_request_data.delete_username,
            purchase_request_data.packing_list_status,
            purchase_request_data.category_id,
            purchase_request_data.sub_item_id,
            purchase_request_data.uom_id,
            category.main_ic,
            subitem.item_code,
            subitem.sub_ic,
            (select sum(purchase_order_qty) from purchase_order_data where purchase_request_data.id = purchase_order_data.purchase_request_data_record_id and purchase_order_data.status = 1) as priviousPurchaseOrderQty
        from purchase_request_data
        INNER JOIN category ON purchase_request_data.category_id = category.id
        INNER JOIN subitem ON purchase_request_data.sub_item_id = subitem.id
        where purchase_request_data.purchase_request_no = '".$prNo."' and purchase_request_data.purchase_order_status = 1");
        
        $accounts = DB::connection('tenant')->table("accounts")->select('*')
            ->whereNotIn('code',function($query){
                $query->select('parent_code')->from('accounts')->where('status','=','1');
            })
            ->where('status','=','1')
            ->where('level1','=',4)
            ->orderBy('level1', 'ASC')
            ->orderBy('level2', 'ASC')
            ->orderBy('level3', 'ASC')
            ->orderBy('level4', 'ASC')
            ->orderBy('level5', 'ASC')
            ->orderBy('level6', 'ASC')
            ->orderBy('level7', 'ASC')
            ->get();
        
        $paymentTypeList = DB::connection('tenant')->table('payment_type')->get();
        // dd($accounts);
        return view('Store.AjaxPages.makeFormPurchaseOrderDetailByPRNo',compact('getPurchaseRequestDetail','getPurchaseRequestDataDetail', 'accounts','paymentTypeList'));
        
    }

    public function makeFormStoreChallanDetailByMRNo(){
        $m = CommonFacades::getSessionCompanyId();
        $makeGetValue = explode('<*>',$_GET['mrNo']);
        $mrNo = $makeGetValue[0];
        $mrDate = $makeGetValue[1];
        
        
        $getMaterialRequestDetail = DB::selectOne("select 
            material_request.id,
            material_request.accounting_year,
            material_request.company_id,
            material_request.material_request_no,
            material_request.material_request_date,
            material_request.department_id,
            material_request.sub_department_id,
            material_request.location_id,
            material_request.project_id,
            material_request.required_date,
            material_request.description,
            material_request.material_request_status,
            material_request.status,
            material_request.date,
            material_request.time,
            material_request.username,
            material_request.user_id,
            material_request.approve_username,
            material_request.approve_date,
            material_request.delete_username,
            sub_department.sub_department_name,
            location.location_name,
            project.project_name,
            department.department_name
        from material_request 
        INNER JOIN sub_department ON material_request.sub_department_id = sub_department.id
        INNER JOIN department ON sub_department.department_id = department.id 
        INNER JOIN location ON material_request.location_id = location.id
        INNER JOIN project ON material_request.project_id = project.id 
        where material_request.material_request_no = '".$mrNo."'");
        $getMaterialRequestDataDetail = DB::select("select
            material_request_data.id,
            material_request_data.accounting_year,
            material_request_data.company_id,
            material_request_data.material_request_no,
            material_request_data.material_request_date,
            material_request_data.required_date,
            material_request_data.qty,
            material_request_data.approx_cost,
            material_request_data.approx_sub_total,
            material_request_data.material_request_status,
            material_request_data.store_challan_status,
            material_request_data.status,
            material_request_data.date,
            material_request_data.time,
            material_request_data.username,
            material_request_data.user_id,
            material_request_data.approve_username,
            material_request_data.delete_username,
            material_request_data.category_id,
            material_request_data.sub_item_id,
            material_request_data.uom_id,
            category.main_ic,
            subitem.item_code,
            subitem.sub_ic,
            uom.uom_name,
            (SELECT COALESCE(SUM(store_challan_data.issue_qty),0) FROM store_challan_data WHERE store_challan_data.material_request_data_id = material_request_data.id GROUP BY store_challan_data.material_request_data_id) as totalIssueQty
        from material_request_data
        INNER JOIN category ON material_request_data.category_id = category.id
        INNER JOIN subitem ON material_request_data.sub_item_id = subitem.id
        INNER JOIN uom ON subitem.uom = uom.id
        where material_request_data.material_request_no = '".$mrNo."' and material_request_data.store_challan_status = 1");

        // location wise current balances array
        $locations = Location::all();
        $itemsBalanceLocationWise = [];
        foreach($getMaterialRequestDataDetail as $itemKey => $gmrddRow){
            foreach ($locations as $key => $location) {
                // $itemsBalanceLocationWise[$itemKey][$location->id] = CommonFacades::checkItemWiseCurrentBalanceQtyNew($m,$gmrddRow->category_id,$gmrddRow->sub_item_id,'',date('Y-m-d'),$location->id);
                $itemsBalanceLocationWise[$itemKey][$location->id] = CommonFacades::stockLocationWiseSum($gmrddRow->category_id, $gmrddRow->sub_item_id, $location->id, 1);           
            }
        }
        // dd($itemsBalanceLocationWise);
        return view('Store.AjaxPages.makeFormStoreChallanDetailByMRNo',compact('getMaterialRequestDetail','getMaterialRequestDataDetail', 'itemsBalanceLocationWise'));
        
    }
    public function makeFormStoreChallanReturnByMRNo(){
        $m = CommonFacades::getSessionCompanyId();
        $makeGetValue = explode('<*>',$_GET['mrNo']);
        $mrNo = $makeGetValue[0];
        $mrDate = $makeGetValue[1];
        
        
        $getMaterialRequestDetail = DB::selectOne("select 
            store_challan.id,
            store_challan.accounting_year,
            store_challan.company_id,
            store_challan.material_request_no,
            store_challan.material_request_date,
            store_challan.department_id,
            store_challan.sub_department_id,
            store_challan.location_id,
            store_challan.project_id,
            store_challan.description,
            store_challan.store_challan_status,
            store_challan.status,
            store_challan.purpose,
            store_challan.warehouse_from_id,
            store_challan.warehouse_to_id,
            store_challan.from_sub_department_id,
            store_challan.date,
            store_challan.time,
            store_challan.username,
            store_challan.user_id,
            store_challan.approve_username,
            store_challan.approve_date,
            store_challan.delete_username,
            sub_department.sub_department_name,
            location.location_name,
            project.project_name,
            department.department_name
        from store_challan 
        INNER JOIN sub_department ON store_challan.sub_department_id = sub_department.id
        INNER JOIN department ON sub_department.department_id = department.id 
        INNER JOIN location ON store_challan.location_id = location.id
        INNER JOIN project ON store_challan.project_id = project.id 
        where store_challan.store_challan_no = '".$mrNo."'");

        
        // $getMaterialRequestDataDetail = DB::select("select
        //     store_challan_data.id,
        //     store_challan_data.accounting_year,
        //     store_challan_data.company_id,
        //     store_challan_data.store_challan_no,
        //     store_challan_data.store_challan_date,
        //     material_request_data.required_date,
        //     material_request_data.qty,
        //     material_request_data.approx_cost,
        //     material_request_data.approx_sub_total,
        //     store_challan_data.store_challan_status,
        //     store_challan_data.status,
        //     store_challan_data.date,
        //     store_challan_data.time,
        //     store_challan_data.username,
        //     store_challan_data.user_id,
        //     store_challan_data.approve_username,
        //     store_challan_data.delete_username,
        //     store_challan_data.category_id,
        //     store_challan_data.sub_item_id,
        //     material_request_data.uom_id,
        //     category.main_ic,
        //     subitem.item_code,
        //     subitem.sub_ic,
        //     uom.uom_name,
        //     (SELECT COALESCE(SUM(store_challan_data.issue_qty),0) FROM store_challan_data WHERE store_challan_data.material_request_data_id = store_challan_data.id GROUP BY store_challan_data.material_request_data_id limit 1) as totalIssueQty
        // from store_challan_data
        // INNER JOIN category ON store_challan_data.category_id = category.id
        // INNER JOIN subitem ON store_challan_data.sub_item_id = subitem.id
        // INNER JOIN material_request_data ON material_request_data.id = store_challan_data.material_request_data_id
        // INNER JOIN uom ON subitem.uom = uom.id
        // where store_challan_data.store_challan_no = '".$mrNo."' and store_challan_data.store_challan_status = 2");

        $getMaterialRequestDataDetail = DB::table('store_challan_data')
        ->select(
            'store_challan_data.id',
            'store_challan_data.accounting_year',
            'store_challan_data.company_id',
            'store_challan_data.store_challan_no',
            'store_challan_data.store_challan_date',
            'store_challan_data.store_challan_status',
            'store_challan_data.status',
            'store_challan_data.date',
            'store_challan_data.time',
            'store_challan_data.username',
            'store_challan_data.user_id',
            'store_challan_data.approve_username',
            'store_challan_data.delete_username',
            'store_challan_data.category_id',
            'store_challan_data.sub_item_id',
            'store_challan_data.issue_qty',
            'subitem.uom',
            'category.main_ic',
            'subitem.item_code',
            'subitem.sub_ic',
            'uom.uom_name',
            DB::raw('(SELECT COALESCE(SUM(store_challan_return_data.return_qty),0) FROM store_challan_return_data WHERE store_challan_data.id = store_challan_return_data.store_challan_data_id GROUP BY store_challan_return_data.store_challan_data_id limit 1) as totalReturnQty')
        )
        ->join('category', 'store_challan_data.category_id', '=', 'category.id')
        ->join('subitem', 'store_challan_data.sub_item_id', '=', 'subitem.id')
        ->join('uom', 'subitem.uom', '=', 'uom.id')
        ->where('store_challan_data.store_challan_status', 2)
        ->where('store_challan_data.issue_qty','!=',0)
        ->where('store_challan_data.store_challan_no',$mrNo)
        ->get();
        // dd($mrNo,$getMaterialRequestDataDetail);
        return view('Store.AjaxPages.makeFormStoreChallanReturnDetailByMRNo',compact('getMaterialRequestDetail','getMaterialRequestDataDetail'));
        
    }
}
?>