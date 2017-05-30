<?php

namespace App\Http\Controllers\Address;

use App\Exceptions\PermissionDeniedException;
use App\Http\Utilities\RedisCacheHelper;
use App\Models\Address;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Response;

class AddressController extends Controller
{
    public function index(Request $request)
    {
        $by = $request->get('by') ? $request->get('by') : 'id';
        $desc = $request->get('desc') ? 'desc' : 'asc';
        $skip = $request->get('skip') ? $request->get('skip') : 0;
        $take = $request->get('take') ? $request->get('take') : 10;
        $user = $request->get('user') ? $request->get('user') : 0;
        $data = RedisCacheHelper::redis('addresses:'.$user.':'.$by.':'.$desc.':'.$skip.':'.$take,
            function () use ($by, $desc, $skip, $take, $user) {
                $result = Address::with('user');
                if ($user) {
                    $result = $result->where('user_id', $user);
                }
                $total = $result->count();
                $result = $result->orderBy($by, $desc)->skip($skip)->take($take)->get();
                return ['result' => $result,
                        'total' => $total];
            });
        return Response::json([
            'msg'  => '地址信息获取成功.',
            'data' => $data,
            'code' => 200,
        ], 200);
    }

    public function store(Request $request)
    {
        $address = new Address;
        try {
            $address->fill($request->json()->all());
            $address->user_id = $request->user('api')->id;
            $address->save();
            RedisCacheHelper::clean([
                'addresses:0*',
                'addresses:'.$address->user_id.'*'
            ]);
        } catch (QueryException $e) {
            return Response::json([
                'msg' => '地址创建失败.',
                'code' => 400,
            ], 400);
        }
        return Response::json([
            'msg'  => '地址创建成功.',
            'data' => $address,
            'code' => 200,
        ], 200);
    }

    public function show($id)
    {
        $address = RedisCacheHelper::redis('address:'.$id,
            function () use ($id) {
                return  Address::with('user')->find($id);
            });
        return Response::json($address, 200);
    }

    public function update(Request $request, $id)
    {
        $address = Address::find($id);
        $address->fill($request->json()->all());
        try {
            $address->save();
            RedisCacheHelper::clean([
                'address:'.$id.'*',
                'addresses:0*',
                'addresses:'.$address->user_id.'*'
            ]);
        } catch (QueryException $e) {
            return Response::json([
                'msg' => '地址更新失败.',
                'code' => 400,
            ], 400);
        }
        return Response::json([
            'msg'  => '地址更新成功.',
            'data' => $address,
            'code' => 201,
        ], 200);
    }

    public function destroy(Request $request, $id)
    {
        try {
            $address = Address::find($id);
            if ($request->user('api')->id !=
                $address->user_id) {
                throw new PermissionDeniedException();
            }
            $address = Address::destroy($id);
            RedisCacheHelper::clean([
                'address:'.$id.'*',
                'addresses:0*',
                'addresses:'.$request->user('api')->id.'*'
            ]);
        } catch (PermissionDeniedException $e) {
            return Response::json([
                'msg' => '操作失败, 权限不足.',
                'code' => 400,
            ], 400);
        } catch (QueryException $e) {
            return Response::json([
                'msg' => '地址删除失败.',
                'code' => 400,
            ], 400);
        }
        return Response::json([
            'msg' => '地址删除成功.',
            'data' => $address,
            'code' => 201,
        ], 200);
    }
}
