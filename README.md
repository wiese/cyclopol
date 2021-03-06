cyclopol – browse and visualize press releases

![Screeshot of search results in a list as well as visualized on a map and timeline](doc/images/articles_searched_map_timeline.png)

# Installation

Copy `.env.example` to `.env` and set appropriate values.

# Commands

* starting the services  
`docker-compose up`
  * db - persistence
  * app - data import pipeline and API
  * ssg - graphical front end
* creating the database schema (keep in mind that the *initial* setup of the db container can take a long time before users are created)  
`docker-compose exec app bin/console doctrine:schema:create`
* Running the data import pipeline  
`docker-compose exec app bin/console app:workflow`
* updating the database schema (only needed after changing DataModels)  
`docker-compose exec app bin/console doctrine:schema:update`

# External services

* talks to the article source website during commands run on app

# Data flow

Index => ArticleTeaser
* link
* listing date

Download => ArticleSource
* HTML
* listing date

Extract (from precisely defined parts of the page) => Article
* title
* date
* text
* district(s)

Derive (from the text, based on some fuzzy logic) => ArticleAddress
* report id
* previous report ids (if the article references former reports)
* street names

Enrich => Coordinate
* coordinates (from the street names)

# Ideas

* move into config
  * default DateTimeZone( 'Europe/Berlin' )
* store Article date and ArticleSource listingDate as UTC and only present it in Berlin time
* maybe use limdu to classify listings https://www.npmjs.com/package/limdu#batch-learning---learn-from-an-array-of-input-output-pairs
* make project extensible to allow for modules to be implemented for different cities and their data shapes

# Problems

* articles sometimes receive updates (e.g. pressemitteilung.885469.php), they are then listed again (a new ArticleTeaser with a new date) and their content should be downloaded again (new ArticleSource) - how does this translate to Article, though. Is only the latest ArticleSource taken into account when building the Article (and its subsequent information)? Do we somehow make its versions available?
