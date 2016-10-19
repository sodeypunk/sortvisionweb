<div class="container">
	<!-- Example row of columns -->
	<div class="row">
		<div class="col-sm-12">
			<h2>Analysis</h2>

			<div class="progress">
				<div class="progress-bar progress-bar-success" role="progressbar" style="width:<?php echo $goodPercent; ?>%">
					Good <span id="good-percent"><?php echo $goodPercent; ?>%</span>
				</div>
				<div class="progress-bar progress-bar-danger" role="progressbar" style="width:<?php echo $cleanupPercent; ?>%">
					Analysis <span id="good-percent"><?php echo $cleanupPercent; ?>%</span>
				</div>
			</div>
		</div>
	</div>
	<form method="POST" action="<?php echo base_url(); ?>index.php/analysis/analysis">
	<div class="row">
		<div class="col-sm-12 col-md-6">
				<h3>Filter Options</h3>
				<div id="filters">
					<input type="hidden" name="ezRefString" value="<?php echo $ezRefString; ?>">
					<input type="checkbox" name="filter-atleast-one" <?php if ($filterAtLeastOne) echo "checked"; ?>> Image must have at least one bib
					<br><br>

					<input type="radio" name="filter-label-contains-choice" value="label" <?php if ($filterLabelContainsChoice == "label") echo "checked"; ?>> Remove label from image
					<input type="radio" name="filter-label-contains-choice" value="image" <?php if ($filterLabelContainsChoice == "image") echo "checked"; ?>> Remove image from clean set
					<div class="input-group">
						<span class="input-group-addon" id="basic-addon1">Labels that contains (ex: 1,2,3; 6-10)</span>
						<input type="text" class="form-control" name="filter-label-contains-value" value="<?php echo $filterLabelContainsValue; ?>">
					</div>
					<br><br>

					<input type="radio" name="filter-label-length-choice" value="label" <?php if ($filterLabelLengthChoice == "label") echo "checked"; ?>> Remove label from image
					<input type="radio" name="filter-label-length-choice" value="image" <?php if ($filterLabelLengthChoice == "image") echo "checked"; ?>> Remove image from clean set
					<div class="input-group">
						<span class="input-group-addon" id="basic-addon2">Labels that have lengths greater than</span>
						<input type="text" class="form-control" name="filter-label-length-value" value="<?php echo $filterLabelLengthValue; ?>">
					</div>
				</div>
		</div>
	</div>
	<div class="row">
		<div class="col-sm-12">
			<br><br>
			<input type="submit" class="btn btn-primary" name="action" value="Preview"/>
			<input type="submit" class="btn btn-primary" name="action" value="Reset"/>
			<input type="submit" class="btn btn-primary" name="action" value="Update"/>
			<input type="button" class="btn btn-primary" value="Cleanup" onclick="location.href='<?php echo base_url(); ?>index.php/cleanup/index/<?php echo $ezRefString; ?>'"/>
		</div>
	</div>
	</form>
	<div class="row">
		<div class="col-sm-12">
			<h3>Good</h3>
			<table id="results-table" class="table table-striped">
				<thead>
				<tr>
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
				foreach ($resultsClientGood as $row)
				{
					echo "<tr>";
					echo "<td>" . $row["ID"] . "</td>";
					echo "<td>" . $row["IDFILE"] . "</td>";
					echo "<td>" . $row["IMAGE_FLATTENED"] . "</td>";
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
	<div class="row">
		<div class="col-sm-12">
			<h3>Analysis</h3>
			<table id="results-table" class="table table-striped">
				<thead>
				<tr>
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
				foreach ($resultsClientCleanup as $row)
				{
					echo "<tr>";
					echo "<td>" . $row["ID"] . "</td>";
					echo "<td>" . $row["IDFILE"] . "</td>";
					echo "<td>" . $row["IMAGE_FLATTENED"] . "</td>";
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