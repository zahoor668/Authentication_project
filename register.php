
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
  <title>Registration Form</title>
</head>
<body>

<?php
// Include the AuthenticationFile.php
require 'csrf.php';

// Create an instance of AuthenticationFile


?>
<div class="container">
  <div class="row justify-content-center">
    <div class="col-md-6 col-lg-4">
      <form class="mt-5 p-4 border rounded shadow-sm bg-light">
        <!-- UserName input -->
        <div class="col-12 mx-auto alert alert-warning alert-dismissible response" role="alert" style="display:none">
            <div class="d-flex">
                <div class="responseIcon"></div>
                <div class="responseTxt"></div>
            </div>
        </div>
        <div class="form-outline mb-4">
            <input type="hidden" name="csrf_token" id="csrf_token" value="<?php echo generateCsrfToken(); ?>">
            <label class="form-label" for="username">Username</label>
            <input type="text" id="username" placeholder="Enter Username" class="form-control" />
            <div id="usernameError"></div>
        </div>

        <!-- Email input -->
        <div class="form-outline mb-4">
            <label class="form-label" for="email">Email address</label>
            <input type="email" id="email" placeholder="Enter Email ID" class="form-control" />
            <div id="emailError"></div>
        </div>

        <!-- Password input -->
        <div class="form-outline mb-4">
            <label class="form-label" for="password">Password</label>
            <input type="password" id="password" placeholder="Enter Password" class="form-control" />
            <div id="passwordError"></div>
        </div>

        <!-- Submit button -->
        <button data-mdb-ripple-init type="button" id="submitBtn" class="btn btn-primary btn-block mb-4 w-100">Sign up</button>

        <!-- Register buttons -->
        <div class="text-center">
          <p>Already a member? <a href="http://localhost/Authentication_project/login.php">Login</a></p>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- MDB Initialization -->

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script>
    $(document).ready(function() 
    {

        $('#username').on('keyup', function() 
        {
            validateInput('username');
        });

        $('#email').on('keyup', function() 
        {
            validateEmail();
        });

        $('#password').on('keyup', function() 
        {
            validateInputPassword('password');
        });

        $('#submitBtn').on('click', function() 
        {
            $('#submitBtn').html('<span class="spinner-border spinner-border-sm me-2" role="status"></span>signing in...').attr('disabled', true);
            $('.error-message').html('');
            var isValid = true;

            isValid = validateInput('username') && isValid;
            isValid = validateEmail() && isValid;
            isValid = validateInputPassword('password') && isValid;
            if(isValid) 
            {
                $('#submitBtn').html('<span class="spinner-border spinner-border-sm me-2" role="status"></span>signing in...').attr('disabled', true);

                var csrfToken = $("#csrf_token").val();
                $.ajax({
                    type: 'POST',
                    url: 'http://localhost/Authentication_project/userAccountSave.php',
                    data: 
                    {
                        csrf_token: csrfToken,
                        username: $('#username').val(),
                        email: $('#email').val(),
                        password: $('#password').val(),
                        
                    },
                    success: function(response) 
                    {
                        // alert(response);
                        var arrayResult = JSON.parse(response);
                        if (arrayResult.success) 
                        {
                            $('.response').removeClass('alert-warning').addClass('alert-success').show();
                            $('.responseIcon').html('<svg xmlns="http://www.w3.org/2000/svg" class="icon alert-icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><path d="M5 12l5 5l10 -10"></path></svg>');
                            $('.responseTxt').html('Registered successfully!');
                            
                            setTimeout(function() 
                            {

                                window.location.replace('http://localhost/Authentication_project/login.php');
                                
                            }, 3000);
                                
                        } 
                        else 
                        {
  
                            if(arrayResult.errors=="account_exist" || arrayResult.errors=="common_error" || arrayResult.errors=="csrf")
                            {
                                $('.response').removeClass('alert-success').addClass('alert-warning').show();
                                let errorMessage = arrayResult.message;


                                $('.responseIcon').html('<svg xmlns="http://www.w3.org/2000/svg" class="icon alert-icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><path d="M3 12a9 9 0 1 0 18 0a9 9 0 0 0 -18 0"></path><path d="M12 8v4"></path><path d="M12 16h.01"></path></svg>');
                                $('.responseTxt').html(errorMessage);

                                // Hide the response after 3 seconds

                                setTimeout(function() {
                                    $('.response').hide();
                                }, 3000);

                            }
                            else if(arrayResult.errors=="email_error")
                            {
                                showError('email', arrayResult.message);
                            }
                            else if(arrayResult.errors=="password_error")
                            {
                                showError('password',arrayResult.message);
                            }
                            else
                            {

                                $('.response').removeClass('alert-success').addClass('alert-warning').show();
                                let errorMessage = 'An error occurred during login.';

                                if (response.errors) {
                                    // If errors are present, concatenate all error messages into one string
                                    const errorMessages = Object.values(response.errors).join('');
                                    errorMessage = errorMessages;
                                }


                                $('.responseIcon').html('<svg xmlns="http://www.w3.org/2000/svg" class="icon alert-icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><path d="M3 12a9 9 0 1 0 18 0a9 9 0 0 0 -18 0"></path><path d="M12 8v4"></path><path d="M12 16h.01"></path></svg>');
                                $('.responseTxt').html(errorMessage);

                                // Hide the response after 3 seconds
                                setTimeout(function() {
                                    $('.response').hide();
                                }, 3000);

                            }
                            $('#submitBtn').html('Sign in').attr('disabled', false);
                        }
                    },
                });
            }
            else{
                $('#submitBtn').html('Sign in').attr('disabled', false);
            }
        });

        function validateInput(field) 
        {
            
            
            var value = $('#' + field).val().trim();
            if (value === '') 
            {
                var message="";

                
                message=field.capitalize() + ' is required';
                

                showError(field, message);
                return false;
            } 
            else 
            {
                hideError(field);
                return true;
            }
        }

        function validateEmail() 
        {
            var emailValue = $('#email').val().trim();
            var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

            if (!emailRegex.test(emailValue)) {
                showError('email', 'Enter a valid email address');
                return false;
            } else {
                hideError('email');
                return true;
            }
        }
        function validateInputPassword(field) 
        {
            var value = $('#' + field).val().trim();
            // var password_format = /^(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}$/;
            if (value.length >= 6) 
            {
                hideError(field);
                return true;
                
            } 
            else 
            {
                showError(field, field.capitalize() + 'must be at least 6 characters.');
                return false;
            }
        }

        function showError(field, message) 
        {
            $('#' + field + 'Error').addClass('text-danger small mt-1').html(message);
            $('#' + field).addClass('is-invalid');
        }

        function hideError(field) 
        {
            $('#' + field + 'Error').removeClass('text-danger small mt-1').html('');
            $('#' + field).removeClass('is-invalid');
        }

        String.prototype.capitalize = function() {
            return this.charAt(0).toUpperCase() + this.slice(1);
        };
    });
</script>

<script type="module">
  import { Input, Ripple, initMDB } from "mdb-ui-kit"; 
  initMDB({ Input, Ripple });
</script>

</body>
</html>
