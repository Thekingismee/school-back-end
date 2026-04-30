<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>{{ $subject ?? $title }}</title>
</head>
<body style="margin:0; padding:0; background-color:#f3f4f6; font-family:'Segoe UI', Arial, sans-serif;">

  {{-- Wrapper --}}
  <table width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color:#f3f4f6; padding:32px 0;">
    <tr>
      <td align="center">

        {{-- Container --}}
        <table width="600" cellpadding="0" cellspacing="0" border="0"
          style="background-color:#ffffff; border-radius:12px; overflow:hidden; max-width:600px; width:100%;">

          {{-- Header --}}
          <tr>
            <td style="background-color:#fff7ed; padding:28px 40px; border-bottom:2px solid #fed7aa;">
              <p style="margin:0; font-size:13px; color:#9ca3af; font-weight:600; letter-spacing:0.05em; text-transform:uppercase;">
                <!-- {{ $senderName ?? config('app.name') }} -->
                  GS l'Atome
              </p>
              <h1 style="margin:8px 0 0; font-size:24px; font-weight:700; color:#111827; line-height:1.3;">
                {{ $title }}
              </h1>
            </td>
          </tr>

          {{-- Body --}}
          <tr>
            <td style="padding:36px 40px; color:#374151; font-size:15px; line-height:1.8;">
              {!! $content !!}
            </td>
          </tr>

          {{-- Divider --}}
          <tr>
            <td style="padding:0 40px;">
              <hr style="border:none; border-top:1px solid #f3f4f6; margin:0;">
            </td>
          </tr>

          {{-- Footer --}}
          <tr>
            <td style="padding:24px 40px; text-align:center;">
              <p style="margin:0 0 8px; font-size:12px; color:#9ca3af;">
                Vous recevez cet email car vous êtes inscrit à notre newsletter.
              </p>
              <a href="{{ $unsubscribeUrl ?? '#' }}"
                style="font-size:12px; color:#f97316; text-decoration:underline;">
                Se désabonner
              </a>
            </td>
          </tr>

        </table>
        {{-- End container --}}

      </td>
    </tr>
  </table>

</body>
</html>