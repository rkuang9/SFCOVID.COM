<!doctype html>
<html lang="en">
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=yes">
    <title>Sign in to Your Account</title>

    <script type="text/javascript" src="/models/OrionAjax.js"></script>
    <script type="text/javascript" src="/models/OrionForm.js"></script>
    <script type="text/javascript" src="/controllers/user/authenticate/login.js"></script>
    <link rel="stylesheet" type="text/css" href="/views/css/login.css">

    <script src="https://www.google.com/recaptcha/api.js"></script>

    <!-- Bootstrap and related JS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css"
          integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"
            integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo"
            crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"
            integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1"
            crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"
            integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM"
            crossorigin="anonymous"></script>
</head>

<?php
include($_SERVER['DOCUMENT_ROOT'] . '/views/include/header.php');
if (isset($_SESSION['username'])) redirectTo('/views/index.php');
?>
<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/models/OrionRecord.php');
$orion = new OrionRecord('view_count', 'record_id');
$orion->get('page', 'login');
$orion->views++;
$orion->update();

$visits = new OrionRecord('page_user_count', 'record_id');
$visits->page = basename(__FILE__);
$visits->ip_address = $_SERVER['REMOTE_ADDR'];
$visits->insert();
?>



<body>

<div class="banner-title container-fluid">
    <div class="row">
        <div class="col-md-3 col-sm-1 col-xs-2"></div>
        <div class="col-md-6 col-sm-10 col-xs-8">
            <label class="title">Login</label>
            <p class="secondary-title">Sign in to your account.&nbsp;
                <a style="text-decoration: underline; color: white" href="/views/user/register.php">Register for one here.</a>
                <!--<a style="text-decoration: underline; color: white" href="">Forgot Password?</a>-->
            </p>
        </div>
        <div class="col-md-3 col-sm-1 col-xs-2"></div>
    </div>
</div>


<div class="container-fluid" style="padding-top:3vh;">

    <div class="row">
        <div class="col-md-3 col-sm-1 col-xs-2"></div>
        <div class="col-md-6 col-sm-10 col-xs-8" style="background-color: #ffffff">

            <!-- form start --><!--action="/controllers/user/login.php"-->
            <form id="form" onsubmit="validateLogin(event)">
                <!-- First Name and Last Name -->
                <div class="row">

                    <div class="form-group col-md-12">
                        <label for="login">Email or Username</label>
                        <input type="text" id="login" name="login" class="form-control" required maxlength="40"
                               placeholder="Email or Username">
                    </div>

                    <div class="form-group col-md-12">
                        <label for="password" class="formText">Password</label>
                        <input type="password" id="password" name="password" class="form-control" required
                               placeholder="Password">
                    </div>

                    <div class="form-group col-md-12">
                        <small id="invalid_login" style="color:red; visibility: hidden">Invalid login and/or password</small>
                    </div>


                </div>


                <!--<div class="g-recaptcha" style="padding-top: 3vh" data-sitekey="6LcUsQEaAAAAANJl_angjyJNoV49KO42pxSzm3AP"></div>-->



                <!-- Submit Button -->
                <div class="submit">
                    <button type="submit" class="btn btn-primary btn-default center-element" id="submit">Submit</button>
                </div>
                <br>


            </form>
            <!--  form end  -->
        </div>
        <div class="col-md-3 col-sm-1 col-xs-2"></div>
    </div>
</div>

</body>


</html>

