<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to Our Service</title>
</head>
<body>
    <h1>Welcome, {{ $employeeName }}!</h1>
    <p>Your account has been created successfully.</p>
    <p>Your temporary password is: <strong>{{ $password }}</strong></p>
    <p>Please change your password after logging in.</p>
    <p>Thank you for joining us!</p>
</body>
</html>
