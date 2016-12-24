<?php $TITLE = "BibSmart - Dashboard"; ?>

<div class="container">
    <h2><?php echo $TITLE; ?></h2>

    <div class="row">
        <div class="col-sm-12">
            <h3>Your Files</h3>
            <br>
            <table id="status-table" class="table table-striped">
                <thead>
                <tr>
                    <th>#</th>
                    <th>IDFILE</th>
                    <th>FILE</th>
                    <th>STATUS</th>
                    <th>S3 BUCKET</th>
                    <th>TIMESTAMP</th>
                </tr>
                </thead>
                <tbody>
                <?php
                $count = 0;
                    foreach ($files as $row)
                    {
                        $count++;
                        echo "<tr>";
                        echo "<td>" . $count . "</td>";
                        echo "<td><a href=\"" . base_url("index.php/files/status") . "?fileid=" . $row["IDFILE"] . "\">" . $row["IDFILE"] . "</a></td>";
                        echo "<td>" . $row["FILE_NAME"] . "</td>";
                        echo "<td>" . $row["STATUS"] . "</td>";
                        echo "<td>" . $row["S3_BUCKET"] . "</td>";
                        echo "<td>" . $row["UPDT"] . "</td>";
                        echo "</tr>";
                    }
                ?>
            </table>
        </div>
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