<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

/**
* @OA\Info(
*    title="Social Media API ",
*    version="1.0",
*    description="Documentación API ",
*   @OA\Contact(
*       name="API Support",
*       email = "tommycontreras34@gmail.com",
*   ),
* )
*/

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;
}
