<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator;
use Auth;

class AdminController extends Controller
{
    public function login(Request $request)
    {
        if($request->isMethod('post')){

            $validator = Validator::make($request->all(),[
                'email' => 'required|email',
                'password' => 'required|min:6',
            ]);

            if ($validator->fails()) {
                return response()->json([
                   'success'=>false,
                   'errors'=>$validator->messages()->toArray(),
                ],400);
            }
            $credentials = $request->only('email', 'password');
            if(!$token = Auth::guard('admin')->attempt($credentials)){
                return response()->json([
                    'success'=>false,
                    'errors'=>['msg'=>['Username or Password does not match']],
                ],400);
            }

            return response()->json([
               'success'=>true,
               'token'=>$token,
            ], 200);
        }
    }
}
