<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$uri_segment_2 = $this->uri->segment(2);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1, maximum-scale=1">
    <link rel="icon" href="<?php echo base_url(); ?>assets/images/favicon.ico"/>
    <link rel="alternate" href="<?php echo base_url(); ?>" hreflang="x-default"/>
    <title><?php echo (!empty($seo_title)) ? $seo_title . ' - ' : '';
        echo $this->config->item('website_name'); ?></title>
    <link href="http://fonts.googleapis.com/css?family=Open+Sans:400,300,600,700&amp;subset=latin,cyrillic-ext"
          rel="stylesheet" type="text/css">
    <link href='http://fonts.googleapis.com/css?family=Oswald:700,400' rel='stylesheet' type='text/css'>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css">
    <link href="<?php echo base_url("assets/css/dataTables.bootstrap.min.css"); ?>" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/css/icons.css"/>
    <link rel="stylesheet" type="text/css" href="<?php echo get_template(); ?>/css/cimembership.css"/>
    <link rel="stylesheet" type="text/css" href="<?php echo get_template(); ?>/css/styles.css"/>
    <!-- Custom styles for this template -->
    <link href="<?php echo base_url("assets/css/site.css"); ?>" rel="stylesheet">
    <script src="<?php echo base_url(); ?>assets/js/jquery-1.11.3.min.js"></script>
    <script src="<?php echo base_url(); ?>assets/js/jquery-migrate-1.2.1.min.js"></script>
    <script src="<?php echo base_url(); ?>assets/js/jquery-ui.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    <script src="<?php echo base_url("assets/js/jquery.dataTables.min.js"); ?>"></script>
    <script src="<?php echo base_url("assets/js/dataTables.bootstrap.min.js"); ?>"></script>
    <script src='https://www.google.com/recaptcha/api.js'></script>
    <script type="text/javascript" src="<?php echo get_template(); ?>js/plugins/forms/jquery.uniform.min.js"></script>
    <script type="text/javascript" src="<?php echo get_template(); ?>js/plugins/forms/validate.min.js"></script>
    <script type="text/javascript" src="<?php echo get_template(); ?>js/plugins/forms/select2.min.js"></script>
    <script type="text/javascript" src="<?php echo get_template(); ?>js/scripts.js"></script>
</head>
<body class="<?php if ($this->ci_auth->is_logged_in()) {
} else {
    echo 'full-width page-condensed';
} ?>">
<!-- Navbar -->
<div class="navbar navbar-inverse navbar-fixed-top" role="navigation">
    <div class="container">
        <?php if (!$this->ci_auth->is_logged_in() && !$this->ci_auth->is_logged_in(FALSE)) { ?>
            <div class="navbar-header">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#navbar-icons"><span
                        class="sr-only">Toggle navbar</span> <i class="icon-grid3"></i></button>
                <a class="navbar-brand" href="https://www.sortvision.com"><img
                        src="<?php echo base_url("assets/img/SORTVISION_100x43.png"); ?>"></a>
            </div>
            <ul class="nav navbar-nav navbar-right collapse" id="navbar-icons">
                <li class="user <?php if (strtolower($uri_segment_2) == 'login') {
                    echo 'open';
                } ?>"><a href="<?php echo site_url('auth/login'); ?>"><span>Login</span></a></li>
                <li class="user <?php if (strtolower($uri_segment_2) == 'register') {
                    echo 'open';
                } ?>"><a href="<?php echo site_url('auth/register'); ?>"><span>Create Account</span></a></li>
                <li class="user dropdown <?php if (strtolower($uri_segment_2) == 'sendactivation' || strtolower($uri_segment_2) == 'forgotpassword' || strtolower($uri_segment_2) == 'retrieveusername') {
                    echo 'active';
                } ?>"><a class="dropdown-toggle" data-toggle="dropdown"><span>Login help</span> <i class="caret"></i>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-right icons-right">
                        <li><a class="<?php if (strtolower($uri_segment_2) == 'retrieveusername') {
                                echo 'active';
                            } ?>" href="<?php echo site_url('auth/retrieveusername'); ?>">Retrieve username</a></li>
                        <li><a class="<?php if (strtolower($uri_segment_2) == 'forgotpassword') {
                                echo 'active';
                            } ?>" href="<?php echo site_url('auth/forgotpassword'); ?>">Retrieve password</a></li>
                        <li><a class="<?php if (strtolower($uri_segment_2) == 'sendactivation') {
                                echo 'active';
                            } ?>" href="<?php echo site_url('auth/sendactivation'); ?>">Resend activation link</a></li>
                    </ul>
                </li>
            </ul>
        <?php } else { ?>
            <div class="navbar-header">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#navbar-icons"><span
                        class="sr-only">Toggle navbar</span> <i class="icon-grid3"></i></button>
                <a class="navbar-brand" href="https://www.sortvision.com"><img
                        src="<?php echo base_url("assets/img/SORTVISION_100x43.png"); ?>"></a>
            </div>
            <ul class="nav navbar-nav navbar-right collapse" id="navbar-icons">
                <li class="user dropdown"><a class="dropdown-toggle"
                                             data-toggle="dropdown"><span>Hi <?php echo $this->ci_auth->username(); ?></span>
                        <i class="caret"></i> </a>
                    <ul class="dropdown-menu dropdown-menu-right icons-right">
                        <li><a href="<?php echo site_url('auth/profile'); ?>">Profile</a></li>
                        <li><a href="<?php echo site_url('auth/bibsmart'); ?>">BibSmart</a></li>
                        <li><a href="<?php echo site_url('auth/logout'); ?>">Logout</a></li>
                    </ul>
                </li>
            </ul>
        <?php } ?>
    </div>
</div>
<!-- /navbar -->
<div id="main_container" class="container">
