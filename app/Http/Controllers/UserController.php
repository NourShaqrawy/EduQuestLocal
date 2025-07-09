<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;

class UserController extends Controller
{
    public function register(Request $request)
    {

$request->validate([
    'first_name'=>'required|string|max:255|',
    'last_name'=>'required|string|max:255|',
    'email'=>'required|string|email|max:255|unique:users,email',
    'password'=>'required|string|min:8|confirmed'
]);
$user=User::create([
    'first_name'=>$request->first_name,
    'last_name'=>$request->last_name,
    'email'=>$request->email,
    'password'=>Hash::make($request->password)
]);
return response()->json([
    'message'=>'User Registered  Successfully',
    'user'=>$user
], 201);
    }
    // public function login(Request $request)
    // {
    //     $request->validate([
    //         'email'=>'required|string|email',
    //         'password'=>'required|string'
    //     ]);
    //     if(!Auth::attempt($request->only('email','password')))
    //     return response()->json(
    //         [
    //         'message'=>'invalid email or password'
    //         ], 401);
    //         $user=User::where('email',$request->email)->firstOrFail();
    //         $token=$user->createToken('auth_Token')->plainTextToken;
    //         return response()->json([
    //             'message'=>'Login  Successful',
    //             'user'=>$user,
    //             'Token'=>$token
    //         ], 201);

    // }
    public function login(Request $request)
{
    $request->validate([
        'email' => 'required|string|email',
        'password' => 'required|string'
    ]);

    if (!Auth::attempt($request->only('email', 'password'))) {
        return response()->json([
            'message' => 'Invalid email or password'
        ], 401);
    }

    $user = User::where('email', $request->email)->firstOrFail();

    // ✅ تحقق من الدور هنا
    if ($user->role === 'admin') {
       return response()->json("admin",201);
    } elseif ($user->role === 'student') {
        // عمليات أو رد مخصص للطلاب
    } elseif ($user->role === 'publisher') {
        // عمليات أو رد مخصص للناشرين
    }

    // 🔐 إنشاء التوكن بعد التحقق من الدور (اختياري حسب منطقك)
    $token = $user->createToken('auth_Token')->plainTextToken;

    return response()->json([
        'message' => 'Login Successful',
        'role' => $user->role,
        'user' => $user,
        'Token' => $token
    ], 200);
}

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json([
            'message'=>'Logout  Successful'
        ], 201);

    }



    public function index()
    {
        return User::with(['courseEnrollments', 'certificates', 'videoReactions'])->get();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_name' => 'required|string|max:100|unique:users',
            'email' => 'required|email|max:100|unique:users',
            'address' => 'nullable|string|max:200',
            'role' => 'required|in:student,publisher,admin',
            'password' => 'required|string|min:8',
        ]);

        $user = User::create([
            'user_name' => $validated['user_name'],
            'email' => $validated['email'],
            'address' => $validated['address'],
            'role' => $validated['role'],
            'password' => Hash::make($validated['password']),
        ]);

        return response()->json($user, 201);
    }

    public function show($id)
    {
        return User::with(['courseEnrollments', 'certificates', 'videoReactions'])->findOrFail($id);
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $validated = $request->validate([
            'user_name' => 'sometimes|string|max:100|unique:users,user_name,' . $id,
            'email' => 'sometimes|email|max:100|unique:users,email,' . $id,
            'address' => 'nullable|string|max:200',
            'role' => 'sometimes|in:student,publisher,admin',
            'password' => 'sometimes|string|min:8',
        ]);

        $user->update($validated);

        if (isset($validated['password'])) {
            $user->update(['password' => Hash::make($validated['password'])]);
        }

        return response()->json($user);
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();
        return response()->json(null, 204);
    }

    // public function verifyEmail(Request $request, $id)
    // {
    //     $user = User::findOrFail($id);
    //     $user->update(['email_verified_at' => now()]);
    //     return response()->json(['message' => 'Email verified successfully']);
    // }

    public function resetPassword(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $status = Password::sendResetLink($request->only('email'));

        return $status === Password::RESET_LINK_SENT
            ? response()->json(['message' => __($status)])
            : response()->json(['error' => __($status)], 400);
    }
}














