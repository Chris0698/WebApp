(function () {
    "use strict";

    angular.module("FilmApp").
        service("applicationData",
            function($rootScope) {
                var sharedService = {};
                sharedService.film = {};

                sharedService.publishInfo = function (key, obj) {
                    this.film[key] = obj;
                    $rootScope.$broadcast("selectedFilm_" + key, obj);
                };

                return sharedService;
            }
        ).
        controller("FilmController",
            [
                "$scope",
                "dataService",
                "$location",
                "applicationData",     //film info service
                function ($scope, dataService, $location, applicationData) {
                    applicationData.publishInfo("film", {});
                    $scope.selectedFilm = {};

                    var getFilms = function () {
                        dataService.getFilms().then(
                            function (response) {
                                $scope.films = response.data;
                                $scope.filmTotal = response.rowCount;
                            },
                            function (err) {
                                console.log("Error getting films: " + err);
                            }
                        );
                    };

                    var getFilmSelectCategory = function() {
                        dataService.getSelectOptions().then(
                            function (response) {
                                $scope.options = response.data;
                            },
                            function (err) {
                                console.log("Error getting select cat: " + err);
                            },
                            function (notify) {
                                console.log(notify);
                            }
                        )
                    };

                    $scope.selectFilm = function($event, film) {
                        console.log(film);
                        applicationData.publishInfo("film", film);
                        $location.path("/films/" + film.film_id);
                    };

                    $scope.searchFilms = function(term) {
                        dataService.getSearchFilms(term).then(
                            function (response) {
                                $scope.filmTotal = response.rowCount;
                                $scope.films = response.data;
                            },
                            function (err) {
                                console.log("Error: " + err);
                            },
                            function (notify) {
                                console.log(notify);
                            }
                        )
                    };

                    $scope.filterFilmsByCat = function (cat) {
                        dataService.filterFilmByCategory(cat).then(
                            function (response) {
                                $scope.filmTotal = response.rowCount;
                                $scope.films = response.data;
                            },
                            function (error) {
                                $scope.films = "Error " + error;
                            },
                            function (notify) {
                                console.log(notify);
                            }
                        );
                    };

                    getFilms();
                    getFilmSelectCategory();
                }
            ]
        ).
        controller("FullFilmController",
            [
                "$scope",
                "dataService",
                "$routeParams",
                "$location",
                function ($scope, dataService, $routeParams, $location) {
                    $scope.selectedFilm = null;
                    $scope.filmNoteVisible = false;

                    $scope.$on("selectedFilm_film", function (ev, film) {
                        console.log(film);
                        $scope.selectedFilm = film;
                    });

                    var showFullDetails = function(filmID) {
                        dataService.getActors(filmID).then(
                            function (response) {
                                $scope.actors = response.data;
                            },
                            function (err) {
                                console.log("Error getting actors: " + err);
                            }
                        );

                        dataService.getNote(filmID).then(
                            function (response) {
                                console.log(response);
                                // if (response.data !== "NotLoggedIn") {
                                $scope.note = response.data;
                                $scope.filmNoteVisible = true;
                                // }
                            },
                            function (err) {
                                console.log("Error getting notes: " + err);
                            }
                        );
                    };

                    $scope.closeFilmPane = function() {
                        $scope.filmDetailsVisible = false;
                        $scope.selectedFilm = null;
                        $location.path("/films");
                    };

                    if($routeParams && $routeParams.filmID) {
                        showFullDetails($routeParams.filmID);
                    }
                }
            ]
        );
}());