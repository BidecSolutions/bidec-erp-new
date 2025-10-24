<?php

namespace App\Repositories;

use App\Repositories\Interfaces\ReceiptRepositoryInterface;
use App\Models\Receipt;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class ReceiptRepository implements ReceiptRepositoryInterface
{

    public function allReceipts($data)
    {
        $status = $data['filterStatus'];
        $companyId = Session::get('company_id');
        $companyLocationId = Session::get('company_location_id');
        $filterVoucherType = $data['filterVoucherType'];
        $fromDate = $data['from_date'];
        $toDate = $data['to_date'];
        return Receipt::status($status)
            ->when($filterVoucherType != '', function ($q) use ($filterVoucherType) {
                return $q->where('voucher_type','=',$filterVoucherType);
            })
            ->whereBetween('rv_date',[$fromDate,$toDate])
            ->OrderBy('rv_date', 'asc')
            ->where('company_id',$companyId)
            ->where('company_location_id',$companyLocationId)
            ->where('entry_option',1)
            ->get();
    }

    public function storeReceipt($data)
    {
        date_default_timezone_set("Asia/Karachi");
        $rvNo = Receipt::VoucherNo($data['voucher_type']);
        $data1['company_id']    = Session::get('company_id');
        $data1['company_location_id']    = Session::get('company_location_id');
        $data1['rv_date']       = $data['rv_date'];
        $data1['date']          = date("Y-m-d");
        $data1['time']          = date("H:i:s");
        $data1['rv_no']         = $rvNo;
        $data1['slip_no']       = $data['slip_no'];
        $data1['voucher_type']  = $data['voucher_type'];
        $data1['receipt_to']    = $data['receipt_to'];
        $data1['cheque_no']     = $data['cheque_no'];
        $data1['cheque_date']   = $data['cheque_date'];
        $data1['description']   = $data['description'];
        $data1['username']      = Auth::user()->name;
        $data1['date']          = date("Y-m-d");
        $data1['time']          = date("H:i:s");
        $receiptId = DB::table('receipts')->insertGetId($data1);

        $data2['receipt_id'] = $receiptId;
        $data2['acc_id'] = $data['debit_account_id'];
        $data2['description'] = $data['description'];
        $data2['debit_credit'] = 1;
        $data2['amount'] = $data['amount'];
        $data2['time'] = date("H:i:s");
        $data2['date'] = date("Y-m-d");
        $data2['username'] = Auth::user()->name;

        DB::table('receipt_data')->insert($data2);

        $data3['receipt_id'] = $receiptId;
        $data3['acc_id'] = $data['credit_account_id'];
        $data3['description'] = $data['description'];
        $data3['debit_credit'] = 2;
        $data3['amount'] = $data['amount'];
        $data3['time'] = date("H:i:s");
        $data3['date'] = date("Y-m-d");
        $data3['username'] = Auth::user()->name;

        return DB::table('receipt_data')->insert($data3);
    }

    public function findReceipt($id)
    {
        return Receipt::find($id);
    }

    public function updateReceipt($data, $id)
    {
        // $class = ClassTimings::where('id', $id)->first();
        // $class->name = $data['name'];
        // $class->save();
    }

    public function changeReceiptStatus($id,$status)
    {
        //$classTiming = ClassTimings::where('id',$id)->update(['status' => $status]);
    }
}
