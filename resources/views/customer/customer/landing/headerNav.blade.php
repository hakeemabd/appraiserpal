<!--navigation-->

<div class="full-container paddingLR">
<div class="container paddingLR">

<div id="header">

<nav class="navbar">
  <div class="container-fluid">
    <div class="navbar-header">
    <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#myNavbar">
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>                        
      </button>
      <a class="navbar-brand" href="#">
      <img src="images/logo.png" />
      </a>
    </div>
    <div class="collapse navbar-collapse" id="myNavbar">
    <ul class="nav navbar-nav  col-lg-offset-3">
      <li class="active"><a href="#features">Features</a></li>
      <li><a href="#reviews">Reviews</a></li>
      <li><a href="#pricing">Pricing</a></li>
    </ul>
    <ul class="nav navbar-nav navbar-right">
      <li><a href="#">866-800-1388</a></li>
      <li><a href="#" data-toggle="modal" data-target="#myModal"><i class="fa fa-user" aria-hidden="true"></i> Login</a></li>
    </ul>
    </div>
  </div>
</nav>
</div>
</div>
@if($errors->any())
<?php 
  $message = $errors->first();
  echo "<script type='text/javascript'>alert('$message');</script>";
  $errors = null;
?>
@endif
<!--navigation end-->