(function () {
    "use strict";

    angular.module("FilmApp").
        service("dataService",
        [
            "$q",
            "$http",
            function ($q, $http) {
                var urlBase = "/wai-assignment/server/index.php";

                this.getFilms = function () {
                    var defer = $q.defer();
                    var data = {
                        action : "list",
                        subject : "film"
                    };

                    $http.get(urlBase, {params : data, cache : true}).
                        success(function (response) {

                            defer.resolve({
                                dataCount : response.rowCount,
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
                        subject : "film",
                        term : term
                    };

                    $http.get(urlBase, {params : data, cache : true}).
                    success(function (response) {
                        defer.resolve({
                            data : response.results,
                            dataCount : response.rowCount
                        })
                    }).error(function (error) {
                        defer.reject(error)
                    });

                    return defer.promise;
                };

                this.checkLogIn = function () {
                    var defer = $q.defer();
                    var data = {
                        action : "checkLogIn"
                    };

                    $http.get(urlBase, {params : data}).
                    success(function (response) {
                        defer.resolve({
                            data: response.results
                        })
                    }).error(function (error) {
                        defer.reject(error)
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

                this.filterFilmByCategory = function (category) {
                    var defer = $q.defer();
                    var data = {
                        action : "list",
                        subject : "film",
                        cat : category
                    };

                    $http.get(urlBase, {params : data, cache : true}).
                    success(function (response) {
                        defer.resolve({
                            data : response.results,
                            dataCount : response.rowCount
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
                            data : response.result
                        })
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

                this.saveNote = function (note) {
                    console.log(note);
                    var defer = $q.defer();
                    var data = {
                        action : "update",
                        subject : "note",
                        data : angular.toJson(note)
                    };

                    $http.post(urlBase, data).
                        success(function (response) {
                            defer.resolve({
                                data : response.result
                            })
                        }).error(function (error) {
                            defer.reject(error)
                        });
                    return defer.promise;
                };
            }
        ])
}());