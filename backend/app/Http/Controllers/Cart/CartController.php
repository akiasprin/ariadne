<?php

namespace App\Http\Controllers\Cart;

use App\Http\Utilities\RedisCacheHelper;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Response;

class CartController extends Controller
{

    public function index(Request $request)
    {
        $user = $request->user('api');
        $data = RedisCacheHelper::redis('cart:'.$user->id,
            function () use ($user) {
                return $user->cart()->with('user')->get()->groupBy('user_id')->values();
            });
        return Response::json([
            'msg'  => '购物车信息获取成功.',
            'data' => $data,
            'code' => 200,
        ], 200);
    }

    public function store(Request $request)
    {
        $good = $request->json('good');
        $user = $request->user('api');
        $exists = $user->cart()->where('id', $good[0])->first();
        $origin = $exists ? $exists->pivot->quantity : 0;
        $user->cart()->syncWithoutDetaching([$good[0] => ['quantity' => $origin + $good[1]]]);
        RedisCacheHelper::clean(['cart:'.$user->id]);
        return Response::json([
            'msg'  => '添加购物车成功.',
            'code' => 200,
        ], 200);
    }

}
