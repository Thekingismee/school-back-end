<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreInscriptionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
  



    public function authorize(): bool
{
    return true;
}

public function rules(): array
{
    return [
        'parentNom' => ['required', 'string', 'max:255'],
        'telephone' => ['required', 'string', 'max:20'],
        'email' => ['nullable', 'email'],
        'eleveNom' => ['required', 'string', 'max:255'],
        'dateNaissance' => ['required', 'date'],
        'etablissement' => ['required', 'in:maternelle,primaire,college,lycee'],
        'niveau' => ['required', 'string'],
        'message' => ['nullable', 'string'],
    ];
}

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
  
}
