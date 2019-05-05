(function () {
    "use strict";

    angular.module("FilmApp").
        controller("filmController",
            [
                "$scope",
                "dataService",
                function ($scope, dataService) {
                    var getFilms = function () {
                        dataService.getFilms().then(
                            function (response) {
                                $scope.films = response.data;
                                $scope.filmTotal = response.rowCount;
                            },
                            function (err) {
                                console.log("Error getting films: " + err);
                                alert("Error getting films: " + err);
                            },
                            function (notify) {
                                console.log(notify);
                            }
                        );
                    };

                    var getFilmSelectCategory = function() {
                        dataService.getSelectOptions().then(
                            function (response) {
                                $scope.options = response.data;
                            },
                            function (err) {
                                console.log("Error: " + err);
                            },
                            function (notify) {
                                console.log(notify);
                            }
                        )
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

                    $scope.selectFilm = function(film) {
                        console.log(film);
                        $scope.filmDetailsVisible = true;
                        $scope.selectedFilm = film;

                        dataService.getActors(film.film_id).then(
                            function (response) {
                                $scope.actors = response.data;
                            },
                            function (err) {
                                console.log("Error getting Actors: " + err);
                            }
                        );

                        dataService.getNote(film.film_id).then(
                            function (response) {
                                console.log(response.data);
                                if(response.data !== "NotLoggedIn") {
                                    $scope.filmNoteVisible = true;
                                    $scope.note = response.data;
                                    console.log($scope.note);
                                }
                            },
                            function (err) {
                                console.log("Error: " + err);
                                alert("Error getting note: " + err);
                            },
                            function (notify) {
                                console.log(notify);
                            }
                        );
                    };

                    $scope.closeFilmPane = function() {
                        $scope.filmDetailsVisible = false;
                        $scope.selectedFilm = null;
                    };


                    $scope.showUserPane = function() {
                        $scope.logInPane = true;
                    };

                    $scope.closeLogIn = function() {
                        $scope.logInPane = false;
                    };

                    $scope.logIn = function(credentials) {
                        console.log("Log in called.");
                        dataService.logIn(credentials).then(
                            function (response) {
                                console.log(response.data);
                                if(response.data === "success") {
                                    location.reload();
                                }

                                $scope.logInMessage = response.data;
                            },
                            function (err) {
                                console.log(err);
                                alert("Error logging in: " + err);
                                $scope.logInMessage = err.data;
                            }
                        );
                    };

                    $scope.logOut = function() {
                        dataService.logOut().then(
                            function (response) {
                                console.log(response);
                                location.reload();
                            },
                            function (err) {
                                $scope.options = "Error " + err;
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

                    //for notes section
                    $scope.closeNoteEditor = function() {
                        $scope.filmNoteVisible = false;
                    };

                    $scope.saveNote = function (ID) {
                        console.log(ID);
                    };

                    getFilms();
                    getFilmSelectCategory();
                }
            ]
        );
}());