<div class="container">
	<!-- Example row of columns -->
	<div class="row">
		<div class="col-sm-12">
			<h2>Cleanup</h2>
            <input type="button" class="btn btn-primary" value="Analysis" onclick="location.href='<?php echo base_url(); ?>index.php/analysis/index/<?php echo $ezRefString; ?>'"/>
            <br><br>
		</div>
	</div>
	<?php
		echo $tiledCleanupResultImages;
	?>
</div>
