<?php

namespace App\Docs\Internal;

/**
 * @OA\Info(
 *     version="1.0.0",
 *     title="Laravel Starter Kit - Internal API",
 *     description="API Documentation untuk modul internal. Endpoint ini hanya untuk konsumsi aplikasi internal dan tidak boleh diakses secara publik.",
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
 * @OA\Tag(name="Approval - Workflow Definitions", description="CRUD workflow definitions")
 * @OA\Tag(name="Approval - Workflow Approvals", description="CRUD workflow approvals (versi aktif)")
 * @OA\Tag(name="Approval - Approval Statuses", description="CRUD status approval per workflow")
 * @OA\Tag(name="Approval - Approver Types", description="CRUD tipe approver (User, Position, dsb)")
 * @OA\Tag(name="Approval - Stages", description="CRUD stage dalam workflow approval")
 * @OA\Tag(name="Approval - Workflow Approvers", description="CRUD konfigurasi approver per stage")
 * @OA\Tag(name="Approval - Delegated Approvers", description="CRUD delegasi approver")
 * @OA\Tag(name="Approval - Workflow Requests", description="Submit dan pantau workflow request")
 * @OA\Tag(name="Approval - Actions", description="Aksi approve/reject oleh approver")
 * @OA\Tag(name="Approval - History", description="Riwayat aksi approval (read-only)")
 */
class ApiInfo
{
}
