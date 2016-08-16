<div class="container">
	<!-- Example row of columns -->
	<div class="row">
		<div class="col-sm-6">
			<?php echo '<h2>Cleanup</h2>'; ?>
		</div>
	</div>
	<div class="row">
		<div class="col-sm-12">
			<div class="progress">
				<div class="progress-bar progress-bar-success" role="progressbar" style="width:<?php echo $goodPercent; ?>%">
					Good <span id="good-percent"><?php echo $goodPercent; ?>%</span>
				</div>
				<div class="progress-bar progress-bar-danger" role="progressbar" style="width:<?php echo $cleanupPercent; ?>%">
					Cleanup <span id="good-percent"><?php echo $cleanupPercent; ?>%</span>
				</div>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-sm-12">
			<div class="card text-xs-center">
				<div class="card-header">
					Filters
				</div>
				<div class="card-block">
					Test
					<br>
					Test2
				</div>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-sm-12">
			<h2>Good</h2>
			<table id="results-table" class="table table-striped">
				<thead>
				<tr>
					<th>ID</th>
					<th>IDFILE</th>
					<th>IMAGE</th>
					<th>LABEL</th>
					<th>COORDINATES</th>
					<th>IMAGE SIZE</th>
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
					echo "<td>" . $row["IMAGE"] . "</td>";
					echo "<td>" . $row["LABEL"] . "</td>";
					echo "<td>" . $row["COORDINATES"] . "</td>";
					echo "<td>" . $row["IMAGE_SIZE"] . "</td>";
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
			<h2>Cleanup</h2>
			<table id="results-table" class="table table-striped">
				<thead>
				<tr>
					<th>ID</th>
					<th>IDFILE</th>
					<th>IMAGE</th>
					<th>LABEL</th>
					<th>COORDINATES</th>
					<th>IMAGE SIZE</th>
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
					echo "<td>" . $row["IMAGE"] . "</td>";
					echo "<td>" . $row["LABEL"] . "</td>";
					echo "<td>" . $row["COORDINATES"] . "</td>";
					echo "<td>" . $row["IMAGE_SIZE"] . "</td>";
					echo "<td>" . $row["CLEANUP"] . "</td>";
					echo "<td>" . $row["UPDT"] . "</td>";
					echo "</tr>";
				}
				?>
			</table>
		</div>
	</div>
</div>

<script>

</script>