<?php

namespace App\Http\Controllers\Api\Auth;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RegisterController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        //set validation
        $validator = Validator::make($request->all(), [
            'name'      => 'required',
            'phone'      => 'required',
            'email'     => 'required|email|unique:users',
            'password'  => 'required|min:8'
                ]);

                //if validation fails
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        //create user
        $user = User::create([
            'name'      => $request->name,
            'phone'      => $request->phone,
            'email'     => $request->email,
            'password'  => bcrypt($request->password),
        ]);

        //return response JSON user is created
        if($user) {
            // Menetapkan peran dan izin untuk peran 'customer'
            $role = Role::where('name', 'customer')->first(); // Mengambil peran 'customer'
            $user->assignRole($role); // Menetapkan peran 'customer' kepada pengguna baru
        
            $permissions = $role->permissions; // Mendapatkan izin dari peran 'customer'
            $user->syncPermissions($permissions); // Menetapkan izin tersebut kepada pengguna baru
        
            return response()->json([
                'success' => true,
                'user'    => $user,  
            ], 201);
        }

        //return JSON process insert failed 
        return response()->json([
            'success' => false,
        ], 409);
    }
}