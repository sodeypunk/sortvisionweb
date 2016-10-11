<div class="container" ng-controller="CleanupController as cleanup">
    {{cleanup.selectedIndex}}
    <div id="keyoress-area" sv-keypress selectedindex="cleanup.selectedIndex">
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
                    <img ng-class="{'glowing-border-selected' : cleanup.selectedIndex == bib.INDEX}" ng-src="<?php echo base_url(); ?>assets/result_images/<?php echo $ezRefString; ?>/{{bib.IMAGE}}" alt="{{bib.IMAGE}}" class="img-responsive" title="{{bib.IMAGE}}">
                        <label class="checkbox" for="{{label.label}}" ng-repeat="label in bib.LABEL_ARRAY">
                            <input type="checkbox" name="{{bib.IMAGE}}" id="{{label.label}}" ng-model="label.cleanup"> {{label.label}} </input>
                        </label>
                </div>
            </div>

        </div>
    </div>
</div>
