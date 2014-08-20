<?php
/**
 * Created by PhpStorm.
 * User: CJ
 * Date: 8/8/14
 * Time: 10:42 PM
 */

/**
 * Class Node
 *
 * Stores the information for a single node
 */
Class Node{

    // the count of how many consecutive counts of the same color we have made
    // we count left to right and top to bottom, so a horizontal count of 2 would have the same color to its left
    public $horizontal_count = 1;
    public $vertical_count = 1;

    // whenever we are done with a node, we mark it as checked
    public $checked = false;
    public $color;

    // the other nodes attached to this one
    public $left = null;
    public $right = null;
    public $up = null;
    public $down = null;

    // location of the node in the map it belongs to
    public $x;
    public $y;


    /**
     * Basic Constructor. Takes the color, and thats it
     * @param string $color - color of the node
     */
    public function __construct($color = 'red'){
        $this->color = $color;
    }

    /**
     * @param $color
     * @return bool - true if the node is not checked AND it is the same color as $color
     */
    public function canVisit($color){
        return !$this->checked && $this->color == $color;
    }

    /** recursively check all the nodes neighbors to find the appropriate score
     * @return array - the horizontal and vertical counts
     */
    public function scoreNode(){

        $color = $this->color;
        $horiz_count = $this->horizontal_count;
        $vert_count = $this->vertical_count;
        $this->checked = true;

        $directions_array = array('right','left','up','down');
        foreach($directions_array as $direction){
            if ($this->$direction != null && $this->$direction->canVisit($color)){
                $returned_count = $this->$direction->scoreNode();
                if ($returned_count['horiz'] > $horiz_count)
                    $horiz_count = $returned_count['horiz'];
                if ($returned_count['vert'] > $vert_count)
                    $vert_count = $returned_count['vert'];

            }
        }

        return array('horiz'=>$horiz_count, 'vert'=>$vert_count);
    }
}