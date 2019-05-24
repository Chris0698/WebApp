(function () {
    "use strict";

    angular.module("FilmApp").
        controller("filmController",
            [
                "$scope",
                "dataService",
                function ($scope, dataService) {
                    $scope.note = {};

                    //Get films to be displayed on the homepgae
                    var getFilms = function () {
                        dataService.getFilms().then(
                            function (response) {
                                $scope.films = response.results;
                                $scope.filmTotal = response.rowCount;
                            },
                            function (err) {
                                console.log(err);
                            }
                        );
                    };

                    //get the select list options
                    var getFilmSelectCategory = function() {
                        dataService.getSelectOptions().then(
                            function (response) {
                                $scope.options = response.data;
                            },
                            function (err) {
                                console.log(err);
                            }
                        );
                    };

                    //search the films every time a new letter is entered into
                    //the search box
                    $scope.searchFilms = function(term) {
                        dataService.getSearchFilms(term).then(
                            function (response) {
                                $scope.filmTotal = response.rowCount;
                                $scope.films = response.data;
                            },
                            function (err) {
                                console.log(err);
                            }
                        );
                    };

                    //called every time a film is selected
                    $scope.selectFilm = function($event, film) {
                        console.log(film);
                        $scope.filmDetailsVisible = true;
                        $scope.selectedFilm = film;
                        $scope.note = {};


                        //positioning of the note editor
                        var element = $event.currentTarget;
                        var padding = 120;
                        var yPos = (element.offsetTop + element.clientTop +
                            padding) - (element.scrollTop + element.clientTop);
                        var noteEditor = document.getElementById("note-editor");
                        noteEditor.style.top = yPos + "px";

                        //Get actors for the selected film
                        dataService.getActors(film.film_id).then(
                            function (response) {
                                $scope.actors = response.data;
                            },
                            function (err) {
                                console.log(err);
                            }
                        );

                        //Get note for the selected film
                        dataService.getNote(film.film_id).then(
                            function (response) {
                                console.log(response);
                                $scope.noteStatus = "";
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

                    //close the side film pane
                    $scope.closeFilmPane = function() {
                        $scope.filmDetailsVisible = false;
                        $scope.selectedFilm = null;
                    };

                    //For when the user presses the log in button
                    $scope.logIn = function(credentials) {
                        console.log(credentials);
                        dataService.logIn(credentials).then(
                            function (response) {
                                console.log(response);

                                if(response.data === "success") {
                                    //refreshes the current webpage so the
                                    //selected film note will be visible per
                                    // film click
                                    location.reload();
                                } else {
                                    $scope.logInMessage = response.error;
                                }
                            },
                            function (err) {
                                console.log(err);
                                $scope.logInMessage = err.data;
                            }
                        );
                    };

                    //Log out of the app function
                    $scope.logOut = function() {
                        dataService.logOut().then(
                            function (response) {
                                console.log(response);
                                window.location.reload();
                            },
                            function (err) {
                                console.log(err);
                            }
                        );
                    };

                    //this is called when a category has been selected from
                    // the select list
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

                    //close the note editor
                    $scope.closeNoteEditor = function() {
                        $scope.filmNoteVisible = false;
                        $scope.note = {};
                    };

                    //Update or insert the editted or created note function
                    $scope.updateNote = function () {
                        console.log($scope.note);
                        dataService.updateNote($scope.note).then(
                            function (response) {
                                console.log(response);
                                if(response.data.results === "success") {
                                    $scope.noteStatus = "Note Updated";
                                } else {
                                    $scope.noteStatus = response.error;
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