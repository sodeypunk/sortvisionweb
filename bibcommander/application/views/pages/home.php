<!-- Main jumbotron for a primary marketing message or call to action -->

<div class="container">
    <h1>BibSmart</h1>
    <p>Auto bib detection and number recognition software.</p>

    <br/>
    <div class="panel panel-primary">
        <div class="panel-heading">Try Bibsmart demo application</div>
        <div class="panel-body">
            <div class="row">
                <div class="col-sm-6">
                    <p>Drag and drop an image into the box.</p>
                    <h5>Accepted files: *.jpg, *.png</h5>
                    <div id="json-result"></div>
                </div>
                <div class="col-sm-6">
                    <div id="dropzone" class="dropzone"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="panel panel-info">
        <div class="panel-heading">For Developers - Bibsmart API</div>
        <div class="panel-body">
            <div class="row">
                <div class="col-md-12">
                    Coming soon!
                </div>
            </div>
        </div>
    </div>
</div>


<script>
    $(document).ready(function() {
        Dropzone.autoDiscover = false;
        $("#dropzone").dropzone({
            url: "<?php echo site_url('/upload/dropzone'); ?>",
            acceptedFiles: ".jpg, .JPG, .png, .PNG, .zip, .ZIP, .jpeg, .JPEG",
            init: function() {
                this.on("success", function(file, response) {
                    var jsonResponse = JSON.parse(response);
                    //var itemsCount = Object.keys(jsonResponse).length;
                    //$("#processing-total").text(itemsCount);
                    var $resultText = "";
                    for (var key in jsonResponse)
                    {
                        if (jsonResponse.hasOwnProperty(key))
                        {
                            if (jsonResponse[key]['STATUS'] == "SUCCESS")
                            {
                                var jsonObj = JSON.parse(jsonResponse[key]['JSON_RESULT']);
                                var jsonPretty = JSON.stringify(jsonObj, null, '\t');
                                $resultText = jsonPretty;
                            }
                            else
                            {
                                $resultText = "Your image " + key + " has failed when uploading.";
                            }
                        }
                    }

                    $("#json-result").prepend('<pre>' + $resultText + '</pre>');
                });
            }
        });
        $(".dz-default.dz-message").html("Drop files here or click to upload.")
    });

</script>