<?php
namespace App\Http\Controllers\api\Backend;

use App\Http\Controllers\Controller;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $users = User::where('role', '==', 'USER');
        if ($request->has('search')) {
            $users = $users->where('name', 'like', '%' . $request->search . '%')
                ->orWhere('email', 'like', '%' . $request->search . '%')
                ->orWhere('address', 'like', '%' . $request->search . '%');
        }
        $users = $users->latest('id')->paginate($request->per_page ?? 10);
        return response()->json([
            'status'  => true,
            'message' => 'User retreived successfully',
            'data'    => $users,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email'        => 'required|email|max:255|unique:users,email',
            'user_name'    => 'required|string|max:255',
            'name'    => 'sometimes|string|max:255',
            'address'    => 'sometimes|string|max:255',
            'badge_number' => 'required|string|max:255',
            'password'     => 'required|string|min:6',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status'  => false,
                'message' => $validator->errors(),
            ]);
        }

        $user = User::create([
            'email'             => $request->email,
            'username'          => $request->user_name,
            'name'          => $request->name,
            'address'          => $request->address,
            'badge_number'      => $request->badge_number,
            'email_verified_at' => now(),
            'password'          => bcrypt($request->password),
        ]);
        return response()->json([
            'status'  => true,
            'message' => 'User created successfully',
            'data'    => $user,
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $user = User::findOrFail($id);
            $user->delete();
            return response()->json([
                'status'  => true,
                'message' => 'User deleted successfully',
                'data'    => $user,
            ]);
        } catch (Exception $e) {
            Log::error('User delete error: ' . $e->getMessage());
            return response()->json([
                'status'  => false,
                'message' => 'User not found',
                'data'    => null,
            ]);
        }
    }
}
