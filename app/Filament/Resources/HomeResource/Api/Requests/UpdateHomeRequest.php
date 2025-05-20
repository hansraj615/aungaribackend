<?php

namespace App\Filament\Resources\HomeResource\Api\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateHomeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
			'hero_section' => 'required',
			'about_section' => 'required',
			'dynamic_sections' => 'required',
			'show_read_more' => 'required',
			'read_more_char_limit' => 'required'
		];
    }
}
