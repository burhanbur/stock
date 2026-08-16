<?php

namespace App\Docs\External;

/**
 * @OA\Info(
 *     version="1.0.0",
 *     title="Laravel Starter Kit - External API",
 *     description="API Documentation untuk modul eksternal. Endpoint ini dapat diakses oleh sistem pihak ketiga atau partner eksternal.",
 *     @OA\Contact(
 *         email="burhan.mafazi@universitaspertamina.ac.id"
 *     ),
 * )
 *
 * @OA\Server(
 *     url="http://localhost/laravel-starter-kit/public/api/v1",
 *     description="Local Server"
 * )
 *
 * @OA\Server(
 *     url="https://your-dev-server.com/api/v1",
 *     description="Development Server"
 * )
 *
 * @OA\Server(
 *     url="https://your-staging-server.com/api/v1",
 *     description="Staging Server"
 * )
 *
 * @OA\Server(
 *     url="https://your-production-server.com/api/v1",
 *     description="Production Server"
 * )
 *
 * @OA\SecurityScheme(
 *     securityScheme="ApiKeyAuth",
 *     type="apiKey",
 *     in="header",
 *     name="X-API-KEY",
 *     description="API Key untuk autentikasi. Format: laravel_[48_characters]. Hubungi administrator untuk mendapatkan API key."
 * )
 *
 * @OA\Tag(name="Payment", description="API Endpoints untuk modul Payment")
 */
class ApiInfo
{
}
