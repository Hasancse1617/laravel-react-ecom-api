<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use Validator;
use Image;

class CategoryController extends Controller
{
    public function allCategory($page)
    {
        $page = $page;
        $perPage = 6;
        $skip = ($page - 1) * $perPage;
        $count = Category::count();
        $categories = Category::with('parentcategory')->orderBy('id','desc')->skip($skip)->take($perPage)->get();
        return response()->json([
            'success' => true,
            'response' => $categories,
            'count' => $count,
            'perPage' => $perPage,
        ], 200);
    }

    public function parentCategory()
    {
        $categories = Category::orderBy('id','desc')->where('parent_id',0)->select('id','category_name')->get();
        return response()->json([
            'success' => true,
            'response' => $categories,
        ], 200);
    }
    
    public function createCategory(Request $request)
    {
        if($request->isMethod('post')){

            $validator = Validator::make($request->all(),[
                'category_name' => 'required|string',
                'url' => 'required|string|unique:categories',
                'category_image' => 'required',
            ]);

            if ($validator->fails()) {
                return response()->json([
                   'success'=>false,
                   'errors'=>$validator->messages()->toArray(),
                ],400);
            }
           
           $category = new Category;
           $category->category_name = $request->category_name;
           $category->url = $request->url;
           if($request->parent_id === null){
                $category->parent_id = 0;
           }else{
               $category->parent_id = $request->parent_id;
           }
            if($request->file('category_image')){
                $image_tmp = $request->file('category_image');
                $extension = $image_tmp->getClientOriginalExtension();
                $imageName = rand(111,99999).'.'.$extension;
                $imagePath = 'images/category_images/'.$imageName;
                Image::make($image_tmp)->resize(190, 190)->save($imagePath);
                $category->category_image = $imageName;    
            }

           $category->status = 1;
           $category->save();

           return response()->json([
                'success'=>true,
                'message'=>'Category created successfully',
            ], 200);
        }
    }

    public function statusCategory(Request $request)
    {
        if($request->isMethod('post')){
            $data = $request->all();
            if($data['status'] == 0){
                $status = 1;
            }else{
                $status = 0;
            }

            Category::where('id', $data['category_id'])->update(['status'=>$status]);

            return response()->json([
                'success'=>true,
                'status'=>$status,
                'category_id'=>$data['category_id'],
            ], 200);
        }
    }

    public function editCategory($id)
    {
        $category = Category::with('parentcategory')->where('id',$id)->first();

        return response()->json([
            'success'=>true,
            'response'=>$category,
        ], 200);
    }

    public function updateCategory(Request $request, $id)
    {
        if($request->isMethod('post')){

            $validator = Validator::make($request->all(),[
                'category_name' => 'required|string',
                'url' => 'required|string|unique:categories,url,'.$id,
            ]);

            if ($validator->fails()) {
                return response()->json([
                   'success'=>false,
                   'errors'=>$validator->messages()->toArray(),
                ],400);
            }
           
           $category = Category::find($id);
           $category->category_name = $request->category_name;
           $category->url = $request->url;
           if($request->parent_id == null){
                $category->parent_id = 0;
           }else{
               $category->parent_id = $request->parent_id;
           }
            if($request->file('category_image')){
                //Delete Old Image
                $path = "images/category_images/";
                if($category->category_image){
                    if (file_exists($path.$category->category_image)) {
                        unlink($path.$category->category_image);
                    }
                }
                //New Image save
                $image_tmp = $request->file('category_image');
                $extension = $image_tmp->getClientOriginalExtension();
                $imageName = rand(111,99999).'.'.$extension;
                $imagePath = 'images/category_images/'.$imageName;
                Image::make($image_tmp)->resize(190, 190)->save($imagePath);
                $category->category_image = $imageName;    
            }

           $category->status = 1;
           $category->save();

           return response()->json([
                'success'=>true,
                'message'=>'Category updated successfully',
            ], 200);
        }
    }

    public function deleteCategory($id)
    {
        $category = Category::where('id',$id)->first();
        $path = "images/category_images/";
        if($category->category_image){
            if (file_exists($path.$category->category_image)) {
                unlink($path.$category->category_image);
            }
        }

        $category->delete();
        return response()->json([
            'success'=>true,
            'message'=>'Category deleted successfully'
        ], 200);
    }
}
