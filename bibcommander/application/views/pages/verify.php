<!-- Main jumbotron for a primary marketing message or call to action -->

<div class="container">
    <h1>BibSmart</h1>
    <p>Auto bib detection and number recognition software.</p>

    <br/>
    <div id="registerbox" class="panel panel-info">
        <div class="panel-heading">Verify Email</div>
        <div class="panel-body">
            <div class="row">
                <div class="col-md-12">
                    <form id="register-form" class="form-horizontal" role="form">

                        <div id="verifyalert" style="display:none" class="alert alert-danger">
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
                            <label for="code" class="col-md-2 control-label">Code</label>
                            <div class="col-md-10">
                                <input type="text" class="form-control" name="code" placeholder="Code" value="<?php echo $code; ?>">
                            </div>
                        </div>

                        <div class="form-group">
                            <!-- Button -->
                            <div class="col-md-offset-2 col-md-10">
                                <button id="btn-signup" type="button" class="btn btn-info" onClick="VerifyUser();"><i class="icon-hand-right"></i>Submit</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function VerifyUser() {
        $("#verifyalert").hide();

        var email = $("#registerbox").find('input[name=email]').val();
        var firstName = $("#registerbox").find('input[name=firstname]').val();
        var lastName = $("#registerbox").find('input[name=lastname]').val();
        var password = $("#registerbox").find('input[name=passwd]').val();

        var result = Register(email, firstName, lastName, password, function(result){

            if (result.error == true)
            {
                $("#registeralert span").text(result.message);
                $("#registeralert").show();
            }
            else
            {
                $("#successalert span").text(result.message);
                $("#successalert").show();
            }

        });
    }
</script>