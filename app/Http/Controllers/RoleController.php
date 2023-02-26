<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RoleController extends Controller
{
    public function index()
    {
        $role = Role::latest()->get();
        if(is_null($role)) {
            return response()->json([], 204);
        }
        return response()->json([
            'status' => true,
            'data' => $role], 200);
    }

    public function create(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'name' => 'required|max:50|min:5|unique:role|alpha_dash'
        ]);
        if($validate->fails()) {
            $error_list = $this->showErrors($validate->errors());
            return response()->json([
                'error' => $error_list], 422);
        }
        try {
            $role = Role::create([
                'name' => $request->name
            ]);
            return response()->json([
                'status' => true,
                'data' => $role
            ], 201);
        } catch (QueryException $ex) {
            return response()->json([
                'status' => false], 500);
        }
    }

    public function read($id)
    {
        $role = Role::find($id);
        if(is_null($role)) {
            return response()->json([], 204);
        }
        return response()->json([
            'status' => true,
            'data' => $role
        ]);
    }

    public function update(Request $request, $id)
    {
        $role = Role::findOrFail($id);

        $validate = Validator::make($request->all(), [
            'name' => 'required|max:50|min:5|alpha_dash'
        ]);
        if($validate->fails()) {
            $error_list = $this->showErrors($validate->errors());
            return response()->json([
                'error' => $error_list], 422);
        }
        try {
            $updated_role = $role->update([
                'name' => $request->name
            ]);
            return response()->json([
                'status' => true,
                'data' => $role
            ], 200);
        } catch(QueryException $ex) {
            return response()->json([
                'status' => false], 304);
        }
    }

    public function delete($id)
    {
        $role = Role::find($id);
        if(is_null($role)) {
            return response()->json([], 204);
        }
        $deleted_role = $role->delete();
        if(!$deleted_role) {
            return response()->json([
                'status' => false
            ], 304);
        }
        return response()->json([
            'status' => true
        ], 200);
    }

    public function restore($id)
    {
        Role::where('id', $id)->withTrashed()->restore();
        return response()->json([
            'status' => true], 200);
    }

    public function restoreAll()
    {
        Role::onlyTrashed()->restore();
        return response()->json([
            'status' => true], 200);
    }

    public function forceDelete($id)
    {
        Role::where('id', $id)->withTrashed()->forceDelete();
        return response()->json([
            'status' => true], 200);
    }
}
