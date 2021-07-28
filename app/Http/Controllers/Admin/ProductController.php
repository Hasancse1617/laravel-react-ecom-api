<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ProductImage;
use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use Validator;
use Image;

class ProductController extends Controller
{
    public function allProduct($page)
    {
        $page = $page;
        $perPage = 6;
        $skip = ($page - 1) * $perPage;
        $count = Product::count();
        $products = Product::with(['category'])->orderBy('id','desc')->skip($skip)->take($perPage)->get();
        return response()->json([
            'success' => true,
            'response' => $products,
            'count' => $count,
            'perPage' => $perPage,
        ], 200);
    }

    public function allCategories()
    {
        $categories = Category::with('subcategories')->where('parent_id',0)->orderBy('id','desc')->select('id','category_name')->get();
        return response()->json([
            'success' => true,
            'response' => $categories,
        ], 200);
    }

    public function allBrands()
    {
        $brands = Brand::orderBy('id','desc')->select('id','name')->get();
        return response()->json([
            'success' => true,
            'response' => $brands,
        ], 200);
    }

    public function createProduct(Request $request)
    {
        if($request->isMethod('post')){
            $data = $request->all();

            $validator = Validator::make($request->all(),[
                'name' => 'required|string',
                'category' => 'required',
                'brand' => 'required',
                'code' => 'required|regex:/^[\w-]*$/',
                'price' => 'required|numeric',
                'color' => 'required|string',
                'description' => 'required|string',
                'short_description' => 'required|string',
                'image' => 'required',
            ]);

            if ($validator->fails()) {
                return response()->json([
                   'success'=>false,
                   'errors'=>$validator->messages()->toArray(),
                ],400);
            }

            $product = new Product;
            $product->product_name = $data['name'];
            $product->category_id = $data['category'];
            $product->brand_id = $data['brand'];
            $product->product_code = $data['code'];
            $product->product_color = $data['color'];
            $product->product_price = $data['price'];
            $product->product_discount = $data['discount'];
            $product->product_weight = $data['weight'];
            $product->description = $data['description'];
            $product->fabric = $data['fabric'];
            $product->sleeve = $data['sleeve'];
            $product->short_description = $data['short_description'];
            
            if($data['featured']){
                $product->is_featured = 'Yes';
            }else{
                $product->is_featured = 'No';
            }

            if($request->file('image')){
                $image_tmp = $request->file('image');
                $extension = $image_tmp->getClientOriginalExtension();
                $imageName = rand(111,99999).'.'.$extension;
                $large_image_path = "images/product_images/large/".$imageName;
                $medium_image_path = "images/product_images/medium/".$imageName;
                $small_image_path = "images/product_images/small/".$imageName;

                Image::make($image_tmp)->save($large_image_path);
                Image::make($image_tmp)->resize(500,500)->save($medium_image_path);
                Image::make($image_tmp)->resize(250,250)->save($small_image_path);
                $product->main_image = $imageName;    
            }

            if($request->file('video')){
                $video_tmp = $request->file('video');
                $extension = $video_tmp->getClientOriginalExtension();
                $videoName = rand(111,99999).'.'.$extension;
                $video_tmp->move(public_path('videos/product_videos/'), $videoName);
                $product->product_video = $videoName;    
            }

            $product->status = 1;
            $product->save();

            return response()->json([
                'success'=>true,
                'message'=>'Product created successfully',
            ], 200);
        }
    }

    public function editProduct($id)
    {
        $product = Product::where('id',$id)->first();

        return response()->json([
            'success'=>true,
            'response'=>$product,
        ], 200);
    }

    public function updateProduct(Request $request, $id)
    {
        if($request->isMethod('post')){
            $data = $request->all();

            $validator = Validator::make($request->all(),[
                'name' => 'required|string',
                'category' => 'required',
                'brand' => 'required',
                'code' => 'required|regex:/^[\w-]*$/',
                'price' => 'required|numeric',
                'color' => 'required|string',
                'description' => 'required|string',
                'short_description' => 'required|string',
            ]);

            if ($validator->fails()) {
                return response()->json([
                   'success'=>false,
                   'errors'=>$validator->messages()->toArray(),
                ],400);
            }

            $product = Product::find($id);
            $product->product_name = $data['name'];
            $product->category_id = $data['category'];
            $product->brand_id = $data['brand'];
            $product->product_code = $data['code'];
            $product->product_color = $data['color'];
            $product->product_price = $data['price'];
            $product->product_discount = $data['discount'];
            $product->product_weight = $data['weight'];
            $product->description = $data['description'];
            $product->fabric = $data['fabric'];
            $product->sleeve = $data['sleeve'];
            $product->short_description = $data['short_description'];
            
            if($data['featured']){
                $product->is_featured = 'Yes';
            }else{
                $product->is_featured = 'No';
            }

            if($request->hasFile('image')){
                
                $small_imagePath = 'images/product_images/small/';
                $medium_imagePath = 'images/product_images/medium/';
                $large_imagePath = 'images/product_images/large/';

                if (file_exists($small_imagePath.$product->main_image)) {
                   unlink($small_imagePath.$product->main_image);
                }
                if (file_exists($medium_imagePath.$product->main_image)) {
                    unlink($medium_imagePath.$product->main_image);
                }
                if (file_exists($large_imagePath.$product->main_image)) {
                    unlink($large_imagePath.$product->main_image);
                }

                $image_tmp = $request->file('image');
                $extension = $image_tmp->getClientOriginalExtension();
                $imageName = rand(111,99999).'.'.$extension;
                $large_image_path = "images/product_images/large/".$imageName;
                $medium_image_path = "images/product_images/medium/".$imageName;
                $small_image_path = "images/product_images/small/".$imageName;

                Image::make($image_tmp)->save($large_image_path);
                Image::make($image_tmp)->resize(500,500)->save($medium_image_path);
                Image::make($image_tmp)->resize(250,250)->save($small_image_path);
                $product->main_image = $imageName;    
            }

            if($request->hasFile('video')){

                $videoPath = 'videos/product_videos/';
                if (file_exists($videoPath.$product->product_video)) {
                    unlink($videoPath.$product->product_video);
                }

                $video_tmp = $request->file('video');
                $extension = $video_tmp->getClientOriginalExtension();
                $videoName = rand(111,99999).'.'.$extension;
                $video_tmp->move(public_path('videos/product_videos/'), $videoName);
                $product->product_video = $videoName;    
            }

            $product->status = 1;
            $product->save();

            return response()->json([
                'success'=>true,
                'message'=>'Product updated successfully',
            ], 200);
        }
    }

    public function statusProduct(Request $request)
    {
        if($request->isMethod('post')){
            $data = $request->all();
            if($data['status'] == 0){
                $status = 1;
            }else{
                $status = 0;
            }

            Product::where('id', $data['product_id'])->update(['status'=>$status]);

            return response()->json([
                'success'=>true,
                'status'=>$status,
                'product_id'=>$data['product_id'],
            ], 200);
        }
    }

    public function deleteProduct($id)
    {
        $product = Product::find($id);
        $small_imagePath = 'images/product_images/small/';
        $medium_imagePath = 'images/product_images/medium/';
        $large_imagePath = 'images/product_images/large/';

        if (file_exists($small_imagePath.$product->main_image)) {
            unlink($small_imagePath.$product->main_image);
        }
        if (file_exists($medium_imagePath.$product->main_image)) {
            unlink($medium_imagePath.$product->main_image);
        }
        if (file_exists($large_imagePath.$product->main_image)) {
            unlink($large_imagePath.$product->main_image);
        }

        $videoPath = 'videos/product_videos/';

        if (file_exists($videoPath.$product->product_video)) {
          unlink($videoPath.$product->product_video);
        }

        $product->delete();

        return response()->json([
            'success'=>true,
            'message'=>'Product deleted successfully',
        ], 200);
    }

    public function addImages(Request $request, $id)
    {
        if($request->isMethod('post')){
            $data = $request->all();
            
            $validator = Validator::make($request->all(),[
                'images' => 'required',
            ]);

            if ($validator->fails()) {
                return response()->json([
                   'success'=>false,
                   'errors'=>$validator->messages()->toArray(),
                ],400);
            }

            if ($request->hasFile('images')) {

              $images = $request->file('images');

              foreach ($images as $key => $image) {
                $productImage = new ProductImage;
                $image_tmp = Image::make($image);
                $extension = $image->getClientOriginalExtension();
                $imageName = rand(111,999999).time().".".$extension;

                $large_image_path = "images/product_images/large/".$imageName;
                $medium_image_path = "images/product_images/medium/".$imageName;
                $small_image_path = "images/product_images/small/".$imageName;

                Image::make($image_tmp)->save($large_image_path);
                Image::make($image_tmp)->resize(520,600)->save($medium_image_path);
                Image::make($image_tmp)->resize(260,300)->save($small_image_path);

                $productImage->image = $imageName;
                $productImage->product_id = $id;
                $productImage->status = 1;
                $productImage->save();
              }

                return response()->json([
                    'success'=>true,
                    'message'=>'Product images added successfully',
                ], 200);
            }
        }
    }

    public function allImages($id)
    {
        $images = ProductImage::orderBy('id','desc')->where('product_id',$id)->get();
        return response()->json([
            'success'=>true,
            'response'=>$images,
        ], 200);
    }

    public function deleteImages($id)
    {
          $productImage = ProductImage::select('image')->where('id',$id)->first();
          $small_imagePath = 'images/product_images/small/';
          $medium_imagePath = 'images/product_images/medium/';
          $large_imagePath = 'images/product_images/large/';

          if (file_exists($small_imagePath.$productImage->image)) {
            unlink($small_imagePath.$productImage->image);
          }
          if (file_exists($medium_imagePath.$productImage->image)) {
            unlink($medium_imagePath.$productImage->image);
          }
          if (file_exists($large_imagePath.$productImage->image)) {
            unlink($large_imagePath.$productImage->image);
          }
          ProductImage::where('id',$id)->delete();

          return response()->json([
             'success'=>true,
             'message'=>'Product image deleted successfully',
          ], 200);
    }

    public function statusImage(Request $request)
    {
        if($request->isMethod('post')){
            $data = $request->all();
            if($data['status'] == 0){
                $status = 1;
            }else{
                $status = 0;
            }

            ProductImage::where('id', $data['image_id'])->update(['status'=>$status]);

            return response()->json([
                'success'=>true,
                'status'=>$status,
                'image_id'=>$data['image_id'],
            ], 200);
        }
    }
}
