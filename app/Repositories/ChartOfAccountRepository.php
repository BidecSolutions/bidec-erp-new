<?php

namespace App\Repositories;

use App\Repositories\Interfaces\ChartOfAccountRepositoryInterface;
use App\Models\ChartOfAccount;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class ChartOfAccountRepository implements ChartOfAccountRepositoryInterface
{

    public function allChartOfAccounts($data)
    {
        $status = $data['filterStatus'];
        return ChartOfAccount::status($status)->with(['parent' => function ($query) {
            $query->get(['name']);
        },])
            ->where('company_id', Session::get('company_id'))
            ->where('company_location_id', Session::get('company_location_id'))
            ->orderBy('code', 'ASC')
            ->get();
    }

    public function storeChartOfAccount($data)
    {
        date_default_timezone_set("Asia/Karachi");
        $code = ChartOfAccount::GenerateAccountCode($data['parent_code']);
        $level_array = explode('-', $code);
        $first_level = $level_array[0];
        $counter = 1;
        foreach ($level_array as $level):
            $data1['level' . $counter] = $level;
            $counter++;
        endforeach;
        $data1['company_id'] = Session::get('company_id');
        $data1['company_location_id'] = Session::get('company_location_id');
        $data1['code'] = $code;
        $data1['name'] = $data['name'];
        $data1['coa_type'] = 1;
        $data1['parent_code'] = $data['parent_code'];
        $data1['username']          = Auth::user()->name;
        $data1['date']            = date("Y-m-d");
        $data1['time']            = date("H:i:s");
        $data1['company_id'] = Session::get('company_id');
        $data1['company_location_id'] = Session::get('company_location_id');
        if($data['parent_code'] == 0){
            $data1['ledger_type'] = $data['ledger_type'] ?? 1;
        }else{
            $getLedgerTypeValue = DB::table('chart_of_accounts')
                ->where('code', $first_level)
                ->where('company_id', Session::get('company_id'))
                ->value('ledger_type');
            $data1['ledger_type'] = $getLedgerTypeValue;
        }
        DB::table('chart_of_accounts')->insert($data1);

        return generate_json('chart_of_accounts');
    }

    public function findChartOfAccount($id)
    {
        $chartOfAccount = ChartOfAccount::find($id);

        if (!$chartOfAccount) {
            abort(404, 'Account not found');
        }

        // Condition 1: Used as parent_code in any other ChartOfAccount
        $isUsedAsParent = ChartOfAccount::where('parent_code', $chartOfAccount->code)->exists();

        // Condition 2: Used in payment_data, receipt_data, or journal_voucher_data tables
        $isUsedInTransactions = DB::table('payment_data')->where('acc_id', $chartOfAccount->id)->exists() ||
            DB::table('receipt_data')->where('acc_id', $chartOfAccount->id)->exists() ||
            DB::table('journal_voucher_data')->where('acc_id', $chartOfAccount->id)->exists() || 
            DB::table('chart_of_account_settings')->where('acc_id', $chartOfAccount->id)->exists();

        $disableFields = $isUsedAsParent || $isUsedInTransactions;
        return [
            'chartOfAccount' => $chartOfAccount,
            'disable_fields' => $disableFields,
        ];
    }


    public function updateChartOfAccount($data, $id, $onlyUpdateName = false)
    {
        $chartOfAccount = ChartOfAccount::find($id);

        if (!$chartOfAccount) {
            return response()->json(['message' => 'Chart of Account not found'], 404);
        }

        if ($onlyUpdateName) {
            if($data['parent_code'] == 0){
                DB::table('chart_of_accounts')
                    ->where('id', $id)
                    ->update([
                        'name' => $data['name'],
                        'ledger_type' => $data['ledger_type'],
                        'username' => Auth::user()->name,
                        'date' => date("Y-m-d"),
                        'time' => date("H:i:s"),
                    ]);
            }else{
                $getLedgerTypeValue = DB::table('chart_of_accounts')
                    ->where('code', $chartOfAccount->level1)
                    ->where('company_id', Session::get('company_id'))
                    ->value('ledger_type');
                DB::table('chart_of_accounts')
                    ->where('id', $id)
                    ->update([
                        'name' => $data['name'],
                        'ledger_type' => $getLedgerTypeValue,
                        'username' => Auth::user()->name,
                        'date' => date("Y-m-d"),
                        'time' => date("H:i:s"),
                    ]);
            }
        } else {
            // Generate new account code
            $code = ChartOfAccount::GenerateAccountCode($data['parent_code']);
            $level_array = explode('-', $code);
            $counter = 1;
            $first_level = $level_array[0];
            // Build update data
            $updateData = [
                'company_id' => Session::get('company_id'),
                'company_location_id' => Session::get('company_location_id'),
                'code' => $code,
                'name' => $data['name'],
                'parent_code' => $data['parent_code'],
                'username' => Auth::user()->name,
                'date' => date("Y-m-d"),
                'time' => date("H:i:s"),
                'coa_type' => 1,
            ];
            
            if($data['parent_code'] == 0){
                $updateData['ledger_type'] = $data['ledger_type'];
            }else{
                $getLedgerTypeValue = DB::table('chart_of_accounts')
                    ->where('code', $first_level)
                    ->where('company_id', Session::get('company_id'))
                    ->value('ledger_type');
                $updateData['ledger_type'] = $getLedgerTypeValue;
            }

            foreach ($level_array as $level) {
                $updateData['level' . $counter++] = $level;
            }

            DB::table('chart_of_accounts')
                ->where('id', $id)
                ->update($updateData);
        }

        return response()->json(['message' => 'Chart of Account updated successfully'], 200);
    }


    public function changeChartOfAccountStatus($id, $status)
    {
        //$city = City::where('id',$id)->update(['status' => $status]);
    }
}
