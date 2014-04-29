<?php

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

event

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

$app = new \Slim\Slim();

$app->group('/event', function () use ($app) {

	// read   :
    $app->get('/:id', function ($id) {

    });

	// index  : Show all events @TODO will this work?
    $app->get('/', function () {

    });

	// create : PUT or POST?
  	$app->post('/', function () {

    });

	// update : PUT or POST?
    $app->post('/:id', function ($id) {

    });

	// delete : 
    $app->delete('/:id', function ($id) {

    });


}




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



$app->get('/', function() {
	echo "Hello, World";
});

$app->run();