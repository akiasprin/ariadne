<?php

namespace App\Http\Controllers\Order;

use App\Exceptions\OrderWrongStateException;
use App\Http\Utilities\RedisCacheHelper;
use App\Models\Order;
use App\Exceptions\GoodUnavailableException;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Response;

class OrderController extends Controller
{
    //需要访问控制:订单信息仅允许当前用户
    public function index(Request $request)
    {
        $by = $request->get('by') ? $request->get('by') : 'id';
        $desc = $request->get('desc') ? 'desc' : 'asc';
        $skip = $request->get('skip') ? $request->get('skip') : 0;
        $take = $request->get('take') ? $request->get('take') : 10;
        $customer = $request->get('customer') ? $request->get('customer') : 0;
        $merchant = $request->get('merchant') ? $request->get('merchant') : 0;
        $state = $request->get('state') ? $request->get('state') : 4095;
        $data = RedisCacheHelper::redis('orders:'.$customer.':'.$merchant.':'.$by.':'.$desc.':'.$skip.':'.$take
            .':'.$state, function () use ($by, $desc, $skip, $take, $customer, $merchant, $state) {
            $result = Order::with('goods', 'address', 'customer', 'merchant');
            if ($customer) {
                $result = $result->where('customer_id', $customer);
            }
            if ($merchant) {
                $result = $result->where('merchant_id', $merchant);
            }
            if ($state != 4095) {
                for ($i = 0; $i < 12; $i++) {
                    if (!($state & (1 << $i))) {
                        $result = $result->where('state', '!=', $i + 1);
                    }
                }
            }
            $total = $result->count();
            $result = $result->orderBy($by, $desc)->skip($skip)->take($take)->get();
            return ['result' => $result,
                    'total' => $total];
        });
        return Response::json([
            'msg'  => '订单信息获取成功.',
            'data' => $data,
            'code' => 200,
        ], 200);
    }

    public function store(Request $request)
    {
        try {
            $order = new Order;
            DB::transaction(function () use ($request, $order) {
                $sum = 0;
                $goods = [];
                $order->address_id = $request->json('address_id');
                $order->customer_id = $request->user('api')->id;
                $order->merchant_id = $order->customer_id;
                $order->sum = $sum;
                $order->save();
                foreach ($request->json('goods') as $good) {
                    $goods = $goods + [$good[0] => ['quantity' => $good[1]]];
                }
                $order->goods()->attach($goods);
                foreach ($order->goods()->get() as $good) {
                    if ($order->merchant_id == $order->customer_id) {
                        $order->merchant_id = $good->user_id;
                    }
                    // 并发控制
                    $res = DB::update('
                        UPDATE `goods` 
                        SET `total` = `total` - ?, `sales` = `sales` + ?, `updated_at` = ?
                        WHERE `id` = ?
                        AND `state` = 2 AND `total` > ? 
                    ', [
                        $good->pivot->quantity,
                        $good->pivot->quantity,
                        date('Y-m-d H:i:s',time()),
                        $good->id,
                        $good->pivot->quantity,
                    ]);
                    if (!$res) {
                        throw new GoodUnavailableException;
                    }
                    $sum += $good->price * $good->pivot->quantity;
                }
                $order->sum = $sum;
                $order->save();
                $order->timelines()->create([
                    'state' => 1,
                    'operated_user_id' => $request->user()->id,
                ]);
                RedisCacheHelper::clean([
                    'good:'.'*',
                    'goods:'.'*',
                    'orders:'.$order->customer_id.'*',
                    'orders:'.'0:'.$order->merchant_id.'*'
                ]);
            });
        } catch (GoodUnavailableException $e) {
            return Response::json([
                'msg'  => '订单商品缺货或已下架.',
                'code' => 400,
            ], 400);
        } catch (QueryException $e) {
            return Response::json([
                'e' => $e,
                'msg' => '订单创建异常.',
                'code' => 400,
            ], 400);
        }
        return Response::json([
            'msg' => '订单创建成功.',
            'data' => $order,
            'code' => 201,
        ], 200);
    }

    public function show($id)
    {
        $result = RedisCacheHelper::redis('order:'.$id, function () use ($id) {
            return Order::with('goods', 'address', 'timelines', 'timelines.user')->find($id);
        });
        return Response::json([
            'msg' => '订单获取成功.',
            'data' => $result,
            'code' => 200,
        ], 200);
    }

    //需要访问控制:订单状态变更区间与用户控制
    public function update(Request $request, $id)
    {
        try {
            $order = Order::with('timelines')->find($id);
            DB::transaction(function () use ($request, $order) {
                $state = $request->json('state');
                $to_state = $order->state;
                switch ($state) {
                    case 1:
                        switch ($to_state) {
                            case 2: break;
                            case 5: break;
                            default: throw new OrderWrongStateException();
                        }
                    case 3:
                        $order->express_name = $request->json('express_name');
                        $order->express_code = $request->json('express_code');
                        if(!$order->express_name || !$order->express_code) {
                            throw new OrderWrongStateException();
                        }
                        break;
                }
                $order->state = $state;
                $order->timelines()->create([
                    'state' => $state,
                    'operated_user_id' => $request->user()->id,
                ]);
                $order->save();
            });
            RedisCacheHelper::clean([
                'order:'.$id,
                'orders:'.$order->customer_id.'*',
                'orders:'.'0:'.$order->merchant_id.'*'
            ]);
        } catch (OrderWrongStateException $e) {
            return Response::json([
                'msg'  => '订单状态更新逻辑检查异常.',
                'code' => 400,
            ], 400);
        } catch (QueryException $e) {
            return Response::json([
                'msg'  => '订单状态更新异常.',
                'code' => 400,
            ], 400);
        }
        return Response::json([
            'data' => $order,
            'msg'  => '订单状态更新成功.',
            'code' => 201,
        ], 200);
    }

    public function destroy($id)
    {
    }
}
