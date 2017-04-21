/**
 * Created by soda on 4/20/17.
 */

var AccountController = new function() {

    this.GetCognitoPool = function() {
        var poolData = {
            UserPoolId: 'us-east-1_psUwaRz7q',
            ClientId: '1d48jr66s1h7slss34ts4qcvmf'
        };
        var userPool = new AWSCognito.CognitoIdentityServiceProvider.CognitoUserPool(poolData);

        return userPool;
    }

    this.GetCognitoUser = function(userName) {

        var userPool = this.GetCognitoPool();

        var userData = {
            Username : userName,
            Pool : userPool
        };
        var cognitoUser = new AWSCognito.CognitoIdentityServiceProvider.CognitoUser(userData);

        return cognitoUser;
    }

    this.LoginUser = function(email, password, callbackFunction)
    {
        var cognitoUser = this.GetCognitoUser(email);

        var authenticationData = {
            Username : email,
            Password : password,
        };
        var authenticationDetails = new AWSCognito.CognitoIdentityServiceProvider.AuthenticationDetails(authenticationData);

        cognitoUser.authenticateUser(authenticationDetails, {
            onSuccess: function (result) {
                var result_message = {error: false, newPassword: false, message: "", token: ""}
                result_message.token = result.getAccessToken().getJwtToken();
                callbackFunction(result_message);
            },

            newPasswordRequired: function(userAttributes, requiredAttributes) {
                // User was signed up by an admin and must provide new
                // password and required attributes, if any, to complete
                // authentication.

                var result_message = {error: false, newPassword: false, message: "", token: ""}
                result_message.error = true;
                result_message.newPassword = true;
                callbackFunction(result_message);

                //$("#updatebox").find('.form-group input[name=email]').val(userAttributes.email);
                //$("#updatebox").find('.form-group input[name=firstname]').val(userAttributes.give_name);
                //$("#updatebox").find('.form-group input[name=lastname]').val(userAttributes.family_name);

            },

            onFailure: function(err) {
                var result_message = {error: false, newPassword: false, message: "", token: ""}
                result_message.error = true;
                result_message.message = err.message;
                callbackFunction(result_message);
            },

        });
    }

    this.UpdateUserPassword = function(email, firstname, lastname, oldpassword, newpassword, callbackFunction)
    {
        var email = $("#updatebox").find('.form-group input[name=email]').val();
        var oldPassword = $("#updatebox").find('.form-group input[name=oldpasswd]').val();

        var cognitoUser = this.GetCognitoUser(email);

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
                //delete userAttributes.email_verified; //Dont delete this in order to auto verify the email
                userAttributes.email = email;
                userAttributes.given_name = firstName;
                userAttributes.family_name = lastName;

                // Get these details and call
                cognitoUser.completeNewPasswordChallenge(newPassword, userAttributes, this);
            },

            onFailure: function(err) {
                var result_message = {error: false, message: ""}
                result_message.error = true;
                result_message.message = err.message;
                callbackFunction(result_message);
            },

        });
    }

    this.Register = function(email, firstname, lastname, password, callbackFunction) {
        var userPool = this.GetCognitoPool();

        var attributeList = [];

        var dataEmail = {
            Name: 'email',
            Value: email
        };
        var dataFirstName = {
            Name: 'given_name',
            Value: firstname
        };
        var dataLastName = {
            Name: 'family_name',
            Value: lastname
        };

        var attributeEmail = new AWSCognito.CognitoIdentityServiceProvider.CognitoUserAttribute(dataEmail);
        var attributeFirstName = new AWSCognito.CognitoIdentityServiceProvider.CognitoUserAttribute(dataFirstName);
        var attributeLastName = new AWSCognito.CognitoIdentityServiceProvider.CognitoUserAttribute(dataLastName);

        attributeList.push(attributeEmail);
        attributeList.push(attributeFirstName);
        attributeList.push(attributeLastName);

        username = firstname.replace(/\s/g, '') + "_" + lastname.replace(/\s/g, '') + "_" + Math.floor((Math.random() * 10000) + 1);

        userPool.signUp(username, password, attributeList, null, function (err, result) {
            var result_message = {error: false, message: ""}

            if (err) {
                result_message.error = true;
                result_message.message = err.message;

            }
            else {
                result_message.message = "A verification code was sent to your email: " + email + ". Please click on the attached link to verify you account."
            }
            callbackFunction(result_message);
        });
    }

    this.VerifyUser = function(username, code, callbackFunction) {
        var userPool = this.GetCognitoPool();
        var userData = {
            Username: username,
            Pool: userPool
        };

        var cognitoUser = new AWSCognito.CognitoIdentityServiceProvider.CognitoUser(userData);
        cognitoUser.confirmRegistration(code, true, function (err, result) {
            var result_message = {error: false, message: ""}

            if (err) {
                result_message.error = true;
                result_message.message = err.message;
            }
            else {
                result_message.message = 'Your email was successfully verified. You may now log into your account.';
            }
            callbackFunction(result_message);
        });
    }
}