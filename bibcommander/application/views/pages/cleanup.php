<div class="container" ng-controller="CleanupController as cleanup">
    <div id="keyoress-area" sv-keypress>
        <!-- Example row of columns -->
        <div class="row">
            <div class="col-sm-12">
                <h2>Cleanup - {{cleanup.imageCount}} images</h2>
                <input type="hidden" name="fileId" value="<?php echo $fileId; ?>">
                <input type="button" class="btn btn-primary" value="Analysis" onclick="location.href='<?php echo base_url(); ?>index.php/analysis?fileid=<?php echo $fileId; ?>'"/>
                <select ng-model="cleanup.currentPage" ng-options="item for item in cleanup.pages">

                </select>
                <br><br>

                <br>
            </div>
        </div>
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
                                    <h5>Keep</h5>
                                <div class="bib-label-row" ng-if="label.REMOVED === '0'" ng-repeat="label in bib.LABELS_ARRAY">
                                    <label class="bib-label">{{label.LABEL}}</label>
                                    <button ng-click="removeLabel(label.ID)" class="btn btn-danger btn-xs bib-label-button"">-</button>
                                </div>
                                    <hr style="border-bottom:1px solid;">
                                    <h5>Removed</h5>
                                    <div class="bib-label-row" ng-if="label.REMOVED === '1'" ng-repeat="label in bib.LABELS_ARRAY">
                                        <label class="bib-label">{{label.LABEL}}</label>
                                        <button ng-click="keepLabel(label.ID)" class="btn btn-success btn-xs bib-label-button">+</button>
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
