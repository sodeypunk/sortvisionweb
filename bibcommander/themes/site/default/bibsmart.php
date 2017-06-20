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
                    <span><?php echo $profile->api_key ? $profile->api_key : '--'; ?></span>
                    <hr>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <h3>New BibSmart Job</h3>
                    <hr>
                    <div class="panel panel-default">
                        <div class="panel-body">
                            <form>
                                <div class="form-group">
                                    <label for="input-file">Image Location</label>
                                    <p class="help-block">Can be file or folder location</p>
                                    <input type="text" class="form-control" id="input-file" placeholder="s3://bucket/race1">
                                </div>
                                <div class="form-group">
                                    <label for="input-ec2-type">Speed</label>
                                    <p class="help-block">Determine how fast you want this job to process</p>
                                    <select name="input-speed">
                                        <option value="slow">Slow ($5/hr)</option>
                                        <option value="fast" selected>Fast ($15/hr)</option>
                                        <option value="fastest">Fastest ($25/hr)</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>
                                    <input type="checkbox" id="input-draw-results"> Draw Results
                                    </label>
                                    <p class="help-block">Will draw the detection box for the first 100 images</p>
                                </div>
                                <button type="submit" class="btn btn-default">Submit</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <h3>History</h3>
                    <hr>
                    <table id="files-history-table" class="table table-striped">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>IDFILE</th>
                            <th>EC2 STATUS</th>
                            <th>FILE PATH</th>
                            <th>FILE STATUS</th>
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
    });

</script>
