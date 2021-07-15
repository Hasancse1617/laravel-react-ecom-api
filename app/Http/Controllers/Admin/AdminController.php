<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
use Validator;
use Auth;
use App\Models\Admin;

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
            $ttl = ($request->remember_me===true) ? env('JWT_REM_TTL') : env('JWT_TTL');
            if(!$token = Auth::guard('admin')->setTTL($ttl)->attempt($credentials)){
                return response()->json([
                    'success'=>false,
                    'errors'=>['msg'=>['Username or Password does not match']],
                ],400);
            }

            return response()->json([
               'success'=>true,
               'user' => Auth::guard('admin')->user(),
               'token'=>$token,
            ], 200);
        }
    }

    public function forgotPassword(Request $request)
    {
        if($request->isMethod('post')){
            $validator = Validator::make($request->all(),[
                'email' => 'required|email',
            ]);

            if ($validator->fails()) {
                return response()->json([
                   'success'=>false,
                   'errors'=>$validator->messages()->toArray(),
                ],400);
            }
            $emailcheck = Admin::where('email',$request->email)->first();
            if(!$emailcheck){
                return response()->json([
                   'success'=>false,
                   'errors'=>['msg'=>['Email not found']],
                ],400);
            }

            $token = base64_encode($request->email.'/'.(time()+600));
            $email = $request->email;
            $messageData = ['token'=>$token];

            Mail::send('emails.forgot_password',$messageData,function($message) use($email){
                $message->to($email)->subject('Welcome to E-com Website');
            });

            return response()->json([
               'success'=>true,
               'message'=>'We sent an email to reset password. Please check!!!',
            ],200);
        }
    }

    public function resetPassword(Request $request, $token)
    {
        if($request->isMethod('post')){
            $validator = Validator::make($request->all(),[
                'password' => 'required|min:6',
                'confirm_password' => 'required|same:password|min:6',
            ]);

            if ($validator->fails()) {
                return response()->json([
                   'success'=>false,
                   'errors'=>$validator->messages()->toArray(),
                ],400);
            }
            if(base64_encode(base64_decode($token, true)) === $token){
                $data = explode('/', base64_decode($token, true));
                if( time() > $data[1] ){
                    return response()->json([
                       'success'=>false,
                       'errors'=>['msg'=>[' Your token is expired!!!']],
                    ],400);
                }
 
                $user = Admin::where('email',$data[0])->first();
                $user->password = Hash::make($request->password);
                $user->save();

                return response()->json([
                   'success'=>true,
                   'message'=>'New password created successfully',
                ],200);

            }
            else{
                return response()->json([
                   'success'=>false,
                   'errors'=>['msg'=>['Invalid token']],
                ],400);
            }
        }
    }
}
