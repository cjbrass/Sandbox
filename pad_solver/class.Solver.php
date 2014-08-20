<?php
/**
 * Created by PhpStorm.
 * User: CJ
 * Date: 8/10/14
 * Time: 9:52 PM
 */


require_once('class.Board.php');

/**
 * Class Solver
 *
 * Plug in the relevant info, and let this class do the solving
 */
Class Solver{

    public $initial_board;
    public $initial_node;
    // the max depth we will go while using dfs
    public $max_depth;

    // the current winning board and score. Not final until the search is done
    public $winning_board;
    public $winning_score = 0;

    /**
     * @param $initial_board - the initial board
     * @param $initial_node_x - the x position of the starting node
     * @param $initial_node_y - the y position of the starting node
     */
    public function __construct($initial_board, $initial_node_x, $initial_node_y){
        $this->initial_board = $initial_board;
        $this->initial_node = $initial_board->map[$initial_node_y][$initial_node_x];
    }


    /**
     * Specify a depth, and start the recursion to solve the board
     * @param $max_depth
     */
    public function begin_solve($max_depth){
        $this->max_depth = $max_depth;
        $this->solve($this->initial_node, $this->initial_board);
    }

    /**
     * This recurisvely solves the board, looking for the best possible score
     * @param $node - the "finger" node
     * @param $board - the setup of the current node
     * @param int $depth - how deep we have gone
     */
    public function solve($node, $board, $depth = 0){
        if ($depth >= $this->max_depth) {
            return;
        }

        // the moves taken up to this point, as well as the current x and y positions
        $old_moves = $board->moves_taken;
        $x = $node->x;
        $y = $node->y;

        // if there exists a node in any of the directions, we swap with it and continue recursively.
        // We do this for all directions
        $directions_array = array('right','left','up','down');
        foreach($directions_array as $direction){
            if ($node->$direction != null){
                //make a new board
                $new_board = new Board();
                $new_board->copy_map($board->map);
                // need to remember the moves taken to get to this point
                $new_board->moves_taken = $old_moves;
                $new_board->moves_taken[] = $direction;
                // set the "finger" node for the new board
                $new_node = $new_board->map[$y][$x];
                $new_node = $new_board->switch_nodes($new_node, $direction);

                $this->solve($new_node, $new_board, $depth + 1);
            }
        }

        $score = $board->score();
        // this score evaluation should probably also look at the number of steps taken (less is better), but this will work for now
        if ($score > $this->winning_score){
            $this->winning_score = $score;
            $this->winning_board = $board;
        }

    }


}