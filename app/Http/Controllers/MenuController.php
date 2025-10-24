<?php

namespace App\Http\Controllers;

use App\Helpers\CommonHelper;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\BaseController as BaseController;
use App\Models\Menu;
use Validator;
use App\Http\Resources\MenuResource;
use Illuminate\Http\JsonResponse;
use DataTables;
use Auth;

class MenuController extends Controller
{   
    protected $segment;
    public function __construct(Request $request)
    {
        $this->segment = $request->segment(1);
        $this->page = 'menus.';
    }
    /**
     * Display a listing of the resource.
     */
    
    public function getMenus(): JsonResponse
    {
        $menus = Menu::get();
        return response()->json([
            'status' => 'success',
            'message' => 'Menus Retrieved successfully',
            'data' => $menus,
        ],200);
    }
    public function index(Request $request)
    {
        $pageUrl = CommonHelper::checkPermissionInnerOptions('menus.create');
        $mainTitle = 'Menu';
        $pageTitle = 'View Menus List';
        $status = '';
        $menus = Menu::status($status);
        
        if ($this->segment == 'api') {
            $menus = $menus->get();
            return response()->json([
                'status' => 'success',
                'message' => 'Menus Retrieved successfully',
                'data' => $menus,
            ],200);
        }else{
            $userCanEdit = Auth::user()->can('menus.edit');
            $userCanStatus = Auth::user()->can('menus.status');
            if($request->ajax()){
                return Datatables::of($menus)
                    ->addIndexColumn()
                    ->addColumn('action', function ($model) use ($userCanEdit) {
                        return view('layouts.action.table', [
                            'editRoute' => $userCanEdit ? route('menus.edit', ['id' => $model->id]) : null
                        ]);
                    })
                    ->addColumn('status',function ($model) use ($userCanStatus){
                        if ($userCanStatus) {
                            return view('layouts.action.status', ['status' => $model->status, 'id' => $model->id]);
                        } else {
                            return $model->status == 1 ? 'Active' : 'Inactive';
                        }
                        
                    })
                    ->rawColumns(['action'])
                    ->make(true);
            }
            return view($this->page.'index',compact('mainTitle','pageTitle','pageUrl'));
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $pageUrl = CommonHelper::checkPermissionInnerOptions('menus.index');
        $mainTitle = 'Menu';
        $pageTitle = 'Add New Menu';
        $menuType = array(
            '1' => 'User',
            '2' => 'Finance',
            '3' => 'Purchase',
            '4' => 'Store',
            '5' => 'Sale',
            '6' => 'HR',
            '7' => 'Reports',
            '8' => 'Dashboard',
            '9' => 'General Setting',
            '10' => 'General Option'
        );
        return view($this->page.'create',compact('mainTitle','pageTitle','menuType','pageUrl'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        
        try {
            $input = $request->all();
    
            $validator = Validator::make($input, [
                'menu_type' => 'required',
                'menu_icon' => 'required|regex:/^[a-zA-Z0-9\s\-\_\@\#\$\%\^\&\*\(\)]+$/',
                'menu_name' => 'required|regex:/^[a-zA-Z0-9\s\-\_\@\#\$\%\^\&\*\(\)]+$/|unique:menus,menu_name'
            ]);
    
            if($validator->fails()){
                return response()->json([
                    'status' => 'warning',
                    'message' => $validator->errors()->first(),
                ]);       
            }
    
            $menu = Menu::create($input);
    
            return response()->json([
                'status' => 'success',
                'message' => 'Menu add successfully',
                'data' => null,
            ],200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'message' => $th->getMessage(),
                'data' => null,
            ],500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id): JsonResponse
    {
        $menu = Menu::find($id);
  
        if (is_null($menu)) {
            return response()->json([
                'status' => 'warning',
                'message' => 'Menu Not Found',
            ]);
        }
   
        return $this->sendResponse(new MenuResource($menu), 'Menu retrieved successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $pageUrl = CommonHelper::checkPermissionInnerOptions('menus.index');
        $menu = Menu::find($id);
  
        if (is_null($menu)) {
            return response()->json([
                'status' => 'warning',
                'message' => 'Menu Not Found',
            ]);
        }
        $mainTitle = 'Menu';
        $pageTitle = 'Edit Menu Detail';
        $menuType = array(
            '1' => 'User',
            '2' => 'Finance',
            '3' => 'Purchase',
            '4' => 'Store',
            '5' => 'Sale',
            '6' => 'HR',
            '7' => 'Reports',
            '8' => 'Dashboard',
            '9' => 'General Setting',
            '10' => 'General Option'
        );
        return view($this->page.'edit', compact('menu','mainTitle','pageTitle','menuType','pageUrl'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request): JsonResponse
    {
        try {
            $id = $request->input('id');
            $menu = Menu::find($id);
            if (is_null($menu)) {
                return response()->json([
                    'status' => 'warning',
                    'message' => 'Menu Not Found',
                ]);
            }
            $input = $request->all();
            
            $validator = Validator::make($input, [
                'menu_type' => 'required',
                'menu_icon' => 'required|regex:/^[a-zA-Z0-9\s\-\_\@\#\$\%\^\&\*\(\)]+$/',
                'menu_name' => 'required|regex:/^[a-zA-Z0-9\s\-\_\@\#\$\%\^\&\*\(\)]+$/|unique:menus,menu_name,'.$id.',id'
            ]);
    
            if($validator->fails()){
                return response()->json([
                    'status' => 'warning',
                    'message' => $validator->errors()->first(),
                ]);       
            }
    
            $menu->menu_icon = $input['menu_icon'];
            $menu->menu_name = $input['menu_name'];
            $menu->menu_type = $input['menu_type'];
            $menu->save();
    
            return response()->json([
                'status' => 'success',
                'message' => 'Menu Updated successfully',
                'data' => null,
            ],200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'message' => $th->getMessage(),
                'data' => null,
            ],500);
        }
    }

    public function status(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'id' => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'status' => 'warning',
                    'message' => $validator->errors()->first(),
                ]);
            }
            $menu = Menu::find($request->id);
            if ($menu == null) {
                return response()->json([
                    'status' => 'warning',
                    'message' => 'Menu Not Found',
                ]);
            }
            $status = $menu->status == 1 ? 2 : 1;
            $menu->status  = $status;
            $menu->save();
            return response()->json([
                'status' => 'success',
                'message' => 'Menu Status Updated successfully',
                'data' => null,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
                'data' => null,
            ], 500);
        }
    }
}
