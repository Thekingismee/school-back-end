<!DOCTYPE html>
<html lang="fr" xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml" xmlns:o="urn:schemas-microsoft-com:office:office">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="x-apple-disable-message-reformatting">
    <title>{{ $title ?? 'Newsletter' }}</title>
    
    <!-- Preheader text (affiché dans la boîte de réception) -->
    <style type="text/css">
        .preheader { display: none !important; visibility: hidden; opacity: 0; color: transparent; height: 0; width: 0; }
    </style>
    <!--[if mso]>
    <noscript>
    <xml>
    <o:OfficeDocumentSettings>
    <o:PixelsPerInch>96</o:PixelsPerInch>
    </o:OfficeDocumentSettings>
    </xml>
    </noscript>
    <![endif]-->
    <style type="text/css">
        /* Reset & Base */
        body, table, td, a { -webkit-text-size-adjust: 100%; -ms-text-size-adjust: 100%; }
        table, td { mso-table-lspace: 0pt; mso-table-rspace: 0pt; }
        img { -ms-interpolation-mode: bicubic; border: 0; outline: none; text-decoration: none; }
        table { border-collapse: collapse !important; }
        body { margin: 0 !important; padding: 0 !important; width: 100% !important; }
        
        /* Dark mode support */
        @media (prefers-color-scheme: dark) {
            body, .email-body { background-color: #0f172a !important; }
            .card { background-color: #1e293b !important; }
            .text-primary { color: #f97316 !important; }
            .text-body { color: #e2e8f0 !important; }
            .text-muted { color: #94a3b8 !important; }
            .divider { border-color: #334155 !important; }
        }
        
        /* Responsive */
        @media screen and (max-width: 600px) {
            .container { width: 100% !important; max-width: 100% !important; }
            .mobile-padding { padding-left: 20px !important; padding-right: 20px !important; }
            .mobile-center { text-align: center !important; }
            .mobile-stack { display: block !important; width: 100% !important; }
            .mobile-mb { margin-bottom: 20px !important; }
            .hero-img { width: 100% !important; height: auto !important; }
            .btn-full { width: 100% !important; display: block !important; }
        }
    </style>
</head>
<body style="margin:0;padding:0;background:#f1f5f9;font-family:'Segoe UI',Tahoma,Geneva,Verdana,sans-serif;">

    <!-- Preheader (invisible mais lu par les clients email) -->
    <div class="preheader">
        {{ $preheader ?? 'Découvrez nos dernières actualités et offres exclusives...' }}
        &#847; &#847; &#847; &#847; &#847; &#847; &#847; &#847; &#847; &#847;
        &#847; &#847; &#847; &#847; &#847; &#847; &#847; &#847; &#847; &#847;
    </div>

    <!-- Background -->
    <table width="100%" cellpadding="0" cellspacing="0" role="presentation" class="email-body">
        <tr>
            <td align="center" style="padding:20px 10px;">

                <!-- Main Container (600px standard email) -->
                <table width="600" cellpadding="0" cellspacing="0" role="presentation" class="container" style="background:#ffffff;border-radius:5px;overflow:hidden;box-shadow:0 10px 40px rgba(0,0,0,0.12);">

                    <!-- === TOP BAR === -->
                    <tr>
                        <td style="background:linear-gradient(135deg,#667eea 0%,#764ba2 100%);padding:8px 0;text-align:center;font-size:12px;color:#ffffff;">
                            <span style="letter-spacing:0.5px;">✨ Offre exclusive : -20% cette semaine avec le code <strong>NEWS20</strong></span>
                        </td>
                    </tr>

                    <!-- === HEADER / LOGO === -->
                    <tr>
                        <td align="center" style="padding:28px 20px;background:#0f172a;">
                            <!-- Logo avec fallback texte -->
                            <!--[if mso]>
                            <table cellpadding="0" cellspacing="0" role="presentation">
                            <tr><td style="font-size:24px;font-weight:bold;color:#ffffff;">{{ $brand_name ?? 'MRX' }}</td></tr>
                            </table>
                            <![endif]-->
                            <!--[if !mso]><!-->
                            <a href="{{ $home_url ?? '#' }}" style="text-decoration:none;">
                                <img src="{{ $logo_url ?? 'https://via.placeholder.com/160x50/ffffff/0f172a?text=MRX' }}" 
                                     alt="{{ $brand_name ?? 'Logo' }}" 
                                     width="160" 
                                     style="display:block;width:160px;max-width:100%;height:auto;">
                            </a>
                            <!--<![endif]-->
                        </td>
                    </tr>

                    <!-- === HERO IMAGE (optionnelle) === -->
                    @if(!empty($hero_image))
                    <tr>
                        <td style="padding:0;">
                            <img src="{{ $hero_image }}" 
                                 alt="Image principale" 
                                 width="600" 
                                 class="hero-img"
                                 style="display:block;width:100%;max-width:600px;height:auto;">
                        </td>
                    </tr>
                    @endif

                    <!-- === TITLE SECTION === -->
                    <tr>
                        <td class="mobile-padding" style="padding:35px 40px 20px 40px;text-align:center;">
                            <h1 style="margin:0;font-size:26px;font-weight:700;color:#0f172a;line-height:1.3;">
                                {{ $title }}
                            </h1>
                            @if(!empty($subtitle))
                            <p style="margin:12px 0 0;font-size:16px;color:#64748b;line-height:1.5;">
                                {{ $subtitle }}
                            </p>
                            @endif
                        </td>
                    </tr>

                    <!-- === MAIN CONTENT === -->
                    <tr>
                        <td class="mobile-padding" style="padding:0 40px 30px 40px;color:#334155;font-size:16px;line-height:1.7;" class="text-body">
                            {!! $content !!}
                        </td>
                    </tr>

                    <!-- === FEATURED BLOCKS (2 colonnes) === -->
                    <tr>
                        <td class="mobile-padding" style="padding:0 40px 30px 40px;">
                            <table width="100%" cellpadding="0" cellspacing="0" role="presentation">
                                <tr>
                                    <!-- Block 1 -->
                                    <td width="280" valign="top" class="mobile-stack mobile-mb" style="padding-right:15px;">
                                        <table width="100%" cellpadding="0" cellspacing="0" role="presentation" class="card" style="background:#f8fafc;border-radius:5px;padding:20px;border:1px solid #e2e8f0;">
                                            <tr>
                                                <td style="padding-bottom:12px;">
                                                    <span style="background:#f97316;color:#fff;padding:4px 10px;border-radius:5px;font-size:11px;font-weight:600;text-transform:uppercase;">Nouveau</span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="padding-bottom:10px;font-weight:600;color:#0f172a;font-size:16px;">
                                                    🚀 Fonctionnalité du mois
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="color:#64748b;font-size:14px;line-height:1.5;padding-bottom:15px;">
                                                    Découvrez notre dernière mise à jour qui va transformer votre expérience.
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <a href="{{ $feature_link ?? '#' }}" style="color:#667eea;font-weight:600;text-decoration:none;font-size:14px;">
                                                        En savoir plus →
                                                    </a>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                    
                                    <!-- Block 2 -->
                                    <td width="280" valign="top" class="mobile-stack" style="padding-left:15px;">
                                        <table width="100%" cellpadding="0" cellspacing="0" role="presentation" class="card" style="background:#f8fafc;border-radius:5px;padding:20px;border:1px solid #e2e8f0;">
                                            <tr>
                                                <td style="padding-bottom:12px;">
                                                    <span style="background:#22c55e;color:#fff;padding:4px 10px;border-radius:5px;font-size:11px;font-weight:600;text-transform:uppercase;">Astuce</span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="padding-bottom:10px;font-weight:600;color:#0f172a;font-size:16px;">
                                                    💡 Le conseil pro
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="color:#64748b;font-size:14px;line-height:1.5;padding-bottom:15px;">
                                                    Optimisez votre workflow avec cette technique simple et efficace.
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <a href="{{ $tip_link ?? '#' }}" style="color:#667eea;font-weight:600;text-decoration:none;font-size:14px;">
                                                        Lire l'astuce →
                                                    </a>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- === PRIMARY CTA BUTTON === -->
                    @if(!empty($cta_text) && !empty($cta_url))
                    <tr>
                        <td align="center" style="padding:10px 40px 35px 40px;">
                            <!--[if mso]>
                            <v:roundrect xmlns:v="urn:schemas-microsoft-com:vml" xmlns:w="urn:schemas-microsoft-com:office:word" 
                                         href="{{ $cta_url }}" style="height:48px;v-text-anchor:middle;width:220px;" arcsize="10%" stroke="f" fillcolor="#f97316">
                                <w:anchorlock/>
                                <center style="color:#ffffff;font-family:Arial,sans-serif;font-size:16px;font-weight:bold;">
                                    {{ $cta_text }}
                                </center>
                            </v:roundrect>
                            <![endif]-->
                            <!--[if !mso]><!-->
                            <a href="{{ $cta_url }}" target="_blank" style="background:linear-gradient(135deg,#f97316 0%,#ea580c 100%);color:#ffffff;padding:14px 32px;text-decoration:none;border-radius:5px;font-weight:700;font-size:16px;display:inline-block;box-shadow:0 4px 14px rgba(249,115,22,0.35);">
                                {{ $cta_text }}
                            </a>
                            <!--<![endif]-->
                        </td>
                    </tr>
                    @endif

                    <!-- === SOCIAL LINKS === -->
                    <tr>
                        <td align="center" style="padding:0 40px 25px 40px;">
                            <table cellpadding="0" cellspacing="0" role="presentation">
                                <tr>
                                    <td style="padding:0 8px;">
                                        <a href="{{ $social_facebook ?? '#' }}" style="text-decoration:none;">
                                            <img src="https://cdn-icons-png.flaticon.com/32/5968/5968764.png" width="32" alt="Facebook" style="display:block;border-radius:50%;">
                                        </a>
                                    </td>
                                    <td style="padding:0 8px;">
                                        <a href="{{ $social_twitter ?? '#' }}" style="text-decoration:none;">
                                            <img src="https://cdn-icons-png.flaticon.com/32/5968/5968830.png" width="32" alt="Twitter/X" style="display:block;border-radius:50%;">
                                        </a>
                                    </td>
                                    <td style="padding:0 8px;">
                                        <a href="{{ $social_instagram ?? '#' }}" style="text-decoration:none;">
                                            <img src="https://cdn-icons-png.flaticon.com/32/5968/5968802.png" width="32" alt="Instagram" style="display:block;border-radius:50%;">
                                        </a>
                                    </td>
                                    <td style="padding:0 8px;">
                                        <a href="{{ $social_linkedin ?? '#' }}" style="text-decoration:none;">
                                            <img src="https://cdn-icons-png.flaticon.com/32/5968/5968780.png" width="32" alt="LinkedIn" style="display:block;border-radius:50%;">
                                        </a>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- === DIVIDER === -->
                    <tr>
                        <td style="padding:0 40px;">
                            <hr class="divider" style="border:none;border-top:1px solid #e2e8f0;margin:0;">
                        </td>
                    </tr>

                    <!-- === FOOTER === -->
                    <!-- <tr>
                        <td style="background:#0f172a;padding:30px 40px;text-align:center;font-size:13px;color:#94a3b8;line-height:1.6;">
                            
                            <p style="margin:0 0 15px 0;">
                                <strong style="color:#cbd5e1;">{{ $brand_name ?? 'MRX' }}</strong><br>
                                {{ $company_address ?? '123 Avenue de l\'Innovation, 75001 Paris' }}<br>
                                <a href="mailto:{{ $contact_email ?? 'hello@mrX.com' }}" style="color:#7dd3fc;text-decoration:none;">{{ $contact_email ?? 'hello@mrX.com' }}</a>
                            </p>
                            
                            <p style="margin:0 0 20px 0;">
                                <a href="{{ $privacy_url ?? '#' }}" style="color:#94a3b8;text-decoration:none;margin:0 8px;">Confidentialité</a>
                                <span style="color:#475569;">•</span>
                                <a href="{{ $terms_url ?? '#' }}" style="color:#94a3b8;text-decoration:none;margin:0 8px;">Mentions légales</a>
                                <span style="color:#475569;">•</span>
                                <a href="{{ $contact_url ?? '#' }}" style="color:#94a3b8;text-decoration:none;margin:0 8px;">Contact</a>
                            </p>
                            
                            <p style="margin:0;">
                                <a href="{{ $unsubscribe_url ?? '#' }}" style="color:#f87171;text-decoration:none;font-weight:600;">
                                    ✉️ Se désabonner
                                </a>
                                <span style="color:#475569;">|</span>
                                <a href="{{ $preferences_url ?? '#' }}" style="color:#94a3b8;text-decoration:none;">
                                    Gérer mes préférences
                                </a>
                            </p>
                            
                            <p style="margin:20px 0 0 0;font-size:11px;color:#64748b;">
                                © {{ date('Y') }} {{ $brand_name ?? 'MRX' }}. Tous droits réservés.<br>
                                <span style="font-size:10px;">Cet email a été envoyé à {{ $recipient_email ?? 'votre@email.com' }}</span>
                            </p>
                            
                        </td>
                    </tr> -->

                </table>
                <!-- End Main Container -->

                <!-- === TRACKING PIXEL (optionnel) === -->
                @if(!empty($tracking_pixel))
                <img src="{{ $tracking_pixel }}" width="1" height="1" alt="" style="display:block;">
                @endif

            </td>
        </tr>
    </table>

</body>
</html>