<?php

namespace App\Http\Controllers;

use App\Helpers\CommonHelper;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\CustomPermission;
use Illuminate\Support\Facades\Validator;
use Session;

class RoleController extends Controller
{   
    protected $segment;
    
    public function __construct(Request $request)
    {
        $this->segment = $request->segment(1);
        $this->page = 'roles.';
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $status = '';
        $companyId = Session::get('company_id');
        if ($request->ajax()) {
            $roles = Role::where('company_id', $companyId)->get();
            return view($this->page . 'indexAjax', compact('roles'));
        }
        return view($this->page . 'index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $all_permissions = CustomPermission::all();
        $permission_groups = CustomPermission::getpermissionGroups();

        return view($this->page . 'create', compact('permission_groups', 'all_permissions'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validation Data
        $request->validate([
            'name' => 'required|max:100|unique:roles'
        ], [
            'name.required' => 'Please give a role name'
        ]);

        $companyId = Session::get('company_id');

        // Process Data
        $role = Role::create(['name' => $request->name, 'guard_name' => 'web', 'company_id' => $companyId]);

        $permissions = $request->input('permissions');

        if (!empty($permissions)) {
            $role->syncPermissions($permissions);
        }

        return redirect()->route('roles.index')->with('message', 'Role Created Successfully');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $pageUrl = CommonHelper::checkPermissionInnerOptions('roles.index');
        $role = Role::find($id);

        if (is_null($role)) {
            return response()->json([
                'status' => 'warning',
                'message' => 'Role Not Found',
            ]);
        }

        $mainTitle = 'Role';
        $pageTitle = 'Edit Role';

        $all_permissions = CustomPermission::all();
        $permission_groups = CustomPermission::getpermissionGroups();

        return view($this->page . 'edit', compact('mainTitle', 'pageTitle', 'permission_groups', 'all_permissions', 'role', 'pageUrl'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        try {
            $role = Role::find($id);
            if (is_null($role)) {
                return response()->json([
                    'status' => 'warning',
                    'message' => 'Role Not Found',
                ]);
            }

            // Validation Data
            $validator = Validator::make($request->all(), [
                'name' => 'required|max:100|unique:roles,name,' . $id
            ], [
                'name.required' => 'Please give a role name'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'warning',
                    'message' => $validator->errors()->first(),
                ]);
            }

            // Update role name
            $role->name = $request->name;
            $role->save();

            // Sync permissions (Fix: Ensure unchecked ones are removed)
            $role->syncPermissions($request->input('permissions', []));

            return redirect()->route('roles.index')->with('message', 'Role Updated Successfully');
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'message' => $th->getMessage(),
                'data' => null,
            ], 500);
        }
    }
}
