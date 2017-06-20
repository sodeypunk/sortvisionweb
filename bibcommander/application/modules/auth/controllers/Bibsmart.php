<?php
require_once(APPPATH."/controllers/Utility.php");

if (! defined ( 'BASEPATH' ))
	exit ( 'No direct script access allowed' );

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
				$files = $this->files_model->get_completed_files_by_api_key($apiKey, 100);
				$data['files'] = $files;
				$files_in_progress = $this->files_model->get_in_progress_files_by_api_key($apiKey, 100);
				$data['files_in_progress'] = $files_in_progress;
				$this->load->view(get_template_directory().'bibsmart', $data);
			} else {
				redirect(site_url('/admin/login'));
			}
		}
	}

	function job()
	{
		if (!$this->ci_auth->is_logged_in()) {
			redirect(site_url('auth/login'));
		} elseif ($this->ci_auth->is_logged_in(FALSE)) { // logged in, not activated
			redirect('/auth/sendactivation/');
		} else { /* logged in */
			if($this->ci_auth->canDo('login_to_frontend')) {
				// Setup user data
				$user_id = $this->ci_auth->get_user_id();
				$data['use_username'] = $this->config->item('use_username');
				$user_profile = $this->user_model->get_user($user_id);
				$data['profile'] = $user_profile[0];
				$data['seo_title'] = 'BibSmart';
				$apiKey = $user_profile[0]->api_key?$user_profile[0]->api_key:'';
				$files = $this->files_model->get_completed_files_by_api_key($apiKey, 100);
				$data['files'] = $files;
				$files_in_progress = $this->files_model->get_in_progress_files_by_api_key($apiKey, 100);
				$data['files_in_progress'] = $files_in_progress;
				// Begin processing post data
				$file = $_POST['input-file'];
				$speed = $_POST['input-speed'];
				$draw_results = isset($_POST['input-draw-results']);
				$user_id = $this->ci_auth->get_user_id();
				$user_profile = $this->user_model->get_user($user_id);
				$apiKey = $user_profile[0]->api_key?$user_profile[0]->api_key:'';

				// Call API here
				$url = 'https://api-test.sortvision.com/bibsmart';
				$contentType = 'application/json';
				$dryRun = 'True';

				$header = array('Content-Type: ' . $contentType,
					'x-api-key: ' . $apiKey);

				$body_data = array('params' => array('dryrun' => $dryRun, 'file' => $file, 'ec2' => 't2.nano'));
				$json_data = json_encode($body_data);

				$result = Util::CallAPI("POST", $url, $header, $json_data);

				if (!empty($result))
				{
					$success = json_decode($result)->result;

					if ($success == "success")
					{
						$data['success'] = 'Job creation ' . $success . '. File ID is: ' . json_decode($result)->fileid;
					}
					elseif ($success == "failed")
					{
						$message = json_decode($result)->error;
						$data['errors'] = 'Job creation ' . $success. '. Message: ' . $message;
					}
				}

				$this->load->view(get_template_directory().'bibsmart', $data);
			} else {
				redirect(site_url('/admin/login'));
			}
		}
	}

}
/* End of file bibsmart.php */
/* Location: ./application/controllers/bibsmart.php */