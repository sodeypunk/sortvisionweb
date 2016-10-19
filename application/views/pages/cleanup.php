<div class="container" ng-controller="CleanupController as cleanup">
    <div id="keyoress-area" sv-keypress>
        <!-- Example row of columns -->
        <div class="row">
            <div class="col-sm-12">
                <h2>Cleanup - </h2>
                <input type="hidden" name="ezRefString" value="<?php echo $ezRefString; ?>">
                <input type="button" class="btn btn-primary" value="Analysis" onclick="location.href='<?php echo base_url(); ?>index.php/analysis/index/<?php echo $ezRefString; ?>'"/>
                <br><br>
            </div>
        </div>
        <div id="bibs">
            <div class="row" ng-repeat="rows in cleanup.chunkedData">
                <div class="col-md-4" ng-repeat="bib in rows">
                    <div id="{{bib.IMAGE_FLATTENED}}">
                        <img ng-class="{'glowing-border-selected' : cleanup.selectedIndex == bib.INDEX}" ng-src="<?php echo base_url(); ?>assets/result_images/<?php echo $ezRefString; ?>/{{bib.IMAGE_FLATTENED}}" alt="{{bib.IMAGE_FLATTENED}}" class="img-responsive" title="{{bib.IMAGE_FLATTENED}}">
                            <label ng-class="{'checkbox-border' : cleanup.selectedIndex == bib.INDEX && cleanup.selectedLabelIndex == label.INDEX}" class="checkbox" for="{{label.LABEL}}" ng-repeat="label in bib.LABELS_ARRAY">
                                <input type="checkbox" name="{{bib.IMAGE_FLATTENED}}" id="{{label.LABEL}}" ng-model="(label.REMOVED === '0')"> {{label.LABEL}} </input>
                            </label>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
