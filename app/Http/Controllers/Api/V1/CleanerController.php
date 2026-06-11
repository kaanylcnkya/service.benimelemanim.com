<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;

class CleanerController extends Controller
{
    
    public function index(Request $request): JsonResponse
    {
        $user = $request->user('sanctum');

        $showContact = $user && in_array($user->role, ['customer', 'admin'], true);

        $perPage = (int) $request->input('per_page', 12);
        $perPage = max(1, min($perPage, 30));

        $query = User::query()
            ->with(['city:id,il_adi', 'district:id,ilce_adi', 'cleanerProfile'])
            ->where('role', 'cleaner')
            ->where('is_active', true)
            ->whereHas('cleanerProfile', function ($query) {
                $query->where('is_visible', true);
            })
            ->latest();

        if ($request->filled('city_id')) {
            $query->where('city_id', $request->integer('city_id'));
        }

        if ($request->filled('district_id')) {
            $query->where('district_id', $request->integer('district_id'));
        }

        if ($request->filled('service_type')) {
            $serviceType = trim((string) $request->input('service_type'));

            $query->whereHas('cleanerProfile', function ($query) use ($serviceType) {
                $query->where(function ($subQuery) use ($serviceType) {
                    $subQuery
                        ->whereJsonContains('services', $serviceType)
                        ->orWhere('services', 'LIKE', '%"' . $serviceType . '"%')
                        ->orWhere('services', 'LIKE', '%' . $serviceType . '%');
                });
            });
        }

        $paginator = $query->paginate($perPage);

        return response()->json([
            'data' => $paginator
                ->getCollection()
                ->map(fn (User $cleaner) => $this->cleanerResponse($cleaner, $showContact))
                ->values(),

            'meta' => [
                'current_page' => $paginator->currentPage(),
                'last_page' => $paginator->lastPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
                'from' => $paginator->firstItem(),
                'to' => $paginator->lastItem(),
            ],
        ]);
    }

    public function show(Request $request, int $id): JsonResponse
    {
        $user = $request->user('sanctum');

        $showContact = $user && in_array($user->role, ['customer', 'admin'], true);

        $cleaner = User::query()
            ->with(['city:id,il_adi', 'district:id,ilce_adi', 'cleanerProfile'])
            ->where('role', 'cleaner')
            ->where('is_active', true)
            ->whereHas('cleanerProfile', function ($query) {
                $query->where('is_visible', true);
            })
            ->findOrFail($id);

        return response()->json([
            'data' => $this->cleanerResponse($cleaner, $showContact),
        ]);
    }

    private function cleanerResponse(User $cleaner, bool $showContact): array
    {
        return [
            'id' => $cleaner->id,
            'name' => e($cleaner->name),

            'phone' => $showContact ? $cleaner->phone : null,
            'email' => $showContact ? $cleaner->email : null,

            'city_id' => $cleaner->city_id,
            'district_id' => $cleaner->district_id,

            'city' => $cleaner->city ? [
                'id' => $cleaner->city->id,
                'name' => e($cleaner->city->il_adi),
            ] : null,

            'district' => $cleaner->district ? [
                'id' => $cleaner->district->id,
                'name' => e($cleaner->district->ilce_adi),
            ] : null,

            'cleaner_profile' => $cleaner->cleanerProfile ? [
                'services' => $cleaner->cleanerProfile->services ?: [],
                'experience' => $cleaner->cleanerProfile->experience ? e($cleaner->cleanerProfile->experience) : null,
                'daily_price' => $cleaner->cleanerProfile->daily_price ? e($cleaner->cleanerProfile->daily_price) : null,
                'description' => $cleaner->cleanerProfile->description ? e($cleaner->cleanerProfile->description) : null,
                'is_verified' => $cleaner->cleanerProfile->is_verified,
                'is_visible' => $cleaner->cleanerProfile->is_visible,
            ] : null,
        ];
    }
}