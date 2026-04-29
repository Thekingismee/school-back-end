<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nouvelle candidature reçue</title>
</head>
<body style="margin: 0; padding: 0; background-color: #f1f5f9; font-family: 'Segoe UI', Arial, sans-serif;">

    <!-- Wrapper -->
    <table width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color: #f1f5f9; padding: 40px 0;">
        <tr>
            <td align="center">

                <!-- Card -->
                <table width="600" cellpadding="0" cellspacing="0" border="0"
                    style="max-width: 600px; width: 100%; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 24px rgba(0,0,0,0.08);">

                    <!-- Header -->
                    <tr>
                        <td style="background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%); padding: 32px 40px;">
                            <table width="100%" cellpadding="0" cellspacing="0" border="0">
                                <tr>
                                    <td>
                                        <div style="display:inline-block; background: rgba(255,255,255,0.15); border-radius: 8px; padding: 8px 12px; margin-bottom: 12px; font-size: 22px;">
                                            🎯
                                        </div>
                                        <h1 style="margin: 0; color: #ffffff; font-size: 22px; font-weight: 700; letter-spacing: -0.3px;">
                                            Nouvelle candidature reçue
                                        </h1>
                                        <p style="margin: 6px 0 0; color: rgba(255,255,255,0.75); font-size: 13px;">
                                            Reçu le {{ now()->format('d/m/Y à H:i') }}
                                        </p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- Body -->
                    <tr>
                        <td style="background-color: #ffffff; padding: 36px 40px;">

                            <!-- Candidate info -->
                            <table width="100%" cellpadding="0" cellspacing="0" border="0"
                                style="background: #f0f9ff; border: 1px solid #7dd3fc; border-radius: 10px; margin-bottom: 20px;">
                                <tr>
                                    <td style="padding: 20px 24px;">
                                        <p style="margin: 0 0 4px; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; color: #0284c7;">
                                            👤 Candidat(e)
                                        </p>
                                        <p style="margin: 0 0 2px; font-size: 18px; font-weight: 700; color: #1e293b;">
                                            {{ $formData['prenom'] }} {{ $formData['nom'] }}
                                        </p>
                                        <p style="margin: 4px 0 0; font-size: 14px; color: #0ea5e9;">
                                            📧 <a href="mailto:{{ $formData['email'] }}" style="color: #0ea5e9; text-decoration: none;">{{ $formData['email'] }}</a>
                                        </p>
                                        <p style="margin: 4px 0 0; font-size: 14px; color: #64748b;">
                                            📞 {{ $formData['telephone'] }} • 📍 {{ $formData['ville'] }}
                                        </p>
                                    </td>
                                </tr>
                            </table>

                            <!-- Position info -->
                            <table width="100%" cellpadding="0" cellspacing="0" border="0"
                                style="background: #fef3c7; border: 1px solid #fcd34d; border-radius: 10px; margin-bottom: 20px;">
                                <tr>
                                    <td style="padding: 20px 24px;">
                                        <p style="margin: 0 0 4px; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; color: #b45309;">
                                            💼 Poste souhaité
                                        </p>
                                        <p style="margin: 0 0 2px; font-size: 18px; font-weight: 700; color: #1e293b;">
                                            {{ $formData['metadata']['poste_label'] ?? $formData['poste_souhaite'] }}
                                        </p>
                                        @if(!empty($formData['etablissement']))
                                        <p style="margin: 4px 0 0; font-size: 14px; color: #64748b;">
                                            🏫 {{ $formData['etablissement'] }}
                                        </p>
                                        @endif
                                    </td>
                                </tr>
                            </table>

                            <!-- Contract types -->
                            <table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-bottom: 20px;">
                                <tr>
                                    <td>
                                        <p style="margin: 0 0 8px; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; color: #94a3b8;">
                                            📋 Types de contrat souhaités
                                        </p>
                                        <div style="display: flex; flex-wrap: wrap; gap: 8px;">
                                            @if($formData['contrat_cdi'] ?? false)
                                            <span style="display: inline-block; background: #dcfce7; color: #166534; padding: 6px 12px; border-radius: 20px; font-size: 13px; font-weight: 600;">✓ CDI</span>
                                            @endif
                                            @if($formData['contrat_cdd'] ?? false)
                                            <span style="display: inline-block; background: #dbeafe; color: #1e40af; padding: 6px 12px; border-radius: 20px; font-size: 13px; font-weight: 600;">✓ CDD</span>
                                            @endif
                                            @if($formData['contrat_temps_plein'] ?? false)
                                            <span style="display: inline-block; background: #fef3c7; color: #854d0e; padding: 6px 12px; border-radius: 20px; font-size: 13px; font-weight: 600;">✓ Temps plein</span>
                                            @endif
                                            @if($formData['contrat_temps_partiel'] ?? false)
                                            <span style="display: inline-block; background: #f3e8ff; color: #6b21a8; padding: 6px 12px; border-radius: 20px; font-size: 13px; font-weight: 600;">✓ Temps partiel</span>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            </table>

                            <!-- Availability -->
                            <table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-bottom: 20px;">
                                <tr>
                                    <td>
                                        <p style="margin: 0 0 4px; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; color: #94a3b8;">
                                            ⏰ Disponibilité
                                        </p>
                                        <p style="margin: 0; font-size: 15px; font-weight: 600; color: #1e293b; padding: 12px 16px; background: #f8fafc; border-left: 4px solid #0ea5e9; border-radius: 0 6px 6px 0;">
                                            @php
                                                $dispoLabels = [
                                                    'immediate' => 'Immédiate',
                                                    '1mois' => 'Sous 1 mois',
                                                    'rentree' => 'Rentrée scolaire',
                                                    'autre' => $formData['disponibilite_autre'] ?? 'Autre'
                                                ];
                                            @endphp
                                            {{ $dispoLabels[$formData['disponibilite']] ?? $formData['disponibilite'] }}
                                        </p>
                                    </td>
                                </tr>
                            </table>

                            <!-- Message optionnel -->
                            @if(!empty($formData['message']))
                            <table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-bottom: 20px;">
                                <tr>
                                    <td>
                                        <p style="margin: 0 0 8px; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; color: #94a3b8;">
                                            💬 Message de motivation
                                        </p>
                                        <div style="font-size: 15px; line-height: 1.75; color: #334155; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 20px 24px;">
                                            {!! nl2br(e($formData['message'])) !!}
                                        </div>
                                    </td>
                                </tr>
                            </table>
                            @endif

                            <!-- 📁 FICHIERs SECTION -->
                            <table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-top: 28px; border-top: 2px solid #e2e8f0; padding-top: 24px;">
                                <tr>
                                    <td>
                                        <p style="margin: 0 0 16px; font-size: 13px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; color: #0284c7;">
                                            📎 Documents joints
                                        </p>

                                        <!-- CV (obligatoire) -->
                                        <table width="100%" cellpadding="0" cellspacing="0" border="0" 
                                            style="background: #fff7ed; border: 1px solid #fed7aa; border-radius: 10px; margin-bottom: 12px;">
                                            <tr>
                                                <td style="padding: 16px 20px;">
                                                    <table width="100%" cellpadding="0" cellspacing="0" border="0">
                                                        <tr>
                                                            <td style="width: 40px; vertical-align: middle; padding-right: 12px;">
                                                                <div style="background: #f97316; color: white; width: 32px; height: 32px; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 16px;">📄</div>
                                                            </td>
                                                            <td style="vertical-align: middle;">
                                                                <p style="margin: 0 0 2px; font-size: 15px; font-weight: 600; color: #1e293b;">
                                                                    {{ $formData['cv_original_name'] }}
                                                                </p>
                                                                <p style="margin: 0; font-size: 12px; color: #64748b;">
                                                                    CV • {{ round($formData['cv_size'] / 1024, 1) }} Ko
                                                                </p>
                                                            </td>
                                                            <td style="vertical-align: middle; text-align: right;">
                                                                <a href="{{ Storage::url($formData['cv_path']) }}" 
                                                                   target="_blank"
                                                                   style="display: inline-block; background: #f97316; color: white; text-decoration: none; padding: 8px 16px; border-radius: 6px; font-size: 13px; font-weight: 600;">
                                                                    👁️ Voir
                                                                </a>
                                                            </td>
                                                        </tr>
                                                    </table>
                                                </td>
                                            </tr>
                                        </table>

                                        <!-- Lettre (optionnelle) -->
                                        @if(!empty($formData['lettre_path']))
                                        <table width="100%" cellpadding="0" cellspacing="0" border="0" 
                                            style="background: #f0f9ff; border: 1px solid #bae6fd; border-radius: 10px; margin-bottom: 12px;">
                                            <tr>
                                                <td style="padding: 16px 20px;">
                                                    <table width="100%" cellpadding="0" cellspacing="0" border="0">
                                                        <tr>
                                                            <td style="width: 40px; vertical-align: middle; padding-right: 12px;">
                                                                <div style="background: #0ea5e9; color: white; width: 32px; height: 32px; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 16px;">✉️</div>
                                                            </td>
                                                            <td style="vertical-align: middle;">
                                                                <p style="margin: 0 0 2px; font-size: 15px; font-weight: 600; color: #1e293b;">
                                                                    {{ $formData['lettre_original_name'] }}
                                                                </p>
                                                                <p style="margin: 0; font-size: 12px; color: #64748b;">
                                                                    Lettre de motivation • {{ round($formData['lettre_size'] / 1024, 1) }} Ko
                                                                </p>
                                                            </td>
                                                            <td style="vertical-align: middle; text-align: right;">
                                                                <a href="{{ Storage::url($formData['lettre_path']) }}" 
                                                                   target="_blank"
                                                                   style="display: inline-block; background: #0ea5e9; color: white; text-decoration: none; padding: 8px 16px; border-radius: 6px; font-size: 13px; font-weight: 600;">
                                                                    👁️ Voir
                                                                </a>
                                                            </td>
                                                        </tr>
                                                    </table>
                                                </td>
                                            </tr>
                                        </table>
                                        @endif

                                        <!-- Diplômes (multiples) -->
                                        @if(!empty($formData['diplomes']) && is_array($formData['diplomes']))
                                            @foreach($formData['diplomes'] as $index => $diplome)
                                            <table width="100%" cellpadding="0" cellspacing="0" border="0" 
                                                style="background: #fafafa; border: 1px solid #e2e8f0; border-radius: 10px; margin-bottom: 8px;">
                                                <tr>
                                                    <td style="padding: 12px 20px;">
                                                        <table width="100%" cellpadding="0" cellspacing="0" border="0">
                                                            <tr>
                                                                <td style="width: 40px; vertical-align: middle; padding-right: 12px;">
                                                                    <div style="background: #64748b; color: white; width: 28px; height: 28px; border-radius: 6px; display: flex; align-items: center; justify-content: center; font-size: 14px;">🎓</div>
                                                                </td>
                                                                <td style="vertical-align: middle;">
                                                                    <p style="margin: 0 0 2px; font-size: 14px; font-weight: 600; color: #1e293b;">
                                                                        {{ $diplome['name'] }}
                                                                    </p>
                                                                    <p style="margin: 0; font-size: 11px; color: #94a3b8;">
                                                                        Diplôme #{{ $index + 1 }} • {{ round($diplome['size'] / 1024, 1) }} Ko
                                                                    </p>
                                                                </td>
                                                                <td style="vertical-align: middle; text-align: right;">
                                                                    <a href="{{ Storage::url($diplome['path']) }}" 
                                                                       target="_blank"
                                                                       style="display: inline-block; background: #64748b; color: white; text-decoration: none; padding: 6px 12px; border-radius: 6px; font-size: 12px; font-weight: 600;">
                                                                        👁️
                                                                    </a>
                                                                </td>
                                                            </tr>
                                                        </table>
                                                    </td>
                                                </tr>
                                            </table>
                                            @endforeach
                                        @endif

                                    </td>
                                </tr>
                            </table>

                            <!-- Meta info -->
                            <table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-top: 28px; border-top: 1px dashed #e2e8f0; padding-top: 20px;">
                                <tr>
                                    <td>
                                        <p style="margin: 0 0 4px; font-size: 12px; color: #94a3b8;">
                                            <strong>🌐 Source :</strong> {{ $formData['source'] }} | 
                                            <strong>IP :</strong> {{ $formData['ip_address'] }}
                                        </p>
                                        <p style="margin: 0; font-size: 12px; color: #94a3b8;">
                                            <strong>📱 Plateforme :</strong> {{ $formData['metadata']['platform'] ?? 'web' }} | 
                                            <strong>Statut :</strong> <span style="color: #0ea5e9; font-weight: 600;">{{ $formData['statut'] }}</span>
                                        </p>
                                    </td>
                                </tr>
                            </table>

                            <!-- Action buttons -->
                            <table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-top: 28px;">
                                <tr>
                                    <td align="center" style="padding: 0 8px;">
                                        <a href="mailto:{{ $formData['email'] }}?subject=Votre candidature - {{ rawurlencode($formData['metadata']['poste_label'] ?? $formData['poste_souhaite']) }}"
                                           style="display: inline-block; background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%); color: #ffffff; font-size: 14px; font-weight: 600; text-decoration: none; padding: 12px 24px; border-radius: 8px; letter-spacing: 0.2px; margin: 4px;">
                                            ✉️ Contacter le candidat
                                        </a>
                                        <a href="tel:{{ preg_replace('/[^0-9+]/', '', $formData['telephone']) }}"
                                           style="display: inline-block; background: #ffffff; color: #0284c7; font-size: 14px; font-weight: 600; text-decoration: none; padding: 12px 24px; border-radius: 8px; letter-spacing: 0.2px; border: 2px solid #0284c7; margin: 4px;">
                                            📞 Appeler
                                        </a>
                                    </td>
                                </tr>
                            </table>

                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="background-color: #f8fafc; border-top: 1px solid #e2e8f0; padding: 20px 40px; text-align: center;">
                            <p style="margin: 0; font-size: 12px; color: #94a3b8; line-height: 1.6;">
                                Cette candidature a été soumise via le formulaire de recrutement de votre site web.<br>
                                Les documents sont accessibles via les liens ci-dessus (hébergement public).
                            </p>
                            <p style="margin: 8px 0 0; font-size: 11px; color: #cbd5e1;">
                                ⚠️ Pensez à sauvegarder les fichiers importants localement.
                            </p>
                        </td>
                    </tr>

                </table>

            </td>
        </tr>
    </table>

</body>
</html>