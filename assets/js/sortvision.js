/**
 * Created by soda on 9/26/16.
 */

(function() {
    var app = angular.module('sortvision', []);

    app.controller('CleanupController', ['$http', '$anchorScroll', '$location', function($http, $anchorScroll, $location) {
        var cleanupCtrl = this;
        this.bibs = [];
        this.chunkedData = [];
        this.ezRefString = $('input[name=ezRefString]').val();
        this.selectedIndex = 0;
        this.selectedLabelIndex = 0;
        $anchorScroll.yOffset = 100;


        $http({
            method: 'POST',
            url: '/index.php/cleanup/bibs',
            params: {ezRefString: this.ezRefString}}
        ).success(function(data) {

            cleanupCtrl.bibs = data;
            cleanupCtrl.chunkedData = chunk(data, 3);

        });

        this.scrollToElement = function()
        {
            var newHash = this.bibs[this.selectedIndex].IMAGE_FLATTENED;
            if ($location.hash() !== newHash)
            {
                $location.hash(newHash);
            }
            else
            {
                $anchorScroll();
            }
        }

        this.addNewLabel = function(newLabel)
        {
            var bib = this.bibs[this.selectedIndex];
            var labelsArray = this.bibs[this.selectedIndex].LABELS_ARRAY;

            labelsArray.push({INDEX: labelsArray.length, IDFILE: bib.IDFILE, IMAGE: bib.IMAGE, LABEL: newLabel, REMOVED: "0", COORDINATE: "[0,0,0,0]" });
        }

        function chunk(myArray, size) {
            var newArray = [];
            for (var i=0; i<myArray.length; i+=size)
            {
                newArray.push(myArray.slice(i, i+size));
            }
            return newArray;
        };



    }]);

    app.directive('svKeypress', function($document){
       return {
           restrict: 'A',
           scope:true,
           link: function (scope, element, attr) {
               $document.on("keydown", function(event) {
                   switch(event.keyCode)
                   {
                       // right key
                       case 39:
                           if (scope.$parent.cleanup.selectedIndex < scope.$parent.cleanup.bibs.length - 1) {
                               scope.$parent.cleanup.selectedIndex += 1;
                               scope.$parent.cleanup.selectedLabelIndex = 0;
                               scope.$parent.cleanup.scrollToElement();
                           }
                           event.preventDefault();
                           break;
                       // left key
                       case 37:
                           if (scope.$parent.cleanup.selectedIndex > 0) {
                               scope.$parent.cleanup.selectedIndex -= 1;
                               scope.$parent.cleanup.selectedLabelIndex = 0;
                               scope.$parent.cleanup.scrollToElement();
                           }
                           event.preventDefault();
                           break;
                       // up key
                       case 38:
                           if (scope.$parent.cleanup.selectedLabelIndex > 0) {
                               scope.$parent.cleanup.selectedLabelIndex -= 1;
                           }
                           event.preventDefault();
                           break;
                       // down key
                       case 40:
                           if (scope.$parent.cleanup.selectedLabelIndex < scope.$parent.cleanup.bibs[scope.$parent.cleanup.selectedIndex].LABELS_ARRAY.length - 1) {
                               scope.$parent.cleanup.selectedLabelIndex += 1;
                           }
                           event.preventDefault();
                           break;
                       // space-bar
                       case 32:
                           var currentLabel = scope.$parent.cleanup.bibs[scope.$parent.cleanup.selectedIndex].LABELS_ARRAY[scope.$parent.cleanup.selectedLabelIndex];
                           var currentLabelCleanup = currentLabel.REMOVED;
                           currentLabel.REMOVED = currentLabelCleanup === "1" ? "0" : "1";
                           event.preventDefault();
                           break;
                       // + button
                       case 107:
                           var newLabel = prompt("Please enter new label");
                           scope.$parent.cleanup.addNewLabel(newLabel);
                           event.preventDefault();
                           break;
                   }

                   scope.$apply();
               });
           }
       }
    });

})();

//$(document).keydown(function(e) {
//    switch(e.which) {
//        case 37: // left
//            break;
//
//        case 38: // up
//            break;
//
//        case 39: console.log("right-click");
//
//            break;
//
//        case 40: // down
//            break;
//
//        default: return; // exit this handler for other keys
//    }
//    e.preventDefault(); // prevent the default action (scroll / move caret)
//});