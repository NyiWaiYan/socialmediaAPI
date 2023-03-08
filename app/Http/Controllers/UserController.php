<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Mail\MailResetPasswordRequest;
use Illuminate\Validation\Rules\Password;

class UserController extends Controller
{
    public function register(Request $request){
        $request->validate([
            'name'=>'required|string|max:150',
            'email'=>'required|string|email|unique:users',
            'password'=>[
                'required',
                'confirmed',
                Password::min(8)
                ->letters()
                ->mixedCase()
                ->numbers()
                ->symbols()
                ->uncompromised() 
            ]   
        ]);
        $user=new User();
        $user->name= $request->name;
        $user->email= $request->email;
        $user->password= bcrypt($request->password);   
        $user->role='user';     
        if($user->save()){
            return response()->json([
                'message'=>'Registertion successful, You can log in now'
            ],201);
        }else{
            return response()->json([
                'message'=>'Some Error Occured,Please try again!'
            ],500);
        }
    }

    public function login(Request $request){
        $request->validate([
            'email'=>'required|string|email',
            'password'=>'required|string'
        ]);
        if(!Auth::attempt(['email'=>$request->email,'password'=>$request->password])){
            return response()->json([
                'message'=>'Invalid email or password'
            ],401); 
        }
        $user = $request->user();
        $user->tokens()->delete();
        if($user->role=='admin'){
             $token=$user->createToken('personal Access Token',['admin']);
        }else{
            $token=$user->createToken('personal Access Token',['user']);
        }
       return response()->json([
        'user'=>$user,
        'access_token'=>$token->accessToken->token,
        'token_type'=>'Bearer',
        'abilities'=> $token->accessToken->abilities
       ],200);
    }


    public function resetPasswordRequest(Request $request){
        $request->validate([
            'email'=>'required|string|email',
        ]);
        $user=User::where('email',$request->email)->first();

        if(!$user){
            return response()->json([
                'message'=>'User not found!',
            ],200);
        }
        $code = rand(111111,999999);
        $user->verification_code = $code;
        
        if($user->save()){
            $emailData=array(
                'heading'=>'Reset password request',  
                'name'=>$user->name,
                'email'=>$user->email,
                'code'=>$user->verification_code
            );

            Mail::to($emailData['email'])->queue(new MailResetPasswordRequest($emailData));
            return response()->json([
                'message'=>'We have sent a verification code to your provided email',
            ],200);
        }else{
            return response()->json([
                'message'=>'Some Error Occured,Please try again!'
            ],500);
        };

    }

    public function resetPassword(Request $request){
        $request->validate([
            'email'=>'required|string|email',
            'verification_code'=>'required|integer',
            'new_password'=>[ 'required',
            'confirmed',
            Password::min(8)
            ->letters()
            ->mixedCase()
            ->numbers()
            ->symbols()
            ->uncompromised()
             ],
        ]);
        $user = User::where('email',$request->email)->where('verification_code',$request->verification_code)->first();
        if(!$user){
            return response()->json([
                'message'=>'user not found',
            ],404);
        }
        $user->password = bcrypt($request->new_password);
        $user->verification_code=NULL;
        if($user->save()){
            return response()->json([
                'message'=>'Password Changed Successfully'
            ],200);    
        }else{
            return response()->json([
                'message'=>'Some Error Occured,Please try again!'
            ],500);
        }

    }
}
