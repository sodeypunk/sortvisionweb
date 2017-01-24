<div class="container" ng-controller="CleanupController as cleanup">
    <div id="keyoress-area" sv-keypress>
        <!-- Example row of columns -->
        <div class="row">
            <div class="col-sm-12">
                <h2>Cleanup - {{cleanup.imageCount}} images</h2>
                <input type="hidden" name="fileId" value="<?php echo $fileId; ?>">
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <input type="button" class="btn btn-primary" value="Back to Analysis" onclick="location.href='<?php echo base_url(); ?>index.php/analysis?fileid=<?php echo $fileId; ?>'"/>
                <input type="button" class="btn btn-primary" value="Save" ng-click="saveCurrentPage()"/>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12 text-right">
                Page <select ng-model="cleanup.currentPage" ng-options="item for item in cleanup.pages">
                </select>
                <br/>
                Showing {{cleanup.currentPage * cleanup.batch - cleanup.batch + 1}} - {{cleanup.currentPage * cleanup.batch}}
            </div>
        </div>
        <br><br>
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
                                        <label class="bib-label">{{label.LABEL}}</label>
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
