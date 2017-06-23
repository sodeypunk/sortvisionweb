<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$this->load->view(get_template_directory() . 'header');
?>

<div class="container">
	<div class="page-content">
		<div class="page-content-inner">
			<!-- Page header -->
			<div class="page-header">
				<div class="page-title profile-page-title">
					<h2><a href="<?php echo site_url('/bibsmart'); ?>">BibSmart</a> > File Status</h2>
				</div>
			</div>
			<input type="hidden" id="apikey" value="<?php echo $profile->api_key ? $profile->api_key : ''; ?>">
			<input type="hidden" id="fileid" value="<?php echo $fileid; ?>">
			<div class="row">
				<div class="col-sm-6">
					<?php echo '<h2>File Status</h2>'; ?>
					<?php
					if ($status != null && $status != "")
					{
						echo '<p>File: ' . $filePath . '</p>';
						echo '<p>Status: <span id="status">' . $status . '</span></p>';
						echo '<p>Draw Images: ' . $drawimages . '</p>';
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
				</div>
			</div>
			<br/>
			<br/>
			<div class="row">
				<div class="col-sm-12">
					<h2>Result Images</h2>
				</div>
			</div>
			<div class="row">
				<div class="col-sm-12">
					<div id="result-image">
						<table id="results-table" class="table table-striped">
							<thead>
							<tr>
								<th>#</th>
								<th>ID</th>
								<th>FILE</th>
								<th>LABELS</th>
								<th width="30%">IMAGE</th>
							</tr>
							</thead>
							<tbody>
							<?php

							$rowNum = 0;
							foreach ($resultImages as $row)
							{
								$rowNum++;
								echo "<tr>";
								echo "<td>" . $rowNum . "</td>";
								echo "<td>" . $row["ID"] . "</td>";
								echo "<td>" . $row["IMAGE"] . "</td>";
								echo "<td>" . $row["LABELS_STRING"] . "</td>";
								if ($drawimages == 'True')
								{
									echo "<td><a href=\"" . $row["IMAGE_PATH"] . "\" data-toggle=\"lightbox\" data-gallery=\"image-gallery\" data-id=\"" . $row["ID"] . "\"><img ng-src=\"" . $row["IMAGE_PATH"] . "\" alt=\"" . $row["IMAGE"] . "\" class=\"img-responsive\" title=\"" . $row["IMAGE"] . "\"></a></td>";
								}
								else
								{
									echo "<td>-</td>";
								}
								echo "</tr>";
							}
							?>
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<?php $this->load->view(get_template_directory() . 'footer'); ?>

<script>
$(document).on('click', '[data-toggle="lightbox"]', function(event) {
	event.preventDefault();
	$(this).ekkoLightbox();
});

$(document).ready(function() {
//$(function() {

	update_status();
    var intervalId = setInterval(function(){ update_status(); }, 5000);
    //$("#status-table tr:last td:first").prepend('<img id="statusgif" src="../../../bibcommander/assets/img/loading_sm_tr.gif">')

	function update_status() {

		var fileid = $("#fileid").val();
		var apikey = $("#apikey").val();

		$.ajax({
			url: "<?php echo base_url(); ?>index.php/files/getupdate",
			type: "POST",
			async: true,
			data: { fileid: fileid, apikey: apikey }
		}).done(function(data, response) {
			
			var jsonResponse = JSON.parse(data);
			var percent = jsonResponse["PERCENT"];
			var status = jsonResponse["STATUS"];
	
			$("#status-progress-bar").text(percent + "%");
			$("#status-progress-bar").css('width', percent+'%').attr('aria-valuenow', percent);

			if (status == "COMPLETED" && percent >= 100) {
				$("#statusgif").hide();
                clearInterval(intervalId);
			}
			
		});
	}

	$("#results-table").DataTable( {
		"lengthMenu": [[25, 50, 100, 200, 500, -1], [25, 50, 100, 200, 500, "All"]],
		destroy: true
	} );
	
});

</script>