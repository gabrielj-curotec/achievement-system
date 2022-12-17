<?php

namespace App\Http\Controllers;

use App\Http\Resources\AchievementsIndexResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;

class AchievementsController extends Controller
{
    public function index(User $user): JsonResponse
    {
        return response()->json(new AchievementsIndexResource($user));
    }
}
