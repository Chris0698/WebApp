(function () {
    "use strict";

    angular.module("FilmApp").
        controller("filmController",
            [
                "$scope",
                "dataService",
                function ($scope, dataService) {
                    $scope.note = {};

                    var getFilms = function () {
                        dataService.getFilms().then(
                            function (response) {
                                $scope.films = response.results;
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


                        //positioning of the note editor
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
                                if(response.status === 200) {
                                    //user logged in and data is good
                                    $scope.note.film_id = film.film_id;
                                    if(response.rowCount === 1) {
                                        //there is a record, so bind the data
                                        $scope.note = response.data[0];
                                    }

                                    $scope.filmNoteVisible = true;
                                } else if(response.status === 500) {
                                    $scope.note.comment = response.error;
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


                    $scope.logIn = function(credentials) {
                        dataService.logIn(credentials).then(
                            function (response) {
                                console.log(response);

                                if(response.data === "success") {
                                    location.reload();
                                }

                                $scope.logInMessage = response.data;
                            },
                            function (err) {
                                console.log(err);
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
                        $scope.note = {};
                    };

                    $scope.updateNote = function () {
                        console.log($scope.note);
                        dataService.updateNote($scope.note).then(
                            function (response) {
                                console.log(response.data);
                                if(response.data === "success") {
                                    alert("Successfully updated note.")
                                }
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