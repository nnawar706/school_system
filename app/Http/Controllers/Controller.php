<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * @OA\Info(
 *    title="School Management System API Documentation",
 *    version="1.0.0",
 * )
 */

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    protected function showErrors($error): array
    {
        $data = json_decode($error, true);
        $data = array_values($data);
        $error_data = array_map(function($item) {
            return $item[0];
        }, $data);
        return $error_data;
    }

    public function generateUniqueID($branch, $role)
    {
        $year = date('Y');
        $year_two_digit = substr($year, -2);

        $padded_branch = str_pad($branch, 2, '0', STR_PAD_LEFT);

        $padded_role = str_pad($role, 3, '0', STR_PAD_LEFT);

        $branch_role = $padded_branch . $padded_role;

        $last_user = DB::table('users')
            ->whereRaw("registration_id LIKE '%{$branch_role}%'")
            ->orderBy('id', 'desc')
            ->first();

        $increment = 1;

        if($last_user)
        {
            $last_user_reg_id = $last_user->registration_id;
            $last_increment = Str::after($last_user_reg_id, $padded_role);
            $increment = intval($last_increment) + 1;
        }

        $increment = str_pad($increment, 3, '0', STR_PAD_LEFT);

        return $year_two_digit . $padded_branch . $padded_role . $increment;
    }
}
