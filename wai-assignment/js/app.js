(function () {
    "use strict";

    angular.module("FilmApp", ["ngRoute"]).
        config(
            [
                "$routeProvider",
                function ($routeProvider) {
                    $routeProvider.
                        when("/logIn", {
                            templateUrl: "js/partials/logIn.php",
                            controller: "logInController"
                        }).
                        when("/films", {
                            controller: "filmController"
                        }).
                        otherwise({
                            redirectTo : "/"
                        });
                }
            ]
        );
}());