<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

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
}
