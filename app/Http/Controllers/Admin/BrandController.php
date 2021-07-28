<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Brand;
use Validator;

class BrandController extends Controller
{
    public function brands($page)
    {
        $page = $page;
        $perPage = 6;
        $skip = ($page - 1) * $perPage;
        $count = Brand::count();
        $brands = Brand::orderBy('id','desc')->skip($skip)->take($perPage)->get();
        return response()->json([
            'success' => true,
            'response' => $brands,
            'count' => $count,
            'perPage' => $perPage,
        ], 200);
    }

    public function createBrand(Request $request)
    {
        if($request->isMethod('post')){
            $validator = Validator::make($request->all(),[
                'name' => 'required|string|unique:brands',
            ]);

            if ($validator->fails()) {
                return response()->json([
                   'success'=>false,
                   'errors'=>$validator->messages()->toArray(),
                ],400);
            }
            $brand = new Brand;
            $brand->name = $request->name;
            $brand->status = 1;
            $brand->save();

            return response()->json([
                'success'=>true,
                'message'=>'Brand created successfully',
            ], 200);
        }
    }

    public function statusBrand(Request $request)
    {
        if($request->isMethod('post')){
            $data = $request->all();
            if($data['status'] == 0){
                $status = 1;
            }else{
                $status = 0;
            }

            Brand::where('id', $data['brand_id'])->update(['status'=>$status]);

            return response()->json([
                'success'=>true,
                'status'=>$status,
                'brand_id'=>$data['brand_id'],
            ], 200);
        }
    }

    public function editBrand($id)
    {
        $brand = Brand::where('id',$id)->first();

        return response()->json([
            'success'=>true,
            'response'=>$brand,
        ], 200);
    }

    public function updateBrand(Request $request, $id)
    {
        if($request->isMethod('post')){
            $validator = Validator::make($request->all(),[
                'name' => 'required|string|unique:brands,name,'.$id,
            ]);

            if ($validator->fails()) {
                return response()->json([
                   'success'=>false,
                   'errors'=>$validator->messages()->toArray(),
                ],400);
            }
            $brand = Brand::find($id);
            $brand->name = $request->name;
            $brand->status = 1;
            $brand->save();

            return response()->json([
                'success'=>true,
                'message'=>'Brand updated successfully',
            ], 200);
        }
    }

    public function deleteBrand($id)
    {
        $brand = Brand::where('id',$id)->first();
        $brand->delete();
        return response()->json([
            'success'=>true,
            'message'=>'Brand deleted successfully'
        ], 200);
    }
}
