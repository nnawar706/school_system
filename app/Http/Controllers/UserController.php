<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\User;

class UserController extends Controller
{
    public function restore($id)
    {
        User::where('id', $id)->withTrashed()->restore();
        return response()->json(['message' => 'User restored successfully']);
    }

    public function restoreAll()
    {
        User::onlyTrashed()->restore();
        return response()->json(['message' => 'All users restored successfully']);
    }

    public function forceDelete($id)
    {
        User::where('id', $id)->withTrashed()->forceDelete();
        return response()->json(['message' => 'User force deleted successfully']);
    }
}
