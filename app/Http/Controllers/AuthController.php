<?php
declare(strict_types=1);

namespace App\Http\Controllers;

use App\Domain\Services\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function __construct(
        private readonly AuthService $authService,
    ) {
    }

    public function login(Request $request) : JsonResponse
    {
        $authenticatedUser = $this->authService->login($request);

        return response()->json($authenticatedUser);
    }
}
