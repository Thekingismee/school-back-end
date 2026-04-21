<?php

namespace App\Http\Requests;

use App\Models\Appointment;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreAppointmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nom'         => ['required', 'string', 'min:2', 'max:255'],
            'email'       => ['required', 'email', 'max:255'],
            'telephone'   => ['required', 'string', 'regex:/^[0-9\s\+\-\(\)]{10,20}$/'],
            'invites'     => ['nullable', 'string', 'max:500'],
            'message'     => ['nullable', 'string', 'max:1000'],
            
            'date_rdv'    => ['required', 'date', 'after_or_equal:today'],
            'heure_rdv'   => [
                'required',
                'date_format:H:i',
                Rule::in([
                    '09:00', '09:15', '09:30', '09:45',
                    '10:00', '10:15', '10:30', '10:45',
                    '11:00', '11:15', '11:30', '11:45',
                    '14:00', '14:15', '14:30', '14:45',
                    '15:00', '15:15', '15:30', '15:45',
                    '16:00', '16:15', '16:30', '16:45',
                ])
            ],
            'lieu'        => ['nullable', 'string', 'max:100'], // ✅ Sans "default:"
            'duree_minutes' => ['nullable', 'integer', Rule::in([15, 30, 45, 60])], // ✅ Sans "default:"
        ];
    }

    public function messages(): array
    {
        return [
            'nom.required' => 'Le nom est obligatoire',
            'nom.min' => 'Le nom doit contenir au moins 2 caractères',
            'email.required' => 'L\'email est obligatoire',
            'email.email' => 'Veuillez entrer un email valide',
            'telephone.required' => 'Le téléphone est obligatoire',
            'telephone.regex' => 'Numéro de téléphone invalide',
            'date_rdv.required' => 'La date est obligatoire',
            'date_rdv.after_or_equal' => 'La date doit être aujourd\'hui ou dans le futur',
            'heure_rdv.required' => 'L\'heure est obligatoire',
            'heure_rdv.date_format' => 'Format d\'heure invalide',
            'heure_rdv.in' => 'Ce créneau horaire n\'est pas disponible',
        ];
    }

    /**
     * Validation personnalisée : vérifier la disponibilité du créneau
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            // Seulement si date et heure sont valides
            if ($validator->errors()->isNotEmpty()) {
                return;
            }

            $date = $this->input('date_rdv');
            $time = $this->input('heure_rdv');
            $lieu = $this->input('lieu', 'Lissasfa');

            if ($date && $time && !Appointment::isSlotAvailable($date, $time, $lieu)) {
                $validator->errors()->add('heure_rdv', 'Ce créneau n\'est plus disponible. Veuillez en choisir un autre.');
            }
        });
    }

    /**
     * Préparer les données avec valeurs par défaut AVANT validation
     */
    protected function prepareForValidation()
    {
        $this->merge([
            'lieu' => $this->input('lieu', 'Lissasfa'),
            'duree_minutes' => $this->input('duree_minutes', 15),
        ]);
    }
}