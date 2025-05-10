<?php

/**
 * @OA\SecurityScheme(
 *     type="http",
 *     description="Use a valid bearer token to access the endpoints",
 *     name="Authorization",
 *     in="header",
 *     scheme="bearer",
 *     bearerFormat="JWT",
 *     securityScheme="bearerAuth"
 * )
 */
