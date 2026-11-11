<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Thông tin khách hàng yêu cầu báo giá - DATYSO</title>
</head>
<body style="font-family: Arial, sans-serif; color: #1f2937; line-height: 1.6;">
    <div style="max-width: 640px; margin: 0 auto; border: 1px solid #e5e7eb; border-radius: 8px; overflow: hidden;">
        <div style="background: #002ab0; color: #ffffff; padding: 16px 24px;">
            <h2 style="margin: 0; font-size: 18px;">Yêu cầu báo giá / Tư vấn mới từ Website DATYSO</h2>
        </div>
        <div style="padding: 20px 24px;">
            <table style="width: 100%; border-collapse: collapse;">
                <tr>
                    <td style="padding: 8px 0; width: 170px; font-weight: bold; color: #4b5563;">Họ và tên:</td>
                    <td style="padding: 8px 0;">{{ $contact->username }}</td>
                </tr>
                <tr>
                    <td style="padding: 8px 0; font-weight: bold; color: #4b5563;">Số điện thoại:</td>
                    <td style="padding: 8px 0;">
                        <a href="tel:{{ $contact->phone }}" style="color: #002ab0; font-weight: bold; text-decoration: none;">
                            {{ $contact->phone }}
                        </a>
                    </td>
                </tr>
                @if(!empty($contact->province))
                <tr>
                    <td style="padding: 8px 0; font-weight: bold; color: #4b5563;">Tỉnh / Thành phố:</td>
                    <td style="padding: 8px 0;">{{ $contact->province }}</td>
                </tr>
                @endif
                @if(!empty($contact->email))
                <tr>
                    <td style="padding: 8px 0; font-weight: bold; color: #4b5563;">Email:</td>
                    <td style="padding: 8px 0;">{{ $contact->email }}</td>
                </tr>
                @endif
                @if(!empty($contact->source_name))
                <tr>
                    <td style="padding: 8px 0; font-weight: bold; color: #4b5563;">
                        {{ ($contact->source_type ?? '') === 'category' ? 'Danh mục đang xem:' : 'Sản phẩm đang xem:' }}
                    </td>
                    <td style="padding: 8px 0; font-weight: bold; color: #002ab0;">{{ $contact->source_name }}</td>
                </tr>
                @elseif($contact->product)
                <tr>
                    <td style="padding: 8px 0; font-weight: bold; color: #4b5563;">Sản phẩm:</td>
                    <td style="padding: 8px 0; font-weight: bold; color: #002ab0;">{{ $contact->product->name }}</td>
                </tr>
                @endif
                @if(!empty($contact->demand))
                <tr>
                    <td style="padding: 8px 0; font-weight: bold; color: #4b5563; vertical-align: top;">Nhu cầu chi tiết:</td>
                    <td style="padding: 8px 0;">{!! nl2br(e($contact->demand)) !!}</td>
                </tr>
                @elseif(!empty($contact->content))
                <tr>
                    <td style="padding: 8px 0; font-weight: bold; color: #4b5563; vertical-align: top;">Nội dung:</td>
                    <td style="padding: 8px 0;">{!! nl2br(e($contact->content)) !!}</td>
                </tr>
                @endif
                @if(!empty($contact->source_url))
                <tr>
                    <td style="padding: 8px 0; font-weight: bold; color: #4b5563;">Trang gửi yêu cầu:</td>
                    <td style="padding: 8px 0;">
                        <a href="{{ $contact->source_url }}" style="color: #2563eb; word-break: break-all;">{{ $contact->source_url }}</a>
                    </td>
                </tr>
                @endif
                <tr>
                    <td style="padding: 8px 0; font-weight: bold; color: #4b5563;">Thời gian gửi:</td>
                    <td style="padding: 8px 0;">{{ optional($contact->created_at)->format('H:i:s d/m/Y') ?? now()->format('H:i:s d/m/Y') }}</td>
                </tr>
            </table>
        </div>
    </div>
</body>
</html>
