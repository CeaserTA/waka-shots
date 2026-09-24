<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your photos are ready</title>
</head>
<body style="margin:0;padding:0;background-color:#0b0b0c;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color:#0b0b0c;">
        <tr>
            <td align="center" style="padding:40px 16px;">
                <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="max-width:560px;background-color:#141416;border:1px solid #2a2622;">
                    <tr>
                        <td style="padding:40px 36px 8px;text-align:center;font-family:Georgia,'Times New Roman',serif;">
                            <p style="margin:0;font-size:13px;letter-spacing:3px;text-transform:uppercase;color:#c9a45c;">{{ $siteSetting->studio_name ?? 'Waka Shots' }}</p>
                            <h1 style="margin:20px 0 0;font-size:28px;line-height:1.3;font-weight:normal;color:#f4efe6;">Your photos are ready</h1>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:20px 36px 8px;font-family:Helvetica,Arial,sans-serif;font-size:15px;line-height:1.7;color:#b9b4ab;">
                            <p style="margin:0 0 14px;">Hi {{ $gallery->client_name }},</p>
                            <p style="margin:0;">Your gallery from <strong style="color:#f4efe6;">{{ $gallery->event_name }}</strong> is now online. You can view, preview and download every photo from the link below.</p>
                        </td>
                    </tr>
                    <tr>
                        <td align="center" style="padding:28px 36px;">
                            <a href="{{ $galleryUrl }}" style="display:inline-block;padding:14px 34px;background-color:#c9a45c;color:#0b0b0c;font-family:Helvetica,Arial,sans-serif;font-size:14px;font-weight:bold;letter-spacing:1px;text-transform:uppercase;text-decoration:none;">View your gallery</a>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:0 36px 32px;">
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="border:1px solid #c9a45c;background-color:#0f0f10;">
                                <tr>
                                    <td align="center" style="padding:24px 20px;font-family:Helvetica,Arial,sans-serif;">
                                        <p style="margin:0;font-size:12px;letter-spacing:2px;text-transform:uppercase;color:#c9a45c;">Your access code</p>
                                        <p style="margin:12px 0;font-family:'Courier New',Courier,monospace;font-size:34px;letter-spacing:10px;font-weight:bold;color:#f4efe6;">{{ $accessCode }}</p>
                                        <p style="margin:0;font-size:13px;line-height:1.6;color:#b9b4ab;">You'll be asked for this code when you open the gallery. Keep it private; anyone with the link and the code can see your photos.</p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:0 36px 36px;font-family:Helvetica,Arial,sans-serif;font-size:12px;line-height:1.6;color:#7d786f;text-align:center;">
                            If the button doesn't work, copy this link into your browser:<br>
                            <a href="{{ $galleryUrl }}" style="color:#c9a45c;word-break:break-all;">{{ $galleryUrl }}</a>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
