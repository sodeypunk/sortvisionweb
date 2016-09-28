/**
 * Created by soda on 9/26/16.
 */

(function() {
    var app = angular.module('sortvision', []);

    app.controller('CleanupController', function() {

    });

})();

$(document).keydown(function(e) {
    switch(e.which) {
        case 37: // left
            break;

        case 38: // up
            break;

        case 39: console.log("right-click");
            break;

        case 40: // down
            break;

        default: return; // exit this handler for other keys
    }
    e.preventDefault(); // prevent the default action (scroll / move caret)
});