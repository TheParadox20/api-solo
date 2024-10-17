<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class UserController extends Controller
{
    /**
     * Given a token return user
     */
    public function index(Request $request){
        $user = $request->user(); // Get the authenticated user
        if ($user) {
            return response()->json([
                'name' => $user->name,
                'phone' => $user->phone,
                'balance' => $user->balance,
                'web3' => $user->wallet!=null,
            ]);
        } else {
            return response()->json(['error' => 'Not Authenticated'], 401);
        }
    }
    /**
     * User signup
     */
    public function signup(Request $request){
        try{
            $credentials = $request->validate([
                'name' => 'required',
                'phone' => 'required',
                'password' => 'required',
            ]);
            User::create($credentials);
            if (Auth::attempt($credentials)) {
                $user = Auth::user();
                if ($request->name == $request->phone && strlen($request->name) == 42 && strpos($request->name, '0x') === 0) {
                    $user->wallet = $request->name;
                    $user->save();
                }
                $token = $user->createToken('auth_token')->plainTextToken;

                return response()->json([
                    'message' => 'Account created',
                    'token' => $token,
                    'name' => $user->name,
                    'balance' => $user->balance,
                    'phone' => $user->phone,
                    'web3' => $user->wallet!=null,
                ]);
            }
            else {
                throw_if(true, \Exception::class, 'User not created');
            }
        } catch (\Exception $e) {
            return response()->json([ 
                'error' => $e->getMessage(),
                'request'=>$request->all()
            ], 401);
        }
    }

    /**
     * User signin
     */
    public function signin(Request $request){
        try{
            $credentials = $request->validate([
                'phone' => 'required',
                'password' => 'required',
            ]);
            throw_if(!User::where('phone',$credentials['phone'])->exists(), \Exception::class, 'Account not found');
            // throw_if(Auth::attempt($credentials), \Exception::class, 'Invalid credentials');
            throw_if(!Auth::guard('web')->attempt(['phone'=>$credentials['phone'], 'password'=>$credentials['password']]), \Exception::class, 'Invalid credentials');
            $user = Auth::user();
            // throw_if(!$user->email_verified_at, \Exception::class, 'Phone number not verified');
            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'message' => 'Successful signin',
                'token' => $token,
                'name' => $user->name,
                'balance' => $user->balance,
                'phone' => $user->phone,
                'web3' => $user->wallet!=null,
            ]);
        } catch (\Exception $e) {
            return response()->json([ 
                'error' => $e->getMessage(),
                'request'=>$request->all()
            ], 401);
        }
    }
}
