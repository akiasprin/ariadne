<?php

namespace App\Http\Controllers\Good;

use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Utilities\RedisCacheHelper;
use App\Models\Good;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Response;
use Mockery\Exception;

class GoodController extends Controller
{

    public function index(Request $request)
    {
        $total = 0;
        $by = $request->get('by') ? $request->get('by') : 'id';
        $desc = $request->get('desc') ? 'desc' : 'asc';
        $skip = $request->get('skip') ? $request->get('skip') : 0;
        $take = $request->get('take') ? $request->get('take') : 10;
        $user = $request->get('user') ? $request->get('user') : 0;
        $state = $request->get('state') ? $request->get('state') : 15;
        $result = RedisCacheHelper::redis(
            'goods:'.$by.':'.$desc.':'.$skip.':'.$take.':'.$user.':'.$state, function ()
        use ($by, $desc, $skip, $take, $user, $state, &$total) {
            $result = Good::with('user', 'categories');
            if ($user) {
                $result = $result->where('user_id', $user);
            }
            if ($state != 15) {
                for ($i = 0; $i < 4; $i++) {
                    if (!($state & (1 << $i))) {
                        $result = $result->where('state', '!=', $i + 1);
                    }
                }
            }
            $total = $result->count();
            return $result->orderBy($by, $desc)->skip($skip)->take($take)->get([
                'id', 'title', 'desc', 'cover', 'price', 'unit', 'province', 'city',
                'state', 'views', 'sales', 'quality', 'purchased_at', 'created_at', 'updated_at',
                'user_id'
            ]);
        });
        return Response::json([
            'msg' => '商品信息获取成功.',
            'data' => ['result' => $result, 'total' => $total],
            'code' => 200,
        ], 200);
    }

    public function store(Request $request)
    {
        try {
            $good = new Good;
            $good->fill($request->json()->all());
            $good->user_id = $request->user('api')->id;
            if ($good->purchased_at) {
                $good->purchased_at = date(
                    'Y-m-d', strtotime($good->purchased_at));
            } else if ($good->quality == 2) {
                return Response::json([
                    'msg' => '未填写购置日期',
                    'code' => 400,
                ], 400);
            } else {
                $good->purchased_at = null;
            }
            DB::transaction(function () use ($request, $good) {
                $good->save();
                $good->categories()->sync($request->json('categories'));
            });
            Redis::delete(Redis::keys('goods'));
        } catch (QueryException $e) {
            error_log($e);
            return Response::json([
                'msg' => '商品信息创建失败.',
                'code' => 400,
            ], 400);
        }
        return Response::json([
            'msg' => '商品信息创建成功.',
            'data' => $good,
            'code' => 201,
        ], 200);
    }

    public function show($id)
    {
        $good = RedisCacheHelper::redis('good:'.$id, function () use ($id) {
            $good = Good::with('user', 'comments', 'comments.user')->find($id);
            $good->increment('views');
            $good->categories = $good->categories()->get()
                ->map(function ($category) {
                    return $category->id;
                });
            return $good;
        });

        return Response::json([
            'msg' => '商品信息获取成功.',
            'data' => $good,
            'code' => 200,
        ], 200);
    }

    public function update(Request $request, $id)
    {
        try {
            $good = Good::with('categories')->find($id);
            $good->fill($request->json()->all());
            if ($good->purchased_at) {
                $good->purchased_at = date(
                    'Y-m-d', strtotime($good->purchased_at));
            } else if ($good->quality == 2) {
                return Response::json([
                    'msg' => '未填写购置日期',
                    'code' => 400,
                ], 400);
            } else {
                $good->purchased_at = null;
            }
            if (!$request->json('total')
                && $request->json('state') == 2
            ) {
                $good->state = 3;
            } else if ($request->json('total')
                && $request->json('state') == 3
            ) {
                $good->total = 0;
            }
            DB::transaction(function () use ($request, $good) {
                $good->categories()->sync($request->json('categories'));
                $good->save();
            });
            Redis::delete('goods:*');
        } catch (QueryException $e) {
            return Response::json([
                'msg' => '商品更新失败.',
                'code' => 400,
            ], 400);
        }
        return Response::json([
            'msg' => '商品更新成功.',
            'data' => $good,
            'code' => 201,
        ], 200);
    }

    public function destroy($id)
    {
    }
}
