<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Services\User\ContentService;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;

class ContentController extends Controller
{
    public function __construct(private readonly ContentService $contentService) {}

    public function termsAndConditions(): JsonResponse
    {
        return ApiResponse::success(
            __('api.content.loaded'),
            $this->contentService->termsAndConditions(),
        );
    }

    public function privacyPolicy(): JsonResponse
    {
        return ApiResponse::success(
            __('api.content.loaded'),
            $this->contentService->privacyPolicy(),
        );
    }

    public function aboutUs(): JsonResponse
    {
        return ApiResponse::success(
            __('api.content.loaded'),
            $this->contentService->aboutUs(),
        );
    }
}
