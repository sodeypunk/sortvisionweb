<html ng-app="sortvision">
	<head>
		<meta charset="utf-8">
	    <meta http-equiv="X-UA-Compatible" content="IE=edge">
	    <meta name="viewport" content="width=device-width, initial-scale=1">
	    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
	    <meta name="description" content="">
	    <meta name="author" content="">
	    <link rel="icon" href="../../favicon.ico">
    
		<title>SortVISION</title>

        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
		<!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
	    <link href="<?php echo base_url("assets/css/ie10-viewport-bug-workaround.css"); ?>" rel="stylesheet">
	    <link href="<?php echo base_url("assets/css/dropzone.css"); ?>" rel="stylesheet">
		<link href="<?php echo base_url("assets/css/ekko-lightbox.min.css"); ?>" rel="stylesheet">
		<link href="<?php echo base_url("assets/css/dataTables.bootstrap.min.css"); ?>" rel="stylesheet">
	    <!-- Custom styles for this template -->
	    <link href="<?php echo base_url("assets/css/site.css"); ?>" rel="stylesheet">

	    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
	    <script>window.jQuery || document.write('<script src="<?php echo base_url("assets/js/jquery-1.11.3.min.js"); ?>"><\/script>')</script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
	    <!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
	    <script src="<?php echo base_url("assets/js/ie10-viewport-bug-workaround.js"); ?>"></script>
	    <script src="<?php echo base_url("assets/js/dropzone.js"); ?>"></script>
		<script src="<?php echo base_url("assets/js/ekko-lightbox-sortvision.js"); ?>"></script>
		<script src="<?php echo base_url("assets/js/jquery.dataTables.min.js"); ?>"></script>
		<script src="<?php echo base_url("assets/js/dataTables.bootstrap.min.js"); ?>"></script>
	    <script type="text/javascript" src="<?php echo base_url("assets/js/ie-emulation-modes-warning.js"); ?>"></script>

		<!--angular-->
        <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.5.7/angular.min.js"></script>
        <script src="<?php echo base_url("assets/js/sortvision.js"); ?>"></script>
		<script src="<?php echo base_url("assets/js/sortvision.account.js"); ?>"></script>

		<!--aws-->
		<script src="<?php echo base_url("assets/js/aws-cognito-sdk.min.js"); ?>"></script>
		<script src="<?php echo base_url("assets/js/amazon-cognito-identity.min.js"); ?>"></script>
		<script src="<?php echo base_url("assets/js/aws-sdk.min.js"); ?>"></script>

	</head>
	<body>

    <nav class="navbar navbar-inverse navbar-fixed-top">
      <div class="container">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="navbar-brand" href="https://www.sortvision.com"><img src="<?php echo base_url("assets/img/SORTVISION_100x43.png") ; ?>"></a>
			<div class="breadcrumb-container">
				<ul class="breadcrumb">
					<?php if(isset($breadcrumb))
					{
						echo $breadcrumb;
					}
					?>
				</ul>
			</div>
        </div>
		  <div id="navbar" class="navbar-collapse collapse">
			  <div class="navbar-right">
				  <?php
					if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] == true)
					{

						echo 	'<div style="display: block;">';
						echo 		$_SESSION['email'] . ' | ' . '<a href= "' . site_url('account/signout') . '"> Sign Out </a>';
						echo 	'</div>';
					}
				  	else {
						echo 	'<form id = "signin" class="navbar-form navbar-right" role = "form" method="POST" action="' . base_url() . 'index.php/account/signin">';
//						  if (!empty($errorMsg))
//						  {
//							  echo '<div class="alert alert-danger alert-navbar" role="alert">';
//							  echo $errorMsg;
//							  echo '</div>';
//						  }
//						echo 	'<div class="input-group" >';
//						echo 	'<span class="input-group-addon" ><i class="glyphicon glyphicon-user" ></i ></span >';
//						echo 	'<input id = "email" type = "email" class="form-control" name = "email" value = "" placeholder = "Email Address" >';
//						echo 	'</div >&nbsp;';
//
//						echo 	'<div class="input-group" >';
//						echo 	'<span class="input-group-addon" ><i class="glyphicon glyphicon-lock" ></i ></span >';
//						echo 	'<input id = "password" type = "password" class="form-control" name = "password" value = "" placeholder = "Password" >';
//						echo 	'</div >&nbsp;';

						echo 	'<a href="' . base_url() . 'index.php/account/login" style="margin-left: 5px;" class="btn btn-primary">Login</a>';
						echo 	'<a href="' . base_url() . 'index.php/account/register" style="margin-left: 5px;" class="btn btn-success">Sign up</a>';
						echo 	'</form >';
				  	}
				?>
			  </div>
		  </div>
      </div>
    </nav>