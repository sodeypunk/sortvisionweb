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
                    <h2>BibSmart</h2>
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
                                            <select name="input-speed">
                                                <option value="slow">Slow ($5/hr)</option>
                                                <option value="fast" selected>Fast ($15/hr)</option>
                                                <option value="fastest">Fastest ($25/hr)</option>
                                            </select>
                                        </div>
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
                                <div class="row">
                                    <div class="col-md-12">
                                        <br/>
                                        <button type="submit" class="btn btn-default">Submit</button>
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
                    <table id="files-inprogress-table" class="table table-striped table-bordered" cellspacing="0" width="100%">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>FILE ID</th>
                            <th>EC2 STATUS</th>
                            <th>FILE PATH</th>
                            <th>FILE STATUS</th>
                            <th>IMAGES</th>
                            <th>IMAGES COMPLETED</th>
                            <th>UPDATE TIME</th>
                            <th>Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php

                        $rowNum = 0;
                        foreach ($files_in_progress as $row) {
                            $rowNum++;
                            echo "<tr>";
                            echo "<td>" . $rowNum . "</td>";
                            echo "<td>" . $row["IDFILE"] . "</td>";
                            echo "<td>" . $row["EC2_STATE"] . "</td>";
                            echo "<td>" . $row["FILE_PATH"] . "</td>";
                            echo "<td>" . $row["FILE_STATUS"] . "</td>";
                            echo "<td>" . $row["IMG_COUNT"] . "</td>";
                            echo "<td>" . $row["IMAGES_COMPLETED"] . "</td>";
                            echo "<td>" . $row["UPDT"] . "</td>";
                            echo "<td><a href='#' class='icon'><span id='" . $row["IDFILE"] . "' class='action-trash glyphicon glyphicon-trash' title='Delete Job'></span></a></td>";
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
                    <table id="files-history-table" class="table table-striped table-bordered" cellspacing="0" width="100%">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>FILE ID</th>
                            <th>EC2 STATUS</th>
                            <th>FILE PATH</th>
                            <th>FILE STATUS</th>
                            <th>IMAGES</th>
                            <th>IMAGES COMPLETED</th>
                            <th>UPDATE TIME</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php

                        $rowNum = 0;
                        foreach ($files as $row) {
                            $rowNum++;
                            echo "<tr>";
                            echo "<td>" . $rowNum . "</td>";
                            echo "<td>" . $row["IDFILE"] . "</td>";
                            echo "<td>" . $row["EC2_STATE"] . "</td>";
                            echo "<td>" . $row["FILE_PATH"] . "</td>";
                            echo "<td>" . $row["FILE_STATUS"] . "</td>";
                            echo "<td>" . $row["IMG_COUNT"] . "</td>";
                            echo "<td>" . $row["IMAGES_COMPLETED"] . "</td>";
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
<?php $this->load->view(get_template_directory() . 'footer'); ?>

<script>

    $(document).on('click', '[data-toggle="lightbox"]', function (event) {
        event.preventDefault();
        $(this).ekkoLightbox();
    });

    $(function () {

        $("#files-history-table").DataTable({
            "lengthMenu": [[25, 50, 100, -1], [25, 50, 100, "All"]],
            destroy: true
        });

        $("#files-inprogress-table").DataTable({
            "paging": false,
            "searching": false,
            "info": false,
            destroy: true
        });

        $("#new-job").submit(function(){

            var postData = $("#new-job").serialize();

            $.ajax({
                url: "<?php echo site_url('/bibsmart/job'); ?>",
                type: "POST",
                async: false,
                data: postData,
                beforeSend: function () {
                    //$('#loadingImage').show();
                }

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
                alert("Error: " + error.statusText);
            })
            .complete(function () {
                //$('#loadingImage').hide();
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
                        beforeSend: function () {
                            //$('#loadingImage').show();
                        }

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
                        alert("Error: " + error.statusText);
                    })
                    .complete(function () {
                        //$('#loadingImage').hide();
                    });
            }

            return false;
        });


    });

</script>
