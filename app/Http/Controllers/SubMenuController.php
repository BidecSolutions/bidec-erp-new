<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseController as BaseController;
use App\Helpers\CommonHelper;
use Illuminate\Http\Request;
use App\Models\SubMenu;
use App\Models\CustomPermission;
use Validator;
use App\Http\Resources\SubMenuResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use DataTables;
use Auth;

class SubMenuController extends BaseController
{
    protected $segment;
    public function __construct(Request $request)
    {
        $this->segment = $request->segment(1);
        $this->page = 'sub-menus.';
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $pageUrl = CommonHelper::checkPermissionInnerOptions('sub-menus.create');
        $mainTitle = 'Sub Menu';
        $pageTitle = 'View Sub Menus List';
        $status = '';
        $subMenus = SubMenu::with(['menu' => function ($query) {
            $query->get(['menu_name']);
        },])->status($status);
        if ($this->segment == 'api') {
            $subMenus = $subMenus->get();
            return response()->json([
                'status' => 'success',
                'message' => 'Sub Menus Retrieved successfully',
                'data' => $subMenus,
            ],200);
        }else{
            if($request->ajax()){
                $userCanEdit = Auth::user()->can('sub-menus.edit');
                $userCanStatus = Auth::user()->can('sub-menus.status');
                return Datatables::of($subMenus)
                    ->addIndexColumn()
                    ->addColumn('menu_name', function ($row) {
                        return $row->menu->menu_name;
                    })
                    ->addColumn('action', function ($model) use ($userCanEdit) {
                        return view('layouts.action.table', [
                            'editRoute' => $userCanEdit ? route('sub-menus.edit', ['id' => $model->id]) : null,
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
        $pageUrl = CommonHelper::checkPermissionInnerOptions('sub-menus.index');
        $mainTitle = 'Sub Menu';
        $pageTitle = 'Add New Sub Menu';
        $result = CommonHelper::makeApiRequest(url('menus/getMenus'), 'GET');
        $menus = $result['data'];
        return view($this->page.'create',compact('mainTitle','pageTitle','menus','pageUrl'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $input = $request->all();
    
            $validator = Validator::make($input, [
                'menu_id' => 'required',
                'sub_menu_icon' => 'required',
                'sub_menu_name' => 'required',
                'url' => 'required|unique:sub_menus,url,NULL,id,menu_id,' . $request->menu_id,
                'sub_menu_type' => 'required'
            ]);
    
            if($validator->fails()){
                return response()->json([
                    'status' => 'warning',
                    'message' => $validator->errors()->first(),
                ]);       
            }
    
            $subMenu = SubMenu::create($input);
            $subMenus = SubMenu::get();

            $data['group_id'] = $input['menu_id'];
            $data['sub_menu_id'] = $subMenu->id;
            $data['name'] = $input['url'];
            $data['guard_name'] = 'web';
            CustomPermission::create($data);
            
            Storage::disk('public')->put('sub_menus.json', json_encode($subMenus));
            return response()->json([
                'status' => 'success',
                'message' => 'Sub Menu add successfully',
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
    public function show(string $id)
    {
        try {
            $subMenu = SubMenu::where('menu_id',$id)->get();
    
            if (is_null($subMenu)) {
                return response()->json([
                    'status' => 'warning',
                    'message' => 'Sub Menu Not Found',
                ]);
            }
            if ($this->segment == 'api') {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Sub Menus Retrieved successfully',
                    'data' => $subMenu,
                ],200);
            }else{

            }
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'message' => $th->getMessage(),
                'data' => null,
            ],500);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $pageUrl = CommonHelper::checkPermissionInnerOptions('sub-menus.index');
        $subMenu = SubMenu::find($id);
        if (is_null($subMenu)) {
            return response()->json([
                'status' => 'warning',
                'message' => 'Sub Menu Not Found',
            ]);
        }

        $mainTitle = 'Sub Menu';
        $pageTitle = 'Edit Sub Menu Detail';
        $result = CommonHelper::makeApiRequest(url('menus/getMenus'), 'GET');
        $menus = $result['data'];
        
        return view($this->page.'edit',compact('mainTitle','pageTitle','menus','subMenu','pageUrl'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        try {
            $id = $request->input('id');
            $subMenu = SubMenu::find($id);
            $permissions = CustomPermission::where('sub_menu_id',$id)->first();
            if (is_null($subMenu)) {
                return response()->json([
                    'status' => 'warning',
                    'message' => 'Sub Menu Not Found',
                ]);
            }

            $input = $request->all();
    
            $validator = Validator::make($input, [
                'menu_id' => 'required',
                'sub_menu_icon' => 'required',
                'sub_menu_name' => 'required',
                'url' => 'required|unique:sub_menus,url,'.$id.',id,menu_id,' . $request->menu_id,
                'sub_menu_type' => 'required'
            ]);
    
            if($validator->fails()){
                return response()->json([
                    'status' => 'warning',
                    'message' => $validator->errors()->first(),
                ]);       
            }

            $subMenu->menu_id = $input['menu_id'];
            $subMenu->sub_menu_icon = $input['sub_menu_icon'];
            $subMenu->sub_menu_name = $input['sub_menu_name'];
            $subMenu->url = $input['url'];
            $subMenu->sub_menu_type = $input['sub_menu_type'];
            $subMenu->save();

            $permissions->group_id = $input['menu_id'];
            $permissions->sub_menu_id = $id;
            $permissions->name = $input['url'];
            $permissions->guard_name = 'web';
            $permissions->save();

            $subMenus = SubMenu::get();
            Storage::disk('public')->put('sub_menus.json', json_encode($subMenus));

            return response()->json([
                'status' => 'success',
                'message' => 'Sub Menu Updated successfully',
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
            $subMenu = SubMenu::find($request->id);
            if ($subMenu == null) {
                return response()->json([
                    'status' => 'warning',
                    'message' => 'Sub Menu Not Found',
                ]);
            }
            $status = $subMenu->status == 1 ? 2 : 1;
            $subMenu->status  = $status;
            $subMenu->save();
            return response()->json([
                'status' => 'success',
                'message' => 'Sub Menu Status Updated successfully',
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
