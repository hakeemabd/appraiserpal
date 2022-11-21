<!--welcome message-->

<div class="home_banner">
    <div class="container paddingLR">
    <div class="col-sm-6 banner_content">
    <p>
    Innovative Customized <br />Appraisal data entry <br/>services for appraisers
    </p>
    <a href="#" style="cursor: default;" class="btn btn-default btn-submit">Get my 1 written appraisal report free</a>
    </div>
    <div class="col-sm-1">&nbsp;</div>
    <div class="col-sm-5 header_form">
    <form role="form" data-toggle="validator" action="{{ route('customer:register') }}" method="post">
      <h2>Get Started</h2>
      @if (session('status'))
          <div class="alert alert-success">
              {{ session('status') }}
          </div>
      @endif
      <div class="form-group">
        <input type="hidden" name="_token" value="{{ csrf_token() }}">
        <input type="email" class="form-control" id="email" name="email" placeholder="Email Address (Username)" required>
      </div>
         <div class="form-group">

        <input type="text" class="form-control" id="phone" name="mobile_phone" placeholder="Cell Phone Number" required>
      </div>
      <div class="form-group">
         <input type="password"  class="form-control" id="pass" name="password" placeholder="Password" required>
      </div>
      <div class="form-group">
         <input type="password"  class="form-control" id="pass" name="password_confirmation" placeholder="Confirm Password" required>
      </div>
      <button type="submit" class="btn btn-default btn-submit">Register</button>
    </form>
    </div>
    </div>
</div>
</div>

<!--welcome message end-->