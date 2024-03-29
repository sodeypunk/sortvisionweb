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
		if ($this->CheckLogin())
		{
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
				$this->load->view('pages/bibsmart', $data);
			} else {
				redirect(site_url('/admin/login'));
			}
		}
	}

	function CheckLogin()
	{
		if (!$this->ci_auth->is_logged_in()) {
			redirect(site_url('auth/login'));
		} elseif ($this->ci_auth->is_logged_in(FALSE)) { // logged in, not activated
			redirect('/auth/sendactivation/');
		} else {
			return true;
		}
	}

	function Job()
	{
		if ($this->CheckLogin())
		{
			// Begin processing post data
			if (isset($_POST['input-file'])) {
				$file = $_POST['input-file'];
				$speed = $_POST['input-speed'];
				$region = $_POST['input-region'];
				$draw_results = isset($_POST['input-draw-results']);
				$dry_run = isset($_POST['input-dryrun']);
				$terminate_timeout = $_POST['input-terminate-timeout'];
				$user_id = $this->ci_auth->get_user_id();
				$user_profile = $this->user_model->get_user($user_id);
				$apiKey = $user_profile[0]->api_key ? $user_profile[0]->api_key : '';
				$hostname = "";
				$instanceid = "";
				$instancetype = "";

				if ($draw_results) {
					$draw_images_param = 1;
				}
				else {
					$draw_images_param = 0;
				}

				if ($dry_run)
					$dry_run_param = 'True';
				else
					$dry_run_param = 'False';

				if ($speed == "custom")
				{
					if (isset($_POST['input-hostname']))
					{
						$hostname = $_POST['input-hostname'];

						if (isset($_POST['input-instanceid'])) {
							$instanceid = $_POST['input-instanceid'];
						}

						if (isset($_POST['input-instance-type'])) {
							$instancetype = $_POST['input-instance-type'];
						}
					}
				}

				// Call API here
				$url = $this->config->item('bibsmart_api_url');
				$stage = $this->config->item('bibsmart_api_stage');
				$contentType = 'application/json';

				$header = array('Content-Type: ' . $contentType,
					'x-api-key: ' . $apiKey);

				$body_data = array('params' => array('region' => $region, 'dryrun' => $dry_run_param, 'stage' => $stage, 'file' => $file, 'speed' => $speed, 'drawimages' => $draw_images_param,
									'terminatetimeout' => $terminate_timeout, 'hostname' => $hostname, 'instanceid' => $instanceid, 'instancetype'  => $instancetype));
				$json_data = json_encode($body_data);

				$result = Util::CallAPI("POST", $url, $header, $json_data);

				if (!empty($result)) {
					$json_result = json_decode($result);
					if (!empty($json_result->result)) {

						$success = $json_result->result;

						if ($success == "success") {
							//$data['success'] = 'Job creation ' . $success . '. File ID is: ' . json_decode($result)->fileid;
							echo "success";
						} elseif ($success == "failed") {
							$message = json_decode($result)->error;
							//$data['errors'] = 'Job creation ' . $success . '. Message: ' . $message;
							echo "error: " . $message;
						}
					}
					else if (!empty($json_result->errorMessage)) {
						echo "error: " . $json_result->errorMessage;
					}
					else
					{
						echo "error: No results from API.";
					}
				}
			}
		}
	}
}
/* End of file bibsmarttest.php */
/* Location: ./application/controllers/bibsmarttest.php */