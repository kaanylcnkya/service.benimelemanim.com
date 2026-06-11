<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\User;
use App\Models\CleanerProfile;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
use Illuminate\Validation\ValidationException;
use App\Http\Requests\Api\V1\LoginRequest;
use App\Http\Requests\Api\V1\RegisterCleanerRequest;
use App\Http\Requests\Api\V1\RegisterCustomerRequest;

class AuthController extends Controller
{
    public function registerCustomer(RegisterCustomerRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $user = DB::transaction(function () use ($validated) {
            return User::create([
                'role' => 'customer',
                'name' => $validated['name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'],
                'city_id' => $validated['city_id'],
                'district_id' => $validated['district_id'],
                'password' => $validated['password'],
                'is_active' => true,
                'kvkk_accepted_at' => now(),
                'terms_accepted_at' => now(),
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);
        });

        $token = $user->createToken('customer-token')->plainTextToken;

        return response()->json([
            'message' => 'Kullanıcı kaydı başarıyla oluşturuldu.',
            'token' => $token,
            'user' => $this->userResponse($user->load(['city', 'district'])),
        ], 201);
    }

    public function registerCleaner(RegisterCleanerRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $user = DB::transaction(function () use ($validated) {
            $user = User::create([
                'role' => 'cleaner',
                'name' => $validated['name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'],
                'city_id' => $validated['city_id'],
                'district_id' => $validated['district_id'],
                'password' => $validated['password'],
                'is_active' => true,
            ]);

            CleanerProfile::create([
                'user_id' => $user->id,
                'services' => $validated['services'],
                'experience' => $validated['experience'] ?? null,
                'daily_price' => $validated['daily_price'] ?? null,
                'description' => $validated['description'] ?? null,
                'is_verified' => false,
                'is_visible' => true,
            ]);

            return $user->load(['city', 'district', 'cleanerProfile']);
        });

        $token = $user->createToken('cleaner-token')->plainTextToken;

        return response()->json([
            'message' => 'Temizlikçi kaydı başarıyla oluşturuldu.',
            'token' => $token,
            'user' => $this->userResponse($user),
        ], 201);
    }

    public function login(LoginRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $login = $validated['login'];

        $user = User::query()
            ->where('email', $login)
            ->orWhere('phone', $login)
            ->first();

        if (! $user || ! Hash::check($validated['password'], $user->password)) {
            throw ValidationException::withMessages([
                'login' => ['Giriş bilgileri hatalı.'],
            ]);
        }

        if (! $user->is_active) {
            throw ValidationException::withMessages([
                'login' => ['Hesabınız pasif durumdadır.'],
            ]);
        }

        $user->forceFill([
            'last_login_at' => now(),
        ])->save();

        $user->tokens()->delete();

        $token = $user->createToken($user->role . '-token')->plainTextToken;

        return response()->json([
            'message' => 'Giriş başarılı.',
            'token' => $token,
            'user' => $this->userResponse($user->load(['city', 'district', 'cleanerProfile'])),
        ]);
    }

    public function me(): JsonResponse
    {
        $user = request()->user()->load(['city', 'district', 'cleanerProfile']);

        return response()->json([
            'user' => $this->userResponse($user),
        ]);
    }

    public function logout(): JsonResponse
    {
        request()->user()?->currentAccessToken()?->delete();

        return response()->json([
            'message' => 'Çıkış yapıldı.',
        ]);
    }

    private function userResponse(User $user): array
    {
        return [
            'id' => $user->id,
            'role' => $user->role,
            'name' => e($user->name),
            'email' => $user->email,
            'phone' => $user->phone,

            'city_id' => $user->city_id,
            'district_id' => $user->district_id,

            'city' => $user->city ? [
                'id' => $user->city->id,
                'name' => e($user->city->il_adi),
            ] : null,

            'district' => $user->district ? [
                'id' => $user->district->id,
                'name' => e($user->district->ilce_adi),
            ] : null,

            'is_active' => $user->is_active,

            'cleaner_profile' => $user->cleanerProfile ? [
                'services' => $user->cleanerProfile->services,
                'experience' => e($user->cleanerProfile->experience),
                'daily_price' => e($user->cleanerProfile->daily_price),
                'description' => e($user->cleanerProfile->description),
                'is_verified' => $user->cleanerProfile->is_verified,
                'is_visible' => $user->cleanerProfile->is_visible,
            ] : null,
        ];
    }
}