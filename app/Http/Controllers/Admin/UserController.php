<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\Admin;
use Validator;
use Image;

class UserController extends Controller
{
    public function allUser($page)
    {
        $page = $page;
        $perPage = 6;
        $skip = ($page - 1) * $perPage;
        $count = Admin::count();
        $users = Admin::orderBy('id','desc')->skip($skip)->take($perPage)->get();
        return response()->json([
            'success' => true,
            'response' => $users,
            'count' => $count,
            'perPage' => $perPage,
        ], 200);
    }

    public function createUser(Request $request)
    {
        if ($request->isMethod('post')) {

            $validator = Validator::make($request->all(),[
                'name' => 'required|string',
                'email' => 'required|email|unique:admins',
                'mobile' => 'required',
                'type' => 'required|string',
                'password' => 'required|min:6',
                'confirm_password' => 'required|same:password|min:6',
            ]);

            if ($validator->fails()) {
                return response()->json([
                   'success'=>false,
                   'errors'=>$validator->messages()->toArray(),
                ],400);
            }

            $user = new Admin;
            $user->name = $request->name;
            $user->email = $request->email;
            $user->mobile = $request->mobile;
            $user->type = $request->type;
            $user->password = Hash::make($request->password);
            $user->status = 1;
            if($request->file('image')){
                $image_tmp = $request->file('image');
                $extension = $image_tmp->getClientOriginalExtension();
                $imageName = rand(111,99999).'.'.$extension;
                $imagePath = 'images/admin_images/'.$imageName;

                Image::make($image_tmp)->resize(215, 215)->save($imagePath);
                $user->image = $imageName;
                
            }
            $user->save();
            return response()->json([
                'success'=>true,
                'message'=>'User created successfully',
            ], 200);
        }
    }

    public function editUser($id)
    {
        $user = Admin::where('id',$id)->first();
        return response()->json([
            'success' => true,
            'response' => $user,
        ], 200);
    }

    public function updateUser(Request $request, $id)
    {
        $validator = Validator::make($request->all(),[
            'name' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
               'success'=>false,
               'errors'=>$validator->messages()->toArray(),
            ],400);
        }

        $user = Admin::find($id);
        $path = "images/admin_images/";
        
        $user->name = $request->name;
        if($request->file('image')){
            if($user->image){
                if (file_exists($path.$user->image)) {
                    unlink($path.$user->image);
                }
            }
            $image_tmp = $request->file('image');
            $extension = $image_tmp->getClientOriginalExtension();
            $imageName = rand(111,99999).'.'.$extension;
            $imagePath = 'images/admin_images/'.$imageName;

            Image::make($image_tmp)->resize(215, 215)->save($imagePath);
            $user->image = $imageName;
        }
        $user->save();
        $admin = Admin::where('id',$id)->first();
        return response()->json([
            'success'=>true,
            'user' => $admin,
            'message'=>'User updated successfully',
        ], 200);
    }

    public function updateUserPassword(Request $request, $id)
    {
        $validator = Validator::make($request->all(),[
            'old_password' => 'required|min:6',
            'new_password' => 'required|min:6',
            'confirm_password' => 'required|same:new_password|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json([
               'success'=>false,
               'errors'=>$validator->messages()->toArray(),
            ],400);
        }
        $user = Admin::find($id);
        if(Hash::check($request->old_password, $user->password)){
            $user->password = Hash::make($request->new_password);
            $user->save();
            return response()->json([
                'success'=>true,
                'message'=>'Password updated successfully',
            ], 200);
        }

        return response()->json([
            'success'=>false,
            'errors'=>['msg'=>['Old password not correct']],
        ], 400);
    }

    public function deleteUser($id)
    {
        $user = Admin::where('id',$id)->first();
        $path = "images/admin_images/";
        if($user->image){
            if (file_exists($path.$user->image)) {
                unlink($path.$user->image);
            }
        }

        $user->delete();
        return response()->json([
            'success'=>true,
            'message'=>'User deleted successfully'
        ], 200);
    }
}
