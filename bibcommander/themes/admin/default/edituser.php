<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$this->load->view(get_theme_directory().'header');
?>

<!-- Page container -->

<div class="page-container">

<!-- sidebar -->
<?php $this->load->view(get_theme_directory().'left_sidebar'); ?>
<!-- /sidebar --> 

<!-- Page content -->
<div class="page-content">

<!-- Page header -->
<div class="page-header">
  <div class="page-title">
    <h3>Edit User<small>Edit <?php echo $this->config->item('website_name');?> user</small></h3>
  </div>
</div>
<!-- /page header -->

<?php if(isset($errors) && $errors!='') { ?>
<div class="alert alert-danger fade in block-inner alert-dismissible" role="alert">
  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
  <?php echo $errors; ?></div>
<?php } ?>
<?php if(isset($success) && $success!='') { ?>
<div class="alert alert-success fade in block-inner alert-dismissible" role="alert">
  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
  <?php echo $success; ?></div>
<?php } ?>
<?php if(isset($message) && $message!='') { ?>
<div class="alert alert-info fade in block-inner alert-dismissible" role="alert">
  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
  <?php echo $message; ?></div>
<?php } ?>

<!-- Breadcrumbs line -->
<div class="breadcrumb-line">
  <ul class="breadcrumb">
    <li><a href="<?php echo site_url('admin/dashboard');?>">Home</a></li>
    <li class="">Users</li>
    <li class="active">New User</li>
  </ul>
</div>
<!-- /breadcrumbs line -->

<div class="post_contents">
  <?php $attributes = array('class' => 'validate', 'id' => 'editUser'); echo form_open_multipart($this->uri->uri_string(), $attributes) ?>
  <div class="block">
    <div class="form-group">
      <div class="row">
        <div class="col-md-6">
          <label>First Name: <span class="mandatory">*</span></label>
          <?php echo form_input($first_name); ?> </div>
        <div class="col-md-6">
          <label>Last Name: <span class="mandatory">*</span></label>
          <?php echo form_input($last_name); ?> </div>
      </div>
    </div>
    <div class="form-group">
      <div class="row">
        <?php if ($use_username){ ?>
        <div class="col-md-6">
          <label>Username: <span class="mandatory">*</span></label>
          <?php echo form_input($username); ?> </div>
        <?php } ?>
        <div class="col-md-6">
          <label>Email address: <span class="mandatory">*</span></label>
          <?php echo form_input($email_address); ?> </div>
      </div>
    </div>
    <div class="form-group">
      <div class="row">
        <div class="col-md-6">
          <label>Password: <span class="mandatory">*</span></label>
          <?php echo form_password($password); ?> </div>
        <div class="col-md-6">
          <label>Confirm Password: <span class="mandatory">*</span></label>
          <?php echo form_password($confirm_password); ?> </div>
      </div>
    </div>
    <div class="form-group">
      <div class="row">
        <div class="col-md-6">
          <label>User Role: <span class="mandatory">*</span></label>
          <br />
          <?php 
			$user_role_atts='data-placeholder="Choose a Status" class="select-full  required" tabindex="2"';
			echo form_dropdown('user_role', $user_roles, $c_user_role ,$user_role_atts); 
			?>
        </div>
        <div class="col-md-6">
          <label>API Key: </label>
          <?php echo form_input($api_key); ?>
        </div>
      </div>
    </div>
    <div class="form-group">
      <div class="row">
        <div class="col-md-6">
          <label>Phone: </label>
          <?php echo form_input($phone); ?> </div>
        <div class="col-md-6">
          <label>Company: </label>
          <?php echo form_input($company); ?> </div>
      </div>
    </div>
    <div class="form-group">
      <div class="row">
        <div class="col-md-12">
          <label>Address: </label>
          <?php echo form_textarea($address); ?> </div>
      </div>
    </div>
    <div class="form-group">
      <div class="row">
<!--        <div class="col-md-6">-->
<!--          <label>Country: </label>-->
<!--          --><?php //
//			$country_atts='data-placeholder="Choose a Country" tabindex="2"';
//			$selected_country=$this->form_validation->set_value('country');
//			if(!isset($selected_country) || $selected_country=='') { $selected_country=''; }
//			echo country_dropdown('country', 'country', 'select-full', $selected_country, array(), '', $selection=NULL, $show_all=TRUE, $country_atts);
//			?>
<!--        </div>-->
        <div class="col-md-6">
          <label>Website: </label>
          <?php echo form_input($website); ?>
        </div>
      </div>
    </div>
    <div class="form-group">
      <div class="row">
        <div class="col-md-6">
          <label>Profile Image: </label>
          <?php echo form_upload($profile_image); ?> </div>
      </div>
    </div>
    <?php if(isset($profile_image_img) && $profile_image_img!='') { ?>
    <div class="form-group">
      <div class="row">
        <div class="col-md-6">
          <div class="profileImage"> <img style="width:200px" src="<?php echo site_url().'uploads/images/profiles/'.$profile_image_img; ?>" alt="Profile Image" /> </div>
          <div class="permission_group">
            <label class="checkbox-inline checkbox-info">
            <div class="checker"> <?php echo form_checkbox($deleteprofileimage); ?> </div>
            Delete Profile Image
            </label>
          </div>
        </div>
      </div>
    </div>
    <?php } ?>
    <?php echo form_hidden('user_id',$user_id); ?> </div>
  <div class="form-actions text-right">
    <input type="submit" class="btn btn-primary" value="Submit">
  </div>
</div>
<?php echo form_close(); ?>
<?php $this->load->view(get_theme_directory().'footer'); ?>
