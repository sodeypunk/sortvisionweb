<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$this->load->view(get_template_directory().'header');
?>

<div class="page-container edit_profile_page"> 
  <!-- Content -->
  <div class="page-content">
    <div class="page-content-inner"> 
      
      <!-- Page header -->
      <div class="page-header">
        <div class="page-title profile-page-title">
          <h2>User profile</h2>
        </div>
      </div>
      <div class="row">
        <div class="left_sidebar pull-left col-md-2">
          <div class="profileImage">
            <?php if(isset($profile->profile_image) && $profile->profile_image!='') {  ?>
            <a class="deleteImage btn btn-icon btn-danger" style="display:none"><i class="icon-remove2"></i></a>
            <img src="<?php echo site_url().'uploads/images/profiles/'.$profile->profile_image; ?>" alt="<?php echo $profile->username;?>" />
            <?php } else {  
			  $size = 170; 
			  $default = site_url().'uploads/images/profiles/profile.jpg'; 
			  $default = '';
			  $grav_url = "http://www.gravatar.com/avatar/" . md5( strtolower( trim( $profile->email ) ) ) . "?d=" . urlencode( $default ) . "&s=" . $size; 
			  ?>
            <img src="<?php echo $grav_url; ?>" alt="<?php echo $profile->username;?>" />
            <?php } ?>
          </div>
          <p class="username"><?php echo $profile->first_name.' '.$profile->last_name;?></p>
          <p class="emailaddress"><?php echo $profile->email;?></p>
        </div>
        <div class="main_content pull-right col-md-10">
          <h3 class="profiletitle"><span class="profile_title"><?php echo $profile->first_name.' '.$profile->last_name;?></span></h3>
          <?php if(!empty($errors)) { if(is_array($errors)) {  ?>
          <div class="alert alert-danger  alert-dismissible fade in block-inner">
            <button class="close" data-dismiss="alert" type="button">&times;</button>
            <?php foreach($errors as $error) { echo '<p>'.$error.'</p>'; }  ?>
          </div>
          <?php } else { ?>
          <div class="alert alert-danger  alert-dismissible fade in block-inner">
            <button class="close" data-dismiss="alert" type="button">&times;</button>
            <?php echo $errors; ?></div>
          <?php } } ?>
          <?php if(isset($success) && $success!='') { ?>
          <div class="alert alert-success fade in block-inner alert-dismissible" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <?php echo $success; ?></div>
          <?php } ?>
          <?php if(isset($message) && $message!='') { ?>
          <div class="alert alert-success fade in block-inner alert-dismissible" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <?php echo $message; ?></div>
          <?php } ?>
          <?php $attributes = array('class' => 'validate', 'id' => 'editUser'); echo form_open_multipart($this->uri->uri_string(), $attributes) ?>
          <div class="user_profile row">
            <div class="edit_profile_fields">
              <div class="col-md-6">
                <label>First name:</label>
                <?php echo form_input($first_name); ?></div>
            </div>
            <div class="edit_profile_fields">
              <div class="col-md-6">
                <label>Last name:</label>
                <?php echo form_input($last_name); ?></div>
            </div>
            <div class="edit_profile_fields">
              <div class="col-md-6">
                <label>Email address:</label>
                <?php echo form_input($email); ?></div>
            </div>
            <div class="edit_profile_fields">
              <div class="col-md-6">
                <label>Username:</label>
                <?php echo form_input($username); ?></div>
            </div>
            <div class="edit_profile_fields">
              <div class="col-md-6">
                <label>Password:</label>
                <?php echo form_password($password); ?></div>
            </div>
            <div class="edit_profile_fields">
              <div class="col-md-6">
                <label>Confirm password:</label>
                <?php echo form_password($confirm_password); ?></div>
            </div>
            <div class="edit_profile_fields">
              <div class="col-md-6">
                <label>Profile Image:</label>
                <?php echo form_upload($profile_image); ?></div>
            </div>
            <div class="edit_profile_fields">
              <div class="col-md-6">
                <label>Phone:</label>
                <?php echo form_input($phone); ?></div>
            </div>
            <div class="edit_profile_fields">
              <div class="col-md-6">
                <label>Company:</label>
                <?php echo form_input($company); ?></div>
            </div>
            <div class="edit_profile_fields">
              <div class="col-md-6">
                <label>Country:</label>
                <?php 
				$country_atts='data-placeholder="Choose a Country" tabindex="2"';
				$selected_country=$profile->country;
				if(!isset($selected_country) || $selected_country=='') { $selected_country=''; }
				echo country_dropdown('country', 'country', 'select-full', $selected_country, array(), '', $selection=NULL, $show_all=TRUE, $country_atts);
                ?>
              </div>
            </div>
            <div class="edit_profile_fields">
              <div class="col-md-6">
                <label>Website:</label>
                <?php echo form_input($website); ?></div>
            </div>
            <div class="edit_profile_fields">
              <div class="col-md-6">
                <label>Address:</label>
                <?php echo form_textarea($address); ?></div>
            </div>
          </div>
          <div class="clear"></div>
          <?php echo form_input($deleteprofileimage); ?>
          <div class="form-actions"> <?php echo form_button($submit);?> </div>
          <?php echo form_close(); ?> </div>
      </div>
    </div>
  </div>
</div>
<script type="text/javascript">
$("div.profileImage").on({
    mouseenter: function () {
		$(this).children('a.deleteImage').show();
    },
    mouseleave: function () {
		$(this).children('a.deleteImage').hide();
    }
});

$(document).on('click', 'a.deleteImage', function() {
	$(this).parent('div.profileImage').children('img').attr('src','');
	<?php 
		$size = 170; 
		$default = site_url().'uploads/images/profiles/profile.jpg'; 
		$default = '';
		$grav_url = "http://www.gravatar.com/avatar/" . md5( strtolower( trim( $profile->email ) ) ) . "?d=" . urlencode( $default ) . "&s=" . $size; 
	?>
	$(this).parent('div.profileImage').children('img').attr('src','<?php echo $grav_url;?>');
	$(this).remove();
	$('#deleteprofileimage').val(1);
});
</script>
<?php $this->load->view(get_template_directory().'footer'); ?>
