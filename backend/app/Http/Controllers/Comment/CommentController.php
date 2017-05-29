<?php

namespace App\Http\Controllers\Comment;

use App\Exceptions\PermissionDeniedException;
use App\Models\Comment;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Response;

class CommentController extends Controller
{
    public function index(Request $request)
    {
        $by = $request->get('by') ? $request->get('by') : 'id';
        $desc = $request->get('desc') ? 'desc' : 'asc';
        $skip = $request->get('skip') ? $request->get('skip') : 0;
        $take = $request->get('take') ? $request->get('take') : 10;
        $user = $request->get('user') ? $request->get('user') : 0;
        $result = Comment::with('user', 'good');
        if ($user) {
            $result = $result->where('user_id', $user);
        }
        $total = $result->count();
        $result = $result->orderBy($by, $desc)->skip($skip)->take($take);
        return Response::json([
            'msg'  => '评论获取成功.',
            'data' => ['result' => $result->get(), 'total' => $total],
            'code' => 200,
        ], 200);
    }

    public function store(Request $request)
    {
        $comment = new Comment();
        try {
            $comment->fill($request->json()->all());
            $comment->user_id = $request->user('api')->id;
            $comment->save();
            $comment->user; $comment->good;
        } catch (QueryException $e) {
            return Response::json([
                'msg'  => '评论发表失败.',
                'code' => 400,
            ], 400);
        }
        return Response::json([
            'msg'  => '评论发表成功.',
            'data' => $comment,
            'code' => 201,
        ], 200);
    }

    public function show($id)
    {
        $comment = Comment::with('user', 'good')->find($id);
        return Response::json($comment, 200);
    }

    public function update(Request $request, $id)
    {
        $comment = Comment::find($id);
        $comment->fill($request->json()->all());
        try {
            if ($request->user('api')->id !=
                $comment->user_id) {
                throw new PermissionDeniedException();
            }
            $comment->save();
            $comment->user; $comment->good;
        } catch (PermissionDeniedException $e) {
            return Response::json([
                'msg'  => '操作失败, 权限不足.',
                'code' => 400,
            ], 400);
        } catch (QueryException $e) {
            return Response::json([
                'msg'  => '评论更新失败.',
                'code' => 400,
            ], 400);
        }
        return Response::json([
            'msg'  => '评论更新成功.',
            'data' => $comment,
            'code' => 200,
        ], 200);
    }

    public function destroy(Request $request, $id)
    {
        try {
            $comment = Comment::find($id);
            if ($request->user('api')->id !=
                $comment->user_id) {
                throw new PermissionDeniedException();
            }
            $comment = Comment::destroy($id);
        } catch (PermissionDeniedException $e) {
            return Response::json([
                'msg' => '操作失败, 权限不足.',
                'code' => 400,
            ], 400);
        } catch (QueryException $e) {
            return Response::json([
                'msg' => '评论删除失败.',
                'code' => 400,
            ], 400);
        }
        return Response::json([
            'msg' => '评论删除成功.',
            'data' => $comment,
            'code' => 201,
        ], 200);
    }
}
