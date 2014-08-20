<?php
/**
 * Created by PhpStorm.
 * User: CJ
 * Date: 8/8/14
 * Time: 10:43 PM
 */

require_once('class.Node.php');

/**
 * Class Board
 *
 * Stores
 */
Class Board{
    // the map of the tiles on the board
    public $map = null;
    // $score for this node
    public $score = 0;
    // keeps track of the moves taken to get to this point
    public $moves_taken = array();

    public function __construct(){

    }

    /**
     * Simply prints out a crude version of the board. Used mostly for testing/debugging
     */
    public function print_map(){
        echo "<table> <tbody>";

        foreach($this->map as $row){
            echo "<tr>";
            foreach($row as $node){
                echo "<td style='background-color: $node->color'> $node->color</td>";
            }
            echo "</tr>";
        }

        echo "</tbody></table>";
    }

    /**
     * @param $img_name - path to the image we want to build the board from
     */
    public function initialize_map_from_image($img_name){
        // grab the image, and rescale it to 100x100
        $im = imagecreatefromjpeg($img_name);
        $im = imagescale($im, 100, 100,  IMG_BICUBIC_FIXED);

        // this where I found the peices are located on the 100x100 image
        // based off of only 1 image, may very well need to be adjusted
        $y_array = array(51,62,73,83,94);
        $x_array = array(9,25,42,58,74,91);

        $this->map = array();
        foreach($y_array as $row_key=>$y){
            $this->map[$row_key] = array();

            foreach($x_array as $column_key=>$x){

                $r = 0;
                $g = 0;
                $b = 0;

                // average the pixel and its 4 immediate neighbors. No need for edge checks, we will be at least a couple pixels from the edge
                $rgb = imagecolorat($im, $x, $y);
                $r += ($rgb >> 16) & 0xFF;
                $g += ($rgb >> 8) & 0xFF;
                $b += $rgb & 0xFF;
                $rgb = imagecolorat($im, $x+1, $y);
                $r += ($rgb >> 16) & 0xFF;
                $g += ($rgb >> 8) & 0xFF;
                $b += $rgb & 0xFF;
                $rgb = imagecolorat($im, $x-1, $y);
                $r += ($rgb >> 16) & 0xFF;
                $g += ($rgb >> 8) & 0xFF;
                $b += $rgb & 0xFF;
                $rgb = imagecolorat($im, $x, $y+1);
                $r += ($rgb >> 16) & 0xFF;
                $g += ($rgb >> 8) & 0xFF;
                $b += $rgb & 0xFF;
                $rgb = imagecolorat($im, $x, $y-1);
                $r += ($rgb >> 16) & 0xFF;
                $g += ($rgb >> 8) & 0xFF;
                $b += $rgb & 0xFF;


                // get the average colour
                $r = intval($r/5);
                $g = intval($g/5);
                $b = intval($b/5);

                // need to get tighter constraints, already found at least 1 place where a colour is misidentified
                if ($r < 100 && $g > 200 && $b < 140) {
                    $this->map[$row_key][$column_key] = new Node("green");
                }

                if ($r < 160 && $g < 160 && $b < 160) {
                    $this->map[$row_key][$column_key] = new Node("purple");
                }

                if ($r > 200 && $g > 200 && $b < 140) {
                    $this->map[$row_key][$column_key] = new Node("yellow");
                }

                if ($r > 210 && $g < 90 && $b > 120) {
                    $this->map[$row_key][$column_key] = new Node("pink");
                }

                if ($r > 220 && $g > 100 && $b < 110) {
                    $this->map[$row_key][$column_key] = new Node("red");
                }

                if ($r < 120 && $g > 150 && $b > 210) {
                    $this->map[$row_key][$column_key] = new Node("blue");
                }

            }

        }

    }

    /** need to call this to set up the data structure correctly
     *  Specifically, it lets each node know its neighbors, as well as its x/y location
     * @return bool - returns false if the map has not been set
     */
    public function build_map(){
        if ($this->map == null){
            return false;
        }

        $height = count($this->map);
        $width = count($this->map[0]);

        // assign the left/right/up/down for all the nodes, as well as x/y
        for($h = 0; $h < $height; $h++){

            for($w = 0; $w < $width; $w++){
                //x and y values
                $this->map[$h][$w]->x = $w;
                $this->map[$h][$w]->y = $h;

                // up node
                if($h > 0){
                    $this->map[$h][$w]->up = $this->map[$h-1][$w];
                }

                // down node
                if($h < $height - 1){
                    $this->map[$h][$w]->down = $this->map[$h+1][$w];
                }

                //left node
                if($w > 0){
                    $this->map[$h][$w]->left = $this->map[$h][$w-1];
                }

                //right node
                if($w < $width -1){
                    $this->map[$h][$w]->right = $this->map[$h][$w+1];
                }
            }
        }
    }

    /**
     * @return int - the score for this map
     */
    public function score(){

        $score = 0;

        //we set the horizontal and vertical counts for all the nodes
        foreach($this->map as $row){
            foreach($row as $node){

                $color = $node->color;
                $vert_count = $node->vertical_count;
                $horizontal_count = $node->horizontal_count;



                // we only ever have to check down and right
                $right_node = $node->right;
                $down_node = $node->down;

                if (isset($right_node->color) && $right_node->color == $color){
                    $right_node->horizontal_count = ++$horizontal_count;
                }

                if (isset($down_node->color) && $down_node->color == $color){
                    $down_node->vertical_count = ++$vert_count;
                }

            }
        }

        // now we start at some node, and check all of its neighbors
        foreach($this->map as $row){
            foreach($row as $node){

                // this node has been checked at some point, continue on
                if($node->checked) continue;

                // get the score for this node
                $scores = $node->scoreNode();

                $total_linked = 0;
                // currently we only account for the simple ways that we can connect the tiles
                if($scores['horiz'] > 2 && $scores['vert'] > 2){
                    $total_linked = $scores['horiz'] + $scores['vert'] -1;
                }elseif ($scores['horiz'] > 2){
                    $total_linked = $scores['horiz'];
                }elseif ($scores['vert'] > 2){
                    $total_linked = $scores['vert'];
                }

                //TODO: Expand this to actually take into account what color is being scored.
                if ($total_linked > 0){
                    $score += 1000 + 250*($total_linked-3);
                }

            }
        }

        $this->score = $score;
        return $score;
    }

    /**
     *  Need to copy the map over for every new search, so we use this function
     * @param $map - the map we want to copy
     */
    function copy_map($map){
        $this->map = array();
        foreach($map as $row_key=>$row){
            $this->map[$row_key] = array();
            foreach($row as $node_key=>$node){


                $this->map[$row_key][$node_key] = new Node($node->color);
            }
        }
        // lastly we build the map
        $this->build_map();
    }

    /**
     * @param $node - node to switch. This is the node your finger would be on while moving
     * @param $direction - direction you are moving the tile
     * @return mixed - returns the new current node. We have swapped the colors (simplest way to swap), so the swapped node is now the node your finger would be on.
     */
    public function switch_nodes($node, $direction){
        $swap_node = $node->$direction;

        $color1 = $node->color;
        $color2 = $swap_node->color;

        $node->color = $color2;
        $swap_node->color = $color1;
        return $swap_node;
    }
}