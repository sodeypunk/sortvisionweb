<!-- Main jumbotron for a primary marketing message or call to action -->

<div class="container sortvision-container">
    <div id = "loginbox" class="panel panel-primary" >
        <div class="panel-heading" > Sign in </div>
        <div class="panel-body" >
            <div class="row" >
                <div class="col-md-12" >
                    <form id = "loginform" class="form-horizontal" role = "form" method="post" action="<?php echo base_url(); ?>index.php/account/login">

                        <?php
                        if (!empty($errorMsg)) {
                            echo '<div id = "loginalert" class="alert alert-danger">';
                            echo '<p> Error:</p>';
                            echo '<span>' . $errorMsg . '</span>';
                            echo '</div>';
                        }
                        ?>

                        <div style = "margin-bottom: 25px" class="input-group" >
                            <span class="input-group-addon" ><i class="glyphicon glyphicon-user" ></i></span>
                            <input id = "login-email" type = "text" class="form-control" name = "email" value = "" placeholder = "email" >
                        </div>

                        <div style = "margin-bottom: 25px" class="input-group" >
                            <span class="input-group-addon" ><i class="glyphicon glyphicon-lock" ></i></span>
                            <input id = "login-password" type = "password" class="form-control" name = "password" placeholder = "password" >
                        </div>

                        <input type="hidden" name="token">

                        <div style = "margin-top:10px" class="form-group" >
                            <!--Button -->
                            <div class="col-sm-12 controls" >
                                <a id = "btn-login" href = "#" class="btn btn-success" onClick = "LoginUser();" > Login  </a>
<!--                                <button class="btn btn-primary" type="submit">Login</button>-->
                            </div>
                        </div>

                        <div class="form-group" >
                            <div class="col-md-12 control" >
                                <div style = "border-top: 1px solid#888; padding-top:15px; font-size:85%" >
                                    Don't have an account?
                                    <a href="register" onClick="$('#loginbox').hide(); $('#signupbox').show()">
                                        Sign Up Here
                                    </a>
                                </div>
                            </div>
                        </div>
                    </form >
                </div>
            </div>
        </div>
    </div>

    <div id="updatebox" style="display:none" class="panel panel-info">
        <div class="panel-heading">Update Name and Password</div>
        <div class="panel-body">
            <div class="row">
                <div class="col-md-12">
                    <form id="update-name-password-form" class="form-horizontal" role="form">

                        <div id="signupalert" style="display:none" class="alert alert-danger">
                            <p>Error:</p>
                            <span></span>
                        </div>

                        <div class="form-group">
                            <label for="email" class="col-md-2 control-label">Email</label>
                            <div class="col-md-10">
                                <input type="text" class="form-control" name="email" placeholder="Email Address">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="firstname" class="col-md-2 control-label">First Name</label>
                            <div class="col-md-10">
                                <input type="text" class="form-control" name="firstname" placeholder="First Name">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="lastname" class="col-md-2 control-label">Last Name</label>
                            <div class="col-md-10">
                                <input type="text" class="form-control" name="lastname" placeholder="Last Name">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="oldpasswd" class="col-md-2 control-label">Old Password</label>
                            <div class="col-md-10">
                                <input type="password" class="form-control" name="oldpasswd" placeholder="Password">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="newpasswd" class="col-md-2 control-label">New Password</label>
                            <div class="col-md-10">
                                <input type="password" class="form-control" name="newpasswd" placeholder="Password">
                            </div>
                        </div>

                        <div class="form-group">
                            <!-- Button -->
                            <div class="col-md-offset-2 col-md-10">
                                <button id="btn-signup" type="button" class="btn btn-info" onClick="UpdateUserPassword();"><i class="icon-hand-right"></i>Update</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function LoginUser() {
        $("#loginalert").hide();

        var email = $("#loginbox").find('input[name=email]').val();
        var password = $("#loginbox").find('input[name=password]').val();

        var result = AccountController.LoginUser(email, password, function(result){

            if (result.error === false)
            {
                $("#loginbox").find('input[name=token]').val(result.token);
                $("#loginform").submit();

            }
            else if (result.error === true && result.newPassword === true)
            {
                alert("New password required");
            }
            else if (result.error === true)
            {
                $("#loginalert span").text(result.message);
                $("#loginalert").show();
            }

        });
    }
</script>