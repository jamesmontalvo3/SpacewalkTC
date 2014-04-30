<?php

ini_set('display_errors',1);
error_reporting(-1);

require 'vendor/autoload.php';

require 'database/rb.phar';


//R::setup('mysql:host=localhost;dbname=DB','USER','PASSWORD');
//R::freeze( true );

// $b = R::dispense( 'book' );

// $b->title = 'Learn to Program';
// $b->rating = 10;
// $b['price'] = 29.99; //you can use array notation as well

// $id = R::store( $b );

// $nb = R::load( 'book', $id );


/*
	required actions:

App        : Send page

revision
	read   : Yes : View a revision
	index  : Yes : See list of revisions for a particular event
	create : ?   : Do you ever say "create me a new revision"? I don't think so...you get the latest, revise it, and send changes
	update : NO  : revisions are frozen. Users create revisions of revisions each time they save
	delete : NO  : user cannot delete...revisions table will get cleaned out when revs very old

itemdefault
	read   : NO  : Only read-all required for now
	index  : Yes : Each initial page load get list of defaults (and perhaps check for changes periodically?)
	create : Yes : each time a user uses an item without a default, it will create and ask for values
	update : Yes : once created only should need to update. Can set values to zero/null/blank.
	delete : NO  : Admin may need to delete at some point if a P/N gets messed up in IMS

userlast
	read   : 
	index  : 
	create : 
	update : 
	delete : 

user
	read   : ?
	index  : ?
	create : ?
	update : ?
	delete : ?

 */

$app = new \Slim\Slim(array(
	"debug" => true,
	'log.enabled' => true
));

$app->get('/', function() {
	echo "Hello, World";
});

/*

$app->group('/event', function () use ($app) {

	// read   :
	// $.ajax("http://localhost/SpacewalkTC/45", {type:"get"});
    $app->get('/:id', function ($id) {
    	echo "read ($id)";
    });

	// index  : Show all events @TODO will this work?
	// $.ajax("http://localhost/SpacewalkTC/", {type:"get"});
    $app->get('/', function () {
    	echo "index";
    });

	// create : PUT or POST?
	// $.ajax("http://localhost/SpacewalkTC/", {type:"post"});
  	$app->post('/', function () {
  		echo "create";
    });

	// update : PUT or POST?
	// $.ajax("http://localhost/SpacewalkTC/45", {type:"post"});
    $app->post('/:id', function ($id) {
    	echo "update ($id)";
    });

	// delete : 
	// $.ajax("http://localhost/SpacewalkTC/45", {type:"delete"});
    $app->delete('/:id', function ($id) {
    	echo "delete ($id)";
    });


});

*/



// index  : Show all events @TODO will this work?
// $.ajax("http://localhost/SpacewalkTC/", {type:"get"});
// NOTE (BUG?): for some reason the trailing slash is needed on this in the
// presence of the other get method with /:controller/:id
$app->get('/:controller/', function ($controller) {
	echo "index $controller";
});

// read   :
// $.ajax("http://localhost/SpacewalkTC/45", {type:"get"});
$app->get('/:controller/:id', function ($controller, $id) {
	echo "read $controller ($id)";
});

// $.ajax("http://localhost/SpacewalkTC/", {type:"post"});
// NOTE (BUG?): for some reason the trailing slash is needed on this in the
// presence of the other post method with /:controller/:id
$app->put('/:controller/', function ($controller) {
	echo "create $controller";
});

// $.ajax("http://localhost/SpacewalkTC/45", {type:"post"});
$app->put('/:controller/:id', function ($controller, $id) {
	echo "update $controller ($id)";
});

// delete : 
// $.ajax("http://localhost/SpacewalkTC/45", {type:"delete"});
$app->delete('/:controller/:id', function ($controller, $id) {
	echo "delete $controller ($id)";
});




// requires index.php in place...
// $app->get('/hello/:name', function ($name) {
//     echo "Hello, $name. Do you like book ID #$id?";
// });

// $app->get('/makeevent', function () {
//     $e = R::dispense( 'event' );
// 	$e->datetime = "20140423000000";
// 	$e->name = "US EVA 26b";
// 	$id = R::store( $e );
// 	echo "done. ID = $id";
// });



// $app->get('/book/:id/:title/:rating/:price', function ($id,$title,$rating,$price) {
	// $b = R::load('book', $id);
	// $b->title = $title;
	// $b->rating = $rating;
	// $b->price = $price;
	// $b->qty = 13465143;
	// R::store( $b );
	// echo "DONE";
	// //echo"<pre>";print_r($b);echo"</pre>";
// });

// $app->get('/books', function() use ($app) {
	// // $bs = R::findAll( 'book' );
	// // $ar = R::exportAll( $bs );
	
	// $b = R::dispense( 'book', 3 );
	// $app->response->headers->set('Content-Type', 'application/json');
	// echo json_encode($b);
	
// });




$app->run();