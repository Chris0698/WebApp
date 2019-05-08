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

                    this.getFilms = function () {
                        var defer = $q.defer();
                        var data = {
                            action : "list",
                            subject : "films"
                        };

                        $http.get(urlBase, {params : data, cache : true}).
                            success(function (response) {
                                defer.resolve({
                                    data : response.results,
                                    rowCount : response.rowCount,
                                });
                            }).error(function (err) {
                                defer.reject(err);
                            });

                        return defer.promise;
                    };

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
                                   data : response.results
                                });
                            }).error(function (err) {
                                defer.reject(err)
                            });

                        return defer.promise;
                    };

                    this.getSelectOptions = function () {
                        var defer = $q.defer();
                        var data = {
                            action : "list",
                            subject : "categories"
                        };

                        $http.get(urlBase, {params : data, cache : true}).
                        success(function (response) {
                            defer.resolve({
                                data : response.results
                            })
                        }).error(function (error) {
                            defer.reject(error)
                        });

                        return defer.promise;
                    };

                    this.getSearchFilms = function (term) {
                        var defer = $q.defer();
                        var data = {
                            action: "list",
                            subject : "films",
                            term : term
                        };

                        $http.get(urlBase, {params : data, cache : true}).
                        success(function (response) {
                            defer.resolve({
                                data : response.results,
                                rowCount : response.rowCount
                            })
                        }).error(function (error) {
                            defer.reject(error)
                        });

                        return defer.promise;
                    };

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
                                    data : response.results
                                });
                            }).error(function (error) {
                                defer.reject(error)
                            });

                        return defer.promise;
                    };

                    this.logOut = function () {
                        var defer = $q.defer();
                        var data = {
                            action : "logOut",
                            subject : "user"
                        };

                        $http.post(urlBase, data).
                        success(function (response) {
                            defer.resolve(response)
                        }).error(function (error) {
                            defer.reject(error)
                        });

                        return defer.promise;
                    };


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
                                data : response.results,
                                rowCount : response.rowCount
                            });
                        }).error(function (error) {
                            defer.reject(error)
                        });

                        return defer.promise;
                    };


                    this.getNote = function (ID) {
                        var defer = $q.defer();
                        var data = {
                            action : "list",
                            subject : "notes",
                            film_id : ID
                        };

                        $http.get(urlBase, {params : data, cache : true}).
                        success(function (response) {
                            defer.resolve({
                                data : response.results
                            });
                        }).error(function (error) {
                            defer.reject(error)
                        });

                        return defer.promise;
                    };

                    this.updateNote = function (note) {
                        console.log(note);

                        var defer = $q.defer();
                        var data = {
                            action: "update",
                            subject: "note",
                            data: angular.toJson(note)
                        };

                        console.log(data);
                        
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
        )
}());