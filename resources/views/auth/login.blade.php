@include('includes._normalUserNavigation')

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Bidec Accounts Login</title>

  <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">

  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

  <style>
    body {
      background: linear-gradient(135deg, #00416A, #E4E5E6);
      font-family: 'Poppins', sans-serif;
      display: flex;
      align-items: center;
      justify-content: center;
      height: 100vh;
      margin: 0;
    }

    .login-container {
      background: #fff;
      width: 100%;
      max-width: 420px;
      border-radius: 15px;
      box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
      padding: 40px 30px;
      text-align: center;
      animation: fadeIn 0.8s ease-in-out;
    }

    .login-container img {
      width: 100px;
      margin-bottom: 15px;
    }

    .login-container h3 {
      font-weight: 600;
      color: #00416A;
      margin-bottom: 8px;
    }

    .login-container h3 span {
      color: #0078d7;
    }

    .login-container p {
      color: #666;
      font-size: 14px;
      margin-bottom: 25px;
    }

    .form-label {
      text-align: left;
      display: block;
      font-size: 14px;
      font-weight: 500;
      margin-bottom: 6px;
      color: #333;
    }

    .form-control {
      width: 100%;
      border: 1px solid #ccc;
      border-radius: 8px;
      padding: 10px 12px;
      font-size: 14px;
      outline: none;
      transition: all 0.3s ease;
    }

    .form-control:focus {
      border-color: #0078d7;
      box-shadow: 0 0 5px rgba(0, 120, 215, 0.3);
    }

    .input-wrapper {
      position: relative;
      margin-bottom: 20px;
    }

    .input-wrapper i {
      position: absolute;
      top: 50%;
      left: 12px;
      transform: translateY(-50%);
      color: #777;
      font-size: 15px;
    }

    .input-wrapper input {
      padding-left: 35px;
    }

    .help-block-c {
      color: #d9534f;
      font-size: 13px;
      margin-top: 4px;
      text-align: left;
      display: block;
    }

    .btn-login {
      background: #0078d7;
      color: #fff;
      border: none;
      border-radius: 8px;
      width: 100%;
      padding: 10px;
      font-size: 15px;
      font-weight: 600;
      transition: all 0.3s ease;
      margin-top: 5px;
    }

    .btn-login:hover {
      background: #005fa3;
      transform: translateY(-2px);
    }

    .forgot-password {
      display: block;
      margin-top: 15px;
      font-size: 13px;
      color: #0078d7;
      text-decoration: none;
      transition: color 0.3s;
    }

    .forgot-password:hover {
      color: #005fa3;
      text-decoration: underline;
    }

    .colorgraph {
      height: 5px;
      border-radius: 5px;
      background: linear-gradient(to right, #0078d7, #00416A, #0078d7);
      margin: 20px 0;
    }

    @keyframes fadeIn {
      from {
        opacity: 0;
        transform: translateY(-15px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    @media (max-width: 480px) {
      .login-container {
        padding: 30px 20px;
      }
      .login-container img {
        width: 80px;
      }
    }
  </style>
</head>

<body>
  <section class="login-sec">
    <div class="login-container">
      <form action="{{ route('login.custom') }}" method="POST" class="form-signin">
        {{ csrf_field() }}

        <h3>Welcome To <span>Bidec ERP</span></h3>
        <p>Please login to your account to continue</p>

        <div class="colorgraph"></div>

        <label class="form-label">Username</label>
        <div class="input-wrapper">
          <i class="fa fa-user"></i>
          <input id="username" type="text" name="username" class="form-control"
                 placeholder="Enter your username" value="{{ old('username') }}" autocomplete="off">
          @if ($errors->has('username'))
            <span class="help-block-c">{{ $errors->first('username') }}</span>
          @endif
        </div>

        <label class="form-label">Password</label>
        <div class="input-wrapper">
          <i class="fa fa-lock"></i>
          <input id="password" type="password" name="password" class="form-control"
                 placeholder="Enter your password" autocomplete="off">
          @if ($errors->has('password'))
            <span class="help-block-c">{{ $errors->first('password') }}</span>
          @endif
        </div>

        <button type="submit" class="btn-login" onclick="loader()">LOGIN <i class="fa fa-arrow-right ms-1"></i></button>

        <a href="{{ route('forgetPasswordForm') }}" class="forgot-password">Forgot Password?</a>
      </form>
    </div>
  </section>
</body>
</html>
