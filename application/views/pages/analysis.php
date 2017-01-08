<div class="container">
	<!-- Example row of columns -->
	<div class="row">
		<div class="col-sm-12">
			<h2>Analysis</h2>
			<div class="panel panel-default">
				<div class="panel-heading">Description</div>
				<div class="panel-body">
					<p>
						This page allows you to filter out any numbers that should not belong in the detected bib results.
					</p>
					<p>
						The progress bar below will indicate how many images currently are currently in a good, partial or cleanup state.
						<ul>
							<li>
								<span style="color: green;">Good</span> - Indicates the image has passed all filters and all of its bib/label results will be kept as is.
							</li>
							<li>
								<span style="color: orange;">Partial</span> - Indicates the image has some bib/label results that has not passed the filters and should be double checked in the cleanup phase.
							</li>
							<li>
								<span style="color: red;">Cleanup</span> - Indicates the image has not passed any of the filters.
							</li>
						</ul>
					</p>
				</div>
			</div>

		</div>
	</div>
	<form method="POST" action="<?php echo base_url(); ?>index.php/analysis/analysis">
	<div class="row">
		<div class="col-sm-12 col-md-6">
			<div class="panel panel-default">
				<div class="panel-heading">Filter options</div>
				<div class="panel-body">
					<div id="filters">
						<input type="hidden" name="fileId" value="<?php echo $fileId; ?>">
						<input type="checkbox" name="filter-atleast-one" <?php if ($filterAtLeastOne) echo "checked"; ?>> Image must have at least one bib
						<span style="margin-left: 10px;" id="filter-help-atleast-one" class="glyphicon glyphicon-question-sign" aria-hidden="true" data-toggle="popover"></span>
						<br><br>

						<input type="radio" name="filter-label-contains-choice" value="label" <?php if ($filterLabelContainsChoice == "label") echo "checked"; ?>> Remove label from image
	<!--					<input type="radio" name="filter-label-contains-choice" value="image" --><?php //if ($filterLabelContainsChoice == "image") echo "checked"; ?><!--> <!--Remove image from clean set-->
						<span style="margin-left: 10px;" id="filter-help-contains" class="glyphicon glyphicon-question-sign" aria-hidden="true" data-toggle="popover"></span>
						<div class="input-group">
							<span class="input-group-addon" id="basic-addon1">Labels that contains (ex: 1,2,3; 6-10; 9999+)</span>
							<input type="text" class="form-control" name="filter-label-contains-value" value="<?php echo $filterLabelContainsValue; ?>">
						</div>
						<br><br>
						<input type="submit" class="btn btn-primary" name="action" value="Preview"/>
						<input type="submit" class="btn btn-primary" name="action" value="Reset"/>
						<input type="submit" class="btn btn-primary" name="action" value="Update"/>
						<input type="button" class="btn btn-primary" value="Cleanup" onclick="location.href='<?php echo base_url(); ?>index.php/cleanup/index/<?php echo $fileId; ?>'"/>


						<!--					<input type="radio" name="filter-label-length-choice" value="label" --><?php //if ($filterLabelLengthChoice == "label") echo "checked"; ?><!--> <!--Remove label from image-->
	<!--					<input type="radio" name="filter-label-length-choice" value="image" --><?php //if ($filterLabelLengthChoice == "image") echo "checked"; ?><!--> <!--Remove image from clean set-->
	<!--					<span style="margin-left: 10px;" id="filter-help-at-least-one" class="glyphicon glyphicon-question-sign" aria-hidden="true" data-toggle="popover"></span>-->
	<!--					<div class="input-group">-->
	<!--						<span class="input-group-addon" id="basic-addon2">Labels that have lengths greater than</span>-->
	<!--						<input type="text" class="form-control" name="filter-label-length-value" value="--><?php //echo $filterLabelLengthValue; ?><!--">-->
	<!--					</div>-->
					</div>
				</div>
		</div>
	</div>
	<div class="row">
		<div class="col-sm-12">
			<div class="progress">
				<div class="progress-bar progress-bar-success" role="progressbar" style="width:<?php echo $goodPercent; ?>%">
					Good <span id="good-percent"><?php echo $goodPercent; ?>%</span>
				</div>
				<div class="progress-bar progress-bar-warning" role="progressbar" style="width:<?php echo $partialPercent; ?>%">
					Partial <span id="good-percent"><?php echo $partialPercent; ?>%</span>
				</div>
				<div class="progress-bar progress-bar-danger" role="progressbar" style="width:<?php echo $cleanupPercent; ?>%">
					Full Cleanup <span id="good-percent"><?php echo $cleanupPercent; ?>%</span>
				</div>
			</div>
		</div>
	</div>

	</form>

	<h4>Click on each section below to see filter results</h4>

	<div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
		<div class="panel panel-default">
			<div class="panel-heading" role="tab" id="headingOne">
				<h4 class="panel-title">
					<a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
						<span style="color: red;">Full Cleanup</span> - <?php echo $cleanupPercent; ?>% - <?php echo $numCleanupImages; ?> images
					</a>
				</h4>
			</div>
			<div id="collapseOne" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingOne">
				<div class="panel-body">
					<table id="cleanup-results-table" class="table table-striped">
						<thead>
						<tr>
							<th>#</th>
							<th>ID</th>
							<th>IDFILE</th>
							<th>IMAGE</th>
							<th>LABELS</th>
							<th>LABELS REMOVED</th>
							<th>CLEANUP</th>
							<th>UPDT</th>
						</tr>
						</thead>
						<tbody>
						<?php

						$rowNum = 0;
						foreach ($resultsClientCleanup as $row)
						{
							$rowNum++;
							echo "<tr>";
							echo "<td>" . $rowNum . "</td>";
							echo "<td>" . $row["ID"] . "</td>";
							echo "<td>" . $row["IDFILE"] . "</td>";
							echo "<td>" . $row["IMAGE_PATH"] . "</td>";
							echo "<td>" . $row["LABELS_STRING"] . "</td>";
							echo "<td>" . $row["LABELS_STRING_REMOVED"] . "</td>";
							echo "<td>" . $row["CLEANUP"] . "</td>";
							echo "<td>" . $row["UPDT"] . "</td>";
							echo "</tr>";
						}
						?>
					</table>
				</div>
			</div>
		</div>
		<div class="panel panel-default">
			<div class="panel-heading" role="tab" id="headingTwo">
				<h4 class="panel-title">
					<a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseTwo" aria-expanded="true" aria-controls="collapseTwo">
						<span style="color: orange;">Partial Cleanup</span> - <?php echo $partialPercent; ?>% - <?php echo $numPartialImages; ?> images
					</a>
				</h4>
			</div>
			<div id="collapseTwo" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingTwo">
				<div class="panel-body">
					<table id="partial-results-table" class="table table-striped">
						<thead>
						<tr>
							<th>#</th>
							<th>ID</th>
							<th>IDFILE</th>
							<th>IMAGE</th>
							<th>LABELS</th>
							<th>LABELS REMOVED</th>
							<th>CLEANUP</th>
							<th>UPDT</th>
						</tr>
						</thead>
						<tbody>
						<?php

						$rowNum = 0;
						foreach ($resultsClientPartial as $row)
						{
							$rowNum++;
							echo "<tr>";
							echo "<td>" . $rowNum . "</td>";
							echo "<td>" . $row["ID"] . "</td>";
							echo "<td>" . $row["IDFILE"] . "</td>";
							echo "<td>" . $row["IMAGE_PATH"] . "</td>";
							echo "<td>" . $row["LABELS_STRING"] . "</td>";
							echo "<td>" . $row["LABELS_STRING_REMOVED"] . "</td>";
							echo "<td>" . $row["CLEANUP"] . "</td>";
							echo "<td>" . $row["UPDT"] . "</td>";
							echo "</tr>";
						}
						?>
					</table>
				</div>
			</div>
		</div>
		<div class="panel panel-default">
			<div class="panel-heading" role="tab" id="headingThree">
				<h4 class="panel-title">
					<a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseThree" aria-expanded="true" aria-controls="collapseThree">
						<span style="color: green;">Good</span> - <?php echo $goodPercent; ?>% - <?php echo $numGoodImages; ?> images
					</a>
				</h4>
			</div>
			<div id="collapseThree" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingThree">
				<div class="panel-body">
					<table id="good-results-table" class="table table-striped">
						<thead>
						<tr>
							<th>#</th>
							<th>ID</th>
							<th>IDFILE</th>
							<th>IMAGE</th>
							<th>LABELS</th>
							<th>LABELS REMOVED</th>
							<th>CLEANUP</th>
							<th>UPDT</th>
						</tr>
						</thead>
						<tbody>
						<?php
						$rowNum = 0;
						foreach ($resultsClientGood as $row)
						{
							$rowNum++;
							echo "<tr>";
							echo "<td>" . $rowNum . "</td>";
							echo "<td>" . $row["ID"] . "</td>";
							echo "<td>" . $row["IDFILE"] . "</td>";
							echo "<td>" . $row["IMAGE_PATH"] . "</td>";
							echo "<td>" . $row["LABELS_STRING"] . "</td>";
							echo "<td>" . $row["LABELS_STRING_REMOVED"] . "</td>";
							echo "<td>" . $row["CLEANUP"] . "</td>";
							echo "<td>" . $row["UPDT"] . "</td>";
							echo "</tr>";
						}
						?>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>

<script>
$(document).on('click', '[data-toggle="lightbox"]', function(event) {
	event.preventDefault();
	$(this).ekkoLightbox();
});

$(function () {

	$("#cleanup-results-table").DataTable( {
		"lengthMenu": [[100, 200, 500, -1], [100, 200, 500, "All"]]
	} );

	$("#partial-results-table").DataTable( {
		"lengthMenu": [[100, 200, 500, -1], [100, 200, 500, "All"]]
	} );

	$("#good-results-table").DataTable( {
		"lengthMenu": [[100, 200, 500, -1], [100, 200, 500, "All"]]
	} );

	$('#filter-help-atleast-one').popover({
		trigger: 'click',
		placement: 'right',
		content: 'Images will fail this filter if there are zero bibs/labels detected. This filter is highly recommended.'
	})

	$('#filter-help-contains').popover({
		trigger: 'click',
		placement: 'right',
		content: 'Filter out any bibs/labels that should not be in this race set. Single digits, years, and very large numbers are recommended here.'
	})
})
</script>