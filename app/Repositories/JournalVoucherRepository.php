<?php

namespace App\Repositories;

use App\Repositories\Interfaces\JournalVoucherRepositoryInterface;
use App\Models\JournalVoucher;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class JournalVoucherRepository implements JournalVoucherRepositoryInterface
{
    public function allJournalVouchers($data)
    {
        $status = $data['filterStatus'];
        $companyId = Session::get('company_id');
        $companyLocationId = Session::get('company_location_id');
        $filterVoucherType = $data['filterVoucherType'];
        $fromDate = $data['from_date'];
        $toDate = $data['to_date'];
        return JournalVoucher::status($status) // Apply the status scope
            ->when($filterVoucherType != '', function ($query) use ($filterVoucherType) {
                return $query->where('voucher_type', $filterVoucherType);
            })
            ->with([
                'journal_voucher_data' => function ($query) {
                    $query->select('journal_voucher_data.journal_voucher_id', 'journal_voucher_data.amount', 'journal_voucher_data.acc_id', 'journal_voucher_data.debit_credit') // Specify columns from payment_data table
                        ->with('account:id,name'); // Eager load the account relationship
                }
            ])
            
            ->whereBetween('jv_date', [$fromDate, $toDate])
            ->where('company_id', $companyId)
            ->OrderBy('jv_date', 'desc')
            ->where('company_location_id', $companyLocationId)
            ->get();
    }

    public function storeJournalVoucher($data)
    {
        date_default_timezone_set("Asia/Karachi");
        $jvNo = JournalVoucher::VoucherNo();
        $data1['company_id']    = Session::get('company_id');
        $data1['company_location_id']    = Session::get('company_location_id');
        $data1['jv_date']       = $data['jv_date'];
        $data1['date']          = date("Y-m-d");
        $data1['time']          = date("H:i:s");
        $data1['jv_no']         = $jvNo;
        $data1['slip_no']       = $data['slip_no'];
        $data1['voucher_type']  = $data['voucher_type'];
        $data1['description']   = $data['description'];
        $data1['username']      = Auth::user()->name;
        $data1['date']          = date("Y-m-d");
        $data1['time']          = date("H:i:s");
        $journalVoucherId = DB::table('journal_vouchers')->insertGetId($data1);

        $data2['journal_voucher_id'] = $journalVoucherId;
        $data2['acc_id'] = $data['debit_account_id'];
        $data2['description'] = $data['description'];
        $data2['debit_credit'] = 1;
        $data2['amount'] = $data['amount'];
        $data2['time'] = date("H:i:s");
        $data2['date'] = date("Y-m-d");
        $data2['username'] = Auth::user()->name;

        DB::table('journal_voucher_data')->insert($data2);

        $data3['journal_voucher_id'] = $journalVoucherId;
        $data3['acc_id'] = $data['credit_account_id'];
        $data3['description'] = $data['description'];
        $data3['debit_credit'] = 2;
        $data3['amount'] = $data['amount'];
        $data3['time'] = date("H:i:s");
        $data3['date'] = date("Y-m-d");
        $data3['username'] = Auth::user()->name;

        return DB::table('journal_voucher_data')->insert($data3);
    }

    public function findJournalVoucher($id)
    {
        return JournalVoucher::find($id);
    }

    public function updateJournalVoucher($data, $id)
    {
        date_default_timezone_set("Asia/Karachi");

        $data1['jv_date']       = $data['jv_date'];
        $data1['slip_no']       = $data['slip_no'];
        $data1['voucher_type']  = $data['voucher_type'];
        $data1['description']   = $data['description'];
        $data1['username']      = Auth::user()->name;
        $data1['date']          = date("Y-m-d");
        $data1['time']          = date("H:i:s");

        DB::table('journal_vouchers')->where('id', $id)->update($data1);

        // Delete existing related entries
        DB::table('journal_voucher_data')->where('journal_voucher_id', $id)->delete();

        // Insert updated debit entry
        $data2 = [
            'journal_voucher_id' => $id,
            'acc_id'             => $data['debit_account_id'],
            'description'        => $data['description'],
            'debit_credit'       => 1,
            'amount'             => $data['amount'],
            'date'               => date("Y-m-d"),
            'time'               => date("H:i:s"),
            'username'           => Auth::user()->name
        ];

        DB::table('journal_voucher_data')->insert($data2);

        // Insert updated credit entry
        $data3 = [
            'journal_voucher_id' => $id,
            'acc_id'             => $data['credit_account_id'],
            'description'        => $data['description'],
            'debit_credit'       => 2,
            'amount'             => $data['amount'],
            'date'               => date("Y-m-d"),
            'time'               => date("H:i:s"),
            'username'           => Auth::user()->name
        ];

        DB::table('journal_voucher_data')->insert($data3);
    }


    public function changeJournalVoucherStatus($id, $status)
    {
        //$class = Classes::where('id',$id)->update(['status' => $status]);
    }
}
