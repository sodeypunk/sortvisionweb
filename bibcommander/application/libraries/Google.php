<?php 
/**
 * CIMembership
 * 
 * @package		Libraries
 * @author		1stcoder Team
 * @copyright   Copyright (c) 2015 - 2017 1stcoder. (http://www.1stcoder.com)
 * @license		http://opensource.org/licenses/MIT	MIT License
 */

if ( ! defined('BASEPATH')) exit('No direct script access allowed');
require_once APPPATH. 'third_party/Socialauth/Oauth2/Bootstrap.php';

class Google 
{
	function __construct()
	{
		$this->ci =& get_instance();
		$this->ci->load->library('phpass');
		$this->ci->load->library('session');
		$this->ci->load->database();
		$this->ci->load->model('auth/users');
		$this->ci->load->model('auth/usergroups_model');
		$this->callback = site_url('auth/oauth2/googlecallback');
        $this->provider = new League\OAuth2\Client\Provider\Google([
            'clientId' => $this->ci->config->item('google_app_id'),
            'clientSecret' => $this->ci->config->item('google_app_secret'),
            'redirectUri' => $this->callback,
            'hostedDomain' => site_url(),
        ]);
	}

	function getUser()
	{
		if (!empty($_GET['error'])) {

			// Got an error, probably user denied access
			$this->ci->session->set_flashdata('errors', $_GET['error']);
			return FALSE;
		} else if (empty($_GET['code'])) {
			
			// If we don't have an authorization code then get one
			$options = [
				'scope' => ['https://www.googleapis.com/auth/plus.login', 'https://www.googleapis.com/auth/plus.me' ,'https://www.googleapis.com/auth/userinfo.email','https://www.googleapis.com/auth/userinfo.profile'] // array or string
			];
			$authUrl = $this->provider->getAuthorizationUrl($options);
			$this->ci->session->set_userdata('oauth2state', $this->provider->getState());
			redirect($authUrl);
			exit;
		} elseif (empty($_GET['state']) || ($_GET['state'] !== $this->ci->session->userdata('oauth2state'))) {
			$this->ci->session->unset_userdata('oauth2state');
			$this->ci->session->set_flashdata('errors','Invalid user state');
			return FALSE;
		} else {
			
			// Try to get an access token (using the authorization code grant)
			$token = $this->provider->getAccessToken('authorization_code', [
				'code' => $_GET['code']
			]);
			$this->ci->session->set_userdata('google_access_token', serialize($token));

			// Optional: Now you have a token you can look up a users profile data
			try {
				// We got an access token, let's now get the user's details
				$user = $this->provider->getResourceOwner($token);

				// Use these details to create a new profile
				return $user;
			} catch (Exception $e) {
				$this->ci->session->set_flashdata('errors', $e->getMessage());

				// Failed to get user details
				return FALSE;
			}
		}
	}

	function getUserProfile()
	{
		if ($this->ci->session->userdata('google_access_token')) {

			// Optional: Now you have a token you can look up a users profile data
			try {
				$token = unserialize($this->ci->session->userdata('google_access_token'));
				
				// We got an access token, let's now get the user's details
				$user = $this->provider->getResourceOwner($token);

				// Use these details to create a new profile
				return $user;
			} catch (Exception $e) {
				$this->ci->session->set_flashdata('errors', $e->getMessage());

				// Failed to get user details
				return FALSE;
			}
		} else {
				return FALSE;
		}
	}
}