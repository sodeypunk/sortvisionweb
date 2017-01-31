<?php

require_once(dirname(__DIR__)."/controllers/Utility.php");

if (! defined ( 'BASEPATH' ))
	exit ( 'No direct script access allowed' );

session_start();

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

	
	public function signin() {
		if (!empty($_POST['email']) && !empty($_POST['password']))
		{
			$db = get_instance()->db->conn_id;
			$email = mysqli_real_escape_string($db, $_POST['email']);
			$password = mysqli_real_escape_string($db, $_POST['password']);

			$login = $this->Users_model->check_user($email, $password);

			if ($login != false)
			{

				$_SESSION['email'] = $email;
				$_SESSION['s3_bucket'] = $login[0]['S3_BUCKET'];
				$_SESSION['logged_in'] = true;
				$_SESSION['id_user'] = $login[0]['IDUSERS'];

				redirect('bibcommander');

			}
			else
			{
				$data['errorMsg'] = "Login incorrect";
			}
		}

		$this->load->view ( 'templates/header', $data );
		$this->load->view ( 'pages/home', $data );
		$this->load->view ( 'templates/footer' );
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