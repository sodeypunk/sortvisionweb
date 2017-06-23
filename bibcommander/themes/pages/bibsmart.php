<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$this->load->view(get_template_directory() . 'header');
?>

<div class="page-container">
    <!-- Content -->
    <div class="page-content">
        <div class="page-content-inner">
            <!-- Page header -->
            <div class="page-header">
                <div class="page-title profile-page-title">
                    <h2><a href="<?php echo site_url('/bibsmart'); ?>">BibSmart</a></h2>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <label>API Key:</label>
                    <span><?php echo $profile->api_key ? $profile->api_key : ''; ?></span>
                    <hr>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <h3>New BibSmart Job</h3>
                    <hr>
                    <?php if(isset($errors) && $errors!='') { ?>
                        <div class="alert alert-danger fade in block-inner alert-dismissible" role="alert">
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <?php echo $errors; ?></div>
                    <?php } ?>
                    <?php if(isset($success) && $success!='') { ?>
                        <div class="alert alert-success fade in block-inner alert-dismissible" role="alert">
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <?php echo $success; ?></div>
                    <?php } ?>
                    <?php if(isset($message) && $message!='') { ?>
                        <div class="alert alert-info fade in block-inner alert-dismissible" role="alert">
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <?php echo $message; ?></div>
                    <?php } ?>
                    <div class="panel panel-default">
                        <div class="panel-body">
                            <form id="new-job">
                                <input type="hidden" id="apikey" value="<?php echo $profile->api_key ? $profile->api_key : ''; ?>">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="input-file">Image Location</label>
                                            <p class="help-block">Can be file or folder location</p>
                                            <input type="text" class="form-control" name="input-file" placeholder="s3://bucket/race1">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="input-ec2-type">Speed</label>
                                            <p class="help-block">Determine how fast you want this job to process</p>
                                            <select name="input-speed" id="input-speed">
                                                <option value="test">Testing (do not use)</option>
                                                <option value="custom">Custom</option>
                                                <option value="slow" selected>Normal ($5/hr)</option>
                                                <option value="fast">Fast ($15/hr)</option>
                                                <option value="fastest">Fastest ($25/hr)</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="row" id="custom-speed" style="display: none;">
                                    <div class="col-md-6">
                                        <label for="input-ec2-type">Custom Speed</label>
                                        <p class="help-block">This will not start an EC2 instance. Use for attaching job to existing instance.</p>
                                        <input type="text" class="form-control" name="input-hostname" placeholder="hostname">
                                        <input type="text" class="form-control" name="input-instanceid" placeholder="instance id">
                                        <br/>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label>
                                            <input type="checkbox" name="input-draw-results"> Draw Results
                                            </label>
                                            <p class="help-block">Will draw the detection box for each image. (Note: could slow performance)</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
                                            <div class="panel panel-default">
                                                <div class="panel-heading" role="tab" id="headingOne">
                                                    <h4 class="panel-title">
                                                        <a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                                            Advanced
                                                        </a>
                                                    </h4>
                                                </div>
                                                <div id="collapseOne" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingOne">
                                                    <div class="panel-body">
                                                        <div class="row">
                                                            <div class="col-md-12">
                                                                <div class="form-group">
                                                                    <label for="input-ec2-type">Terminate Timeout (mins)</label>
                                                                    <p class="help-block">Will terminate EC2 instance after minutes of idling from last image.</p>
                                                                    <input type="number" name="input-terminate-timeout" value="5">
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-md-12">
                                                                <div class="form-group">
                                                                    <label>
                                                                        <input type="checkbox" name="input-dryrun"> Dry Run
                                                                    </label>
                                                                    <p class="help-block">Will not start an EC2 instance if checked.</p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <br/>
                                        <button type="submit" id="submit-btn-create-job" class="btn btn-default">Submit</button><span style="padding-left: 5px;" id="submit-loading-text"></span>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <h3><span style="color: green"> In Progress</span></h3>
                    <hr>
                    <table id="files-inprogress-table" class="table table-striped table-bordered nowrap" cellspacing="0" width="100%">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>Actions</th>
                            <th>File ID</th>
                            <th>EC2 Status</th>
                            <th>File Path</th>
                            <th>File Status</th>
                            <th>Images Completed</th>
                            <th>Start Time</th>
                            <th>End Time</th>
                            <th>EC2 Host</th>
                            <th>EC2 Instance ID</th>
                            <th>Speed</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php

                        $rowNum = 0;
                        foreach ($files_in_progress as $row) {
                            $rowNum++;
                            if ($row["IMG_COUNT"] == "")
                                $row["IMG_COUNT"] = "0";
                            $imagesCompleted = $row["IMAGES_COMPLETED"] . " / " . $row["IMG_COUNT"];
                            echo "<tr id='" . $row["IDFILE"] . "'>";
                            echo "<td>" . $rowNum . "</td>";
                            echo "<td><div class='row'><div class='col-md-6'><a href='" . site_url('/files/status?fileid=' . $row["IDFILE"]) . "' class='icon'><span id='" . $row["IDFILE"] . "' class='action-view-result glyphicon glyphicon-list-alt' title='View Result'></span></a></div>" .
                                "<div class='col-md-6'><a href='#' class='icon'><span id='" . $row["IDFILE"] . "' class='action-trash glyphicon glyphicon-trash' title='Delete Job'></span></a></div></div></td>";
                            echo "<td>" . $row["IDFILE"] . "</td>";
                            echo "<td><span class='ec2-state'>" . $row["EC2_STATE"] . "</span><img class='loadingImage' src='" . base_url("assets/img/loading_sm_tr.gif") . "'/></td>";
                            echo "<td>" . $row["FILE_PATH"] . "</td>";
                            echo "<td class='file-status'>" . $row["FILE_STATUS"] . "</td>";
                            echo "<td class='images-completed'>" . $imagesCompleted . "</td>";
                            echo "<td class='start-time'>" . $row["START_TIME"] . "</td>";
                            echo "<td class='end-time'>" . $row["END_TIME"] . "</td>";
                            echo "<td>" . $row["EC2_HOSTNAME"] . "</td>";
                            echo "<td>" . $row["EC2_INSTANCE_ID"] . "</td>";
                            echo "<td>" . $row["EC2_INSTANCE_TYPE"] . "</td>";
                            echo "</tr>";
                        }
                        ?>
                    </table>
                </div>
            </div>
            </br>
            <div class="row">
                <div class="col-md-12">
                    <h3><span style="color: darkblue">History</span></h3>
                    <hr>
                    <table id="files-history-table" class="table table-striped table-bordered nowrap" cellspacing="0" width="100%">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>Actions</th>
                            <th>File ID</th>
                            <th>EC2 Status</th>
                            <th>File Path</th>
                            <th>File Status</th>
                            <th>Images Completed</th>
                            <th>Start Time</th>
                            <th>End Time</th>
                            <th>EC2 Host</th>
                            <th>EC2 Instance ID</th>
                            <th>Speed</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php

                        $rowNum = 0;
                        foreach ($files as $row) {
                            $rowNum++;
                            if ($row["IMG_COUNT"] == "")
                                $row["IMG_COUNT"] = "0";
                            $imagesCompleted = $row["IMAGES_COMPLETED"] . " / " . $row["IMG_COUNT"];
                            echo "<tr>";
                            echo "<td>" . $rowNum . "</td>";
                            echo "<td><div class='row'><div class='col-md-6'><a href='" . site_url('/files/status?fileid=' . $row["IDFILE"]) . "' class='icon'><span id='" . $row["IDFILE"] . "' class='action-view-result glyphicon glyphicon-list-alt' title='View Result'></span></a></div></div></td>";
                            echo "<td>" . $row["IDFILE"] . "</td>";
                            echo "<td>" . $row["EC2_STATE"] . "</td>";
                            echo "<td>" . $row["FILE_PATH"] . "</td>";
                            echo "<td>" . $row["FILE_STATUS"] . "</td>";
                            echo "<td>" . $imagesCompleted . "</td>";
                            echo "<td>" . $row["START_TIME"] . "</td>";
                            echo "<td>" . $row["END_TIME"] . "</td>";
                            echo "<td>" . $row["EC2_HOSTNAME"] . "</td>";
                            echo "<td>" . $row["EC2_INSTANCE_ID"] . "</td>";
                            echo "<td>" . $row["EC2_INSTANCE_TYPE"] . "</td>";
                            echo "</tr>";
                        }
                        ?>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $this->load->view(get_template_directory() . 'footer'); ?>

<script>

    $(document).on('click', '[data-toggle="lightbox"]', function (event) {
        event.preventDefault();
        $(this).ekkoLightbox();
    });

    $(function () {

        $("#files-history-table").DataTable({
            "lengthMenu": [[25, 50, 100, -1], [25, 50, 100, "All"]],
            "scrollX": true,
            destroy: true
        });

        $("#files-inprogress-table").DataTable({
            "paging": false,
            "searching": false,
            "info": false,
            "scrollX": true,
            destroy: true
        });

        $("#input-speed").change(function(e){
            var speed = $(this).val();
            if (speed == "custom")
            {
                $("#custom-speed").show();
            }
            else
            {
                $("#custom-speed").hide();
            }
        });

        $("#new-job").submit(function(){

            var postData = $("#new-job").serialize();

            $.ajax({
                url: "<?php echo site_url('/bibsmart/job'); ?>",
                type: "POST",
                async: false,
                data: postData,
                beforeSend: function () {
                    $("#submit-loading-text").text("Creating job...");
                }

            })
            .done(function (msg) {
                if (msg.indexOf("error") >= 0) {
                    alert('Adding a new job failed. ' + msg);
                    $("#submit-loading-text").text("");
                    return;
                }
                else {
                    window.location.replace("<?php echo site_url('/bibsmart'); ?>");
                }
            })
            .fail(function (error) {
                alert("New Job Error: " + error.statusText);
                $("#submit-loading-text").text("");
            })
            .complete(function () {

            });

            return false;
        });

        $(".action-trash").click(function(){

            if (confirm("Delete Job?")) {
                var apiKeyString = $("#apikey").val();

                $.ajax({
                        url: "<?php echo site_url('/files/deletefile'); ?>",
                        type: "POST",
                        async: false,
                        data: {fileid: this.id, apikey: apiKeyString},
                    })
                    .done(function (msg) {
                        if (msg == "failed") {
                            alert('Delete failed.');
                            return;
                        }
                        else {
                            window.location.replace("<?php echo site_url('/bibsmart'); ?>");
                        }
                    })
                    .fail(function (error) {
                        alert("Trash action Error: " + error.statusText);
                    })
                    .complete(function () {
                        //$('#loadingImage').hide();
                    });
            }

            return false;
        });
    });

    function refreshInProgressStatus()
    {
        $('#files-inprogress-table > tbody  > tr').each(function() {

            if ($(this)[0].innerText.indexOf("No data") < 0) {

                var fileid = $(this).attr('id');
                var apiKeyString = $("#apikey").val();

                $.ajax({
                        url: "<?php echo site_url('/files/filestatusjson'); ?>",
                        type: "POST",
                        async: true,
                        data: {fileid: this.id, apikey: apiKeyString},
                    })
                    .done(function (msg) {
                        var json_result = JSON.parse(msg);
                        if (json_result.length > 0) {
                            var file_id = json_result[0].IDFILE;
                            var ec2_state = json_result[0].EC2_STATE;
                            if (ec2_state === null) {
                                ec2_state = '';
                            }
                            var images_count = json_result[0].IMG_COUNT;
                            if (images_count === null) {
                                images_count = '0';
                            }
                            var images_completed = json_result[0].IMAGES_COMPLETED + ' / ' + images_count;
                            var file_status = json_result[0].FILE_STATUS;
                            var start_time = json_result[0].START_TIME;
                            var end_time = json_result[0].END_TIME;

                            var currentRow = $('#files-inprogress-table').find("#" + file_id);

                            $(currentRow).find(".ec2-state").text(ec2_state);
                            $(currentRow).find(".images-count").text(images_count);
                            $(currentRow).find(".images-completed").text(images_completed);
                            $(currentRow).find(".file-status").text(file_status);
                            $(currentRow).find(".start-time").text(start_time);
                            $(currentRow).find(".end-time").text(end_time);
                        }
                    })
                    .fail(function (error) {
                        console.log("File Progress Error: " + error.statusText);
                    })
            }
        });
    }

    setInterval(function(){
        console.log("Refreshing in progress jobs");
        refreshInProgressStatus(); // this will run after every 5 seconds
    }, 5000);

</script>
