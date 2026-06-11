<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class RegisterCleanerRequest extends FormRequest
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
            'experience' => trim((string) $this->experience),
            'daily_price' => trim((string) $this->daily_price),
            'description' => trim((string) $this->description),
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

            'services' => [
                'required',
                'array',
                'min:1',
                'max:10',
            ],

            'services.*' => [
                'required',
                'string',
                'max:100',
            ],

            'experience' => [
                'nullable',
                'string',
                'max:100',
            ],

            'daily_price' => [
                'nullable',
                'string',
                'max:100',
            ],

            'description' => [
                'nullable',
                'string',
                'max:1000',
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

            'services.required' => 'En az bir hizmet türü seçmelisiniz.',
            'services.array' => 'Hizmet türleri geçerli formatta olmalıdır.',
            'services.min' => 'En az bir hizmet türü seçmelisiniz.',
            'services.max' => 'En fazla 10 hizmet türü seçebilirsiniz.',
            'services.*.max' => 'Hizmet türü en fazla 100 karakter olabilir.',

            'experience.max' => 'Deneyim bilgisi en fazla 100 karakter olabilir.',
            'daily_price.max' => 'Günlük ücret bilgisi en fazla 100 karakter olabilir.',
            'description.max' => 'Açıklama en fazla 1000 karakter olabilir.',

            'kvkk_accepted.accepted' => 'KVKK ve kullanım şartlarını kabul etmelisiniz.',
        ];
    }
}