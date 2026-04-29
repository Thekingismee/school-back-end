<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nouvelle inscription</title>
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
                        <td style="background: linear-gradient(135deg, #f97316 0%, #ea580c 100%); padding: 32px 40px;">
                            <table width="100%" cellpadding="0" cellspacing="0" border="0">
                                <tr>
                                    <td>
                                        <div style="display:inline-block; background: rgba(255,255,255,0.15); border-radius: 8px; padding: 8px 12px; margin-bottom: 12px; font-size: 22px;">
                                            🎓
                                        </div>
                                        <h1 style="margin: 0; color: #ffffff; font-size: 22px; font-weight: 700; letter-spacing: -0.3px;">
                                            Nouvelle inscription
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

                            <!-- Parent info -->
                            <table width="100%" cellpadding="0" cellspacing="0" border="0"
                                style="background: #fff7ed; border: 1px solid #fed7aa; border-radius: 10px; margin-bottom: 20px;">
                                <tr>
                                    <td style="padding: 20px 24px;">
                                        <p style="margin: 0 0 4px; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; color: #ea580c;">
                                            👤 Parent / Responsable
                                        </p>
                                        <p style="margin: 0 0 2px; font-size: 18px; font-weight: 700; color: #1e293b;">
                                            {{ $formData['parent_nom'] }}
                                        </p>
                                        <p style="margin: 4px 0 0; font-size: 14px; color: #f97316;">
                                            📧 <a href="mailto:{{ $formData['email'] }}" style="color: #f97316; text-decoration: none;">{{ $formData['email'] }}</a>
                                        </p>
                                        <p style="margin: 4px 0 0; font-size: 14px; color: #64748b;">
                                            📞 {{ $formData['telephone'] }}
                                        </p>
                                    </td>
                                </tr>
                            </table>

                            <!-- Student info -->
                            <table width="100%" cellpadding="0" cellspacing="0" border="0"
                                style="background: #f0f9ff; border: 1px solid #bae6fd; border-radius: 10px; margin-bottom: 20px;">
                                <tr>
                                    <td style="padding: 20px 24px;">
                                        <p style="margin: 0 0 4px; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; color: #0284c7;">
                                            🎒 Élève
                                        </p>
                                        <p style="margin: 0 0 2px; font-size: 18px; font-weight: 700; color: #1e293b;">
                                            {{ $formData['eleve_nom'] }}
                                        </p>
                                        <p style="margin: 4px 0 0; font-size: 14px; color: #64748b;">
                                            🎂 Né(e) le {{ \Carbon\Carbon::parse($formData['date_naissance'])->format('d/m/Y') }}
                                        </p>
                                    </td>
                                </tr>
                            </table>

                            <!-- School info -->
                            <table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-bottom: 20px;">
                                <tr>
                                    <td style="padding-bottom: 6px;">
                                        <p style="margin: 0 0 4px; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; color: #94a3b8;">
                                            🏫 Établissement & Niveau
                                        </p>
                                        <p style="margin: 0; font-size: 16px; font-weight: 600; color: #1e293b; padding: 12px 16px; background: #f8fafc; border-left: 4px solid #f97316; border-radius: 0 6px 6px 0;">
                                            {{ $formData['etablissement'] }} — {{ $formData['niveau'] }}
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
                                            💬 Message complémentaire
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
                                        <p style="margin: 0; font-size: 12px; color: #94a3b8;">
                                            <strong>IP :</strong> {{ $formData['ip_address'] }}<br>
                                            <strong>Date :</strong> {{ now()->format('d/m/Y H:i:s') }}
                                        </p>
                                    </td>
                                </tr>
                            </table>

                            <!-- Reply CTA -->
                            <table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-top: 28px;">
                                <tr>
                                    <td align="center">
                                        <a href="mailto:{{ $formData['email'] }}?subject=Re: Inscription de {{ rawurlencode($formData['eleve_nom']) }}"
                                           style="display: inline-block; background: linear-gradient(135deg, #f97316 0%, #ea580c 100%); color: #ffffff; font-size: 14px; font-weight: 600; text-decoration: none; padding: 12px 32px; border-radius: 8px; letter-spacing: 0.2px;">
                                            ↩ Contacter {{ $formData['parent_nom'] }}
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
                                Cette inscription a été soumise via le formulaire de votre site web.<br>
                                Statut initial : <strong style="color: #f97316;">en_attente</strong>
                            </p>
                        </td>
                    </tr>

                </table>

            </td>
        </tr>
    </table>

</body>
</html>