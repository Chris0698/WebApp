<section>
    <form id="search-and-filter">
        <h2>Search</h2>
        <label>
            Search through the list:
            <!--data-ng-change calls the searchFilm function every time a char entered-->
            <input type="text" data-ng-model="term" data-ng-change="searchFilms(term)"/>
        </label>

        <label>
            Select Category:
            <select data-ng-model="selectedCat"
                    data-ng-change="filterFilmsByCat(selectedCat)">
                <option value="0" selected>List All</option>
                <option data-ng-repeat="option in options"
                        value="{{option.category_id}}">
                    {{option.name}}
                </option>
            </select>
        </label>
    </form>

    <p>Number of films: {{filmTotal}}</p>
    <!--for fill notes is user logged in-->
    <section>
        <div data-ng-include="'js/partials/notes.html'"
             data-ng-show="filmNoteVisible">
        </div>
    </section>

    <div id="filmDisplayTitles">
        <span>Title</span>
        <span>Description</span>
        <span>Year</span>
        <span>Category</span>
        <span>Age Rating</span>
        <span>Last Updated</span>
    </div>
    <div id="film"
         data-ng-repeat="film in films"
         data-ng-click="selectFilm($event, film)"
         data-ng-class="{'selected':selectedFilm.film_id === film.film_id}">

        <span>{{film.title}}</span>
        <span>{{film.description}}</span>
        <span>{{film.release_year}}</span>
        <span>{{film.category}}</span>
        <span>{{film.rating}}</span>
        <span>{{film.last_update}}</span>
    </div>
</section>