<?php

namespace App\Http\Controllers\Api\Admin;

use App\Models\User;
use Spatie\Permission\Models\Role;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\UserStoreRequest;
use Illuminate\Support\Facades\Request;

class UserController extends Controller
{
    public function index(Request $request)
    {
        try {
            $searchTerm = request('searchTerm');
            $roleCategory = request('roleCategory');



            $query = User::with('role');

            // Apply search by name
            if ($searchTerm) {
                $query->where('name', 'like', '%' . $searchTerm . '%');
            }

            // Apply filtering by role category
            if ($roleCategory) {
                $query->whereHas('role', function ($q) use ($roleCategory) {
                    $q->where('name', $roleCategory);
                });
            }

            $users = $query->get();

            return response()->json(['users' => $users], 200);
        } catch (\Exception $exception) {
            return response()->json(['message' => 'Error fetching users'], 500);
        }
    }

    public function getUsersByRoleId(Request $request)
    {
        try {
            $role_id = $request->input('role_id');

            if (!$role_id) {
                return response()->json(['message' => 'Role ID is required'], 400);
            }

            $users = User::with('role')->where('role_id', $role_id)->get();

            return response()->json(['users' => $users], 200);
        } catch (\Exception $exception) {
            return response()->json(['message' => 'Error fetching users by role ID'], 500);
        }
    }

    public function store(UserStoreRequest $request)
    {
        try {
            $validatedData = $request->validated();
    
            $role = Role::find($validatedData['role_id']);
    
            if (!$role) {
                return response()->json([
                    'message' => 'Peran yang diberikan tidak valid.',
                ], 422);
            }
    
            $user = User::create([
                'number' => $validatedData['number'],
                'name' => $validatedData['name'],
                'longName' => $validatedData['longName'],
                'gender' => $validatedData['gender'],
                'email' => $validatedData['email'],
                'password' => bcrypt($validatedData['password']),
                'role_id' => $role->id,
            ]);
    
            $user->assignRole($role->name);
    
            $user = $user->fresh();
    
            if (!$user->hasRole($role->name)) {
                return response()->json([
                    'message' => 'Gagal menetapkan peran untuk pengguna.',
                ], 500);
            }
    
            return response()->json([
                'user' => $user,
                'message' => 'Pengguna berhasil dibuat.',
            ], 201);
        } catch (\Exception $exception) {
            return response()->json([
                'message' => 'Terjadi kesalahan. Silakan coba lagi nanti.',
            ], 500);
        }
    }


    public function show($id)
    {
        // User Detail 
        $users = User::find($id);
        if (!$users) {
            return response()->json([
                'message' => 'User Not Found.'
            ], 404);
        }

        // Return Json Response
        return response()->json([
            'users' => $users
        ], 200);
    }

    public function update(UserStoreRequest $request, $id)
    {
        try {
            $validatedData = $request->validated();
            $user = User::findOrFail($id);
    
            // Update user data
            $user->update([
                'number' => $validatedData['number'],
                'name' => $validatedData['name'],
                'longName' => $validatedData['longName'],
                'gender' => $validatedData['gender'],
                'email' => $validatedData['email'],
            ]);
    
            // Optionally, update the password if provided
            if ($request->filled('password')) {
                $user->update(['password' => Hash::make($request->input('password'))]);
            }
    
            // Update user role
            $role = Role::find($validatedData['role_id']);
            if (!$role) {
                return response()->json(['message' => 'Peran yang diberikan tidak valid.'], 422);
            }
    
            // Sinkronisasi peran pengguna
            $user->syncRoles([$role->id]);
            $user->role_id = $role->id;
            $user->save();
    
            // Menyegarkan data pengguna untuk mendapatkan data yang diperbarui, termasuk peran
            $user = $user->fresh();
    
            return response()->json(['user' => $user, 'message' => 'User updated successfully']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error updating user'], 500);
        }
    }
    


    public function destroy($id)
    {
        // Detail 
        $users = User::find($id);
        if (!$users) {
            return response()->json([
                'message' => 'User Not Found.'
            ], 404);
        }

        // Delete User
        $users->delete();

        // Return Json Response
        return response()->json([
            'message' => "User successfully deleted."
        ], 200);

    }
}