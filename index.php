<?php

require 'vendor/autoload.php';

require 'rb.phar';
R::setup('mysql:host=localhost;dbname=DB','USER','PASSWORD');
R::freeze( true );

// $b = R::dispense( 'book' );

// $b->title = 'Learn to Program';
// $b->rating = 10;
// $b['price'] = 29.99; //you can use array notation as well

// $id = R::store( $b );

// $nb = R::load( 'book', $id );

$app = new \Slim\Slim();


// requires index.php in place...
$app->get('/hello/:name', function ($name) {
    echo "Hello, $name. Do you like book ID #$id?";
});

$app->get('/makeevent', function () {
    $e = R::dispense( 'event' );
	$e->datetime = "20140423000000";
	$e->name = "US EVA 26b";
	$id = R::store( $e );
	echo "done. ID = $id";
});



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