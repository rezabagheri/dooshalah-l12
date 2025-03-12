<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Welcome to Doosh Chat!</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            color: #333;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            text-align: center;
            padding: 20px;
            background-color: #f8f9fa;
            border-bottom: 1px solid #dee2e6;
        }
        .header h1 {
            color: #007bff;
            margin: 0;
        }
        .content {
            padding: 20px;
        }
        .content p {
            font-size: 16px;
            line-height: 1.5;
        }
        .button {
            display: inline-block;
            padding: 10px 20px;
            background-color: #007bff;
            color: #fff;
            text-decoration: none;
            border-radius: 5px;
            text-align: center;
        }
        .footer {
            text-align: center;
            padding: 20px;
            background-color: #f8f9fa;
            border-top: 1px solid #dee2e6;
            color: #6c757d;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Welcome to Doosh Chat!</h1>
        </div>

        <div class="content">
            <p>Hello {{ $user->first_name }},</p>

            <p>We’re thrilled to have you join Doosh Chat! Your account has been successfully created.</p>

            <p>Here’s what you can do next:</p>
            <ul>
                <li>Explore your profile</li>
                <li>Connect with friends</li>
                <li>Start chatting!</li>
            </ul>

            <p style="text-align: center;">
                <a href="{{ url('/settings/profile') }}" class="button">Visit Your Profile</a>
            </p>

            <p>If you have any questions, feel free to reach out to our support team at <a href="mailto:support@doosh-chat.com" style="color: #007bff;">support@doosh-chat.com</a>.</p>
        </div>

        <div class="footer">
            <p>Best regards,<br>The Doosh Chat Team</p>
        </div>
    </div>
</body>
</html>
