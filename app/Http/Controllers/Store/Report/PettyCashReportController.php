<?php

namespace App\Http\Controllers\Store\Report;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\Transactions;
use App\Models\PurchaseOrder;
use DB;

class PettyCashReportController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth','MultiDB']);
    }
    public function stockReport(Request $request)
    {
        $from = date("Y-m-d", mktime(0, 0, 0, date("m")-1, 1));
        $to = date("Y-m-d", mktime(0, 0, 0, date("m"), 0));
        if(!empty($request->all()))
        { 
            // dd($request->all());
        $from  = date("Y-m-d", strtotime($request->fromDate));
        $to = date("Y-m-d", strtotime($request->toDate));
        }
  
    $data2 = PurchaseOrder::join('purchase_order_data','purchase_order_data.purchase_order_no','purchase_order.purchase_order_no')
    ->leftJoin('payment_data_against_po','purchase_order.id','payment_data_against_po.po_id')
    ->join('project','project.id','purchase_order.project_id')
   ->join('sub_department','sub_department.id','purchase_order.sub_department_id')
   ->join('supplier','supplier.id','purchase_order_data.supplier_id')
   ->join('location','location.id','purchase_order.location_id')
   ->select('location.location_name',
   'project.project_name',
   'supplier.name',
   'sub_department.sub_department_name',
   'purchase_order_data.sub_item_id',
   'purchase_order.purchase_request_date',
   'purchase_order.purchase_request_no',
   'purchase_order_data.purchase_order_no',
   'purchase_order_data.purchase_order_qty',
   'purchase_order_data.unit_price',
   'purchase_order_data.sub_total',
   'purchase_order_data.id',
   'purchase_order.purchase_order_date');
   
   if(!empty($from) && !empty($to)){ 
    $data2->whereBetween('purchase_order.purchase_order_date',[$from,$to]);
   }
   $data2->where('supplier.supplier_type',1);
   $record = $data2->get();
    
   $query = '
   SELECT 
       ecv.id, ecv.ev_no, ecvd.qty,ecvd.expense_amount, ecv.expense_date, ecv.description, ecv.status, ecv.created_at, ecv.updated_at, s.name, p.project_name, sd.sub_department_name, d.department_name, l.location_name
   FROM expense_claim_voucher_datas as ecvd
   INNER JOIN expense_claim_vouchers as ecv on ecvd.master_id = ecv.id
   INNER JOIN supplier as s on ecv.supplier_id = s.id 
   LEFT JOIN project as p on ecv.project_id = p.id
   LEFT JOIN location as l on ecv.location_id = l.id
   LEFT JOIN department as d on ecv.department_id = d.id
   LEFT JOIN sub_department as sd on ecv.sub_department_id = sd.id';

    
    if (!empty($from) && !empty($to)) {
        $query .= ' WHERE ecv.expense_date BETWEEN "' . $from . '" AND "' . $to . '"';
        $query .= ' AND s.supplier_type = 1';
    }else{
        $query .= ' WHERE s.supplier_type = 1';
    }
    
    $vouchers = DB::connection('tenant')->select($query);

        return view('Store.Report.pettycash')->with(['stocks'=>$record,'vouchers'=>$vouchers]);
    }

    public function pettyCashReport()
    {
          

    //    $account_petty_cash = Account::where('parent_code',$account->code)
    //    ->join('transactions','transactions.acc_code','accounts.code')->get();

    //   $transaction =  Transactions::join('accounts','accounts.id','transactions.acc_id')
    //   ->where('accounts.parent_code',$account->code)
    //   ->get();

    //    $transaction= Transaction::where('acc_code','like','%'.$account->code.'%');
    //    $accoun2 = DB::connection('tenant')->select("SELECT * From accounts where code like '% 1-2-7-1%");
       
  $account = Account::where('code','1-2-7-1')->first();
    
    // dd($account);

        $data = PurchaseOrder::join('purchase_request','purchase_request.purchase_request_no','purchase_order.purchase_request_no')
            ->join('pvs','pvs.po_no','purchase_order.purchase_order_no')
            ->join('pv_data','pv_data.pv_no','pvs.pv_no')
            ->join('accounts','accounts.id','pv_data.acc_id')
            ->join('project','project.id','purchase_order.project_id')
            ->join('sub_department','sub_department.id','purchase_order.sub_department_id')
            ->join('location','location.id','purchase_order.location_id')
           ->where('accounts.parent_code',$account->code)
           ->get();
            // dd($data);
           return view('Store.Report.pettycashorignal')->with(['pattyCash'=>$data]);
    }
}
