<?php
namespace App\Http\Controllers\User;
use App\Http\Controllers\Controller;
use App\Http\Utilities\OAuthProxy;
use App\Models\User;
use Exception;
use Illuminate\Foundation\Application;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Laravel\Passport\Bridge\UserRepository;
use Optimus\ApiConsumer\Facade\ApiConsumer;
use Thomaswelton\LaravelGravatar\Facades\Gravatar;


class UserController extends Controller
{
    use OAuthProxy;

    public function index(Request $request)
    {
        $by = $request->get('by') ? $request->get('by') : 'id';
        $desc = $request->get('desc') ? 'desc' : 'asc';
        $skip = $request->get('skip') ? $request->get('skip') : 0;
        $take = $request->get('take') ? $request->get('take') : 10;
        $result = User::orderBy($by, $desc)->skip($skip)->take($take)->get();
        return Response::json([
            'msg'  => '账户信息获取成功.',
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
                'msg'  => '账户信息更新失败.',
                'code' => 400,
            ], 400);
        }
        return Response::json([
            'msg'  => '账户信息更新成功.',
            'code' => 200,
        ], 200);
    }

    public function register(Request $request)
    {
        $user = new User;
        $user->fill($request->json()->all());
        $user->email = $request->json('email');
        $user->password = bcrypt($user->password);
        $user->avatar = Gravatar::src($user->email);
        try {
            $user->save();
        } catch (QueryException $e) {
            error_log($e);
            if (count(user::where('email', $user->email)->get())) {
                return Response::json([
                    'msg'  => '该邮箱已被注册.',
                    'code' => 400,
                ], 400);
            }
            return Response::json([
                'msg'  => $e,
                'code' => 400,
            ], 400);
        }
        $result = $this->proxy('password', [
            'username' => $user->email,
            'password' => $request->json('password')
        ]);
        $result = array_merge($result, $user->toArray());
        return Response::json([
            'msg'  => '注册成功.',
            'data' => $result,
            'code' => 200,
        ], 200);
    }

    public function login(Request $request)
    {
        $email = $request->json('email');
        $password = $request->json('password');
        $user = User::where('email', $email)->first();
        if (!count($user)) {
            return Response::json([
                'msg'  => '该邮箱未被注册.',
                'code' => 400,
            ], 400);
        }
        $result = $this->proxy('password', [
            'username' => $email,
            'password' => $password
        ]);
        if ($result) {
            $user->last_login_at = $user->curr_login_at;
            $user->curr_login_at = date('Y-m-d H:i:s', time());
            $user->save();
            $result = array_merge($result, $user->toArray());
            return Response::json([
                'msg'  => '登录成功.',
                'data' => $result,
                'code' => 201,
            ], 200);
        }
        return Response::json([
            'msg'  => '账户密码错误.',
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
            'msg'  => '注销成功.',
            'code' => 202,
        ], 200);
    }

}