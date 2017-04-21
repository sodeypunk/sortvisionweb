<!-- Main jumbotron for a primary marketing message or call to action -->

<div class="container">
    <h1>BibSmart</h1>
    <p>Auto bib detection and number recognition software.</p>

    <br/>
    <div id="verifybox" class="panel panel-info">
        <div class="panel-heading">Verify Email</div>
        <div class="panel-body">
            <div class="row">
                <div class="col-md-12">
                    <form id="register-form" class="form-horizontal" role="form">

                        <div id="verifyalert" style="display:none" class="alert alert-danger">
                            <p>Error:</p>
                            <span></span>
                        </div>

                        <input type="hidden" name="username" value="<?php echo $username; ?>">

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

    <div id="successbox" style="display: none" class="panel panel-info">
        <div class="panel-heading">Verify Email</div>
        <div class="panel-body">
            <div class="row">
                <div class="col-md-12">
                    <div id="successalert" class="alert alert-success">
                        <span></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function VerifyUser() {
        $("#verifyalert").hide();

        var username = $("#verifybox").find('input[name=username]').val();
        var code = $("#verifybox").find('input[name=code]').val();

        var result = AccountController.VerifyUser(username, code, function(result){

            if (result.error == true)
            {
                $("#verifyalert span").text(result.message);
                $("#verifyalert").show();
            }
            else
            {
                $("#successalert span").text(result.message);
                $("#verifybox").hide();
                $("#successbox").show();
            }

        });
    }
</script>