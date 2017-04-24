<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<?php if ($this->ci_auth->is_logged_in()) { ?>
<!-- Footer -->

<div class="footer clearfix">
  <div class="fotter_content">&copy; <?php echo date('Y');?>. <a href="<?php echo site_url();?>"><?php echo $this->config->item('website_name');?></a> by <a target="_blank" href="http://www.1stcoder.com/ci-membership/">1stCoder</a></div>
</div>
<!-- /footer -->
</div>
<!-- /page content -->
</div>
<!-- /page container -->
<?php } else { ?>
<!-- Footer -->
<div class="footer clearfix">
  <div class="fotter_content">&copy; <?php echo date('Y');?>. <a href="<?php echo site_url();?>"><?php echo $this->config->item('website_name');?></a> by <a target="_blank" href="http://www.1stcoder.com/ci-membership/">1stCoder</a></div>
</div>
<!-- /footer -->
<?php } ?>
</body></html>