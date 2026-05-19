<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Carbon;
use Override;

class NewEntryRequest extends FormRequest
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
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:150'],
            'notes' => ['string', 'max:1500', 'nullable'],
            'completed' => ['string', 'in:on']
        ];
    }

    /**
     *
     */
    public function passedValidation()
    {
        $this->merge(['created_at' => Carbon::parse($this->day)]);
        if ($this->has('completed')) {
            $this->merge(['completed' => true, 'completed_on' => Carbon::parse($this->day)]);
        }
        return $this;
    }
}
