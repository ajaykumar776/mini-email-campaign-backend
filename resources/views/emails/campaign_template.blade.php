<!DOCTYPE html>
<html>
<head>
    <title>Campaign Notification</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            color: #333;
            background: linear-gradient(to bottom, #ff9933, #ffffff, #138808);
        }
        .container {
            width: 80%;
            margin: 20px auto;
            padding: 20px;
            background-color: #ffffff;
            border: 1px solid #dee2e6;
            border-radius: 5px;
        }
        .header {
            background-color: #ff9933; /* Saffron color */
            padding: 10px;
            text-align: center;
            border-bottom: 1px solid #dee2e6;
            border-radius: 5px 5px 0 0;
            color: #ffffff;
        }
        .content {
            margin-top: 20px;
            color: #333;
        }
        .footer {
            margin-top: 30px;
            padding: 10px;
            background-color: #ffffff;
            text-align: center;
            border-top: 1px solid #dee2e6;
            border-radius: 0 0 5px 5px;
        }
        .footer p {
            margin: 0;
        }
        .footer p:first-child {
            font-weight: bold;
        }
        .independence-day {
            text-align: center;
            margin-top: 20px;
            color: #138808; /* Green color */
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Campaign Notification</h1>
        </div>

        <div class="content">
            <p>Hello {{ $username }},</p>

            <p>We wanted to let you know that your campaign is currently running as part of the "Rannkly" series. This email serves as a test of our email functionality to ensure everything is operating smoothly.</p>

            <p>This message is part of our mini email campaign created by Ajay to test the system's performance and reliability. Should you have any feedback or questions, please don't hesitate to reach out to us.</p>

            <p>Thank you for your participation and understanding!</p>

            <p>Best regards,<br>
            Ajay Kumar</p>
        </div>

        <div class="footer">
            <p>© 2024 Ajay@krdtg5. All rights reserved.</p>
            <p class="independence-day">
                Happy Independence Day! 🇮🇳<br>
                Celebrating the spirit of freedom and unity.
            </p>
        </div>
    </div>
</body>
</html>
