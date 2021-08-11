<!doctype html>
<html lang="en">
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=yes">
    <title>Create an Account</title>

    <script type="text/javascript" src="/models/OrionAjax.js"></script>
    <script type="text/javascript" src="/models/OrionForm.js"></script>
    <script type="text/javascript" src="/controllers/user/authenticate/register.js"></script>
    <link rel="stylesheet" type="text/css" href="/views/css/register.css">

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

<?php include($_SERVER['DOCUMENT_ROOT'] . '/views/include/header.php'); ?>
<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/models/OrionRecord.php');
$orion = new OrionRecord('view_count', 'record_id');
$orion->get('page', 'registration');
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
            <label class="title container-fluid">Create an Account</label>
            <p class="secondary-title container-fluid">Create an account for free.&nbsp;
                <a style="text-decoration: underline; color: white" href="/views/user/login.php">Already have an
                    account?</a>
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
            <form id="form" onsubmit="validateRegistration(event);" novalidate>

                <div class="row">

                    <div class="form-group col-md-12">
                        <label for="username" id="username_label">Username</label>
                        <input type="text" name="username" class="form-control" id="username" minlength="1"
                               maxlength="40" required placeholder="Username">
                        <small id="username_helper" class="helper-text">Between 5 and 20 alphanumeric and underscore characters</small>
                    </div>


                    <div class="form-group col-md-12">
                        <label for="email" id="email_label">Email</label>
                        <input type="text" id="email" name="email" class="form-control" required minlength="5"
                               maxlength="50" placeholder="Email">
                        <small id="email_helper" class="helper-text" style="visibility: hidden">Please enter a valid email</small>
                    </div>



                    <div class="form-group col-md-12">
                        <label for="password" id="password_label" class="formText">Password</label>
                        <input type="password" id="password" name="password" class="form-control" minlength="8"
                               maxlength=20 required placeholder="Password">
                        <small id="password_helper" class="helper-text">Must be 8-20 characters long </small>
                    </div>


                    <div class="form-group col-md-12">
                        <label for="confirm_password" id="confirm_password_label" class="formText">Confirm Password</label>
                        <input type="password" id="confirm_password" name="confirm_password" class="form-control"
                               minlength="8" maxlength=20 required placeholder="Confirm Password">
                        <small id="confirm_password_helper" class="helper-text">Passwords must match </small>
                    </div>

                    <!--
                    <div class="form-group col-md-12">
                        <label for="first_name" id="first_name_label" style="color: grey">First Name (Optional)</label>

                        <input type="text" name="first_name" class="form-control" id="first_name" minlength="1"
                               maxlength="40" required placeholder="First Name">
                    </div>


                    <div class="form-group col-md-12">
                        <label for="last_name" id="last_name_label" style="color: grey">Last Name (Optiona)</label>
                        <input type="text" name="last_name" class="form-control" id="last_name" minlength="1"
                               maxlength="40" required placeholder="Last Name">
                    </div>
                    -->
                </div>


                <!--<div class="g-recaptcha" style="padding-top: 3vh" data-sitekey="6LcUsQEaAAAAANJl_angjyJNoV49KO42pxSzm3AP"></div>-->


                <!--
                <div class="checkbox" style="padding-top:3vh">
                    <label>
                        <input type="checkbox" id="terms" required>
                        &nbsp;I agree that I am not using the same password as accounts for other websites that I use.
                    </label>
                    <small id="terms_helper" style="color:red; visibility: hidden">Please check this box to continue</small>
                </div>
                -->


                <br>
                <!-- Submit Button -->
                <div class="submit">
                    <button type="submit" class="btn btn-primary btn-default center-element" id="submit">Submit</button>
                </div>

                <div class="form-group col-md-12">
                    <small id="registration_error" style="color:red; visibility: hidden"></small>
                </div>


            </form>
            <!--  form end  -->
        </div>
        <div class="col-md-3 col-sm-1 col-xs-2"></div>
    </div>
</div>

</body>


</html>

