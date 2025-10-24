<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forget Password Alert - {{ env('APP_NAME') }}</title>
</head>

<body>
    <p>Hi, {{ $user->name }}!</p>
    <p>We noticed that there was an attempt to login to your {{ env('APP_NAME') }} account on a new device. Please enter
        the following One Time PIN (OTP) in the {{ env('APP_NAME') }} app to login:</p>

    <h2 style="background-color: #f0f0f0; padding: 10px; display: inline-block;">
        <a class="btn btn-sm btn-success" href="{{ route('resetPasswordForm', ['token' => $token]) }}">Reset Link</a>
    </h2>

    <p>If this wasn't you:</p>
    <p>Your account may have been compromised. Please call {{ env('APP_NAME') }} Customer Service at (021) 111-222-729
        immediately.</p>

    <p>Thank You,</p>
    <p>{{ env('APP_NAME') }}</p>
</body>

</html>
