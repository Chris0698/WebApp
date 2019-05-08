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
                                console.log(err);
                            },
                        );
                    };

                    var getFilmSelectCategory = function() {
                        dataService.getSelectOptions().then(
                            function (response) {
                                $scope.options = response.data;
                            },
                            function (err) {
                                console.log(err);
                            },
                        )
                    };

                    $scope.searchFilms = function(term) {
                        dataService.getSearchFilms(term).then(
                            function (response) {
                                $scope.filmTotal = response.rowCount;
                                $scope.films = response.data;
                            },
                            function (err) {
                                console.log(err);
                            }
                        )
                    };

                    $scope.selectFilm = function($event, film) {
                        console.log(film);
                        $scope.filmDetailsVisible = true;
                        $scope.selectedFilm = film;
                        $scope.note = {};

                        var element = $event.currentTarget;
                        var padding = 120;
                        var yPos = (element.offsetTop + element.clientTop + padding) - (element.scrollTop + element.clientTop);
                        var noteEditorElement = document.getElementById("note-editor");

                        noteEditorElement.style.top = yPos + "px";

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
                                $scope.note.film_id = film.film_id;
                                console.log($scope.note.film_id);
                                if(response.data !== "NotLoggedIn") {
                                    $scope.note = response.data;
                                    $scope.filmNoteVisible = true;
                                }
                            },
                            function (err) {
                                console.log(err);
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
                        dataService.logIn(credentials).then(
                            function (response) {
                                console.log(response);

                                if(response.data === "success") {
                                    location.reload();
                                }

                                $scope.logInMessage = response.results.data;
                            },
                            function (err) {
                                console.log(err);
                                $scope.logInMessage = err.results.data;
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
                                console.log(err);
                            }
                        )
                    };

                    $scope.filterFilmsByCat = function (cat) {
                        dataService.filterFilmByCategory(cat).then(
                            function (response) {
                                $scope.filmTotal = response.rowCount;
                                $scope.films = response.data;
                            },
                            function (err) {
                                console.log(err);
                            }
                        );
                    };

                    //for notes section
                    $scope.closeNoteEditor = function() {
                        $scope.filmNoteVisible = false;
                    };

                    $scope.updateNote = function () {
                        console.log($scope.note);
                        dataService.updateNote($scope.note).then(
                            function (response) {
                                console.log(response.data);
                            },
                            function (err) {
                                console.log(err);
                            }
                        );
                    };

                    getFilms();
                    getFilmSelectCategory();
                }
            ]
        );
}());