<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$this->load->view(get_template_directory().'header');
?>

<div class="page-container"> 
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
        </div>
        <div class="main_content pull-right col-md-10">
          <h3 class="profiletitle"><span class="profile_title"><?php echo $profile->first_name.' '.$profile->last_name;?></span></h3>
          <div class="user_profile">
            <div class="profile_fields">
              <label>First name:</label>
              <span><?php echo $profile->first_name?$profile->first_name:'--';?></span></div>
            <div class="profile_fields">
              <label>Last name:</label>
              <span><?php echo $profile->last_name?$profile->last_name:'--';?></span></div>
            <div class="profile_fields">
              <label>Email address:</label>
              <span><?php echo $profile->email?$profile->email:'--';?></span></div>
            <?php if ($use_username){ ?>
            <div class="profile_fields">
              <label>Username:</label>
              <span><?php echo $profile->username?$profile->username:'--';?></span></div>
            <?php } ?>
            <div class="profile_fields">
              <label>API Key:</label>
              <span><?php echo $profile->api_key?$profile->api_key:'--';?></span></div>
            <div class="profile_fields">
              <label>Phone:</label>
              <span><?php echo $profile->phone?$profile->phone:'--';?></span></div>
            <div class="profile_fields">
              <label>Company:</label>
              <span><?php echo $profile->company?$profile->company:'--';?></span></div>
<!--            <div class="profile_fields">-->
<!--              <label>Country:</label>-->
<!--              <span>--><?php //echo $profile->country?$profile->country:'--';?><!--</span></div>-->
            <div class="profile_fields">
              <label>Website:</label>
              <span><?php echo $profile->website?$profile->website:'--';?></span></div>
            <div class="profile_fields">
              <label>Address:</label>
              <span><?php echo $profile->address?$profile->address:'--';?></span></div>
          </div>
          <p><a href="<?php echo site_url('auth/profile/editprofile');?>" class="btn btn-warning">Edit profile</a></p>
        </div>
      </div>
    </div>
  </div>
</div>
<?php $this->load->view(get_template_directory().'footer'); ?>
