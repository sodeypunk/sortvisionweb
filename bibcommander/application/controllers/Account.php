<?php

require_once(dirname(__DIR__)."/controllers/Utility.php");
require_once(dirname(__DIR__)."/controllers/AWSCognitoWrapper.php");

if (! defined ( 'BASEPATH' ))
	exit ( 'No direct script access allowed' );

//session_start();

class Account extends CI_Controller {

	public function __construct() {
		parent::__construct ();
		
		$this->load->helper ( array (
				'url',
				'html',
				'form' 
		) );
		$this->load->model ( 'Users_model' );
	}

	public function index() {
//		$wrapper = new AWSCognitoWrapper();
//		$wrapper->initialize();
//		if(!$wrapper->isAuthenticated()) {
//			redirect('account/login');
//			exit;
//		}

		if (!isset($_SESSION['logged_in']))
		{
			redirect('account/login');
			exit;
		}

		$data['id_user'] = $_SESSION['id_user'];
		$data['token'] = $_SESSION['token'];

		$this->load->view ( 'templates/header');
		$this->load->view ( 'pages/account', $data);
		$this->load->view ( 'templates/footer' );
	}

	public function register() {
		$this->load->view ( 'templates/header');
		$this->load->view ( 'pages/register');
		$this->load->view ( 'templates/footer' );
	}

	public function verify($username, $code) {
		$data['username'] = $username;
		$data['code'] = $code;

		$this->load->view ( 'templates/header');
		$this->load->view ( 'pages/verify', $data);
		$this->load->view ( 'templates/footer' );
	}

	
	public function login() {
		$data = null;

		/*
		 * The security logic used here is a huge security risk where anyone who has anything in the token can get access to the account
		 * Will need to rethink this logic when there is more time. Either with a node.js backend or get the php SRP_A calculation to work.
		 * */
		if (!empty($_POST['email']) && !empty($_POST['password'] && !empty($_POST['token'])))
		{
			$email = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);
			$token = $_POST['token'];

//			$wrapper = new AWSCognitoWrapper();
//			$wrapper->initialize();
//			if(!$wrapper->isAuthenticated()) {
//				$resultMessage = $wrapper->authenticate($email, $password);
//			}

			$_SESSION['email'] = $email;
			//$_SESSION['s3_bucket'] = $login[0]['S3_BUCKET'];
			$_SESSION['s3_bucket'] = 'bibsmart-demo';
			$_SESSION['logged_in'] = true;
			$_SESSION['id_user'] = $email;
			$_SESSION['token'] = $token;


			redirect('account');

		}

		$this->load->view('templates/header', $data);
		$this->load->view('pages/login', $data);
		$this->load->view('templates/footer');

	}

	public function signout() {
		$_SESSION = array();

		// If it's desired to kill the session, also delete the session cookie.
		// Note: This will destroy the session, and not just the session data!
		if (ini_get("session.use_cookies")) {
			$params = session_get_cookie_params();
			setcookie(session_name(), '', time() - 42000,
				$params["path"], $params["domain"],
				$params["secure"], $params["httponly"]
			);
		}

		session_destroy();

		redirect('home');
	}
}