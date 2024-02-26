<?php

namespace App\Http\Controllers\API;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Notify;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum', ['except' => ['login', 'register' ,'logout','refresh','forget_password', 'forget_password_verify','resend_otp','verify_otp']]);
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email',
            'password' => 'required|string',
        ],
        [
            'email.required' => 'Please enter email address',
            'email.email' => 'Please enter a valid email address',
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => $validator->errors()->first()]);
        }

        $credentials = $request->only('email', 'password');
        if (Auth::attempt($credentials)) {

            if(auth()->user()->role != 1){
                Auth::user()->tokens()->delete();
                return response()->json([
                    'status' => false,
                    'message' => 'Invalid credentials...'
                ]);
            }

            if(auth()->user()->status == 2){
                Auth::user()->tokens()->delete();
                return response()->json([
                    'status' => false,
                    'message' => 'Your account has been deactivated temporarily. Please contact trackcert@gmail.com for the same.'
                ]);
            }

            if($request->filled('fcm_token')){
                User::where('id', auth()->user()->id)->update(['fcm_token' => $request->fcm_token ?? null]);
            }

            $user = Auth::user();
            if($user->profile_image!="" && $user->profile_image!=null){
                $user->profile_image = uploadAssets('upload/profile-image/'.$user->profile_image);
            }else $user->profile_image= null;
            return response()->json([
                'user' => $user,
                'status' => true,
                'authorization' => [
                    'token' => $user->createToken('ApiToken')->plainTextToken,
                ]
            ]);
        }

        return response()->json([
            'status' => false,
            'message' => 'Invalid credentials',
        ], 401);
    }


    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'phone' => 'required',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
            'profile_image' => 'required|mimes:jpeg,png,jpg|image',
            'role' => 'required',
            'fcm_token' => 'required',
        ],
        [
            'email.required' => 'Please enter email address',
            'email.email' => 'Please enter a valid email address',
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => $validator->errors()->first()]);
        }

        $img = null;
        if($request->profile_image){
            $img = fileUpload($request->profile_image, 'upload/profile-image');  
        }

        $user = new User;
        $user->first_name = $request->first_name;
        $user->last_name = $request->last_name;
        $user->phone = $request->phone;
        $user->profile_image = $img;
        $user->email = $request->email;
        $user->status = 1;
        $user->role = 1;
        $user->fcm_token = $request->fcm_token ?? null;
        $user->password = $request->password;
        $user->created_at = date('Y-m-d H:i:s');
        $user->save();

        if($user){
            if(isset($user->profile_image) && $user->profile_image != ""){
                $user->profile_image = uploadAssets('upload/profile-image/'.$user->profile_image);
            }
            return response()->json([
                'status' => true,
                'message' => 'User created successfully',
                'user' => $user
            ]);
        }else{
            return response()->json([
                'status' => false,
                'message' => 'Something went wrong',
                'user' => ''
            ]);
        }
    }

    public function logout()
    {
        User::where('id', auth()->user()->id)->update([
            'fcm_token' => null
        ]);
        Auth::user()->tokens()->delete();
        return response()->json([
            'status' => true,
            'message' => 'Successfully logged out',
        ]);
    }

    public function forget_password_verify(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "email" => 'required',
            "password" => "required",
            'password_confirmation' => 'required_with:password|same:password|min:6'
        ]);
        if ($validator->fails()) {
            return errorMsg($validator->errors()->first());
        }
        $user = User::where('email', $request->email)->where('status', 1)->orderBy('id','DESC')->first();

        if (isset($user->id)) {
            $user_login_source = User::where('id', $user->id)->where('verification_code', $request->otp)->orderBy('id','DESC')->first();
            if (Hash::check($request->password, $user->password)) {
                return response()->json([
                    'status' => false,
                    'message' => 'New password cannot same as old password.',
                ], 200);
            } else {
                $user->password = bcrypt($request->password);
                $user->verification_code = null;
                $user->updated_at = null;
                if ($user->save()) {
                    $notify = new Notify;
                    $notify->added_by = $user_login_source->id;
                    $notify->user_id = $user_login_source->id;
                    $notify->module_name = 'password';
                    $notify->title = 'Password Reset Successfully';
                    $notify->message = 'Hello, ' . $user_login_source->first_name . "\nYour password has been reset successfully.";
                    if($user_login_source->profile_image == "" || $user_login_source->profile_image == null){
                        $profile_image = null;
                    } else $profile_image = uploadAssets('upload/profile-image/'.$user_login_source->profile_image);
                    $notify->image = $profile_image;
                    $notify->is_seen = '0';
                    $notify->redirect_url = null;
                    $notify->created_at = date('Y-m-d H:i:s');
                    $notify->updated_at = date('Y-m-d H:i:s');
                    $notify->save();

                    $data = array(
                        'msg' => 'Hello, ' . $user_login_source->first_name . "\nYour password has been reset successfully.",
                        'title' => 'Password Reset Successfully'
                    );
                    sendNotification($user_login_source->fcm_token ?? "", $data);

                    return response()->json([
                        'status' => true,
                        'message' => 'Password reset successfully.'
                    ]);
                }
            }
        } else return response()->json([
            'status' => false,
            'message' => 'Invalid credentials',
        ], 401);
    }

    public function forget_password(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required',
        ]);
        if ($validator->fails()) {
            return errorMsg($validator->errors()->first());
        }

        $user = User::where('email', $request->email)->where('status', 1)->orderBy('id','DESC')->first();
        $msg = " registered email address";

        if (isset($user->id)) {
            $code = rand(1000, 9999);
            $user->Verification_code = $code;
            $user->updated_at = date('Y-m-d H:i:s');
            if ($user->save()) {
                $data['subject']    = 'Track Cert Student Forgot Password OTP';
                $data['from_email'] = env('MAIL_FROM_ADDRESS');
                $data['site_title'] = 'Track Cert Student Forgot Password OTP';
                $data['view'] = 'email.otp';
                $data['otp'] = $code;
                $data['customer_name'] = $user->first_name ?? 'NA' + ' ' + $user->last_name ?? '';
                $data['to_email'] = $user->email ?? 'NA';
                sendEmail($data);

                return response()->json(['status' => true, 'message' => "OTP send to your " . $msg . ". OTP is expire in 5 minutes...", 'otp' => $code]);
            }
        } else return errorMsg('Entered email address is not registered with us');
    }

    public function resend_otp(Request $request)
    {
        try {
            $user = User::where('email', $request->email)->where('status', 1)->orderBy('id','DESC')->first();
            $code = rand(1000, 9999);
            $user->verification_code = $code;
            $user->updated_at = date('Y-m-d H:i:s');
            $user->save();
            $data['subject']    = 'Track Cert Student Forgot Password OTP';
            $data['from_email'] = env('MAIL_FROM_ADDRESS');
            $data['site_title'] = 'Track Cert Student Forgot Password OTP';
            $data['view'] = 'email.otp';
            $data['otp'] = $code;
            $data['customer_name'] = $user->first_name ?? 'NA' + ' ' + $user->last_name ?? '';
            $data['to_email'] = $user->email ?? 'NA';
            sendEmail($data);
            return response()->json(['status' => true, 'message' => 'OTP resend successfully.', 'code' => $code]);
        } catch (\Exception $e) {
            return errorMsg("Exception -> " . $e->getMessage());
        }
    }

    public function verify_otp(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'email' => 'required|email',
                'otp' => 'required|numeric|digits:4'
            ]);
            if ($validator->fails()) {
                return errorMsg($validator->errors()->first());
            }
            $user = User::where('email', $request->email)->orderBy('id','DESC')->first();
            if (isset($user->id)) {
                if(!empty($user->verification_code))
                {
                    if($user->verification_code == $request->otp)
                    {
                        $now = Carbon::now();
                        $fivemin = date('Y-m-d H:i:s', strtotime($user->updated_at.'+5 mins'));
                        if($fivemin >= $now){
                            return response()->json(['status' => true, 'message' => 'OTP verified succesfully.']);
                        }else{
                            $user->verification_code = null;
                            $user->save();
                            return response()->json(['status' => false, 'message' => 'OTP verification timeout. Please click resend otp']);
                        }
                    }else{
                        return response()->json(['status' => false, 'message' => 'OTP is incorrect.']);
                    }
                }else return response()->json(['status' => false, 'message' => 'OTP is incorrect.']);
            } else return response()->json(['status' => false, 'message' => 'OTP is incorrect.']);
        } catch (\Exception $e) {
            return errorMsg("Exception -> " . $e->getMessage());
        }
    }
}
