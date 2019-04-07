(function () {
    "use strict";

    angular.module("FilmApp").
        controller("logInController",
        [
            "$scope",
            "dataService",
            "$location",
            function ($scope, dataService, $location) {
                //function for log in
                $scope.logIn = function(credentials) {
                    console.log("Log in called.");
                    console.log(credentials);
                    dataService.logIn(credentials).then(
                        function (response) {
                            console.log(response.data);
                            $scope.logInMessage = response.data;
                            $location.path("/");
                            location.reload();
                        },
                        function (error) {
                            console.log(error);
                            $scope.logInMessage = error.data;
                        }
                    )
                };

                //close the form by going back to view all films
                $scope.closeLogIn = function () {
                    $location.path("#/");
                };
            }
        ]).
        controller("filmController",
        [
            "$scope",
            "dataService",
            function ($scope, dataService) {
                var getFilms = function () {
                    dataService.getFilms().then(
                        function (response) {
                            $scope.filmTotal = response.dataCount;
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

                $scope.searchFilms = function(term) {
                    dataService.getSearchFilms(term).then(
                        function (response) {
                            $scope.filmTotal = response.dataCount;
                            $scope.films = response.data;
                        },
                        function (error) {
                            $scope.films = "Error " + error;
                        },
                        function (notify) {
                            console.log(notify);
                        }
                    )
                };


                $scope.filterFilmsByCat = function (cat) {
                    dataService.filterFilmByCategory(cat).then(
                        function (response) {
                            $scope.filmTotal = response.dataCount;
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

                var getFilmCategories = function() {
                    dataService.getSelectOptions().then(
                        function (response) {
                            $scope.options = response.data;
                        },
                        function (error) {
                            $scope.options = "Error " + error;
                        },
                        function (notify) {
                            console.log(notify);
                        }
                    )
                };

                $scope.selectedFilm = null;


                //called when a film is clicked
                $scope.selectFilm = function(film) {
                    console.log(film);
                    $scope.selectedFilm = angular.copy(film);

                    //Show the film details fragment
                    $scope.filmDetailsVisible = true;

                    dataService.checkLogIn().then(
                        function (response) {
                            if(response.data === 'true') {   //stop the form from being shown
                                $scope.filmNoteVisible = true;
                            }
                        },
                        function (error) {
                            $scope.note = "Error " + error;
                        },
                        function (notify) {
                            console.log(notify);
                        }
                    )
                };

                $scope.closeFilmPane = function() {
                    $scope.filmDetailsVisible = false;
                    $scope.selectedFilm = null;
                    // $location.path("#/");
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

                $scope.saveNote = function() {
                    console.log("Save note");
                    dataService.saveNote($scope.selectedFilm).then(
                        function (response) {
                            //$scope.status = response.success;
                            console.log(response.data);
                            if(response.data === "success") {
                                alert("Updated note for film");
                            } else {
                                alert("Fail");
                            }
                        },
                        function (err) {
                            $scope.options = "Error " + err;
                        }
                    )
                };

                $scope.cancelEdit = function() {
                    console.log("Hide editor");
                    $scope.filmNoteVisible = false;
                };

                getFilms();
                getFilmCategories();
            }
        ]
    )
}());