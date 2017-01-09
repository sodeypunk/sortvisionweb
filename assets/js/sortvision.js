/**
 * Created by soda on 9/26/16.
 */

(function() {
    var app = angular.module('sortvision', [], function($httpProvider) {
        $httpProvider.defaults.headers.post['Content-Type'] = 'application/x-www-form-urlencoded;charset=utf-8'; // To allow PHP servers to read angular's serialization

        /**
         * The workhorse; converts an object to x-www-form-urlencoded serialization.
         * @param {Object} obj
         * @return {String}
         */
        var param = function(obj) {
            var query = '', name, value, fullSubName, subName, subValue, innerObj, i;

            for(name in obj) {
                value = obj[name];

                if(value instanceof Array) {
                    for(i=0; i<value.length; ++i) {
                        subValue = value[i];
                        fullSubName = name + '[' + i + ']';
                        innerObj = {};
                        innerObj[fullSubName] = subValue;
                        query += param(innerObj) + '&';
                    }
                }
                else if(value instanceof Object) {
                    for(subName in value) {
                        subValue = value[subName];
                        fullSubName = name + '[' + subName + ']';
                        innerObj = {};
                        innerObj[fullSubName] = subValue;
                        query += param(innerObj) + '&';
                    }
                }
                else if(value !== undefined && value !== null)
                    query += encodeURIComponent(name) + '=' + encodeURIComponent(value) + '&';
            }

            return query.length ? query.substr(0, query.length - 1) : query;
        };

        // Override $http service's default transformRequest
        $httpProvider.defaults.transformRequest = [function(data) {
            return angular.isObject(data) && String(data) !== '[object File]' ? param(data) : data;
        }];
    });

    app.controller('CleanupController', ['$scope','$http', '$anchorScroll', '$location', function($scope, $http, $anchorScroll, $location) {
        var cleanupCtrl = this;
        this.bibs = [];
        this.chunkedData = [];
        this.fileId = $('input[name=fileId]').val();
        this.selectedIndex = 0;
        this.selectedLabelIndex = 0;
        this.savingIndex = 0;
        this.saving = false;
        this.imageCount = 0;
        this.pages = [];
        this.currentPage = 1;
        $anchorScroll.yOffset = 100;

        $http({
            method: 'POST',
            url: '/bibcommander/index.php/cleanup/bibs',
            params: {fileid: this.fileId}}
        ).success(function(data) {

            cleanupCtrl.bibs = data;
            cleanupCtrl.chunkedData = chunk(data, 3);

        });

        $http({
            method: 'POST',
            url: '/bibcommander/index.php/cleanup/getTotalCleanupImageCount',
            params: {fileid: this.fileId, batch: 100}}
        ).success(function(data) {

            cleanupCtrl.imageCount = data['COUNT'];
            cleanupCtrl.pages = data['PAGES'];

        });

        $scope.$watch('cleanup.currentPage', function(newValue, oldValue){
            if (newValue !== oldValue) {
                alert("Property has changed to " + newValue + " from " + oldValue);
            }
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

            labelsArray.push({INDEX: labelsArray.length, ID: -1, IDFILE: bib.IDFILE, IMAGE: bib.IMAGE, LABEL: newLabel, REMOVED: "0", COORDINATE: "[0,0,0,0]", CHECKED: true });
        }

        this.markImageCompleted = function(status)
        {
            if (status == true)
            {
                this.ajaxSave(true);
            }
            else
            {
                this.ajaxSave(false);
            }
        }

        this.ajaxSave = function(status)
        {
            var bibsArray = this.bibs[this.selectedIndex];
            this.savingIndex = this.selectedIndex;
            this.saving = true;

            $http({
                method: 'POST',
                url: '/bibcommander/index.php/cleanup/update',
                data: {bibsArray: bibsArray, cleaned: status}}
            ).success(function(data) {

                if (data.success === true) {
                    $scope.cleanup.bibs[$scope.cleanup.savingIndex].CLEANUP_STATUS = data.bib.CLEANUP_STATUS;
                    $scope.cleanup.saving = false;
                }

            }).error(function(data) {
                $scope.cleanup.saving = false;
                alert("Saving failed");
            });
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
                           currentLabel.CHECKED = currentLabel.REMOVED === "0";
                           scope.$parent.cleanup.markImageCompleted(false);
                           event.preventDefault();
                           break;
                       // + button
                       case 107:
                           var newLabel = prompt("Please enter new label");
                           scope.$parent.cleanup.addNewLabel(newLabel);
                           event.preventDefault();
                           break;
                       // enter button
                       case 13:
                           scope.$parent.cleanup.markImageCompleted(true);
                           event.preventDefault();
                           break;
                       // delete button
                       case 46:
                           scope.$parent.cleanup.markImageCompleted(false);
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