
<?php
if (isset($_SESSION['user_id'])) {
    header('Location: dashboard.php');
    exit();
}

require 'csrf.php';
?>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

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
        <div data-mdb-input-init class="form-outline mb-4">
        <label class="form-label" for="username">Username/Email</label>
        <input type="hidden" name="csrf_token" id="csrf_token" value="<?php echo generateCsrfToken(); ?>">
          <input type="email" id="username" placeholder="Enter Username/email" class="form-control" />
          <div id="usernameError"></div>
          
        </div>

        <!-- Password input -->
        <div data-mdb-input-init class="form-outline mb-4">
        <label class="form-label" for="password">Password</label>
          <input type="password" id="password" placeholder="Enter the password"class="form-control"/>
          <div id="passwordError"></div>
        </div>

        <!-- 2 column grid layout for inline styling -->
        <div class="row mb-4">
          <div class="col d-flex justify-content-center">
            <!-- Checkbox -->
            <div class="form-check">
              <input class="form-check-input" type="checkbox" id="rememberMe"/>
              <label class="form-check-label" for="rememberMe"> Remember me </label>
            </div>
          </div>

          <div class="col">
            
          </div>
        </div>

        <!-- Submit button -->
        <button type="button" id="submitBtn" class="btn btn-primary btn-block mb-4 w-100">Sign in</button>

        <!-- Register buttons -->
        <div class="text-center">
          <p>Not a member? <a href="http://localhost/Authentication_project/register.php">Register</a></p>
          
        </div>
      </form>
    </div>
  </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script>
    $(document).ready(function() {

        $('#username').on('keyup', function() 
        {
            validateInput('username');
        });
        $('#password').on('keyup', function() 
        {
            validateInput('password');
        });
        // Add keyup event listeners for real-time validation



        $('#submitBtn').on('click', function() 
        {
            $('#submitBtn').html('<span class="spinner-border spinner-border-sm me-2" role="status"></span>signing in...').attr('disabled', true);
            // Clear existing error messages
            $('.error-message').html('');
            var isValid = true;

            isValid = validateInput('password') && isValid;
            isValid = validateInput('username') && isValid;
            if(isValid) 
            {
                $('#submitBtn').html('<span class="spinner-border spinner-border-sm me-2" role="status"></span>signing in...').attr('disabled', true);

                var csrfToken = $("#csrf_token").val();

                // Send data using AJAX
                $.ajax({
                    type: 'POST',
                    url: 'http://localhost/Authentication_project/checkUser.php',
                    data: 
                    {
                        csrf_token: csrfToken,
                        username: $('#username').val(),
                        password: $('#password').val(),
                        rememberMe: $('#rememberMe').is(':checked') ? 1 : 0,
                        
                    },
                    beforeSend: function() 
                    {
                        $('#submitBtn').html('<span class="spinner-border spinner-border-sm me-2" role="status"></span>signing in...').attr('disabled', true);
                    },
                    success: function(response) 
                    { var arrayResult = JSON.parse(response);
                        if (arrayResult.success) 
                        {
                            $('.response').removeClass('alert-warning').addClass('alert-success').show();
                            $('.responseIcon').html('<svg xmlns="http://www.w3.org/2000/svg" class="icon alert-icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><path d="M5 12l5 5l10 -10"></path></svg>');
                            $('.responseTxt').html('Login successfully!');

                           

                            setTimeout(function() 
                            {

                                window.location.replace('http://localhost/Authentication_project/dashboard.php');
                                
                            }, 2000);

                        } 
                        else 
                        {
                            
                            let errorMessage="";
                            if(arrayResult.errors=="common_error" || arrayResult.errors=="csrf" || arrayResult.errors== "invalid")
                            {
                                errorMessage = arrayResult.message;

                            }
                            else
                            {
                                errorMessage = 'An error occurred during login.';
                            }
                            $('.response').removeClass('alert-success').addClass('alert-warning').show();
                            $('.responseIcon').html('<svg xmlns="http://www.w3.org/2000/svg" class="icon alert-icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><path d="M3 12a9 9 0 1 0 18 0a9 9 0 0 0 -18 0"></path><path d="M12 8v4"></path><path d="M12 16h.01"></path></svg>');
                            $('.responseTxt').html(errorMessage);
                            // alert(errorMessage);
                            // Hide the response after 3 seconds
                            setTimeout(function() {
                                $('.response').hide();
                            }, 3000);
                            $('#submitBtn').html('Sign in').attr('disabled', false);


                        }
                    },
                    complete: function() 
                    {
                        // Enable the submit button after request completion
                        $('#submitBtn').html('Sign in').attr('disabled', false);
                    }
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

                if(field == "username")
                {
                    message="Username / email is required";
                }
                else
                {
                    message=field.capitalize() + ' is required';
                }

                showError(field, message);
                return false;
            } 
            else 
            {
                hideError(field);
                return true;
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
