<?php
namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Mail\OtpMail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email'    => 'required|email',
            'password' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status'  => false,
                'message' => $validator->errors(),
            ]);
        }
        $credentials = [
            'email'    => $request->email,
            'password' => $request->password,
        ];
        if (Auth::attempt($credentials)) {
            $user  = Auth::user();
            $token = $user->createToken('nobl_application')->plainTextToken;
            return response()->json([
                'status'  => true,
                'message' => 'Login successful',
                'data'    => [
                    'access_token' => $token,
                    'user'         => $user,
                ],
            ]);

        } else {
            return response()->json([
                'status'  => false,
                'message' => 'Invalid credentials',
            ]);
        }
    }

    public function logout(Request $request)
    {
        $user = Auth::user();
        $user->tokens()->delete();
        return response()->json([
            'status'  => true,
            'message' => 'Logout successful',
        ]);
    }

    public function forgotPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status'  => false,
                'message' => $validator->errors(),
            ]);
        }
        $otp                  = rand(100000, 999999);
        $user                 = User::where('email', $request->email)->first();
        $user->otp            = $otp;
        $user->otp_expires_at = now()->addMinutes(10);
        $user->save();
        Mail::to($user->email)->send(new OtpMail($otp));
        return response()->json([
            'status'  => true,
            'message' => 'OTP sent to your email',
        ]);
    }

    public function verifyOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
            'otp'   => 'required|digits:6',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status'  => false,
                'message' => $validator->errors(),
            ]);
        }
        $user = User::where('email', $request->email)->where('otp', $request->otp)->first();
        if ($user && $user->otp_expires_at > now()) {
            $token                = $user->createToken('nobl_application')->plainTextToken;
            $user->otp            = null;
            $user->otp_expires_at = null;
            $user->save();
            return response()->json([
                'status'  => true,
                'message' => 'OTP verified successfully',
                'data'    => [
                    'access_token' => $token,
                    'user'         => $user,
                ],
            ]);
        }
        return response()->json([
            'status'  => false,
            'message' => 'Invalid OTP or OTP expired',
        ]);
    }

    public function resetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'password'   => 'required|string|min:4|same:c_password',
            'c_password' => 'required|string|min:4',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status'  => false,
                'message' => $validator->errors(),
            ]);
        }
        $user           = Auth::user();
        $user->password = Hash::make($request->password);
        $user->save();
        return response()->json([
            'status'  => true,
            'message' => 'Password reset successfully',
        ]);
    }

    public function profile()
    {
        $user = Auth::user();
        return response()->json([
            'status'  => true,
            'message' => 'Data retreived successfully',
            'data'    => $user,
        ]);
    }

    public function changePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'old_password' => 'required|string|min:4',
            'password'     => 'required|string|min:4|same:c_password',
            'c_password'   => 'required|string|min:4',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status'  => false,
                'message' => $validator->errors(),
            ]);
        }
        $user = Auth::user();
        if (Hash::check($request->old_password, $user->password)) {
            $user->password = Hash::make($request->password);
            $user->save();
            return response()->json([
                'status'  => true,
                'message' => 'Password changed successfully',
            ]);
        } else {
            return response()->json([
                'status'  => false,
                'message' => 'Old password is incorrect',
            ]);
        }
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        if ($user->role == 'ADMIN') {
            $validator = Validator::make($request->all(), [
                'name'  => 'required|string|max:255',
                'photo' => 'sometimes|image|mimes:jpeg,png,jpg,gif|max:10240',
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'status'  => false,
                    'message' => $validator->errors(),
                ]);
            }
        }
        $validator = Validator::make($request->all(), [
            'name'    => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'photo'   => 'sometimes|image|mimes:jpeg,png,jpg,gif|max:10240',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status'  => false,
                'message' => $validator->errors(),
            ]);
        }
        $user->name    = $request->name ?? $user->name;
        $user->address = $request->address ?? $user->address;
        if ($request->hasFile('photo')) {
            $photo_location = public_path('uploads/users');
            $old_photo      = basename($user->photo);
            if ($old_photo != 'default_photo.png') {
                $old_photo_location = $photo_location . '/' . $old_photo;
                if (file_exists($old_photo_location)) {
                    unlink($old_photo_location);
                }
            }

            $final_photo_name = time() . '.' . $request->photo->extension();
            $request->photo->move($photo_location, $final_photo_name);
            $user->photo = $final_photo_name;
        }
        $user->save();
        return response()->json([
            'status'  => true,
            'message' => 'Profile updated successfully',
            'data'    => $user,
        ]);
    }
}
