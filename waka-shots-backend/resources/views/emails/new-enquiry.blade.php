@php
    $rows = array_filter([
        'Name' => $enquiry->name,
        'Email' => $enquiry->email,
        'Phone' => $enquiry->phone,
        'Service' => $enquiry->service?->name,
        'Package' => $enquiry->package?->name,
        'Preferred date' => $enquiry->preferred_date,
        'Location' => $enquiry->location,
    ], fn ($value) => filled($value));
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New enquiry</title>
</head>
<body style="margin:0;padding:0;background-color:#0b0b0c;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color:#0b0b0c;">
        <tr>
            <td align="center" style="padding:40px 16px;">
                <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="max-width:560px;background-color:#141416;border:1px solid #2a2622;">
                    <tr>
                        <td style="padding:40px 36px 8px;text-align:center;font-family:Georgia,'Times New Roman',serif;">
                            <p style="margin:0;font-size:13px;letter-spacing:3px;text-transform:uppercase;color:#c9a45c;">{{ $siteSetting->studio_name ?? 'Waka Shots' }}</p>
                            <h1 style="margin:20px 0 0;font-size:28px;line-height:1.3;font-weight:normal;color:#f4efe6;">New enquiry received</h1>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:24px 36px 8px;">
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="font-family:Helvetica,Arial,sans-serif;font-size:14px;line-height:1.6;">
                                @foreach ($rows as $label => $value)
                                    <tr>
                                        <td style="padding:6px 12px 6px 0;color:#7d786f;white-space:nowrap;vertical-align:top;">{{ $label }}</td>
                                        <td style="padding:6px 0;color:#f4efe6;word-break:break-word;">{{ $value }}</td>
                                    </tr>
                                @endforeach
                            </table>
                        </td>
                    </tr>
                    @if (filled($enquiry->details))
                        <tr>
                            <td style="padding:16px 36px 8px;font-family:Helvetica,Arial,sans-serif;">
                                <p style="margin:0 0 8px;font-size:12px;letter-spacing:2px;text-transform:uppercase;color:#c9a45c;">Message</p>
                                <p style="margin:0;font-size:15px;line-height:1.7;color:#b9b4ab;white-space:pre-line;">{{ $enquiry->details }}</p>
                            </td>
                        </tr>
                    @endif
                    <tr>
                        <td align="center" style="padding:28px 36px;">
                            <a href="{{ $dashboardUrl }}" style="display:inline-block;padding:14px 34px;background-color:#c9a45c;color:#0b0b0c;font-family:Helvetica,Arial,sans-serif;font-size:14px;font-weight:bold;letter-spacing:1px;text-transform:uppercase;text-decoration:none;">Open in dashboard</a>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:0 36px 36px;font-family:Helvetica,Arial,sans-serif;font-size:12px;line-height:1.6;color:#7d786f;text-align:center;">
                            Replying to this email goes straight to {{ $enquiry->email }}.
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
