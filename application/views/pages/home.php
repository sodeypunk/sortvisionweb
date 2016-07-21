<!-- Main jumbotron for a primary marketing message or call to action -->
<div class="jumbotron">
	<div class="container">
		<h1>BibSmart</h1>
		<p>Auto bib detection and number recognition software.</p>
		<p>
			<a class="btn btn-primary btn-lg" href="#" role="button">Learn more
				&raquo;</a>
		</p>
	</div>
</div>
<div id="slide2">
    <div class="container">
        <div class="row">
            <div class="col-sm-3">
                <h4>Why SortVISION</h4>
                <img src="../../../assets/img/photography_white.png">
                <br><br>
                <p class="text-left">
                    Millions of photos are captured and manually sorted and we can help streamline your process within the Sports  Photography.
                    <br><br>
                    <?php echo anchor('why', 'More >>', 'More'); ?>
                </p>
            </div>
            <div class="col-sm-3">
                <h4>Extraction</h4>
                <img src="../../../assets/img/scan_white.png">
                <br><br>
                <p class="text-left">
                    BibSMART™ identification enables automatic bib detection to reduce human error and decrease time to market. 
                    <br><br>
                    <?php echo anchor('extraction', 'More >>', 'More'); ?>
                </p>
            </div>
            <div class="col-sm-3">
                <h4>Process</h4>
                <img src="../../../assets/img/gear_white.png">
                <br><br>
                <p class="text-left">
                    With just a few clicks you can streamline and automate your manual outsourced tagging to decrease time to market and increase revenue with on-demand photos.
                    <br><br>
                    <?php echo anchor('process', 'More >>', 'More'); ?>
                </p>
            </div>
            <div class="col-sm-3">
                <h4>Price</h4>
                <img src="../../../assets/img/price_white.png">
                <br><br>
                <p class="text-left">
                    Simple and affordable with multiple tiered options tailored towards your monthly tagging requirements.
                </p>
            </div>
        </div>
    </div>
</div>
<div id="slide1">
    <div class="container">
        <div class="row">
            <div class="col-sm-6">
                <h2>Try BibSmart!</h2>
                <p>Drag and drop an image into the box below to see the software in
                    action.</p>
                <p>Accepted files: *.jpg, *.png, *.zip (for multiple images)</p>
                <p>
                    <span id="result-status"></span>
                    <br>
                    <span id="result-descr"></span>
                </p>
            </div>
            <div class="col-sm-6">
                <div id="dropzone" class="dropzone"></div>
            </div>
        </div>
    </div>
    <div class="container">
        <!--        <div class="row">-->
        <!--            <div class="col-sm-12">-->
        <!--                <h5>Recent Jobs</h5>-->
        <!--                    --><?php //echo $test ?>
        <!--            </div>-->
        <!--        </div>-->
    </div>
</div>
<script>
$(document).ready(function() {

	Dropzone.autoDiscover = false;
	var $resultText = "";

	$("#dropzone").dropzone({
		url: "<?php echo site_url('/upload/dropzone'); ?>",
		acceptedFiles: ".jpg, .JPG, .png, .PNG, .zip, .ZIP, .jpeg, .JPEG",
		init: function() {
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