/**
 * Created by soda on 4/20/17.
 */

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

function Register(email, firstname, lastname, password, callbackFunction)
{
    var userPool = GetCognitoPool();

    var attributeList = [];

    var dataEmail = {
        Name : 'email',
        Value : email
    };
    var dataFirstName = {
        Name : 'given_name',
        Value : firstname
    };
    var dataLastName = {
        Name : 'family_name',
        Value : lastname
    };
    var attributeEmail = new AWSCognito.CognitoIdentityServiceProvider.CognitoUserAttribute(dataEmail);
    var attributeFirstName = new AWSCognito.CognitoIdentityServiceProvider.CognitoUserAttribute(dataFirstName);
    var attributeLastName = new AWSCognito.CognitoIdentityServiceProvider.CognitoUserAttribute(dataLastName);

    attributeList.push(attributeEmail);
    attributeList.push(dataFirstName);
    attributeList.push(dataLastName);

    username = firstname + "_" + lastname;

    userPool.signUp(username, password, attributeList, null, function(err, result){
        var result_message = {error: false, message: ""}

        if (err) {
            result_message.error = true;
            result_message.message = err.message;

        }
        else {
            result_message.message = "A verification code was sent to your email: " + email + ". Please click on the link to verify your email."
        }
        callbackFunction(result_message);
    });
}