<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Exception;
use Validator;
use Helper;
use App\User;
use DB;
use Auth;
use Hash;

class UserController extends Controller
{
    //
    // Signup Api 
    public function userRegister(Request $request)
	{
		try {
			$validated = Validator::make($request->all(), [
				'name' => 'required|alpha',
				'email' => 'required|email|unique:users',
				'password' => 'required|min:6',
				'phone_no' => 'required|numeric|unique:users',
			]);

			if ($validated->fails()) {
				throw new Exception($validated->errors()->first());
			} else {
				$details = [
					'name' => $request->name,
					'role' => $request->role,
					'email' => $request->email,
					'phone_no' => $request->phone_no,
					'password' => Hash::make($request->password),
                    'email_verified_at'=>'1'
				];
				$user = User::create($details);
				
                $accessToken = $user->createToken('authToken')->accessToken;
				return response()->json(['status' => true, 'message' => 'Register Successfully!','access_token' => $accessToken, 'response' => $user]);
			}
		} catch (Exception $e) {
			return response()->json(['status' => false, 'message' => $e->getMessage()]);
		}
	}
    // login api
    public function login(Request $request){
        $validated = Validator::make($request->all(),[
            'phone_no'=>'required|exists:users',
            'otp'=>'numeric'
        ]);
        if($validated->fails()){
            return  response()->json(['status'=>false,'message'=>$validated->errors()->first()]);
        }
        else{
            $otp = Helper::generateOTP();
            DB::table('userss')->where('phone_no',$request->phone_no)->update(['otp',$otp]);

            /**send otp to user using twilio */
            $message = 'Your OTP is --'.$otp;
            Helper::sendOtp($request->phone_no,$message);
            if($request->has('otp')){
                $auth =  Auth::attempt(['phone_no'=>$request->phone_no,'otp'=>$request->otp]);
                if($auth){
                    $user = User::where('phone_no',$request)->first();
                    return response()->json(['status'=>false,'message'=>'LOgin successfull','response'=>$user]);
                }
            }
            return response()->json(['status'=>true,'message'=>'Otp sent successffully','response'=>[]]);
        }
    }

    public function addBooks(Request $request){
        $validated = Validator::make($request->all(),[
            'user_id'=>'required|exists:users,id',
            'book_description'=>'required'
        ]);
        if($validated->fails()){
            return  response()->json(['status'=>false,'message'=>$validated->errors()->first()]);
        }
        else{
            $book = DB::table('books')->create([
                
            ]);

        }
    }
}
