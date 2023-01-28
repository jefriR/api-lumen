<?php

namespace App\Http\Controllers;

use App\Models\User;
use Firebase\JWT\JWT;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public function register(Request $request){
        try {
            $this->validate($request,[
//                'email' => 'required|max:50|email|unique:users,email',
                'email' => 'required|max:50|email',
                'name'  => 'required|max:50',
                'password'  => 'required',
            ]);

            $user       = new User();
            $name       = $request->name;
            $email      = $request->email;
            $password   = Hash::make($request->password);

            $user::create([
                'name'      => $name,
                'email'     => $email,
                'password'  => $password,
                'member_id' => rand(11111,99999),
                'flag_active'=> 1,
                'flag_verif' => 0
            ]);

            return response()->json(['status' => 200, 'message' => "Register success", 'data' => null]);
        }catch (\Exception $e){
            return response()->json(['status' => $e->getCode(), 'message' => $e->getMessage(), 'data' => null]);
        }
    }

    public function login(Request $request) {
        try {
            $this->validate($request, [
                'email' => 'required|max:50|email|exists:users,email',
                'password' => 'required',
            ]);

            $email  = $request->email;
            $password = $request->password;
            $user   = User::where('email', $email)->first();

            if (!Hash::check($password, $user->password)) {
                return response()->json(['status' => '401', 'message' => 'Password not valid!', 'data' => null]);
            }

            $payload = [
                'iat' => intval(microtime(true)),
                'exp' => intval(microtime(true)) + (60*60*1000),
                'uid' => $user->id
            ];

            $token = JWT::encode($payload, env('JWT_SECRET'), 'HS256');
            $user->tokens = $token;

            return response()->json(['status' => '200', 'message' => 'Login Success!!', 'data' => $token]);

        } catch (\Exception $e) {
            return response()->json(['status' => $e->getCode(), 'message' => $e->getMessage(), 'data' => null]);
        }
    }

    public function loginSelfToken(Request $request) {
        try {
            $this->validate($request, [
                'email' => 'required|max:50|email',
                'password' => 'required',
            ]);

            $user = new User();
            $email = $request->email;
            $password = $request->password;

            $user = User::where('email', $email)->first();
            if(!$user) return response()->json(['status' => '401', 'message' => 'Email Not Register', 'data' => null]);

            $isValidPassword = Hash::check($password, $user->password);
            if (!$isValidPassword) return response()->json(['status' => '401', 'message' => 'Password Wrong!', 'data' => null]);

            $generateToken = bin2hex(random_bytes(40));
            $user->update([
                'token' => $generateToken
            ]);

            return response()->json(['status' => '200', 'message' => 'Login Success!!', 'data' => $user]);

        } catch (\Exception $e) {
            return response()->json(['status' => $e->getCode(), 'message' => $e->getMessage(), 'data' => null]);
        }
    }
}
