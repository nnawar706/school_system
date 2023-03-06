<?php

namespace App\Http\Controllers;

use App\Models\Teacher;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class TeacherController extends Controller
{
    public function index()
    {
        $branch_id = (new AuthController)->getBranch();
    }

    public function create(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'designation_id' => 'required|integer',
            'name' => 'required|string|min:5|max:255',
            'email' => 'required|email|unique:teacher',
            'phone_no' => 'required|unique:teacher',
            'nid_no' => 'required|unique:teacher',
            'religion_id' => 'required|integer',
            'expertise_subject_id' => 'required|integer',
            'dob' => 'required|date',
            'gender_id' => 'required|integer',
            'profile_photo_url' => 'required|image|unique:teacher|mimes:jpeg,png,jpg|max:2048'
        ]);

        if($validate->fails())
        {
            return response()->json([
                'status' => false,
                'error' => $this->showErrors($validate->errors())
            ], 422);
        }

        $branch_id = (new AuthController)->getBranch();

        DB::beginTransaction();

        try
        {
            $reg_id = $this->generateUniqueID($branch_id, 2);

            $user = User::create([
                'role_id' => 2,
                'branch_id' => $branch_id,
                'registration_id' => $reg_id,
                'password' => Hash::make($reg_id),
            ]);

            $uid = $user->id;

            $photo = $request->file('profile_photo_url');
            $photo_file = "teacher_" . time() . rand(10, 100);
            $photo_path = $photo->storeAs('public/images/teacher', $photo_file);
            $photoURL = Storage::url($photo_path);

            $teacher = Teacher::create([
                'user_id' => $uid,
                'designation_id' => $request->designation_id,
                'name' => $request->name,
                'email' => $request->email,
                'phone_no' => $request->phone_no,
                'nid_no' => $request->nid_no,
                'religion_id' => $request->religion_id,
                'expertise_subject_id' => $request->expertise_subject_id,
                'dob' => $request->dob,
                'gender_id' => $request->gender_id,
                'profile_photo_url' => "http://192.168.68.128:8002" . $photoURL,
            ]);

            DB::commit();

            $data['user_id'] = $reg_id;
            $data['password'] = $reg_id;

            return response()->json([
                'data' => $data,
                'status' => true,
            ], 201);
        }
        catch(QueryException $ex)
        {
            DB::rollback();

            return response()->json([
                'status' => false,
            ], 500);
        }
    }
}
