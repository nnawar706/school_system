<?php

namespace App\Http\Controllers;

use App\Models\ReaderType;
use Illuminate\Http\Request;

class ReaderTypeController extends Controller
{
    public function index()
    {
        if(ReaderType::count() == 0)
        {
            return response()->json([], 204);
        }
        $type = ReaderType::latest()->get();

        return response()->json([
            'status' => true,
            'data' => $type
        ], 200);
    }
}
