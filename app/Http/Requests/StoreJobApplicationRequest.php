<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\File;

class StoreJobApplicationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Formulaire public
    }

    public function rules(): array
    {
        return [
            // 👤 Informations personnelles
            'nom'         => ['required', 'string', 'min:2', 'max:100'],
            'prenom'      => ['required', 'string', 'min:2', 'max:100'],
            'email'       => ['required', 'email', 'max:255'],
            'telephone'   => ['required', 'string', 'regex:/^[0-9\s\+\-\(\)]{10,20}$/'],
            'ville'       => ['required', 'string', 'max:100'],

            // 🏫 Poste
            'etablissement' => ['nullable', 'string', 'max:100'],
            'poste_souhaite' => ['required', 'string', 'max:100'],
            'poste_autre' => ['nullable', 'string', 'max:200', 'required_if:poste_souhaite,autre'],

            // 📋 Contrat (au moins un sélectionné)
            'contrat_cdi'           => ['boolean'],
            'contrat_cdd'           => ['boolean'],
            'contrat_temps_plein'   => ['boolean'],
            'contrat_temps_partiel' => ['boolean'],

            // 📁 CV (obligatoire)
            'cv' => [
                'required',
                'file',
                File::types(['pdf', 'doc', 'docx'])->max(5 * 1024), // 5MB
            ],

            // 📁 Lettre de motivation (optionnelle)
            'lettre' => [
                'nullable',
                'file',
                File::types(['pdf', 'doc', 'docx'])->max(5 * 1024),
            ],

            // 📁 Diplômes (optionnels, multiples)
            'diplomes.*' => [
                'nullable',
                'file',
                File::types(['pdf', 'jpg', 'jpeg', 'png', 'doc', 'docx'])->max(10 * 1024), // 10MB each
            ],

            // 🕒 Disponibilité
            'disponibilite' => ['required', 'in:immediate,1mois,rentree,autre'],
            'disponibilite_autre' => [
                'nullable', 'string', 'max:200', 'required_if:disponibilite,autre'
            ],

            // 💬 Message
            'message' => ['nullable', 'string', 'max:2000'],
        ];
    }

    public function messages(): array
    {
        return [
            'nom.required' => 'Le nom est obligatoire',
            'prenom.required' => 'Le prénom est obligatoire',
            'email.required' => 'L\'email est obligatoire',
            'email.email' => 'Veuillez entrer un email valide',
            'telephone.required' => 'Le téléphone est obligatoire',
            'telephone.regex' => 'Numéro de téléphone invalide',
            'ville.required' => 'La ville est obligatoire',
            'poste_souhaite.required' => 'Veuillez sélectionner un poste',
            'poste_autre.required_if' => 'Veuillez préciser le poste souhaité',
            'cv.required' => 'Le CV est obligatoire',
            'cv.file' => 'Le CV doit être un fichier',
            'cv.max' => 'Le CV ne doit pas dépasser 5 Mo',
            'cv.mimes' => 'Format du CV non accepté (PDF, DOC ou DOCX uniquement)',
            'lettre.file' => 'La lettre doit être un fichier valide',
            'lettre.max' => 'La lettre ne doit pas dépasser 5 Mo',
            'diplomes.*.file' => 'Un diplôme doit être un fichier valide',
            'diplomes.*.max' => 'Un diplôme ne doit pas dépasser 10 Mo',
            'disponibilite.required' => 'Veuillez sélectionner votre disponibilité',
            'disponibilite.in' => 'Disponibilité invalide',
            'disponibilite_autre.required_if' => 'Veuillez préciser votre disponibilité',
        ];
    }

    /**
     * Validation personnalisée : au moins un type de contrat sélectionné
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $contrats = [
                $this->input('contrat_cdi'),
                $this->input('contrat_cdd'),
                $this->input('contrat_temps_plein'),
                $this->input('contrat_temps_partiel'),
            ];

            if (!array_filter($contrats)) {
                $validator->errors()->add('contrat_cdi', 'Veuillez sélectionner au moins un type de contrat');
            }
        });
    }

    /**
     * Préparation des données avant validation
     */
    protected function prepareForValidation()
    {
        // Valeurs par défaut
        $this->merge([
            'etablissement' => $this->input('etablissement', "l'Atome-lissasfa"),
        ]);
    }
}