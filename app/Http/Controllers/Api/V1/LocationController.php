<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\BolgeIl;
use App\Models\BolgeIlce;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;

class LocationController extends Controller
{
    public function cities(): JsonResponse
    {
        $cities = BolgeIl::query()
            ->select('id', 'il_adi')
            ->orderBy('il_adi')
            ->get()
            ->map(fn ($city) => [
                'id' => $city->id,
                'name' => e($city->il_adi),
            ]);

        return response()->json([
            'data' => $cities,
        ]);
    }

    public function districts(int $cityId): JsonResponse
    {
        $districts = BolgeIlce::query()
            ->select('id', 'il_id', 'ilce_adi')
            ->where('il_id', $cityId)
            ->orderBy('ilce_adi')
            ->get()
            ->map(fn ($district) => [
                'id' => $district->id,
                'city_id' => $district->il_id,
                'name' => e($district->ilce_adi),
            ]);

        return response()->json([
            'data' => $districts,
        ]);
    }
}