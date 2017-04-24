<?php
require(dirname(__DIR__)."/../assets/aws/aws-autoloader.php");
use Aws\CognitoIdentityProvider\CognitoIdentityProviderClient;

class ResultMessage
{
    public $error = false;
    public $error_message = "";
    public $token = "";

    public function __construct()
    {

    }

}

class AWSCognitoWrapper
{
    private $COOKIE_NAME = 'aws-cognito-app-access-token';

    private $region;
    private $client_id;
    private $userpool_id;

    private $client;

    private $user = null;

    public function __construct()
    {
        putenv("REGION=us-east-1");
        putenv("USERPOOL_ID=us-east-1_psUwaRz7q");
        putenv("CLIENT_ID=1d48jr66s1h7slss34ts4qcvmf");

        if(!getenv('REGION') || !getenv('CLIENT_ID') || !getenv('USERPOOL_ID')) {
            throw new \InvalidArgumentException("Please provide the region, client_id and userpool_id variables in the .env file");
        }

        $this->region = getenv('REGION');
        $this->client_id = getenv('CLIENT_ID');
        $this->userpool_id = getenv('USERPOOL_ID');
    }

    public function initialize()
    {
        $this->client = new CognitoIdentityProviderClient([
            'version' => 'latest',
            'region' => $this->region,
        ]);

        try {
            $this->user = $this->client->getUser([
                'AccessToken' => $this->getAuthenticationCookie()
            ]);
        } catch(\Exception  $e) {
            // an exception indicates the accesstoken is incorrect - $this->user will still be null
        }
    }

    public function authenticate($username, $password)
    {
        $resultMessage = new ResultMessage();

        try {
            $result = $this->client->adminInitiateAuth([
                'AuthFlow' => 'ADMIN_NO_SRP_AUTH',
                'ClientId' => $this->client_id,
                'UserPoolId' => $this->userpool_id,
                'AuthParameters' => [
                    'USERNAME' => $username,
                    'PASSWORD' => $password,
                ],
            ]);
        } catch (\Exception $e) {

            $resultMessage->error = true;
            $resultMessage->error_message = $e->getMessage();
            return $resultMessage;
        }

        $this->setAuthenticationCookie($result->get('AuthenticationResult')['AccessToken']);
        return $resultMessage;
    }

    public function signup($username, $email, $password)
    {
        try {
            $result = $this->client->signUp([
                'ClientId' => $this->client_id,
                'Username' => $username,
                'Password' => $password,
                'UserAttributes' => [
                    [
                        'Name' => 'name',
                        'Value' => $username
                    ],
                    [
                        'Name' => 'email',
                        'Value' => $email
                    ]
                ],
            ]);
        } catch (\Exception $e) {
            return $e->getMessage();
        }

        return '';
    }

    public function confirmSignup($username, $code)
    {
        try {
            $result = $this->client->confirmSignUp([
                'ClientId' => $this->client_id,
                'Username' => $username,
                'ConfirmationCode' => $code,
            ]);
        } catch (\Exception $e) {
            return $e->getMessage();
        }

        return '';
    }

    public function sendPasswordResetMail($username)
    {
        try {
            $this->client->forgotPassword([
                'ClientId' => $this->client_id,
                'Username' => $username
            ]);
        } catch (Exception $e) {
            return $e->getMessage();
        }

        return '';
    }

    public function resetPassword($code, $password, $username)
    {
        try {
            $this->client->confirmForgotPassword([
                'ClientId' => $this->client_id,
                'ConfirmationCode' => $code,
                'Password' => $password,
                'Username' => $username
            ]);
        } catch (Exception $e) {
            return $e->getMessage();
        }

        return '';
    }

    public function isAuthenticated()
    {
        return null !== $this->user;
    }

    public function getPoolMetadata()
    {
        $result = $this->client->describeUserPool([
            'UserPoolId' => $this->userpool_id,
        ]);

        return $result->get('UserPool');
    }

    public function getPoolUsers()
    {
        $result = $this->client->listUsers([
            'UserPoolId' => $this->userpool_id,
        ]);

        return $result->get('Users');
    }

    public function getUser()
    {
        return $this->user;
    }

    public function logout()
    {
        if(isset($_COOKIE[$this->COOKIE_NAME])) {
            unset($_COOKIE[$this->COOKIE_NAME]);
            setcookie($this->COOKIE_NAME, '', time() - 3600);
        }
    }

    private function setAuthenticationCookie($accessToken)
    {
        /*
         * Please note that plain-text storage of the access token is insecure and
         * not recommended by AWS. This is only done to keep this example
         * application as easy as possible. Read the AWS docs for more info:
         * http://docs.aws.amazon.com/cognito/latest/developerguide/amazon-cognito-user-pools-using-tokens-with-identity-providers.html
        */
        setcookie($this->COOKIE_NAME, $accessToken, time() + 3600);
    }

    private function getAuthenticationCookie()
    {
        if (isset($_COOKIE[$this->COOKIE_NAME]) && $_COOKIE[$this->COOKIE_NAME] != null)
        {
            return $_COOKIE[$this->COOKIE_NAME];
        }
        return '';
    }
}