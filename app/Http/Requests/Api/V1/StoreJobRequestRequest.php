<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreJobRequestRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->role === 'customer';
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'title' => trim((string) $this->title),
            'service_type' => trim((string) $this->service_type),
            'address_detail' => trim((string) $this->address_detail),
            'work_time' => trim((string) $this->work_time),
            'description' => trim((string) $this->description),
            'budget' => trim((string) $this->budget),
        ]);
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'min:3', 'max:190'],
            'service_type' => ['required', 'string', 'max:120'],

            'city_id' => [
                'required',
                'integer',
                Rule::exists('bolge_iller', 'id'),
            ],

            'district_id' => [
                'required',
                'integer',
                Rule::exists('bolge_ilceler', 'id')->where(function ($query) {
                    return $query->where('il_id', $this->input('city_id'));
                }),
            ],

            'address_detail' => ['nullable', 'string', 'max:255'],
            'work_date' => ['nullable', 'date'],
            'work_time' => ['nullable', 'string', 'max:50'],
            'description' => ['nullable', 'string', 'max:1500'],
            'budget' => ['nullable', 'string', 'max:100'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Talep başlığı zorunludur.',
            'title.min' => 'Talep başlığı en az 3 karakter olmalıdır.',
            'service_type.required' => 'Hizmet türü seçimi zorunludur.',
            'city_id.required' => 'İl seçimi zorunludur.',
            'city_id.exists' => 'Seçilen il geçerli değildir.',
            'district_id.required' => 'İlçe seçimi zorunludur.',
            'district_id.exists' => 'Seçilen ilçe, seçilen ile ait değildir.',
            'description.max' => 'Açıklama en fazla 1500 karakter olabilir.',
        ];
    }
}