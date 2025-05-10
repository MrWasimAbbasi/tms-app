<?php

namespace App\Http\Controllers;
/**
 * @OA\Info(
 *      title="TMS API",
 *      version="1.0.0"
 *  )
 *
 * @OA\SecurityScheme(
 *      securityScheme="bearerAuth",
 *      type="http",
 *      scheme="bearer",
 *      bearerFormat="JWT",
 *      description="Bearer token authentication"
 *  )
 *
 * @OA\Parameter(
 *       name="Accept",
 *       in="header",
 *       required=true,
 *       description="Accept header, only application/json is supported",
 *       @OA\Schema(type="string", default="application/json")
 *   )
 */
abstract class Controller
{
    //
}
