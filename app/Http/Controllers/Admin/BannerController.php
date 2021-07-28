<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Banner;
use Validator;
use Image;

class BannerController extends Controller
{
    public function allBanner($page)
    {
        $page = $page;
        $perPage = 6;
        $skip = ($page - 1) * $perPage;
        $count = Banner::count();
        $banners = Banner::orderBy('id','desc')->skip($skip)->take($perPage)->get();
        return response()->json([
            'success' => true,
            'response' => $banners,
            'count' => $count,
            'perPage' => $perPage,
        ], 200);
    }
    
    public function createBanner(Request $request)
    {
        if($request->isMethod('post')){

            $validator = Validator::make($request->all(),[
                'title' => 'required|string|unique:banners',
                'link' => 'required|string',
                'banner_image' => 'required',
                'btn_text' => 'required|string',
            ]);

            if ($validator->fails()) {
                return response()->json([
                   'success'=>false,
                   'errors'=>$validator->messages()->toArray(),
                ],400);
            }
           
           $banner = new Banner;
           $banner->title = $request->title;
           $banner->subtitle = $request->subtitle;
           $banner->link = $request->link;
           $banner->btn_text = $request->btn_text;
           $banner->alt = $request->alt;
            if($request->file('banner_image')){
                $image_tmp = $request->file('banner_image');
                $extension = $image_tmp->getClientOriginalExtension();
                $imageName = rand(111,99999).'.'.$extension;
                $imagePath = 'images/banner_images/'.$imageName;
                Image::make($image_tmp)->resize(450, 650)->save($imagePath);
                $banner->image = $imageName;    
            }

           $banner->status = 1;
           $banner->save();

           return response()->json([
                'success'=>true,
                'message'=>'Banner created successfully',
            ], 200);
        }
    }

    public function statusBanner(Request $request)
    {
        if($request->isMethod('post')){
            $data = $request->all();
            if($data['status'] == 0){
                $status = 1;
            }else{
                $status = 0;
            }

            Banner::where('id', $data['banner_id'])->update(['status'=>$status]);

            return response()->json([
                'success'=>true,
                'status'=>$status,
                'banner_id'=>$data['banner_id'],
            ], 200);
        }
    }

    public function editBanner($id)
    {
        $banner = Banner::where('id',$id)->first();

        return response()->json([
            'success'=>true,
            'response'=>$banner,
        ], 200);
    }

    public function updateBanner(Request $request, $id)
    {
        if($request->isMethod('post')){

            $validator = Validator::make($request->all(),[
                'link' => 'required|string',
                'btn_text' => 'required|string',
                'title' => 'required|string|unique:banners,title,'.$id,
            ]);

            if ($validator->fails()) {
                return response()->json([
                   'success'=>false,
                   'errors'=>$validator->messages()->toArray(),
                ],400);
            }
           
           $banner = Banner::find($id);
           $banner->title = $request->title;
           $banner->subtitle = $request->subtitle;
           $banner->link = $request->link;
           $banner->btn_text = $request->btn_text;
           $banner->alt = $request->alt;
 
            if($request->file('banner_image')){
                //Delete Old Image
                $path = "images/banner_images/";
                if($banner->banner_image){
                    if (file_exists($path.$banner->banner_image)) {
                        unlink($path.$banner->banner_image);
                    }
                }
                //New Image save
                $image_tmp = $request->file('banner_image');
                $extension = $image_tmp->getClientOriginalExtension();
                $imageName = rand(111,99999).'.'.$extension;
                $imagePath = 'images/banner_images/'.$imageName;
                Image::make($image_tmp)->resize(190, 190)->save($imagePath);
                $banner->image = $imageName;    
            }

           $banner->status = 1;
           $banner->save();

           return response()->json([
                'success'=>true,
                'message'=>'Banner updated successfully',
            ], 200);
        }
    }

    public function deleteBanner($id)
    {
        $banner = Banner::where('id',$id)->first();
        $path = "images/banner_images/";
        if($banner->image){
            if (file_exists($path.$banner->image)) {
                unlink($path.$banner->image);
            }
        }

        $banner->delete();
        return response()->json([
            'success'=>true,
            'message'=>'Banner deleted successfully'
        ], 200);
    }
}
