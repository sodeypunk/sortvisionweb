/**
 * Created by soda on 9/26/16.
 */

(function() {
    var app = angular.module('sortvision', []);

    app.controller('CleanupController', ['$http', function($http) {
        var cleanupCtrl = this;
        this.bibs = [];
        this.chunkedData = [];
        this.ezRefString = $('input[name=ezRefString]').val();
        this.selectedIndex = 0;



        $http({
            method: 'POST',
            url: '/index.php/cleanup/bibs',
            params: {ezRefString: this.ezRefString}}
        ).success(function(data) {

            cleanupCtrl.bibs = data;
            cleanupCtrl.chunkedData = chunk(data, 3);

        });

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
           scope: {selectedindex:'='},
           link: function (scope, element, attr) {
               $document.on("keydown", function(event) {
                   switch(event.keyCode)
                   {
                       case 39:
                           scope.selectedindex += 1;
                           break;
                       case 37:
                           if (scope.selectedindex > 0)
                               scope.selectedindex -= 1;
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