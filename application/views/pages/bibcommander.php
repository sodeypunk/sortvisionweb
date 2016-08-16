<?php $TITLE = "BibCommander v0.1"; ?>

<h2><?php echo $TITLE; ?></h2>
<div class="row">
    <div class="col-sm-12">
        Current Machine:
        <select id="user-system">
        <?php
            foreach ($systems as $system)
            {
                if ($system["HOSTNAME"] == "soda-desktop")
                {
                    echo '<option value="' . $system["IDSYSTEM"] . '" selected>' . $system["HOSTNAME"] . '</option>';
                }
                else
                    echo '<option value="' . $system["IDSYSTEM"] . '">' . $system["HOSTNAME"] . '</option>';
            }
        ?>
        </select>
        <br>
        Unzip Files After Upload: <input id="unzip-after-upload" type="checkbox">
    </div>
</div>
<br><br>
<div class="row">
    <div class="col-sm-6">
        Drag and drop an image into the box below to see the software in action.<br>
        Accepted files: *.jpg, *.png, *.zip (for multiple images)<br>
        <br>
            <span id="result-status"></span>
            <br>
            <span id="result-descr"></span>
        <br>
    </div>
    <div class="col-sm-6">
        <div id="dropzone" class="dropzone"></div>
    </div>
</div>
<div class="row">
    <div class="col-sm-12">
        <h2>Job History</h2>
        <br><Br>
        <table id="status-table" class="table table-striped">
            <thead>
            <tr>
                <th>ID</th>
                <th>FILE</th>
                <th>STATUS</th>
                <th>LOCATION</th>
                <th>TIMESTAMP</th>
            </tr>
            </thead>
            <tbody>
            <?php
                foreach ($files as $row)
                {
                    echo "<tr>";
                    echo "<td><a href=\"http://sortvision.localhost.com/index.php/files/status/" . $row["EZ_REF_STRING"] . "\">" . $row["IDFILE"] . "</a></td>";
                    echo "<td>" . $row["FILE_NAME"] . "</td>";
                    echo "<td>" . $row["STATUS"] . "</td>";
                    echo "<td>" . $row["FILE_PATH"] . $row["EZ_REF_STRING"] . "</td>";
                    echo "<td>" . $row["UPDT"] . "</td>";
                    echo "</tr>";
                }
            ?>
        </table>
    </div>
</div>
<script>
$(document).ready(function() {

	Dropzone.autoDiscover = false;
	var $resultText = "";

	$("#dropzone").dropzone({
		url: "<?php echo site_url('/upload/dropzone'); ?>",
		acceptedFiles: ".jpg, .JPG, .png, .PNG, .zip, .ZIP, .jpeg, .JPEG",
        maxFilesize: 50000,
		init: function() {
            this.on("sending", function(file, xhr, formData){
                var userSystem = $("#user-system option:selected").val()
                var unzipAfterUpload = $("#unzip-after-upload").is(':checked');
                formData.append("UserSystem", userSystem)
                formData.append("UnzipAfterUpload", unzipAfterUpload)
            }),
		    this.on("success", function(file, response) { 
			    var jsonResponse = JSON.parse(response);

			    //var itemsCount = Object.keys(jsonResponse).length;
			    //$("#processing-total").text(itemsCount);

		        for (var key in jsonResponse)
                {
                    if (jsonResponse.hasOwnProperty(key))
                    {
                        if (jsonResponse[key]['STATUS'] == "SUCCESS")
                        {
                        	$resultText += "Your image " + key + " has been uploaded and its progress can be found here: <a href='" + jsonResponse[key]['STATUS_URL'] + "'>" + jsonResponse[key]['STATUS_URL'] + "</a>";
                        }
                        else
                        {
                        	$resultText += "Your image " + key + " has failed when uploading.";
                        }
                    }
                }
				$resultText += "<br><br>";
		        $("#result-status").text(jsonResponse[key]['STATUS']);
			    $("#result-descr").html($resultText);
			});
		}
	});

    $(".dz-default.dz-message").html("Drop files here or click to upload.")

});
  
 </script>