<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UploadController extends Controller
{
    //

    public function upload(Request $request)
{
    $path = $request->file('image')->store('newsletter', 'public');

    return response()->json([
        'url' => asset('storage/' . $path)
    ]);
}
}
