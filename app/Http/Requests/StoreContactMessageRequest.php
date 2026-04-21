<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreContactMessageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // ✅ Autoriser tout le monde (formulaire public)
    }

    public function rules(): array
    {
        return [
            'nom'     => ['required', 'string', 'min:2', 'max:255'],
            'email'   => ['required', 'email', 'max:255'],
            'telephone' => ['nullable', 'string', 'max:20'],
            'sujet'   => ['required', 'in:admission,information,visite,autre'],
            'message' => ['required', 'string', 'min:10', 'max:2000'],
        ];
    }

    public function messages(): array
    {
        return [
            'nom.required'     => 'Le nom est obligatoire',
            'nom.min'          => 'Le nom doit contenir au moins 2 caractères',
            'email.required'   => 'L\'email est obligatoire',
            'email.email'      => 'Veuillez entrer un email valide',
            'sujet.required'   => 'Veuillez choisir un sujet',
            'sujet.in'         => 'Sujet invalide',
            'message.required' => 'Le message est obligatoire',
            'message.min'      => 'Votre message est trop court (10 caractères minimum)',
            'message.max'      => 'Votre message est trop long (2000 caractères maximum)',
        ];
    }
}