<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Welcome to MageShop</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f7f7f7; margin: 0; padding: 0; }
        .container { background: #fff; max-width: 600px; margin: 40px auto; padding: 30px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.05);}
        h1 { color: #333; }
        p { color: #555; }
        .footer { margin-top: 30px; font-size: 12px; color: #aaa; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Welcome, {{ $user->name }}!</h1>
        <p>
            Thank you for joining MageShop. We're excited to have you as part of our community.
        </p>
        <p>
            If you have any questions, feel free to reply to this email.
        </p>
        <p>
            Happy shopping!<br>
            <strong>The MageShop Team</strong>
        </p>
        <div class="footer">
            &copy; {{ date('Y') }} MageShop. All rights reserved.
        </div>
    </div>
</body>
</html>