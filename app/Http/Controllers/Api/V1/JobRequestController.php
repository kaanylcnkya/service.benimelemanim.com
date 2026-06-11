<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\JobRequest;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\StoreJobRequestRequest;

class JobRequestController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        if (! in_array($user->role, ['cleaner', 'admin'], true)) {
            return response()->json([
                'message' => 'Bu alanı görüntüleme yetkiniz bulunmamaktadır.',
            ], 403);
        }

        $query = JobRequest::query()
            ->with(['user:id,name,phone,email', 'city:id,il_adi', 'district:id,ilce_adi'])
            ->where('status', 'open')
            ->latest();

        if ($user->role === 'cleaner') {
            $query->where('city_id', $user->city_id);
        }

        if ($request->filled('city_id')) {
            $query->where('city_id', $request->integer('city_id'));
        }

        if ($request->filled('district_id')) {
            $query->where('district_id', $request->integer('district_id'));
        }

        $jobRequests = $query
            ->limit(50)
            ->get()
            ->map(fn (JobRequest $jobRequest) => $this->jobRequestResponse($jobRequest, true));

        return response()->json([
            'data' => $jobRequests,
        ]);
    }

    public function myRequests(Request $request): JsonResponse
    {
        $user = $request->user();

        if ($user->role !== 'customer') {
            return response()->json([
                'message' => 'Bu alan sadece temizlikçi arayan kullanıcılar içindir.',
            ], 403);
        }

        $jobRequests = JobRequest::query()
            ->with(['city:id,il_adi', 'district:id,ilce_adi'])
            ->where('user_id', $user->id)
            ->latest()
            ->get()
            ->map(fn (JobRequest $jobRequest) => $this->jobRequestResponse($jobRequest, false));

        return response()->json([
            'data' => $jobRequests,
        ]);
    }

    public function store(StoreJobRequestRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $jobRequest = JobRequest::create([
            'user_id' => $request->user()->id,
            'title' => $validated['title'],
            'service_type' => $validated['service_type'],
            'city_id' => $validated['city_id'],
            'district_id' => $validated['district_id'],
            'address_detail' => $validated['address_detail'] ?? null,
            'work_date' => $validated['work_date'] ?? null,
            'work_time' => $validated['work_time'] ?? null,
            'description' => $validated['description'] ?? null,
            'budget' => $validated['budget'] ?? null,
            'status' => 'open',
        ]);

        return response()->json([
            'message' => 'Talebiniz başarıyla oluşturuldu.',
            'data' => $this->jobRequestResponse(
                $jobRequest->load(['city', 'district']),
                false
            ),
        ], 201);
    }

    private function jobRequestResponse(JobRequest $jobRequest, bool $showContact): array
    {
        return [
            'id' => $jobRequest->id,
            'title' => e($jobRequest->title),
            'service_type' => e($jobRequest->service_type),

            'city_id' => $jobRequest->city_id,
            'district_id' => $jobRequest->district_id,

            'city' => $jobRequest->city ? [
                'id' => $jobRequest->city->id,
                'name' => e($jobRequest->city->il_adi),
            ] : null,

            'district' => $jobRequest->district ? [
                'id' => $jobRequest->district->id,
                'name' => e($jobRequest->district->ilce_adi),
            ] : null,

            'address_detail' => e($jobRequest->address_detail),
            'work_date' => $jobRequest->work_date?->format('Y-m-d'),
            'work_time' => e($jobRequest->work_time),
            'description' => e($jobRequest->description),
            'budget' => e($jobRequest->budget),
            'status' => $jobRequest->status,

            'customer' => $showContact && $jobRequest->user ? [
                'id' => $jobRequest->user->id,
                'name' => e($jobRequest->user->name),
                'phone' => $jobRequest->user->phone,
                'email' => $jobRequest->user->email,
            ] : null,

            'created_at' => $jobRequest->created_at?->format('Y-m-d H:i:s'),
        ];
    }
}