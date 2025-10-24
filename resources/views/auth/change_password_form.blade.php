@extends('layouts.layouts')
@section('content')
<div class="well_N">
    <div class="boking-wrp dp_sdw">
        <form action="{{ route('changePasswordTwo') }}" method="POST" class="form-signin">
            {{ csrf_field() }}
            <h3>Change Password</h3>
            <span id="colorgraph">
                <hr class="colorgraph">
            </span>


            <div class="inner-addon left-addon">
                <div class="form-control" style="display: flex; align-items: center; gap: 10px; position: relative;">
                    <i class="fal fa-unlock-alt loginicons"></i>
                    <input id="old_password" type="password" class="password-input form-control" name="old_password"
                        autocomplete="off" placeholder="Enter Old Password" />
                    <i class="fal fa-eye password-toggle old_password-toggle"
                        onclick="toggleOldPasswordVisibility()"></i>
                </div>
            </div>
            <br>
            <div class="inner-addon left-addon">
                <div class="form-control" style="display: flex; align-items: center; gap: 10px; position: relative;">
                    <i class="fal fa-unlock-alt loginicons"></i>
                    <input id="new_password" type="password" class="password-input form-control" name="password" autocomplete="off"
                        placeholder="Enter New Password" />
                    <i class="fal fa-eye password-toggle new_password-toggle"
                        onclick="toggleNewPasswordVisibility()"></i>
                </div>
            </div>
            <br>

            <div class="inner-addon left-addon">
                <div class="form-control" style="display: flex; align-items: center; gap: 10px; position: relative;">
                    <i class="fal fa-unlock-alt loginicons"></i>
                    <input id="confirm_password" type="password" name="password_confirmation" autocomplete="off"
                        placeholder="Confirm Password" class="password-input form-control" />
                    <i class="fal fa-eye password-toggle confirm_password-toggle"
                        onclick="toggleConfirmPasswordVisibility()"></i>
                </div>
            </div>

            <br>
            <!-- Submit Button -->
            <button type="submit" class="btn login-btn">Submit <i class="fal fa-arrow-right"></i></button>
        </form>
    </div>
</div>
@endsection
@section('script')
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const newPassword = document.getElementById("new_password");
        const confirmPassword = document.getElementById("confirm_password");
        const submitButton = document.querySelector(".login-btn");
 
        // Disable submit button initially
        submitButton.disabled = true;
        submitButton.style.opacity = "0.5"; // Optional: Indicate it's disabled

        function validatePasswords() {
            if (newPassword.value === confirmPassword.value && newPassword.value !== "") {
                submitButton.disabled = false;
                submitButton.style.opacity = "1"; // Optional: Make it look active
            } else {
                submitButton.disabled = true;
                submitButton.style.opacity = "0.5";
            }
        }

        // Attach input event listeners
        newPassword.addEventListener("input", validatePasswords);
        confirmPassword.addEventListener("input", validatePasswords);
    });

    function toggleOldPasswordVisibility() {
        const passwordInput = document.getElementById('old_password');
        const eyeIcon = document.querySelector('.old_password-toggle');

        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            eyeIcon.classList.replace('fa-eye', 'fa-eye-slash');
        } else {
            passwordInput.type = 'password';
            eyeIcon.classList.replace('fa-eye-slash', 'fa-eye');
        }
    }

    function toggleNewPasswordVisibility() {
        const passwordInput = document.getElementById('new_password');
        const eyeIcon = document.querySelector('.new_password-toggle');

        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            eyeIcon.classList.replace('fa-eye', 'fa-eye-slash');
        } else {
            passwordInput.type = 'password';
            eyeIcon.classList.replace('fa-eye-slash', 'fa-eye');
        }
    }

    function toggleConfirmPasswordVisibility() {
        const passwordInput = document.getElementById('confirm_password');
        const eyeIcon = document.querySelector('.confirm_password-toggle');

        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            eyeIcon.classList.replace('fa-eye', 'fa-eye-slash');
        } else {
            passwordInput.type = 'password';
            eyeIcon.classList.replace('fa-eye-slash', 'fa-eye');
        }
    }
</script>
@endsection