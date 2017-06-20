<?php
/**
 * CIMembership
 * 
 * @package		Modules
 * @author		1stcoder Team
 * @copyright   Copyright (c) 2015 - 2017 1stcoder. (http://www.1stcoder.com)
 * @license		http://opensource.org/licenses/MIT	MIT License
 */

if (!defined('BASEPATH')) exit('No direct script access allowed');
class Bibsmart extends CI_Controller
{
	function __construct()
	{
		parent::__construct();
		$this->load->helper(array('form', 'url', 'security'));
		$this->load->library('form_validation');
		$this->load->library('my_form_validation');
		$this->load->model('auth/user_model');
		$this->load->model ('files_model');
	}

	/* User Bibsmart Page */
	function index()
	{
		if (!$this->ci_auth->is_logged_in()) {	
			redirect(site_url('auth/login'));
		} elseif ($this->ci_auth->is_logged_in(FALSE)) { // logged in, not activated
			redirect('/auth/sendactivation/');
		} else { /* logged in */
			if($this->ci_auth->canDo('login_to_frontend')) {
				$user_id = $this->ci_auth->get_user_id();
				$data['use_username'] = $this->config->item('use_username');
				$data['errors'] = $this->session->flashdata('errors');
				$data['message'] = $this->session->flashdata('message');
				$data['success'] = $this->session->flashdata('success');
				$user_profile = $this->user_model->get_user($user_id);
				$data['profile'] = $user_profile[0];
				$data['seo_title'] = 'BibSmart';
				$apiKey = $user_profile[0]->api_key?$user_profile[0]->api_key:'';
				$files = $this->files_model->get_files_by_api_key($apiKey, 25);
				$data['files'] = $files;
				$this->load->view(get_template_directory().'bibsmart', $data);
			} else {
				redirect(site_url('/admin/login'));
			}
		}
	}

}
/* End of file bibsmart.php */
/* Location: ./application/controllers/bibsmart.php */