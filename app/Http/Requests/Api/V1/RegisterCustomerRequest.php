<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class RegisterCustomerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'name' => trim((string) $this->name),
            'email' => strtolower(trim((string) $this->email)),
            'phone' => preg_replace('/\s+/', '', (string) $this->phone),
        ]);
    }

    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'min:2',
                'max:120',
            ],

            'email' => [
                'required',
                'email:rfc',
                'max:190',
                'unique:users,email',
            ],

            'phone' => [
                'required',
                'string',
                'min:10',
                'max:30',
                'unique:users,phone',
            ],

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

            'password' => [
                'required',
                'confirmed',
                Password::min(8)->letters()->numbers(),
            ],

            'kvkk_accepted' => [
                'accepted',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Ad soyad alanı zorunludur.',
            'name.min' => 'Ad soyad en az 2 karakter olmalıdır.',
            'name.max' => 'Ad soyad en fazla 120 karakter olabilir.',

            'email.required' => 'E-posta alanı zorunludur.',
            'email.email' => 'Geçerli bir e-posta adresi giriniz.',
            'email.unique' => 'Bu e-posta adresi ile daha önce kayıt olunmuş.',

            'phone.required' => 'Telefon alanı zorunludur.',
            'phone.min' => 'Telefon numarası en az 10 karakter olmalıdır.',
            'phone.max' => 'Telefon numarası en fazla 30 karakter olabilir.',
            'phone.unique' => 'Bu telefon numarası ile daha önce kayıt olunmuş.',

            'city_id.required' => 'İl seçimi zorunludur.',
            'city_id.exists' => 'Seçilen il geçerli değildir.',

            'district_id.required' => 'İlçe seçimi zorunludur.',
            'district_id.exists' => 'Seçilen ilçe, seçilen ile ait değildir.',

            'password.required' => 'Şifre alanı zorunludur.',
            'password.confirmed' => 'Şifre tekrarı eşleşmiyor.',

            'kvkk_accepted.accepted' => 'KVKK ve kullanım şartlarını kabul etmelisiniz.',
        ];
    }
}