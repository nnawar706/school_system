<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\QueryException;

class BranchController extends Controller
{
    public function index()
    {
        if(Branch::count() == 0)
        {
            return response()->json([], 204);
        }

        $branch = Branch::latest()->get();

        return response()->json([
            'status' => true,
            'data' => $branch], 200);
    }

    public function create(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'name' => 'required|max:50|min:5|unique:branch|alpha_dash',
            'location' => 'required|max:255'
        ]);

        if($validate->fails())
        {
            return response()->json([
                'status' => false,
                'error' => $this->showErrors($validate->errors())], 422);
        }
        try
        {
            $branch = Branch::create([
                'name' => $request->name,
                'location' => $request->location
            ]);

            return response()->json([
                'status' => true,
                'data' => $branch
            ], 201);
        }
        catch (QueryException $ex)
        {
            return response()->json([
                'status' => false], 500);
        }
    }

    public function read($id)
    {
        $branch = Branch::find($id);

        if(is_null($branch))
        {
            return response()->json([], 204);
        }

        return response()->json([
            'status' => true,
            'data' => $branch
        ]);
    }

    public function update(Request $request, $id)
    {
        $branch = Branch::findOrFail($id);

        $validate = Validator::make($request->all(), [
            'name' => 'required|max:50|min:5|alpha_dash',
            'location' => 'required|max:255'
        ]);

        if($validate->fails())
        {
            return response()->json([
                'status' => false,
                'error' => $this->showErrors($validate->errors())], 422);
        }
        try {
            $branch->update([
                'name' => $request->name,
                'location' => $request->location
            ]);

            return response()->json([
                'status' => true,
                'data' => $branch
            ], 200);
        }
        catch(QueryException $ex)
        {
            return response()->json([
                'status' => false], 304);
        }
    }

    public function delete($id)
    {
        $branch = Branch::findOrFail($id);
        try
        {
            $branch->delete();

            return response()->json([
                'status' => true], 200);
        }
        catch(QueryException $ex)
        {
            return response()->json([
                'status' => false
            ], 304);
        }
    }

    public function restore($id)
    {
        Branch::where('id', $id)->withTrashed()->restore();

        return response()->json([
            'status' => true], 200);
    }

    public function restoreAll()
    {
        Branch::onlyTrashed()->restore();

        return response()->json([
            'status' => true], 200);
    }

    public function forceDelete($id)
    {
        Branch::where('id', $id)->withTrashed()->forceDelete();

        return response()->json([
            'status' => true], 200);
    }

}
