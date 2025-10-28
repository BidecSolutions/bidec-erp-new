<?php

namespace App\Http\Controllers;

use App\Models\ChartOfAccount;
use Illuminate\Http\Request;
use App\Repositories\Interfaces\ChartOfAccountRepositoryInterface;
use Illuminate\Support\Facades\DB;

class ChartOfAccountController extends Controller
{
    private $chartOfAccountRepository;
    protected $page;
    public function __construct(ChartOfAccountRepositoryInterface $chartOfAccountRepository)
    {
        $this->page = 'Finance.ChartOfAccounts.';
        $this->chartOfAccountRepository = $chartOfAccountRepository;
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $chartOfAccounts =  $this->chartOfAccountRepository->allChartOfAccounts($request->all());
            return view($this->page . 'indexAjax', compact('chartOfAccounts'));
        }
        return view($this->page . 'index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view($this->page . 'create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'debit_credit' => 'required',
            'parent_code' => ''
        ]);
        $this->chartOfAccountRepository->storeChartOfAccount($data);

       return redirect()
    ->route('chartofaccounts.index')
    ->with('success', 'Chart Of Account Created Successfully');

    }

    /**
     * Display the specified resource.
     */
    public function show(ChartOfAccount $chartOfAccount)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {

        $accountData = $this->chartOfAccountRepository->findChartOfAccount($id);

        return view($this->page . 'edit', [
            'chartOfAccount' => $accountData['chartOfAccount'],
            'disable_fields' => $accountData['disable_fields'],
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ChartOfAccount $chartOfAccount, $id)
    {
        // Validate incoming data
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'debit_credit' => 'required',
            'parent_code' => 'nullable',
            'opening_balance' => 'nullable|numeric',
            'old_parent_code' => 'nullable',
            'ledger_type' => 'nullable'
        ]);

        // Fetch the current ChartOfAccount object by its ID
        $chartOfAccount = ChartOfAccount::findOrFail($id);

        // Determine update scope based on parent_code comparison
        $onlyUpdateName = $data['parent_code'] === $data['old_parent_code'];

        // Delegate to repository method with condition
        $this->chartOfAccountRepository->updateChartOfAccount($data, $id, $onlyUpdateName);

       return redirect()
    ->route('chartofaccounts.index')
    ->with('success', 'Chart Of Account Updated Successfully');

    }


    /**
     * Remove the specified resource from storage.
     */
    public function status($id)
    {
        $chartOfAccount = ChartOfAccount::find($id);

        $chartOfAccount->status = 2;
        $chartOfAccount->save();
        return response()->json(['success' => 'Inactive Successfully!']);
    }

    public function changeInactiveToActiveRecord($id)
    {
        //
        $chartOfAccount = ChartOfAccount::find($id);

        $chartOfAccount->status = 1;
        $chartOfAccount->save();
        return response()->json(['success' => 'Active Successfully!']);
    }
}
