<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nouveau rendez-vous réservé</title>
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
                        <td style="background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%); padding: 32px 40px;">
                            <table width="100%" cellpadding="0" cellspacing="0" border="0">
                                <tr>
                                    <td>
                                        <div style="display:inline-block; background: rgba(255,255,255,0.15); border-radius: 8px; padding: 8px 12px; margin-bottom: 12px; font-size: 22px;">
                                            📅
                                        </div>
                                        <h1 style="margin: 0; color: #ffffff; font-size: 22px; font-weight: 700; letter-spacing: -0.3px;">
                                            Nouveau rendez-vous réservé
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

                            <!-- Client info -->
                            <table width="100%" cellpadding="0" cellspacing="0" border="0"
                                style="background: #f5f3ff; border: 1px solid #ddd6fe; border-radius: 10px; margin-bottom: 20px;">
                                <tr>
                                    <td style="padding: 20px 24px;">
                                        <p style="margin: 0 0 4px; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; color: #7c3aed;">
                                            👤 Client
                                        </p>
                                        <p style="margin: 0 0 2px; font-size: 18px; font-weight: 700; color: #1e293b;">
                                            {{ $formData['nom'] }}
                                        </p>
                                        <p style="margin: 4px 0 0; font-size: 14px; color: #8b5cf6;">
                                            📧 <a href="mailto:{{ $formData['email'] }}" style="color: #8b5cf6; text-decoration: none;">{{ $formData['email'] }}</a>
                                        </p>
                                        <p style="margin: 4px 0 0; font-size: 14px; color: #64748b;">
                                            📞 {{ $formData['telephone'] }}
                                        </p>
                                    </td>
                                </tr>
                            </table>

                            <!-- Appointment details -->
                            <table width="100%" cellpadding="0" cellspacing="0" border="0"
                                style="background: #ecfdf5; border: 1px solid #6ee7b7; border-radius: 10px; margin-bottom: 20px;">
                                <tr>
                                    <td style="padding: 20px 24px;">
                                        <p style="margin: 0 0 4px; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; color: #059669;">
                                            📋 Détails du rendez-vous
                                        </p>
                                        <table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-top: 8px;">
                                            <tr>
                                                <td style="padding: 4px 0; font-size: 15px; color: #1e293b;">
                                                    <strong>🗓️ Date :</strong> {{ \Carbon\Carbon::parse($formData['date_rdv'])->format('d/m/Y') }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="padding: 4px 0; font-size: 15px; color: #1e293b;">
                                                    <strong>⏰ Heure :</strong> {{ $formData['heure_rdv'] }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="padding: 4px 0; font-size: 15px; color: #1e293b;">
                                                    <strong>⏱️ Durée :</strong> {{ $formData['duree_minutes'] }} minutes
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="padding: 4px 0; font-size: 15px; color: #1e293b;">
                                                    <strong>📍 Lieu :</strong> {{ $formData['lieu'] }}
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>

                            <!-- Invites optionnel -->
                            @if(!empty($formData['invites']))
                            <table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-bottom: 20px;">
                                <tr>
                                    <td>
                                        <p style="margin: 0 0 4px; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; color: #94a3b8;">
                                            👥 Invités / Participants
                                        </p>
                                        <div style="font-size: 15px; line-height: 1.6; color: #334155; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 16px 20px;">
                                            {{ $formData['invites'] }}
                                        </div>
                                    </td>
                                </tr>
                            </table>
                            @endif

                            <!-- Message optionnel -->
                            @if(!empty($formData['message']))
                            <table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-bottom: 20px;">
                                <tr>
                                    <td>
                                        <p style="margin: 0 0 8px; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; color: #94a3b8;">
                                            💬 Message du client
                                        </p>
                                        <div style="font-size: 15px; line-height: 1.75; color: #334155; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 20px 24px;">
                                            {!! nl2br(e($formData['message'])) !!}
                                        </div>
                                    </td>
                                </tr>
                            </table>
                            @endif

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
                                            <strong>Statut :</strong> <span style="color: #f97316; font-weight: 600;">{{ $formData['statut'] }}</span>
                                        </p>
                                    </td>
                                </tr>
                            </table>

                            <!-- Action buttons -->
                            <table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-top: 28px;">
                                <tr>
                                    <td align="center" style="padding: 0 8px;">
                                        <a href="mailto:{{ $formData['email'] }}?subject=Confirmation RDV {{ rawurlencode($formData['date_rdv']) }}"
                                           style="display: inline-block; background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%); color: #ffffff; font-size: 14px; font-weight: 600; text-decoration: none; padding: 12px 24px; border-radius: 8px; letter-spacing: 0.2px; margin: 4px;">
                                            ✉️ Contacter le client
                                        </a>
                                        <a href="https://calendar.google.com/calendar/render?action=TEMPLATE&text={{ rawurlencode('RDV - ' . $formData['nom']) }}&dates={{ \Carbon\Carbon::parse($formData['date_rdv'])->format('Ymd\THis') }}/{{ \Carbon\Carbon::parse($formData['date_rdv'])->addMinutes($formData['duree_minutes'])->format('Ymd\THis') }}&details={{ rawurlencode($formData['message'] ?? '') }}&location={{ rawurlencode($formData['lieu']) }}"
                                           target="_blank"
                                           style="display: inline-block; background: #ffffff; color: #7c3aed; font-size: 14px; font-weight: 600; text-decoration: none; padding: 12px 24px; border-radius: 8px; letter-spacing: 0.2px; border: 2px solid #7c3aed; margin: 4px;">
                                            📅 Ajouter à Google Calendar
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
                                Ce rendez-vous a été réservé via le formulaire de votre site web.<br>
                                Pensez à confirmer la disponibilité dans les plus brefs délais.
                            </p>
                        </td>
                    </tr>

                </table>

            </td>
        </tr>
    </table>

</body>
</html>