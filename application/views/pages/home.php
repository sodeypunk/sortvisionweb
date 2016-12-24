<!-- Main jumbotron for a primary marketing message or call to action -->
<div class="jumbotron">
	<div class="container">
		<h1>BibSmart - BibCommander</h1>
		<p>Auto bib detection and number recognition software.</p>
	</div>
    <div class="container center-div">
        <form class="form-signin" method="POST" action="<?php echo base_url(); ?>index.php/account/signin">
            <h2 class="form-signin-heading">Sign In</h2>
            <?php if (!empty($errorMsg))
            {
                echo '<div class="alert alert-danger">';
                echo $errorMsg;
                echo '</div>';
            }
            ?>
            <label for="email" class="sr-only">Email address</label>
            <input type="email" name="email" class="form-control" placeholder="Email address" required autofocus>
            <label for="password" class="sr-only">Password</label>
            <input type="password" name="password" class="form-control" placeholder="Password" required>
<!--            <div class="checkbox">-->
<!--                <label>-->
<!--                    <input type="checkbox" value="remember-me"> Remember me-->
<!--                </label>-->
<!--            </div>-->
            <button class="btn btn-lg btn-primary btn-block" type="submit">Sign in</button>
        </form>

    </div>
</div>