<div class="container" ng-controller="CleanupController as cleanup">
    <div id="keyoress-area" sv-keypress>
        <!-- Example row of columns -->
        <div class="row">
            <div class="col-sm-12">
                <h2>Cleanup - <?php echo $imageCount; ?> total images</h2>
                Showing {{cleanup.page * cleanup.batch - cleanup.batch + 1}} - {{cleanup.page * cleanup.batch}}
            </div>
        </div>
        <br/>
        <div class="row">
            <div class="col-sm-12">
                <input type="button" class="btn btn-primary" value="Save" ng-click="saveBibs()"/>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12 text-right">
                Page <select ng-model="cleanup.page" ng-options="value.id as value.name for (key, value) in cleanup.pages">
                </select>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <form method="POST" action="<?php echo base_url(); ?>index.php/cleanup/reviewer">
                    <input type="hidden" name="fileid" value="<?php echo $fileid; ?>">
                    <input type="hidden" name="batch" value="<?php echo $batch; ?>">
                    <input type="hidden" name="page" value="<?php echo $page; ?>">
                    Add Reviewer: <select name="userid">
                        <?php
                            foreach ($users as $user)
                            {
                                echo "<option value=\"" . $user['IDUSERS'] . "\">" . $user['EMAIL'] . "</option>";
                            }
                        ?>
                    </select>
                    <input type="text" name="user-percent" placeholder="Percent" size="6">
                    <input type="submit" class="btn btn-primary" name="action" value="Add">
                </form>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <div class="panel panel-default">
                    <div class="panel-heading">Reviewers</div>
                    <div class="panel-body">
                        <p>
                            <?php
                                if (count($reviewingUsersCount) == 0)
                                {
                                    echo "No reviewers currently assigned";
                                }
                                else
                                {
                                    foreach ($reviewingUsersCount as $reviewer)
                                    {
                                        echo $reviewer['EMAIL'] . " - " . $reviewer['PERCENT'] . "% with " . $reviewer['COUNT'] . " images <br>";
                                    }
                                }
                            ?>
                        </p>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                Total Completion
                <div class="progress">
                    <div class="progress-bar progress-bar-success" role="progressbar" style="width:<?php echo $reviewedPercent; ?>%; min-width: 2em;">
                        <span id="good-percent"><?php echo $reviewedPercent; ?>%</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                By Reviewers
                <div class="progress">
                    <div class="progress-bar progress-bar-success" role="progressbar" style="width:<?php echo $reviewedPercent; ?>%; min-width: 2em;">
                        <span id="good-percent"><?php echo $reviewedPercent; ?>%</span>
                    </div>
                </div>
            </div>
        </div>
        <br>
        <div id="bibs">
            <div class="row image-row" ng-repeat="rows in cleanup.chunkedData">
                <div class="col-md-4" ng-repeat="bib in rows">
                    <div id="{{bib.IMAGE_FLATTENED}}">
                        <!--<div ng-class="{'glowing-border-selected' : cleanup.selectedIndex == bib.INDEX}" style="background-image:url(<?php echo base_url(); ?>assets/result_images/<?php echo $ezRefString; ?>/{{bib.IMAGE_FLATTENED}});">-->
                        <img ng-if="bib.CLEANUP_STATUS != null" class="checkmark" src="<?php echo base_url(); ?>assets/img/checkmark_small.png">
                        <img ng-if="cleanup.saving === true" class="loading" src="<?php echo base_url(); ?>assets/img/loading_sm.gif">
                        <img ng-src="{{bib.IMAGE_PATH}}" alt="{{bib.IMAGE_FLATTENED}}" class="img-responsive" title="{{bib.IMAGE_FLATTENED}}">
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                ID: {{bib.ID}}

                                <button ng-click="addNewLabel(bib.INDEX)" class="btn btn-primary btn-xs">New Bib Label</button>
                            </div>
                            <div class="panel-body">
                                <p>
                                <div class="bib-label-row" ng-if="label.REMOVED === '0'" ng-repeat="label in bib.LABELS_ARRAY">
                                    <label class="bib-label">{{label.LABEL}}</label>
                                    <button ng-click="removeLabel(bib.INDEX, label.ID)" class="btn btn-danger btn-xs bib-label-button"">REMOVE</button>
                                    <span ng-if="label.STATE === 'NEW'" class="label label-default bib-label-button"">NEW</span>
                                    <span ng-if="label.STATE === 'KEEP'" class="label label-default bib-label-button"">KEPT</span>
                                </div>
                                    <hr style="border-bottom:1px solid;">
                                    <h5>Removed</h5>
                                    <div class="bib-label-row" ng-if="label.REMOVED === '1'" ng-repeat="label in bib.LABELS_ARRAY">
                                        <label class="bib-label strikethrough">{{label.LABEL}}</label>
                                        <button ng-click="keepLabel(bib.INDEX, label.ID)" class="btn btn-success btn-xs bib-label-button">Keep</button>
                                        <span ng-if="label.STATE === 'REMOVED'" class="label label-default bib-label-button"">REMOVED</span>
                                    </div>

                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<script>

</script>
