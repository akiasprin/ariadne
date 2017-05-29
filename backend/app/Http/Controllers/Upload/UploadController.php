<?php

namespace App\Http\Controllers\Upload;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;

class UploadController extends Controller
{
    public function index()
    {

    }

    public function store(Request $request)
    {
        if ($request->hasFile('cover')) {
            $path = $request->cover->store('public');
            return Response::json([
               'url' => url('/').Storage::url($path)
            ]);
        }
    }
}
