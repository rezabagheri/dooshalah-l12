<!DOCTYPE html>
<html>
<head>
    <title>Payment Invoice</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <div style="max-width: 600px; margin: 0 auto; padding: 20px;">
        <h2 style="color: #28a745;">Payment Successful</h2>
        <p>Hello {{ $userName }},</p>
        <p>Thank you for your purchase! Below is your payment invoice:</p>

        <table style="width: 100%; border-collapse: collapse; margin: 20px 0;">
            <tr style="background: #f8f9fa; border: 1px solid #ddd;">
                <th style="padding: 10px; text-align: left;">Description</th>
                <th style="padding: 10px; text-align: left;">Details</th>
            </tr>
            <tr style="border: 1px solid #ddd;">
                <td style="padding: 10px;">Plan</td>
                <td style="padding: 10px;">{{ $plan }}</td>
            </tr>
            <tr style="border: 1px solid #ddd;">
                <td style="padding: 10px;">Duration</td>
                <td style="padding: 10px;">{{ $duration }}</td>
            </tr>
            <tr style="border: 1px solid #ddd;">
                <td style="padding: 10px;">Amount</td>
                <td style="padding: 10px;">${{ $amount }}</td>
            </tr>
            <tr style="border: 1px solid #ddd;">
                <td style="padding: 10px;">Transaction ID</td>
                <td style="padding: 10px;">{{ $transactionId }}</td>
            </tr>
            <tr style="border: 1px solid #ddd;">
                <td style="padding: 10px;">Payment Date</td>
                <td style="padding: 10px;">{{ $paymentDate }}</td>
            </tr>
        </table>

        <p>If you have any questions, feel free to contact us at <a href="mailto:support@yourapp.com">support@yourapp.com</a>.</p>
        <p>Best regards,<br>Your App Team</p>
    </div>
</body>
</html>
