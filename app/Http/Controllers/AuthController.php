<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(Request $request) 
    {
    $http = new Client;
    try {
        $response = $http->post(config('services.passport.login_endpoint'), [
            'form_params' => [
                'grant_type' => 'password',
                'client_id' => config('services.passport.client_id'),
                'client_secret' => config('services.passport.client_secret'),
                'username' => $request->username,
                'password' => $request->password,
                'scope' => '',
            ],
        ]);
        return json_decode((string) $response->getBody(), true);
        } catch (\GuzzleHttp\Exception\BadResponseException $e) {
            if ($e->getCode() === 400) {
                return response()->json('Invalid Request. Please enter a username or a password.', $e->getCode());
            } else if ($e->getCode() === 401) {
                return response()->json('Your credentials are incorrect. Please try again', $e->getCode());
            }
            return response()->json('Something went wrong on the server.', $e->getCode());
        }
    }
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
        ]);
        return User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);
    }
    public function logout()
    {
        auth()->user()->tokens->each(function ($token, $key) {
            $token->delete();
        });
        return response()->json('Logged out successfully', 200);
    }

    // public function login(Request $request) {
    //     try{
    //         if(Auth::attempt(['email' => $request->username, 'password' => $request->password])) {
    //             $user = User::where('email',$request->username)->first();
    //             Auth::login($user);
    //             $token = $user->createToken('LOGIN')->accessToken;
    //             $user['token'] = $token;

    //             return $this->dataSuccess('Đăng nhập thành công',$user,200);
    //         }
    //         else
    //         {
    //             return $this->dataError('Tên đăng nhập hoặc mật khẩu không đúng',[],422);
    //         }
    //     }catch(\Exception $exception){
    //         return $this->dataError($exception->getMessage(),[],422);
    //     }
    // }

    // public function logout() {
    //     $accessToken = Auth::user()->token();
    //     DB::table('oauth_refresh_tokens')
    //         ->where('access_token_id', $accessToken->id)
    //         ->update([
    //             'revoked' => true
    //         ]);
    //     $accessToken->revoke();
    //     return $this->dataSuccess('Đăng xuat thành công',[],200);
    // }

    // public function register(Request $request) {
    //     $request->validate([
    //         'email' => 'required|unique:users,email'
    //     ]);
    //     try{
    //         $input = $request->all();
    //         $input['password'] = bcrypt($input['password']);
    //         $user = User::create($input);

    //         return $this->dataSuccess('Đăng ky thành công',$user,200);
    //     }catch(\Exception $exception){
    //         return $this->dataError($exception->getMessage(),[],422);
    //     }
    // }
}
