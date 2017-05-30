<?php

namespace App\Http\Controllers\Category;

use App\Http\Utilities\RedisCacheHelper;
use App\Models\Category;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Response;
use Optimus\ApiConsumer\Facade\ApiConsumer;

class CategoryController extends Controller
{
    public function index()
    {
        $data = RedisCacheHelper::redis('categories', function () {
            $categories = Category::get();
            $map = []; $edge = []; $ret = [];
            foreach ($categories as $item) $map += [$item->id => $item];
            // 反转建边
            foreach ($categories as $item) {
                if ($item->parent_id) {
                    if (assert($edge[$item->parent_id]))
                        $edge += [$item->parent_id => []];
                    $edge[$item->parent_id][] = $item->id;
                }
            }
            // DFS建图
            $make = function ($from) use (&$make, $map, $edge) {
                //  === Indirect modification of overloaded element ===
                //            $subs = clone $map[$from];     //????
                //  ===================================================
                $subs['id'] = $map[$from]['id'];
                $subs['name'] = $map[$from]['name'];
                $subs['slug'] = $map[$from]['slug'];

                if (!isset($edge[$from])) return $subs;
                foreach ($edge[$from] as $to) {
                    $subs['child'][] =  $make($to);
                }
                return $subs;
            };
            // 森林找根
            foreach ($categories as $item) {
                if (!$item->parent_id) {
                    $ret[] = $make($item->id);
                }
            }
            return $ret;
        });
        return Response::json([
            'msg'  => '分类获取成功.',
            'data' => $data,
            'code' => 200,
        ], 200);

    }

    public function store(Request $request)
    {
        $category = new Category();
        $category->fill($request->json()->all());
        try{
            $category->save();
            RedisCacheHelper::clean(['categories']);
        } catch (QueryException $e) {
            return Response::json([
                'msg' => '分类创建失败.',
                'code' => 400,
            ], 400);
        }
        return Response::json([
            'msg' => '分类创建成功.',
            'data' => $category,
            'code' => 201,
        ], 200);
    }

//    public function show($id)
//    {
//        $categories = Category::with('goods', 'goods.user')->find($id);
//        return Response::json([
//            'msg'  => '分类获取成功.',
//            'data' => $categories,
//            'code' => 200,
//        ], 200);
//    }

    public function show($slug)
    {
        $category = Category::with('goods', 'goods.user')->where('slug', $slug)->get();
        return Response::json([
            'msg'  => '分类获取成功.',
            'data' => $category,
            'code' => 200,
        ], 200);
    }

    public function update(Request $request, $id)
    {
        $category = Category::find($id);
        $category->fill($request->json()->all());
        try{
            $category->save();
            RedisCacheHelper::clean(['categories']);
        } catch (QueryException $e) {
            error_log($e);
            return Response::json([
                'msg' => '分类保存失败.',
                'code' => 400,
            ], 400);
        }
        return Response::json([
            'msg' => '分类保存成功.',
            'data' => $category,
            'code' => 201,
        ], 200);
    }

    public function destroy($id)
    {
        try {
            $category = Category::destroy($id);
            RedisCacheHelper::clean(['categories']);
        } catch (QueryException $e) {
            return Response::json([
                'msg' => '分类删除失败.',
                'code' => 400,
            ], 400);
        }
        return Response::json([
            'msg' => '分类删除成功.',
            'data' => $category,
            'code' => 201,
        ], 200);
    }
}
