<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Email Reset Password Request</title>
</head>
<body>
        <h1>{{ $data['heading'] }}</h1>
        <strong>Dear {{ $data['name'] }}</strong>
        <p>Your verification code to reset password is</p>
        <br>
        <p>{{ $data['code'] }}</p>


        Regards {{ env ('APP_NAME') }}
</body>
</html>