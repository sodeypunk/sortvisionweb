<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$this->load->view(get_template_directory().'header');
?>

<div class="register_container">
  <h1 class="text-center page-title">Register</h1>
  <div class="register_content well">
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
    <?php $attributes = array('class' => 'validate', 'id' => 'loginForm'); echo form_open($this->uri->uri_string(), $attributes) ?>
    <div class="form-group has-feedback">
      <label>First name: <span class="mandatory">*</span></label>
      <?php echo form_input($first_name); ?></div>
    <div class="form-group has-feedback">
      <label>Last name: <span class="mandatory">*</span></label>
      <?php echo form_input($last_name); ?></div>
    <?php if ($use_username){ ?>
    <div class="form-group has-feedback">
      <label>Username: <span class="mandatory">*</span></label>
      <?php
          echo form_input($username);
      ?></div>
    <?php }?>
    <div class="form-group has-feedback">
      <label>Email address: <span class="mandatory">*</span></label>
      <?php echo form_input($email); ?></div>
    <div class="form-group has-feedback">
      <label>Password: <span class="mandatory">*</span></label>
      <?php echo form_password($password); ?></div>
    <div class="form-group has-feedback">
      <label>Confirm Password: <span class="mandatory">*</span></label>
      <?php echo form_password($confirm_password); ?></div>
    <?php if ($show_captcha) {

		if ($use_recaptcha) { /* Google Recaptcha Part*/?>
    <div class="form-group has-feedback">
      <label>Captcha:</label>
      <div class="g-recaptcha" data-size="normal" data-sitekey="<?php echo $this->config->item('recaptcha_sitekey'); ?>" style="transform:scale(0.88);transform-origin:0;-webkit-transform:scale(0.88);transform:scale(0.88);-webkit-transform-origin:0 0;transform-origin:0 0; 0"></div>
    </div>
    <?php } else { ?>
    <div class="form-group has-feedback">
      <label>Enter the code exactly as it appears:</label><br />
      <?php echo $captcha_html; ?></div>
    <div class="form-group has-feedback">
      <label>Confirmation Code</label>
      <?php echo form_input($captcha); ?> <i class="icon-shield form-control-feedback"></i> </div>
    <?php } } ?>
    <div class="row form-actions">
      <div class="col-xs-6"> </div>
      <div class="col-xs-6"> <?php echo form_button($submit); ?> </div>
    </div>
    <?php echo form_close(); ?> </div>
  <div id="oauth_container" class="col-lg-8 center-box clearfix">
<!--    <p>Or Register with the following</p>-->
    <ul class="list-inline oauth_ul text-center">
      <?php if($this->config->item('enable_facebook')==1) { ?>
      <li><a class="ci_facebook" href="<?php echo site_url('auth/oauth2/facebook');?>"></a></li>
      <?php }?>
      <?php if($this->config->item('enable_twitter')==1) { ?>
      <li><a class="ci_twitter" href="<?php echo site_url('auth/oauth/twitter');?>"></a></li>
      <?php }?>
      <?php if($this->config->item('enable_gplus')==1) { ?>
      <li><a class="ci_google" href="<?php echo site_url('auth/oauth2/google');?>"></a></li>
      <?php }?>
      <?php if($this->config->item('enable_linkedin')==1) { ?>
      <li><a class="ci_linkedin" href="<?php echo site_url('auth/oauth2/linkedin');?>"></a></li>
      <?php }?>
      <?php if($this->config->item('enable_github')==1) { ?>
      <li><a class="ci_github" href="<?php echo site_url('auth/oauth2/github');?>"></a></li>
      <?php }?>
      <?php if($this->config->item('enable_instagram')==1) { ?>
      <li><a class="ci_instagram" href="<?php echo site_url('auth/oauth2/instagram');?>"></a></li>
      <?php }?>
      <?php if($this->config->item('enable_microsoft')==1) { ?>
      <li><a class="ci_windows" href="<?php echo site_url('auth/oauth/microsoft');?>"></a></li>
      <?php }?>
      <?php if($this->config->item('enable_envato')==1) { ?>
      <li><a class="ci_envato" href="<?php echo site_url('auth/oauth/envato');?>"></a></li>
      <?php }?>
      <?php if($this->config->item('enable_bitbucket')==1) { ?>
      <li><a class="ci_bitbucket" href="<?php echo site_url('auth/oauth/bitbucket');?>"></a></li>
      <?php }?>
      <?php if($this->config->item('enable_paypal')==1) { ?>
      <li><a class="ci_paypal" href="<?php echo site_url('auth/oauth/paypal');?>"></a></li>
      <?php }?>
      <?php if($this->config->item('enable_yandex')==1) { ?>
      <li><a class="ci_yandex" href="<?php echo site_url('auth/oauth/yandex');?>"></a></li>
      <?php }?>
    </ul>
  </div>
</div>
<?php $this->load->view(get_template_directory().'footer'); ?>
