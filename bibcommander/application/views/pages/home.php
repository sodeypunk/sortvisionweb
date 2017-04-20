<!-- Main jumbotron for a primary marketing message or call to action -->

<div class="container">
    <h1>BibSmart</h1>
    <p>Auto bib detection and number recognition software.</p>

    <br/>
    <div class="panel panel-primary">
        <div class="panel-heading">Try Bibsmart demo application</div>
        <div class="panel-body">
            <div class="row">
                <div class="col-sm-6">
                    <p>Drag and drop an image into the box.</p>
                    <h5>Accepted files: *.jpg, *.png</h5>
                    <div id="json-result"></div>
                </div>
                <div class="col-sm-6">
                    <div id="dropzone" class="dropzone"></div>
                </div>
            </div>
        </div>
    </div>

    <div id="loginbox" class="panel panel-info">
        <div class="panel-heading">For Developers - Bibsmart API</div>
        <div class="panel-body">
            <div class="row">
                <div class="col-md-12">
                    Sign up for an account to test the Bibsmart API!
                </div>
            </div>
        </div>
    </div>

    </div>
</div>


<script>
    $(document).ready(function() {
        Dropzone.autoDiscover = false;
        $("#dropzone").dropzone({
            url: "<?php echo site_url('/upload/dropzone'); ?>",
            acceptedFiles: ".jpg, .JPG, .png, .PNG, .zip, .ZIP, .jpeg, .JPEG",
            init: function() {
                this.on("success", function(file, response) {
                    var jsonResponse = JSON.parse(response);
                    //var itemsCount = Object.keys(jsonResponse).length;
                    //$("#processing-total").text(itemsCount);
                    var $resultText = "";
                    for (var key in jsonResponse)
                    {
                        if (jsonResponse.hasOwnProperty(key))
                        {
                            if (jsonResponse[key]['STATUS'] == "SUCCESS")
                            {
                                var jsonObj = JSON.parse(jsonResponse[key]['JSON_RESULT']);
                                var jsonPretty = JSON.stringify(jsonObj, null, '\t');
                                $resultText = jsonPretty;
                            }
                            else
                            {
                                $resultText = "Your image " + key + " has failed when uploading.";
                            }
                        }
                    }

                    $("#json-result").prepend('<pre>' + $resultText + '</pre>');
                });
            }
        });
        $(".dz-default.dz-message").html("Drop files here or click to upload.")
    });

    function GetCognitoPool() {
        var poolData = {
            UserPoolId: 'us-east-1_psUwaRz7q',
            ClientId: '1d48jr66s1h7slss34ts4qcvmf'
        };
        var userPool = new AWSCognito.CognitoIdentityServiceProvider.CognitoUserPool(poolData);

        return userPool;
    }

    function GetCognitoUser(userName) {

        var userPool = GetCognitoPool();

        var userData = {
            Username : userName,
            Pool : userPool
        };
        var cognitoUser = new AWSCognito.CognitoIdentityServiceProvider.CognitoUser(userData);

        return cognitoUser;
    }
    function AuthenticateUser()
    {
        var userName = $("#loginbox").find('input[name=username]').val();
        var password = $("#loginbox").find('input[name=passwd]').val();
        var cognitoUser = GetCognitoUser(userName);

        var authenticationData = {
            Username : cognitoUser.username,
            Password : password,
        };
        var authenticationDetails = new AWSCognito.CognitoIdentityServiceProvider.AuthenticationDetails(authenticationData);

        cognitoUser.authenticateUser(authenticationDetails, {
            onSuccess: function (result) {
                console.log('access token + ' + result.getAccessToken().getJwtToken());

                AWS.config.credentials = new AWS.CognitoIdentityCredentials({
                    IdentityPoolId : 'us-east-1_LyX4DYvBc', // your identity pool id here
                    Logins : {
                        // Change the key below according to the specific region your user pool is in.
                        'cognito-idp.us-east-1.amazonaws.com/us-east-1_LyX4DYvBc' : result.getIdToken().getJwtToken()
                    }
                });

                // Instantiate aws sdk service objects now that the credentials have been updated.
                // example: var s3 = new AWS.S3();

            },

            newPasswordRequired: function(userAttributes, requiredAttributes) {
                // User was signed up by an admin and must provide new
                // password and required attributes, if any, to complete
                // authentication.

                console.log("New password required");

                $("#updatebox").find('.form-group input[name=email]').val(userAttributes.email);
                $("#updatebox").find('.form-group input[name=firstname]').val(userAttributes.give_name);
                $("#updatebox").find('.form-group input[name=lastname]').val(userAttributes.family_name);

            },

            onFailure: function(err) {
                alert(err);
            },

        });
    }

    function UpdateUserPassword()
    {
        var email = $("#updatebox").find('.form-group input[name=email]').val();
        var oldPassword = $("#updatebox").find('.form-group input[name=oldpasswd]').val();

        var cognitoUser = GetCognitoUser(email);

        var authenticationData = {
            Username : email,
            Password : oldPassword,
        };
        var authenticationDetails = new AWSCognito.CognitoIdentityServiceProvider.AuthenticationDetails(authenticationData);

        cognitoUser.authenticateUser(authenticationDetails, {
            onSuccess: function (result) {
                alert("Information updated successfully.");
            },
            newPasswordRequired: function(userAttributes, requiredAttributes) {
                // User was signed up by an admin and must provide new
                // password and required attributes, if any, to complete
                // authentication.

                console.log("New password required");

                var email = $("#updatebox").find('.form-group input[name=email]').val();
                var firstName = $("#updatebox").find('.form-group input[name=firstname]').val();
                var lastName = $("#updatebox").find('.form-group input[name=lastname]').val();
                var newPassword = $("#updatebox").find('.form-group input[name=newpasswd]').val();

                // the api doesn't accept this field back
                delete userAttributes.email_verified;
                userAttributes.email = email;
                userAttributes.given_name = firstName;
                userAttributes.family_name = lastName;

                // Get these details and call
                cognitoUser.completeNewPasswordChallenge(newPassword, userAttributes, this);
            },

            onFailure: function(err) {
                alert(err);
            },

        });
    }

    function SignUp()
    {
        var userPool = GetCognitoPool();

        var attributeList = [];

        var dataEmail = {
            Name : 'email',
            Value : 'email@mydomain.com'
        };
        var dataPhoneNumber = {
            Name : 'phone_number',
            Value : '+15555555555'
        };
        var attributeEmail = new AWSCognito.CognitoIdentityServiceProvider.CognitoUserAttribute(dataEmail);
        var attributePhoneNumber = new AWSCognito.CognitoIdentityServiceProvider.CognitoUserAttribute(dataPhoneNumber);

        attributeList.push(attributeEmail);
        attributeList.push(attributePhoneNumber);

        userPool.signUp('username', 'password', attributeList, null, function(err, result){
            if (err) {
                alert(err);
                return;
            }
            cognitoUser = result.user;
            console.log('user name is ' + cognitoUser.getUsername());
        });
    }
</script>