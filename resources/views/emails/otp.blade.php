<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Password Reset OTP</title>
<style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { background: #f0fdf4; font-family: 'Segoe UI', sans-serif; }

    .wrapper {
        max-width: 520px;
        margin: 40px auto;
        background: white;
        border-radius: 20px;
        overflow: hidden;
        box-shadow: 0 10px 40px rgba(16,185,129,0.15);
    }

    .header {
        background: linear-gradient(135deg, #16a34a, #065f46);
        padding: 40px 32px;
        text-align: center;
    }

    .header h1 {
        color: white;
        font-size: 20px;
        font-weight: 800;
        letter-spacing: 1.5px;
        text-transform: uppercase;
    }

    .header p {
        color: rgba(255,255,255,0.75);
        font-size: 13px;
        margin-top: 4px;
    }

    .body { padding: 40px 32px; }

    .body p {
        color: #334155;
        font-size: 15px;
        line-height: 1.7;
    }

    .otp-box {
        background: linear-gradient(135deg, #ecfdf5, #d1fae5);
        border: 2px dashed #16a34a;
        border-radius: 16px;
        text-align: center;
        padding: 28px;
        margin: 28px 0;
    }

    .otp-label {
        font-size: 12px;
        color: #6b7280;
        text-transform: uppercase;
        letter-spacing: 2px;
        font-weight: 700;
        margin-bottom: 12px;
    }

    .otp-code {
        font-size: 48px;
        font-weight: 900;
        letter-spacing: 14px;
        color: #065f46;
        font-family: 'Courier New', monospace;
    }

    .expires {
        font-size: 13px;
        color: #9ca3af;
        text-align: center;
        margin-top: 8px;
    }

    .expires span {
        color: #dc2626;
        font-weight: 700;
    }

    .warning {
        background: #fef2f2;
        border-left: 4px solid #dc2626;
        border-radius: 8px;
        padding: 14px 16px;
        margin-top: 24px;
        font-size: 13px;
        color: #7f1d1d;
        line-height: 1.6;
    }

    .footer {
        background: #f0fdf4;
        padding: 24px 32px;
        text-align: center;
        border-top: 1px solid #d1fae5;
    }

    .footer p {
        font-size: 12px;
        color: #6b7280;
        line-height: 1.6;
    }

    .footer strong {
        color: #16a34a;
    }
</style>
</head>
<body>
<div class="wrapper">
    <div class="header">
        <h1>Green Valley College Foundation Inc.</h1>
        <p>Password Reset Request</p>
    </div>
    <div class="body">
        <p>Hello,</p>
        <br>
        <p>We received a request to reset your password. Use the OTP code below to proceed. This code is valid for <strong>10 minutes</strong>.</p>

        <div class="otp-box">
            <div class="otp-label">Your One-Time Password</div>
            <div class="otp-code">{{ $otp }}</div>
        </div>

        <p class="expires">Expires in <span>10 minutes</span></p>

        <div class="warning">
            ⚠️ If you did not request a password reset, please ignore this email. Your account remains secure and no changes have been made.
        </div>
    </div>
    <div class="footer">
        <p>This email was sent by <strong>Green Valley College Foundation Inc.</strong><br>Do not reply to this email.</p>
    </div>
</div>
</body>
</html>

