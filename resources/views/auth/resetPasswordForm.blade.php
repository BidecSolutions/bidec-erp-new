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
            <form action="{{ route('reset_password') }}" method="POST" class="form-signin">
                {{ csrf_field() }}
                <input type="hidden" name="token" id="token" value="{{$token}}" />
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
              <br>
                     <label>Password</label>
                    <div class="inner-addon left-addon">
                      <span><i class="fal fa-unlock-alt"></i></span>
                      <input id="password" type="password" class="form-control singInput" name="password" autocomplete="off"  placeholder="Password" />
                      @if ($errors->has('password'))
          <span class="help-block help-block-c">
            <strong>{{ $errors->first('password') }}</strong>
          </span>
        @endif
                  </div>
                  <br>
                  <br>
                     <label>Confirm Password</label>
                    <div class="inner-addon left-addon">
                      <span><i class="fal fa-unlock-alt"></i></span>
                      <input id="password" type="password" class="form-control singInput" name="password_confirmation" autocomplete="off"  placeholder="Confirm Password" />
                      @if ($errors->has('password_confirmation'))
          <span class="help-block help-block-c">
            <strong>{{ $errors->first('password_confirmation') }}</strong>
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
