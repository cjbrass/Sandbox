<?php


require_once('class.Solver.php');

$board = new Board();

// use this to build your own board

//$board->map = array(
//    array(new Node('blue'), new Node('red'), new Node('red'), new Node('red'), new Node('red'), new Node('blue')),
//    array(new Node('yellow'), new Node('blue'), new Node('green'), new Node('blue'), new Node('red'), new Node('blue')),
//    array(new Node('red'), new Node('blue'), new Node('blue'), new Node('pink'), new Node('red'), new Node('pink')),
//    array(new Node('blue'), new Node('pink'), new Node('red'), new Node('green'), new Node('yellow'), new Node('pink')),
//    array(new Node('yellow'), new Node('green'), new Node('blue'), new Node('green'), new Node('blue'), new Node('pink')),
//);

// use this to build a board based off an image
$image_name = "test-image.jpg";
$board->initialize_map_from_image($image_name);

$board->build_map();

// define your starting points
$starting_x = 5;
$starting_y = 4;

$solver = new Solver($board, $starting_x, $starting_y);

// how deep do you want to go down? starts to really slow down past 5
$number_of_steps = 5;
$solver->begin_solve($number_of_steps);

$winning_board = $solver->winning_board;
$winning_score = $solver->winning_score;

$winning_board->print_map();
    echo "<p> score for this board is $winning_score</p>";
echo "moves to get there:";
var_dump($winning_board->moves_taken);

?>
