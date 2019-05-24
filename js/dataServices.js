(function () {
    "use strict";

    angular.module("FilmApp").
        service("dataService",
            [
                "$q",
                "$http",
                function ($q, $http) {
                    //url to index.php where the data will come from
                    var urlBase = "/wai-assignment/server/index.php";

                    //Calls listFilms in index.php to get films
                    this.getFilms = function () {
                        var defer = $q.defer();
                        var data = {
                            action : "list",
                            subject : "films"
                        };

                        $http.get(urlBase, {params : data, cache : true}).
                            success(function (response) {
                                defer.resolve({
                                    //the results is inside an array called data
                                    results : response.data.results,
                                    rowCount : response.data.rowCount
                                });
                            }).error(function (err) {
                                defer.reject(err);
                            });

                        return defer.promise;
                    };

                    //get all actors for a specific film from server code
                    this.getActors = function (filmID) {
                        var defer = $q.defer();
                        var data = {
                            action: "list",
                            subject: "actors",
                            film_id: filmID
                        };

                        $http.get(urlBase, {params : data, cache : true}).
                            success(function (response) {
                                defer.resolve({
                                    //the results is inside an array called data
                                   data : response.data.results
                                });
                            }).error(function (err) {
                                defer.reject(err);
                            });

                        return defer.promise;
                    };

                    //Gets the select categories for filtering
                    this.getSelectOptions = function () {
                        var defer = $q.defer();
                        var data = {
                            action : "list",
                            subject : "categories"
                        };

                        $http.get(urlBase, {params : data, cache : true}).
                            success(function (response) {
                                defer.resolve({
                                    //the results is inside an array called data
                                    data : response.data.results
                                });
                            }).error(function (err) {
                                defer.reject(err);
                            });

                        return defer.promise;
                    };

                    //get the films that are like the entered term from the
                    // server
                    this.getSearchFilms = function (term) {
                        var defer = $q.defer();
                        var data = {
                            action: "list",
                            subject : "films",
                            searchTerm : term
                        };

                        $http.get(urlBase, {params : data, cache : true}).
                            success(function (response) {
                                defer.resolve({
                                    //the results is inside an array called data
                                    data : response.data.results,
                                    rowCount : response.data.rowCount
                                });
                            }).error(function (err) {
                                defer.reject(err);
                            });

                        return defer.promise;
                    };

                    //Calls log in on the server
                    this.logIn = function (credentials) {
                        var defer = $q.defer();
                        var data = {
                            action : "logIn",
                            subject : "user",
                            data : credentials
                        };

                        $http.post(urlBase, data).
                            success(function (response) {
                                defer.resolve({
                                    //the results is inside an array called data
                                    data : response.data.results,
                                    error : response.error
                                });
                            }).error(function (err) {
                                defer.reject(err);
                            });

                        return defer.promise;
                    };

                    //Logging out of the app
                    this.logOut = function () {
                        var defer = $q.defer();
                        var data = {
                            action : "logOut",
                            subject : "user"
                        };

                        $http.post(urlBase, data).
                            success(function (response) {
                                defer.resolve(response);
                            }).error(function (err) {
                                defer.reject(err);
                            });

                        return defer.promise;
                    };

                    //Gets the films that have the parameter category
                    this.filterFilmByCategory = function (category) {
                        var defer = $q.defer();
                        var data = {
                            action : "list",
                            subject : "films",
                            cat : category
                        };

                        $http.get(urlBase, {params : data, cache : true}).
                            success(function (response) {
                                defer.resolve({
                                    //the results is inside an array called data
                                    data : response.data.results,
                                    rowCount : response.data.rowCount
                                });
                            }).error(function (err) {
                                defer.reject(err);
                            });

                        return defer.promise;
                    };

                    //Get the note for the film from index
                    this.getNote = function (ID) {
                        var defer = $q.defer();
                        var data = {
                            action : "list",
                            subject : "notes",
                            film_id : ID
                        };

                        //set cache to false because for some reason if a note
                        //updated, then the editor is closed, then is film is
                        //reselected the last note would be shown
                        $http.get(urlBase, {params : data, cache : false}).
                            success(function (response) {
                                defer.resolve({
                                    status : response.status,
                                    rowCount : response.data.rowCount,
                                    data : response.data.results,
                                    //for debugging, a possible error and
                                    //status was returned
                                    error : response.error
                                });
                            }).error(function (err) {
                                defer.reject(err);
                            });

                        return defer.promise;
                    };

                    //Update or save the edit note
                    this.updateNote = function (note) {
                        var defer = $q.defer();
                        var data = {
                            action: "update",
                            subject: "note",
                            data: angular.toJson(note)
                        };
                        
                        $http.post(urlBase, data).
                            success(function (response) {
                                defer.resolve(response);
                            }).
                            error(function (err) {
                                defer.reject(err);
                            });

                        return defer.promise;
                    };
                }
            ]
        );
}());