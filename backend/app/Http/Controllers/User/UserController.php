<?php

namespace App\Http\Controllers\User;

use App\Exceptions\SmsPusherException;
use App\Http\Controllers\Controller;
use App\Http\Utilities\AlidayuHelper;
use App\Http\Utilities\OAuthProxy;
use App\Http\Utilities\RedisCacheHelper;
use App\Jobs\SendReminderEmail;
use App\Models\User;
use Exception;
use Illuminate\Foundation\Application;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Response;
use Laravel\Passport\Bridge\UserRepository;
use Optimus\ApiConsumer\Facade\ApiConsumer;
use Thomaswelton\LaravelGravatar\Facades\Gravatar;
use iscms\Alisms\SendsmsPusher as Sms;


class UserController extends Controller
{
    use OAuthProxy;

    protected $sms;

    public function __construct(Sms $sms)
    {
        $this->sms = $sms;
    }

    public function index(Request $request)
    {
        $by = $request->get('by') ? $request->get('by') : 'id';
        $desc = $request->get('desc') ? 'desc' : 'asc';
        $skip = $request->get('skip') ? $request->get('skip') : 0;
        $take = $request->get('take') ? $request->get('take') : 10;
        $result = User::orderBy($by, $desc)->skip($skip)->take($take)->get();
        return Response::json([
            'msg' => '账户信息获取成功.',
            'data' => $result,
            'code' => 200,
        ], 200);
    }

    public function show($id)
    {
        $user = User::find($id);
        return Response::json($user, 200);
    }

    public function modify(Request $request, $id)
    {
        $user = User::find($id);
        $user->fill($request->json()->all());
        try {
            $user->save();
        } catch (QueryException $e) {
            return Response::json([
                'msg' => '账户信息更新失败.',
                'code' => 400,
            ], 400);
        }
        return Response::json([
            'msg' => '账户信息更新成功.',
            'code' => 200,
        ], 200);
    }

    public function register(Request $request)
    {
        $code = $request->json('code');
        $name = $request->json('name');
        $phone = $request->json('phone');
        $password = $request->json('password');
        if (User::where('phone', $phone)->count()) {
            return Response::json([
                'msg' => '该手机已注册!',
                'code' => 400
            ], 400);
        }
        if (User::where('name', $name)->count()) {
            return Response::json([
                'msg' => '该名字已被注册!',
                'code' => 400
            ], 400);
        }
        if (!Redis::exists('user:code:' . $phone)) {
            return Response::json([
                'msg' => '验证码已过期!',
                'code' => 400
            ], 400);
        }
        if (Redis::get('user:code:' . $phone) != $code) {
            return Response::json([
                'msg' => '验证码错误!',
                'code' => 400
            ], 400);
        }
        $user = new User;
        $user->name = $name;
        $user->phone = $phone;
        $user->password = bcrypt($password);
        try {
            $user->save();
        } catch (QueryException $e) {
            return Response::json([
                'msg' => '注册失败!',
                'code' => 400
            ], 400);
        }
        $result = $this->proxy('password', [
            'username' => $phone,
            'password' => $password
        ]);
        $result = array_merge($result, $user->toArray());
        return Response::json([
            'msg'  => '注册成功!',
            'data' => $result,
            'code' => 201,
        ], 200);
    }

    public function sendRegCode(Request $request)
    {
        $name = $request->json('name');
        $phone = $request->json('phone');
        $exists = Redis::exists('user:code:' . $phone);
        if ($exists) {
            return Response::json([
                'msg'  => '重复发送!',
                'code' => 400
            ], 400);
        }
        try {
            $code = RedisCacheHelper::redis('user:code:'.$phone,
                function () use ($phone, $name) {
                $code = rand(100000, 999999);
                AlidayuHelper::code($this->sms, $phone, $name, $code);
                return $code;
            });
            Redis::expire('user:code:'.$phone, 300);
            error_log($code);
        } catch (SmsPusherException $e) {
            return Response::json([
                'msg'  => '发送失败.',
                'code' => 400
            ], 400);
        }
        return Response::json([
            'msg'  => '发送成功.',
            'code' => 201
        ], 200);
    }

    public function login(Request $request)
    {
        $phone = $request->json('phone');
        $password = $request->json('password');
        $user = User::where('phone', $phone)->first();
        if (!count($user)) {
            return Response::json([
                'msg' => '该手机未被注册.',
                'code' => 400,
            ], 400);
        }
        $result = $this->proxy('password', [
            'username' => $phone,
            'password' => $password
        ]);
        if ($result) {
            $user->last_login_at = $user->curr_login_at;
            $user->curr_login_at = date('Y-m-d H:i:s', time());
            $user->save();
            $result = array_merge($result, $user->toArray());
            return Response::json([
                'msg' => '登录成功.',
                'data' => $result,
                'code' => 201,
            ], 200);
        }
        return Response::json([
            'msg' => '账户密码错误.',
            'code' => 400,
        ], 400);
    }

    public function refresh()
    {
        $refreshToken = $this->request->cookie('refreshToken');

        return $this->proxy('refresh_token', [
            'refresh_token' => $refreshToken
        ]);
    }

    public function logout(Request $request)
    {
        $accessToken = $request->user('api')->token();

        $refreshToken = DB::table('oauth_refresh_tokens')
            ->where('access_token_id', $accessToken->id)
            ->update([
                'revoked' => true
            ]);

        $accessToken->revoke();

        Cookie::queue(Cookie::forget('refreshToken'));
        return Response::json([
            'msg' => '注销成功.',
            'code' => 202,
        ], 200);
    }

}