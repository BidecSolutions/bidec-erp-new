@include('includes._normalUserNavigation')

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Bidec Accounts Login</title>

  <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet" />

  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />

  <style>
    :root {
      --text-color: #00416A;
      --accent-color: #186f75;
      --bg_white: #ffffff;
    }

    /* 🌟 Body Styling */
    body {
      background: linear-gradient(135deg, #00416A, #E4E5E6);
      font-family: 'Poppins', sans-serif;
      display: flex;
      align-items: center;
      justify-content: center;
      height: 100vh;
      margin: 0;
    }

    /* 🌟 Preloader Container */
    #preloader {
      position: fixed;
      inset: 0;
      display: flex;
      align-items: center;
      justify-content: center;
      background: var(--bg_white);
      z-index: 9999;
      flex-direction: column;
      transition: opacity 0.6s ease, visibility 0.6s ease;
    }

    #preloader.hide {
      opacity: 0;
      visibility: hidden;
    }

    /* 🌟 Logo Animation Only (Spinner Removed) */
    .bidec-logo {
      position: relative;
      font-size: 8vw;
      font-weight: 800;
      text-transform: uppercase;
      color: transparent;
      -webkit-text-stroke: 2px var(--text-color);
      letter-spacing: 2px;
      overflow: hidden;
    }

    .bidec-logo::before {
      content: attr(data-text);
      position: absolute;
      top: 0;
      left: 0;
      width: 0%;
      height: 100%;
      color: var(--text-color);
      white-space: nowrap;
      overflow: hidden;
      border-right: 4px solid var(--text-color);
      animation: fillText 2.8s ease-in-out forwards;
    }

    @keyframes fillText {
      0% { width: 0; color: #fff; }
      50% { width: 100%; color: var(--text-color); }
      100% { width: 100%; color: var(--text-color); border-right: none; }
    }

    /* 🌟 Login Container */
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

    .login-container h3 {
      font-weight: 600;
      color: #000;
      margin-bottom: 8px;
    }

    .login-container h3 span {
      background: linear-gradient(to right, var(--accent-color), #0f4f54);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
    }

    .login-container p {
      color: #666;
      font-size: 14px;
      margin-bottom: 25px;
    }

    /* 🌟 Form Inputs */
    .form-label {
      display: block;
      font-size: 14px;
      font-weight: 500;
      margin-bottom: 6px;
      color: #333;
      text-align: left;
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
      border-color: var(--accent-color);
      box-shadow: 0 0 5px rgba(24, 111, 117, 0.3);
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

    /* 🌟 Buttons */
    .btn-login {
      background: linear-gradient(to right, var(--accent-color), #0f4f54);
      color: #fff;
      border: none;
      border-radius: 8px;
      width: 100%;
      padding: 10px;
      font-size: 15px;
      font-weight: 600;
      transition: all 0.3s ease;
      margin-top: 5px;
      cursor: pointer;
    }

    .btn-login:hover {
      background: linear-gradient(90deg, #0f4f54, #186f75);
      transform: translateY(-2px);
    }

    /* 🌟 Forgot Password */
    .forgot-password {
      display: block;
      margin-top: 15px;
      font-size: 13px;
      color: var(--accent-color);
      text-decoration: none;
      transition: color 0.3s;
    }

    .forgot-password:hover {
      color: #0f4f54;
      text-decoration: underline;
    }

    /* 🌟 Separator Line */
    .colorgraph {
      height: 5px;
      border-radius: 5px;
      background: linear-gradient(to right, #186f75bf, #186f75, #186f75bf);
      margin: 20px 0;
    }

    /* 🌟 Animations */
    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(-15px); }
      to { opacity: 1; transform: translateY(0); }
    }

    @media (max-width: 480px) {
      .login-container {
        padding: 30px 20px;
      }
      .bidec-logo {
        font-size: 12vw;
      }
    }
  </style>
</head>

<body>
  <!-- 🌟 Preloader (Spinner Removed) -->
  <div id="preloader">
    <div class="preloader-content">
      <h3 class="bidec-logo" data-text="BIDEC..">BIDEC..</h3>
    </div>
  </div>

  <!-- 🌟 Login Section -->
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
                 placeholder="Enter your username" value="{{ old('username') }}" autocomplete="off" />
          @if ($errors->has('username'))
            <span class="help-block-c">{{ $errors->first('username') }}</span>
          @endif
        </div>

        <label class="form-label">Password</label>
        <div class="input-wrapper">
          <i class="fa fa-lock"></i>
          <input id="password" type="password" name="password" class="form-control"
                 placeholder="Enter your password" autocomplete="off" />
          @if ($errors->has('password'))
            <span class="help-block-c">{{ $errors->first('password') }}</span>
          @endif
        </div>

        <button type="submit" class="btn-login">
          LOGIN <i class="fa fa-arrow-right ms-1"></i>
        </button>

        <a href="{{ route('forgetPasswordForm') }}" class="forgot-password">Forgot Password?</a>
      </form>
    </div>
  </section>

  <!-- 🌟 Fade Out Script -->
  <script>
    window.addEventListener("load", function () {
      const loader = document.getElementById("preloader");
      setTimeout(() => loader.classList.add("hide"), 600);
    });
  </script>
</body>
</html>
