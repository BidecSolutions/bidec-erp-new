@include('includes._normalUserNavigation')
<style>
  .help-block-c{
    height: 0px !important;
    top: unset !important;
  }
</style>
<section class="login-sec">
  <div class="container">
    <div class="row">

        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
          <div class="login-fwrp p1">
            <form action="{{ route('forget_password') }}" method="POST" class="form-signin">
            {{ csrf_field() }}
              <div class="l-logo">
                <img src="./assets/img/logo.png" style="width: 25%;">
              </div>
                <h3>Forget Password</span></h3>
                <span id="colorgraph"><hr class="colorgraph"></span>
                 <label>Email</label>
                  <div class="inner-addon left-addon">

                      <span><i class="fal fa-envelope"></i></span>
                      <input id="email" type="text" class="form-control singInput" name="email" value="{{ old('email') }}" autocomplete="off" placeholder="Company email" />
                      @if ($errors->has('email'))
                  <span class="help-block help-block-c">
                      <strong>{{ $errors->first('email') }}</strong>
                    </span>
                @endif
              </div>
              <br>
                <button type="submit" class="btn login-btn" onclick="loader()">Submit  <i class="fal fa-arrow-right"></i></button>
               <div class="fga">
                            <a href="{{route('login')}}">Sign In</a>
                          </div>
            </form>
          </div>
        </div>
    </div>
</div>
</section>
<!-- <footer class="login-footer">
	<div class="container">
    	<div class="row text-center">
        	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            	<p>&copy; <?php echo date('Y')?> Bidec Accounts | All rights reserved.</p>
            </div>
        </div>
    </div>
</footer> -->
</body>
</html>
