<?php

namespace App\Helpers;

class Helper{
    public static function sayHello(){
        //function check
        return "hello world";
    }
    public static function generateOTP(){
        $otp = mt_rand(1000,9999);
        return $otp;
    }
    public static function sendOtp($receiver_number, $message){
        /* Get credentials from .env */
        try {
            
            $account_sid = getenv("TWILIO_SID");
            $auth_token = getenv("TWILIO_TOKEN");
            $twilio_number = getenv("TWILIO_FROM");
            $client = new Client($account_sid, $auth_token);
            $client->messages->create($receiverNumber, [
                'from' => $twilio_number, 
                'body' => $message
            ]);
            // dd('SMS Sent Successfully.');
            return response()->json(['status'=>true,'message'=>'SMS sent successfully','response'=>[]]);
        } catch (\Exception $e) {
            return response()->json(['status'=>false,'message'=>$e->getMessage()]);
            // dd("Error: ". $e->getMessage());
        }
    }
}