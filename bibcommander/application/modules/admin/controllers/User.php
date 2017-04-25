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

class User extends CI_Controller
{
	function __construct()
	{
		parent::__construct();

		$this->load->helper(array('form', 'url', 'security'));
		$this->load->library('form_validation');
		$this->load->library('my_form_validation');
		$this->load->model('users');
	}

	function index()
	{
		$this->users();
	}


	/**
	 * List all users 
	 *
	 */
	 function users()
	 {
		 
		if (!$this->ci_auth->is_logged_in()) {	
			redirect(site_url('/admin/login'));
		} elseif ($this->ci_auth->is_logged_in(FALSE)) {						// logged in, not activated
			redirect('/auth/sendactivation/');
		} else { /* logged in */
		
		if($this->ci_auth->canDo('access_backend')) {
			if($this->ci_auth->canDo('view_users')) {
				$data['seo_pagetitle'] = 'Dashboard';
				$data['errors'] =  $this->session->flashdata('errors');
				$data['success'] =  $this->session->flashdata('success');
				$data['users']=$this->users->listallusers(); 
				
				/* Check the logged in user can create users with posted user role*/
				$user_roles =  $this->users->user_roles_list();
				foreach($user_roles as $key => $value) {
					if($key==1) {
						if($this->ci_auth->is_superadmin()) {
							$data['user_roles'][$key]=$key;
						}
					} else if($key==2) {
						if($this->ci_auth->is_superadmin() || $this->ci_auth->is_admin()) {
							$data['user_roles'][$key]=$key;
						}
					} else {
					$data['user_roles'][$key]=$key;
					}
				}
				
				$logged_in_user=$this->ci_auth->get_user_id();
				foreach($data['users'] as $key => $usersdata) {
					$role = $usersdata->gid;
					if(in_array($role, $data['user_roles'])) { 
						$data['users'][$key]->can_edit = 1;
						$data['users'][$key]->can_delete = 1;
					} else {
						$data['users'][$key]->can_edit = 0;
						$data['users'][$key]->can_delete = 0;
					}
			
					if($usersdata->uid==$logged_in_user) { 
						$data['users'][$key]->can_delete = 0;
					}
				}


				$this->load->view(get_theme_directory().'/users', $data);
			} else  {
				$this->session->set_flashdata('errors', 'You dont have permission to access this part of the site.');
				redirect(site_url('/admin/dashboard'));
			}
		} else {
				$this->session->set_flashdata('errors', 'You dont have permission to access the admin part of the site.');
				redirect(site_url('auth/profile/'));
		}
		
		}
	 
	 }

	/**
	 * New User
	 *
	 */
	 function newuser()
	 {
		if (!$this->ci_auth->is_logged_in()) {	
			redirect(site_url('/admin/login'));
		} elseif ($this->ci_auth->is_logged_in(FALSE)) {						// logged in, not activated
		redirect('/auth/sendactivation/');
		} else { /* logged in */
		
		if($this->ci_auth->canDo('access_backend')) {
			if($this->ci_auth->canDo('edit_users')) {
		$data['seo_title'] = 'New User'; 
		$use_username = $this->config->item('use_username');
		
		$this->form_validation->set_rules('first_name', 'First name', 'trim|required|xss_clean|min_length[3]|max_length[10]');
		$this->form_validation->set_rules('last_name', 'Last name', 'trim|required|xss_clean|min_length[1]|max_length[10]');
		$this->form_validation->set_rules('phone', 'Phone', 'trim|xss_clean|min_length[3]|max_length[20]');
		$this->form_validation->set_rules('country', 'Country', 'trim|xss_clean|min_length[2]|max_length[20]');
		$this->form_validation->set_rules('company', 'Company', 'trim|xss_clean|min_length[3]|max_length[20]');
		$this->form_validation->set_rules('website', 'Website', 'trim|xss_clean|min_length[3]|max_length[50]');
		$this->form_validation->set_rules('address', 'Address', 'trim|xss_clean|min_length[3]|max_length[100]');

		$this->form_validation->set_rules('username', 'Username', 'trim|required|xss_clean|min_length[5]|max_length[12]|is_unique[users.username]');
		$this->form_validation->set_rules('email_address', 'Email', 'trim|required|valid_email|is_unique[users.email]');

		$this->form_validation->set_rules('password', 'Password', 'required');
		$this->form_validation->set_rules('confirm_password', 'Password Confirmation', 'required|matches[password]');

		$this->form_validation->set_rules('user_role', 'User Role', 'trim|required|xss_clean');
		

		if ($this->form_validation->run()) {
			if (!empty($_FILES['profile_image']['name']))
			{
				$config['upload_path'] = FCPATH.'/uploads/images/profiles/';
				$config['allowed_types'] = 'gif|jpg|png|jpeg';     
				$config['max_size'] = '4000';
				$this->load->library('upload', $config);
				$this->upload->initialize($config);
	
				if ($this->upload->do_upload('profile_image'))
				{
					$img = $this->upload->data();
					$profile_image = $img['file_name'];
				}
				else
				{
					$this->session->set_flashdata('errors', $this->upload->display_errors());
					redirect(site_url('admin/user/newuser')); 
	
				}
			} else { 
					$profile_image = NULL;	
			}

			$user_data=array(
				'username'=>$this->input->post('username'),
				'password'=>$this->input->post('password'),
				'email'=>$this->input->post('email_address'),
				'activated'=>1,
				'user_role'=>$this->input->post('user_role'),
			);
			
			$user_profile_data=array(
				'first_name'=>$this->input->post('first_name'),
				'last_name'=>$this->input->post('last_name'),
				'phone'=>$this->input->post('phone'),
				'company'=>$this->input->post('company'),
				'country'=>$this->input->post('country'),
				'website'=>$this->input->post('website'),
				'address'=>$this->input->post('address'),
				'profile_image'=>$this->input->post('profile_image')
			);
			$send_welcome_email=$this->input->post('welcome_email');
			
			/* Check the logged in user can create users with posted user role*/
			$user_roles =  $this->users->user_roles_list();
			foreach($user_roles as $key => $value) {
				if($key==1) {
					if($this->ci_auth->is_superadmin()) {
						$data['user_roles'][$key]=$key;
					}
				} else if($key==2) {
					if($this->ci_auth->is_superadmin() || $this->ci_auth->is_admin()) {
						$data['user_roles'][$key]=$key;
					}
				} else {
				$data['user_roles'][$key]=$key;
				}
			}

			$newuser_role = $this->input->post('user_role');

			if(!in_array($newuser_role, $data['user_roles'])) { 
						$this->session->set_flashdata('errors', "You can't create users with this user group");
						//redirect(site_url('admin/user/newuser/'));
			} else { 
	
				$insUser=$this->ci_auth->create_user($user_data, $user_profile_data);
				 
				if($insUser) {
						$data['site_name'] = $this->config->item('website_name');
	
						if ($send_welcome_email) {									// send "activate" email
							//if ($this->config->item('email_account_details')) {	// send "welcome" email
								$this->_send_email('welcome', $data['email'], $data);
							//}
							unset($data['password']); // Clear password (just for any case)
	
							$this->session->set_flashdata('success', 'New account created successfully');
							redirect(site_url('admin/user/edituser/'.$insUser['user_id']));
	
						} else {
							$this->session->set_flashdata('success', 'New account created successfully');
							redirect(site_url('admin/user/edituser/'.$insUser['user_id']));
						}
					
				} else {
					$errors = $this->ci_auth->get_error_message();
					foreach ($errors as $k => $v)	$data['errors'][$k] = $this->lang->line($v);
					$this->session->set_flashdata('errors', $data['errors']);
				}
				
			}
			
		} 
		$data['errors'] =  (validation_errors()) ? validation_errors() : $this->session->flashdata('errors');
		$data['success'] =  $this->session->flashdata('success');
		$user_roles =  $this->users->user_roles_list();
		foreach($user_roles as $key => $value) {
			if($key==1) {
				if($this->ci_auth->is_superadmin()) {
					$data['user_roles'][$key]=$value;
				}
			} else if($key==2) {
				if($this->ci_auth->is_superadmin() || $this->ci_auth->is_admin()) {
					$data['user_roles'][$key]=$value;
				}
			} else {
			$data['user_roles'][$key]=$value;
			}
		}
		$data['use_username'] = $use_username;
		$data['first_name'] = array(
			'name'	=> 'first_name',
			'id'	=> 'first_name',
			'class' => 'required form-control',
			'placeholder'	=> 'First Name',
			'value' =>  $this->form_validation->set_value('first_name'),
			'maxlength'	=> 32,
			'size'	=> 30,
		);
		$data['last_name'] = array(
			'name'	=> 'last_name',
			'id'	=> 'last_name',
			'class' => 'required form-control',
			'placeholder'	=> 'Last Name',
			'value' =>  $this->form_validation->set_value('last_name'),
			'maxlength'	=> 32,
			'size'	=> 30,
		);
		$data['username'] = array(
			'name'	=> 'username',
			'id'	=> 'username',
			'class' => 'required form-control',
			'placeholder'	=> 'Username',
			'value' =>  $this->form_validation->set_value('username'),
			'maxlength'	=> 32,
			'size'	=> 30,
		);
		$data['email_address'] = array(
			'name'	=> 'email_address',
			'id'	=> 'email_address',
			'class' => 'required form-control',
			'placeholder'	=> 'Email address',
			'value' =>  $this->form_validation->set_value('email_address'),
			'maxlength'	=> 60,
			'size'	=> 60,
		);
		$data['password'] = array(
			'name'	=> 'password',
			'id'	=> 'password',
			'class' => 'required form-control',
			'placeholder'	=> 'Password',
			'value' =>  $this->form_validation->set_value('password'),
		);		
		$data['confirm_password'] = array(
			'name'	=> 'confirm_password',
			'id'	=> 'confirm_password',
			'class' => 'required form-control',
			'placeholder'	=> 'Confirm password',
			'value' =>  $this->form_validation->set_value('confirm_password'),
		);	
		$data['profile_image'] = array(
			'name'	=> 'profile_image',
			'id'	=> 'unstyled-file',
			'class' => 'profile-image',
		);
		$data['phone'] = array(
			'name'	=> 'phone',
			'id'	=> 'phone',
			'class' => 'form-control',
			'placeholder'	=> 'Phone',
			'value' =>  $this->form_validation->set_value('phone'),
		);
		$data['company'] = array(
			'name'	=> 'company',
			'id'	=> 'company',
			'class' => 'form-control',
			'placeholder'	=> 'Company',
			'value' =>  $this->form_validation->set_value('company'),
		);
		$data['address'] = array(
			'name'	=> 'address',
			'id'	=> 'address',
			'class' => 'form-control',
			'placeholder'	=> 'Address',
			'value' =>  $this->form_validation->set_value('address'),
		);
		$data['website'] = array(
			'name'	=> 'website',
			'id'	=> 'website',
			'class' => 'form-control',
			'placeholder'	=> 'Website',
			'value' =>  $this->form_validation->set_value('website'),
		);
		
		$this->load->view(get_theme_directory().'/newuser', $data);
			} else  {
				$this->session->set_flashdata('errors', 'You dont have permission to create/edit users.');
				redirect(site_url('/admin/user'));
			}
		} else {
				$this->session->set_flashdata('errors', 'You dont have permission to access the admin part of the site.');
				redirect(site_url('auth/profile/'));
		}
		}
	 }

	 
	/**
	 * Edit User
	 *
	 */
	 function edituser($user_id)
	 {
		if (!$this->ci_auth->is_logged_in()) {	
			redirect(site_url('/admin/login'));
		} elseif ($this->ci_auth->is_logged_in(FALSE)) {						// logged in, not activated
		redirect('/auth/sendactivation/');
		} else { /* logged in */
		
			if(!isset($user_id) || $user_id=='') { 
				$this->session->set_flashdata('errors', 'User does not exists'); 
				redirect(site_url('admin/user/'));
			} 
			
			if(!$this->ci_auth->is_superadmin()) {
				$p_user_group=$this->users->group_by_user_id($user_id);
				if($p_user_group==1) { 
					$this->session->set_flashdata('errors', 'You dont have permission to edit this account');
					redirect(site_url('admin/user/'));
				}
				
			}
			
			if(!$this->ci_auth->is_superadmin() && !$this->ci_auth->is_admin()) {
				$p_user_group=$this->users->group_by_user_id($user_id);
				if($p_user_group==2) { 
					$this->session->set_flashdata('errors', 'You dont have permission to edit this account');
					redirect(site_url('admin/user/'));
				}
				
			}
		
		if($this->ci_auth->canDo('access_backend')) {
			if($this->ci_auth->canDo('edit_users')) {
		$data['seo_title'] = 'Edit User'; 
		$use_username = $this->config->item('use_username');
		
		$this->form_validation->set_rules('first_name', 'First name', 'trim|required|xss_clean|min_length[3]|max_length[10]');
		$this->form_validation->set_rules('last_name', 'Last name', 'trim|required|xss_clean|min_length[1]|max_length[10]');
		$this->form_validation->set_rules('phone', 'Phone', 'trim|xss_clean|min_length[3]|max_length[20]');
		$this->form_validation->set_rules('country', 'Country', 'trim|xss_clean|min_length[2]|max_length[20]');
		$this->form_validation->set_rules('company', 'Company', 'trim|xss_clean|min_length[3]|max_length[20]');
		$this->form_validation->set_rules('website', 'Website', 'trim|xss_clean|min_length[3]|max_length[50]');
		$this->form_validation->set_rules('api_key', 'API Key', 'trim|xss_clean|min_length[3]|max_length[100]');
		$this->form_validation->set_rules('address', 'Address', 'trim|xss_clean|min_length[3]|max_length[100]');

		if ($this->config->item('use_username')){
			$this->form_validation->set_rules('username', 'Username', 'trim|required|xss_clean|min_length[5]|max_length[12]|edit_unique[users.username.' . $user_id . ']');
		}
		$this->form_validation->set_rules('email_address', 'Email', 'trim|required|valid_email|edit_unique[users.email.'.$user_id.']');
		
		$password_post=$this->input->post('password');
		if(isset($password_post) && $password_post!='') {
		$this->form_validation->set_rules('password', 'Password', 'required');
		$this->form_validation->set_rules('confirm_password', 'Password Confirmation', 'required|matches[password]');
		}


		$this->form_validation->set_rules('user_role', 'User Role', 'trim|required|xss_clean');
		

		if ($this->form_validation->run()) {
			$user_id =  $this->input->post('user_id');
			if (!empty($_FILES['profile_image']['name']))
			{
				$config['upload_path'] = FCPATH.'/uploads/images/profiles/';
				$config['allowed_types'] = 'gif|jpg|png|jpeg';     
				$config['max_size'] = '4000';
				$this->load->library('upload', $config);
				$this->upload->initialize($config);

				if ($this->upload->do_upload('profile_image'))
				{
					$img = $this->upload->data();
					$profile_image = $img['file_name'];
				}
				else
				{
					$this->session->set_flashdata('errors', $this->upload->display_errors());
					redirect(site_url('admin/user/edituser/'.$user_id)); 
	
				} 
			} else { 
				if((null !==$this->input->post('deleteprofileimage')) && $this->input->post('deleteprofileimage')==1) {
					$profile_image = NULL;	
				} else {
					$profile_image = $this->users->user_data($user_id)[0]->profile_image;
				}
			}

			if(isset($password_post) && $password_post!='') {
				$user_data=array(
					'username'=>$this->input->post('username'),
					'password'=>$password_post,
					'email'=>$this->input->post('email_address'),
					'gid'=>$this->input->post('user_role'),
				);
			} else {
				$user_data=array(
					'username'=>$this->input->post('username'),
					'email'=>$this->input->post('email_address'),
					'gid'=>$this->input->post('user_role'),
				);
			}

			$user_profile_data=array(
				'first_name'=>$this->input->post('first_name'),
				'last_name'=>$this->input->post('last_name'),
				'api_key'=>$this->input->post('api_key'),
				'phone'=>$this->input->post('phone'),
				'company'=>$this->input->post('company'),
				'country'=>$this->input->post('country'),
				'website'=>$this->input->post('website'),
				'address'=>$this->input->post('address'),
				'profile_image'=>$profile_image
			);
			
			/* Check the logged in user can create users with posted user role*/
			$user_roles =  $this->users->user_roles_list();
			foreach($user_roles as $key => $value) {
				if($key==1) {
					if($this->ci_auth->is_superadmin()) {
						$data['user_roles'][$key]=$key;
					}
				} else if($key==2) {
					if($this->ci_auth->is_superadmin() || $this->ci_auth->is_admin()) {
						$data['user_roles'][$key]=$key;
					}
				} else {
				$data['user_roles'][$key]=$key;
				}
			}

			$newuser_role = $this->input->post('user_role');

			if(!in_array($newuser_role, $data['user_roles'])) { 
						$this->session->set_flashdata('errors', "You can't change the user to this user group");
						//redirect(site_url('admin/user/newuser/'));
			} else { 

				$uptUser=$this->ci_auth->edituser($user_id, $user_data, $user_profile_data);
				if($uptUser) {
							$this->session->set_flashdata('success', 'User profile updated');
							redirect(site_url('admin/user/edituser/'.$uptUser));
				} else {
						$errors = $this->ci_auth->get_error_message();
						foreach ($errors as $k => $v)	$data['errors'][$k] = $this->lang->line($v);
					$this->session->set_flashdata('errors', $data['errors']);
				}
			}
			
		} 
		$data['errors'] =  (validation_errors()) ? validation_errors() : $this->session->flashdata('errors');
		$data['success'] =  $this->session->flashdata('success');
		//$data['user_roles'] =  $this->users->user_roles_list();
		$user_roles =  $this->users->user_roles_list();
		foreach($user_roles as $key => $value) {
			if($key==1) {
				if($this->ci_auth->is_superadmin()) {
					$data['user_roles'][$key]=$value;
				}
			} else if($key==2) {
				if($this->ci_auth->is_superadmin() || $this->ci_auth->is_admin()) {
					$data['user_roles'][$key]=$value;
				}
			} else {
			$data['user_roles'][$key]=$value;
			}
		}
		$data['user_id'] = $user_id;
		$data['use_username'] = $use_username;
		$user_data1 = $this->users->user_data($user_id);
		$user_data = $user_data1[0];
		$data['first_name'] = array(
			'name'	=> 'first_name',
			'id'	=> 'first_name',
			'class' => 'required form-control',
			'placeholder'	=> 'First Name',
			'value' =>  $user_data->first_name,
			'maxlength'	=> 32,
			'size'	=> 30,
		);
		$data['last_name'] = array(
			'name'	=> 'last_name',
			'id'	=> 'last_name',
			'class' => 'required form-control',
			'placeholder'	=> 'Last Name',
			'value' =>  $user_data->last_name,
			'maxlength'	=> 32,
			'size'	=> 30,
		);
		$data['username'] = array(
			'name'	=> 'username',
			'id'	=> 'username',
			'class' => 'required form-control',
			'placeholder'	=> 'Username',
			'value' =>  $user_data->username,
			'maxlength'	=> 32,
			'size'	=> 30,
		);
		$data['email_address'] = array(
			'name'	=> 'email_address',
			'id'	=> 'email_address',
			'class' => 'required form-control',
			'placeholder'	=> 'Email address',
			'value' =>  $user_data->email,
			'maxlength'	=> 60,
			'size'	=> 60,
		);
		$data['password'] = array(
			'name'	=> 'password',
			'id'	=> 'password',
			'class' => 'form-control',
			'placeholder'	=> 'Password',
			'value' =>  '',
		);
		$data['confirm_password'] = array(
			'name'	=> 'confirm_password',
			'id'	=> 'confirm_password',
			'class' => 'form-control',
			'placeholder'	=> 'Confirm password',
			'value' =>  '',
		);
		$data['profile_image'] = array(
			'name'	=> 'profile_image',
			'id'	=> 'unstyled-file',
			'class' => 'profile-image',
		);
		$data['profile_image_img'] = isset($user_data->profile_image)?$user_data->profile_image:'';
		$data['deleteprofileimage'] = array(
			'type'  => 'checkbox',
			'name'  => 'deleteprofileimage',
			'id'    => 'deleteprofileimage',
			'value' => 1,
			'class' => 'hiddenpimage styled'
		);
		$data['api_key'] = array(
			'name'	=> 'api_key',
			'id'	=> 'api_key',
			'class' => 'form-control',
			'placeholder'	=> 'API Key',
			'value' =>  $user_data->api_key,
		);
		$data['phone'] = array(
			'name'	=> 'phone',
			'id'	=> 'phone',
			'class' => 'form-control',
			'placeholder'	=> 'Phone',
			'value' =>  $user_data->phone,
		);
		$data['company'] = array(
			'name'	=> 'company',
			'id'	=> 'company',
			'class' => 'form-control',
			'placeholder'	=> 'Company',
			'value' =>  $user_data->company,
		);
		$data['address'] = array(
			'name'	=> 'address',
			'id'	=> 'address',
			'class' => 'form-control',
			'placeholder'	=> 'Address',
			'value' =>  $user_data->address,
		);
		$data['website'] = array(
			'name'	=> 'website',
			'id'	=> 'website',
			'class' => 'form-control',
			'placeholder'	=> 'Website',
			'value' =>  $user_data->website,
		);
		$data['c_user_role'] = $user_data->gid;
		$this->load->view(get_theme_directory().'/edituser', $data);
			} else  {
				$this->session->set_flashdata('errors', 'You dont have permission to edit users.');
				redirect(site_url('/admin/user'));
			}
		} else {
				$this->session->set_flashdata('errors', 'You dont have permission to access the admin part of the site.');
				redirect(site_url('auth/profile/'));
		}
		}
	 }

	 
	/**
	 * Delete User Group
	 *
	 */
	 
	 function deleteuser($user_id) 
	 {
		 
		 
		if (!$this->ci_auth->is_logged_in()) {	
			redirect(site_url('/admin/login'));
		} elseif ($this->ci_auth->is_logged_in(FALSE)) {						// logged in, not activated
		redirect('/auth/sendactivation/');
		} else { /* logged in */
		$logged_in_user=$this->ci_auth->get_user_id();

		if($user_id==$logged_in_user) { 
			$this->session->set_flashdata('errors', 'You cant delete your account'); 
			redirect(site_url('admin/user/'));
		}
		
		if($this->ci_auth->canDo('access_backend')) {
			if($this->ci_auth->canDo('delete_users')) {
				
				if(!isset($user_id) || $user_id=='') { 
					$this->session->set_flashdata('errors', 'User does not exists'); 
					redirect(site_url('admin/user/'));
				} 
				
				if(!$this->ci_auth->is_superadmin()) {
					$del_user_group=$this->users->group_by_user_id($user_id);
					if($del_user_group==1) { 
						$this->session->set_flashdata('errors', 'You dont have permission to delete this account');
						redirect(site_url('admin/user/'));
					}
					
				}
				
				if(!$this->ci_auth->is_superadmin() && !$this->ci_auth->is_admin()) {
					$del_user_group=$this->users->group_by_user_id($user_id);
					if($del_user_group==2) { 
						$this->session->set_flashdata('errors', 'You dont have permission to delete this account');
						redirect(site_url('admin/user/'));
					}
					
				}
			
				$deleteUser=$this->users->delete_user($user_id); 
				
				if($deleteUser) {
					$this->session->set_flashdata('success', 'User deleted successfully');
				} else {
					$this->session->set_flashdata('errors', 'There is a problem while delete the user.');
				}
				redirect(site_url('admin/user/'));
			} else  {
				$this->session->set_flashdata('errors', 'You dont have permission to delete the users.');
				redirect(site_url('/admin/user'));
			}
		} else {
			$this->session->set_flashdata('errors', 'You dont have permission to access the admin part of the site.');
			redirect(site_url('/auth/login'));
		}
		
		}		
		
	 }

	/**
	 * Send email message of given type (activate, forgot_password, etc.)
	 *
	 * @param	string
	 * @param	string
	 * @param	array
	 * @return	void
	 */
	function _send_email($type, $email, &$data)
	{
		$this->load->library('email');
		$this->email->set_mailtype("html");
		$this->email->from($this->config->item('webmaster_email'), $this->config->item('website_name'));
		$this->email->reply_to($this->config->item('webmaster_email'), $this->config->item('website_name'));
		$this->email->to($email);
		$this->email->subject(sprintf($this->lang->line('auth_subject_'.$type), $this->config->item('website_name')));
		$this->email->message($this->load->view('email/'.$type.'-html', $data, TRUE));
		$this->email->set_alt_message($this->load->view('email/'.$type.'-txt', $data, TRUE));
		$this->email->send();
	}

}

/* End of file Users.php */
/* Location: ./modules/admin/controllers/Users.php */