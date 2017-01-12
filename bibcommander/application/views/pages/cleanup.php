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
            </div>
        </div>
        <div id="bibs">
            <div class="row image-row" ng-repeat="rows in cleanup.chunkedData">
                <div class="col-md-4" ng-repeat="bib in rows">
                    <div id="{{bib.IMAGE_FLATTENED}}">
                        <!--<div ng-class="{'glowing-border-selected' : cleanup.selectedIndex == bib.INDEX}" style="background-image:url(<?php echo base_url(); ?>assets/result_images/<?php echo $ezRefString; ?>/{{bib.IMAGE_FLATTENED}});">-->
                        <img ng-if="bib.CLEANUP_STATUS != null" class="checkmark" src="<?php echo base_url(); ?>assets/img/checkmark_small.png">
                        <img ng-if="cleanup.saving === true" class="loading" src="<?php echo base_url(); ?>assets/img/loading_sm.gif">
                        <img ng-class="{'glowing-border-selected' : cleanup.selectedIndex == bib.INDEX}" ng-src="{{bib.IMAGE_PATH}}" alt="{{bib.IMAGE_FLATTENED}}" class="img-responsive" title="{{bib.IMAGE_FLATTENED}}">
                            <label ng-class="{'checkbox-border' : cleanup.selectedIndex == bib.INDEX && cleanup.selectedLabelIndex == label.INDEX}" class="checkbox" for="{{label.LABEL}}" ng-repeat="label in bib.LABELS_ARRAY">
                                <input type="checkbox" name="{{bib.IMAGE_FLATTENED}}" id="{{label.LABEL}}" ng-model="label.CHECKED"> {{label.LABEL}} </input>
                            </label>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
