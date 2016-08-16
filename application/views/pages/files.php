<div class="container">
	<!-- Example row of columns -->
	<div class="row">
		<div class="col-sm-6">
			<?php echo '<h2>FILE STATUS | <a href="http://sortvision.localhost.com/index.php/cleanup/index/' . $ezRefString . '">Cleanup</a></h2>'; ?>
			<h5>Note: Please refresh this page for updates on your image</h5>
			<?php 
			if ($status != null && $status != "") 
			{
				echo '<p>Image: ' . $fileNm . '</p>';
				echo '<p>Status: <span id="status">' . $status . '</span></p>';
				echo '<p>Last Update: ' . $uploadedDt . '</p>';
				echo '<div class="progress">';
				echo '<div id="status-progress-bar" class="progress-bar progress-bar-striped active" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%;">';
				echo '0%';
				echo '</div>';
				echo '</div>';
			}
			else
			{
				echo "<p>No status available</p>";
			}
			?>

			<table id="status-table" class="table table-striped">
				<thead>
					<tr>
						<th>Detail</th>
						<th>Time</th>
					</tr>
				</thead>
				<tbody>
					<tr><td>Initializing...</td><td></td></tr>
					<?php
					if (!empty($filesHistory))
					{
						foreach ($filesHistory as $row)
						{
							if ($row['DESCR'] != "")
							{
								echo "<tr>";
								echo "<td>" . $row['DESCR'] . "</td>";
								echo "<td>" . $row['UPDT'] . "</td>";
								echo "</tr>";
							}
						}
					}
					
					?>
				</tbody>
			</table>
		</div>
		<div class="col-sm-6">
			<?php
			if ($status != null && $status != "")
			{
                echo $tiledUploadedImages;
			}?>
		</div>
	</div>
	<div class="row">
		<div class="col-sm-12">
			<h2>Result</h2>
		</div>
	</div>
	<div class="row">
		<div class="col-sm-12">
			<div id="result-image">
				<?php
				if ($status == "COMPLETED")
				{
                    echo $tiledResultImages;
				}
				?>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-sm-12">
			<h2>Cleanup</h2>
		</div>
	</div>
	<div class="row">
		<div class="col-sm-12">
			<div id="result-image-cleanup">
				<?php
				if ($status == "COMPLETED")
				{
					echo $tiledCleanupResultImages;
				}
				?>
			</div>
		</div>
	</div>
</div>

<script>
$(document).ready(function() {
//$(function() {

	//update_status();
    var intervalId = setInterval(function(){ update_status(); }, 5000);
    $("#status-table tr:last td:first").prepend('<img id="statusgif" src="../../../assets/img/loading_sm_tr.gif">')

	function update_status() {
		$.ajax({
			type: "POST",
			url: "<?php echo base_url(); ?>index.php/files/getupdate",
			data: { ezRefString: "<?php echo $ezRefString; ?>" }
		}).done(function(data, response) {
			
			var jsonResponse = JSON.parse(data);
			var percent = jsonResponse["PERCENT"];
			var status = jsonResponse["STATUS"];
				
			var status_table_html = jsonResponse["STATUS_TABLE_HTML"];
			var image_html = jsonResponse["IMAGE_HTML"];
			var image_html_cleanup = jsonResponse["IMAGE_HTML_CLEANUP"];
	
			$("#status-progress-bar").text(percent + "%");
			$("#status-progress-bar").css('width', percent+'%').attr('aria-valuenow', percent);
			$("#status").html(status);
			$("#status-table tbody").html(status_table_html);
			$("#result-image").html(image_html);
			$("#result-image-cleanup").html(image_html_cleanup);
            $("#status-table tr:last td:first").prepend('<img id="statusgif" src="../../../assets/img/loading_sm_tr.gif">')

			if (status == "COMPLETED" && percent >= 100) {
				$("#statusgif").hide();
                clearInterval(intervalId);
			}
			
		});
	}
	
});

</script>