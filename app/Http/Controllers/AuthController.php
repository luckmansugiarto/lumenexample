<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Response as HttpResponse;

class AuthController extends Controller
{
    public function tokenDetails(Request $request): JsonResponse
    {
        return response()->json(
            ['username' => $request->user()->username],
            HttpResponse::HTTP_OK
        );
    }
}
