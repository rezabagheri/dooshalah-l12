<!DOCTYPE html>
<html>
<head>
    <title>Payment Failed</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <div style="max-width: 600px; margin: 0 auto; padding: 20px;">
        <h2 style="color: #dc3545;">Payment Failed</h2>
        <p>Hello {{ $userName }},</p>
        <p>We’re sorry, but your recent payment attempt was unsuccessful. Here’s why:</p>

        <p style="background: #f8d7da; padding: 10px; border: 1px solid #f5c6cb; color: #721c24;">
            <strong>Reason:</strong> {{ $reason }}
        </p>

        <p>You can try again using the link below, or contact our support team for assistance:</p>
        <p>
            <a href="{{ $supportLink }}" style="display: inline-block; padding: 10px 20px; background: #007bff; color: #fff; text-decoration: none; border-radius: 5px;">Contact Support</a>
        </p>
        <p>Or try again here: <a href="{{ route('plans.upgrade') }}">Upgrade Your Plan</a></p>

        <p>We’re here to help!<br>Your App Team</p>
    </div>
</body>
</html>
