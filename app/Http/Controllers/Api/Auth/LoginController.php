<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator; //untuk validasi data
use Tymon\JWTAuth\Facades\JWTAuth; //untuk mendapatkan token jwt

class LoginController extends Controller
{

    /** 
     *
     * 
     * 
     * @param mixed $request
     * @return void
     */
    public function index(Request $request)
{
    //set validasi
    $validator = Validator::make($request->all(), [
        'email'   =>  'required|email',
        'password'=>  'required',
    ]);

    //response eror validasi
    if ($validator->fails()) {
        return response()->json($validator->errors(), 422);
    }

    $credentials = $request->only('email', 'password');

    //check jika email dan password tidak sesuai 
    if (!$token = auth()->guard('api')->attempt($credentials)) {
        return response()->json([
            'success' => false,
            'message' => 'Email or Password is incorrect'
        ], 400);
    }

    $user = auth()->guard('api')->user();

    // Ambil role pengguna
    $role = $user->role->name; // Sesuaikan dengan relasi dan struktur tabel Anda

    // Perbarui token JWT dengan role yang terbaru
    $customClaims = ['role' => $role];
    $token = auth()->guard('api')->claims($customClaims)->refresh();

    return response()->json([
        'success' => true,
        'user' => $user->only(['name', 'phone', 'email']),
        'permissions' => $user->getPermissionArray(),
        'roles' => $user->getRoleNames(),
        'token' => $token,
    ], 200);
}

        /**
         * logout
         * berfungsi untuk menghapus jwt token
         *  @return void
         */
        public function logout()
        {
            //remove "token" jwt 
            JWTAuth::invalidate(JWTAuth::getToken());

            //response "success" logout
            return response()->json([
                'berhasil logout' => true,
            ], 200);
        }


}