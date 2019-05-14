(function () {
    "use strict";

    angular.module("FilmApp",
        [
            "ngRoute"
        ]
    ).config(
        [
            "$routeProvider",
            function ($routeProvider) {
                $routeProvider.
                    when("/films", {
                        templateUrl : "js/partials/film.php",
                        controller : "filmController"
                    }).
                    otherwise({
                        redirectTo: "/"
                    })
            }
        ]
    );
}());