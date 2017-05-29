<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Response;

class AdminController extends Controller
{
    public function __construct()
    {
        //$this->middleware('auth.filter:admin', ['except' => 'logout']);
    }

    public function login(Request $request)
    {
        $email = $request->input('email');
        $password = $request->input('password');
        if (Auth::guard('admin')->attempt(
            ['email' => $email, 'password' => $password]
        )) {
            $request->user('admin')->last_login_at = $request->user('admin')->curr_login_at;
            $request->user('admin')->curr_login_at = date('Y-m-d H:i:s', time());
            $request->user('admin')->save();
            return Response::json([
                'success' => true,
                'message' => '登录成功.',
            ], 200);
        } else {
            if (!count(Admin::where('email', $email)->get())) {
                return Response::json([
                    'success' => false,
                    'message' => '该邮箱未被注册.',
                ], 400);
            }
        }
        return Response::json([
            'success' => false,
            'message' => '密码错误.',
        ], 400);
    }

    public function logout(Request $request)
    {
        Auth::guard('admin')->logout();
        return Response::json([
            'success' => true,
            'message' => '注销成功.',
        ], 200);
    }

}