<?php

namespace App\Repositories;

use App\Repositories\Interfaces\PaymentRepositoryInterface;
use App\Models\Payment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class PaymentRepository implements PaymentRepositoryInterface
{

    public function allPayments($data)
    {
        $status = $data['filterStatus'];
        $fromDate = $data['from_date'];
        $toDate = $data['to_date'];
        $companyId = Session::get('company_id');
        $companyLocationId = Session::get('company_location_id');
        $filterVoucherType = $data['filterVoucherType'];
        return Payment::status($status) // Apply the status scope
            ->when($filterVoucherType != '', function ($query) use ($filterVoucherType) {
                return $query->where('voucher_type', $filterVoucherType);
            })
            ->with([
                'payment_data' => function ($query) {
                    $query->select('payment_data.payment_id', 'payment_data.amount', 'payment_data.acc_id','payment_data.debit_credit') // Specify columns from payment_data table
                        ->with('account:id,name'); // Eager load the account relationship
                }
            ])
            ->whereBetween('pv_date',[$fromDate,$toDate])
            ->where('company_id',$companyId)
            ->OrderBy('pv_date', 'asc')
            ->where('company_location_id',$companyLocationId)
            ->where('entry_option',1)
            ->get();
    }

    public function storePayment($data)
    {
        date_default_timezone_set("Asia/Karachi");
        $pvNo = Payment::VoucherNo($data['voucher_type']);
        $data1['company_id']    = Session::get('company_id');
        $data1['company_location_id']    = Session::get('company_location_id');
        $data1['pv_date']       = $data['pv_date'];
        $data1['date']          = date("Y-m-d");
        $data1['time']          = date("H:i:s");
        $data1['pv_no']         = $pvNo;
        $data1['slip_no']       = $data['slip_no'];
        $data1['voucher_type']  = $data['voucher_type'];
        $data1['paid_to']       = $data['paid_to'];
        $data1['cheque_no']     = $data['cheque_no'];
        $data1['cheque_date']   = $data['cheque_date'];
        $data1['description']   = $data['description'];
        $data1['username']      = Auth::user()->name;
        $data1['date']          = date("Y-m-d");
        $data1['time']          = date("H:i:s");
        $paymentId = DB::table('payments')->insertGetId($data1);

        $data2['payment_id'] = $paymentId;
        $data2['acc_id'] = $data['debit_account_id'];
        $data2['description'] = $data['description'];
        $data2['debit_credit'] = 1;
        $data2['amount'] = $data['amount'];
        $data2['time'] = date("H:i:s");
        $data2['date'] = date("Y-m-d");
        $data2['username'] = Auth::user()->name;

        DB::table('payment_data')->insert($data2);

        $data3['payment_id'] = $paymentId;
        $data3['acc_id'] = $data['credit_account_id'];
        $data3['description'] = $data['description'];
        $data3['debit_credit'] = 2;
        $data3['amount'] = $data['amount'];
        $data3['time'] = date("H:i:s");
        $data3['date'] = date("Y-m-d");
        $data3['username'] = Auth::user()->name;

        return DB::table('payment_data')->insert($data3);
    }

    public function findPayment($id)
    {
        return Payment::find($id);
    }

    public function updatePayment($data, $id)
    {
        //$class = Classes::where('id', $id)->update($data);
    }

    public function changePaymentStatus($id,$status)
    {
        //$class = Classes::where('id',$id)->update(['status' => $status]);
    }
}
