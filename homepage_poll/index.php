<?php
require_once('db-settings.php');
$date = date('Y-m-d H:i:s');
$ip = $_SERVER['REMOTE_ADDR'] ;
$device_info =  $_SERVER['HTTP_USER_AGENT'];
$page_name = 'homepage_poll';

$stmt = $db->prepare('INSERT INTO visitor_log VALUES (?,?,?,?)');
$stmt->bind_param('ssss', $ip, $date,$page_name ,$device_info);
$stmt->execute();
$problem_with_rankings = false;
$vote_submitted = false;
if(isset($_POST['submit'])){
    $h_1 = intval($_POST['h_1']);
    $h_2 = intval($_POST['h_2']);
    $comment = $_POST['comments'];

    if($h_1 > 0 && $h_1 < 11 && $h_2 > 0 && $h_2 < 11){
        $vote_submitted = true;
        $stmt = $db->prepare('INSERT INTO poll_table VALUES (?,?,?,?,?)');
        $stmt->bind_param('siiss', $ip, $h_1, $h_2, $date, $comment);

        $stmt->execute();
    }else{
        $problem_with_rankings = true;
    }

}

?>

<html>

<body>

<div style=" width: 700px; margin-top: 100px; margin-left: auto; margin-right: auto;">
    <p>Hello!</p>
    <p>
        My homepage is nearly finished, the problem is that I have 2 designs that I'm having troubles choosing between.
        Take a look at both, and give each a rating out of 10 (10 being best site you've ever seen, 0 being the worst).

    </p>

    <br>
    <br>
    You can find the sites here: <br>
    <a href="homepage_1" target="_blank">Homepage 1</a>
    <br>
    <a href="homepage_2" target="_blank">Homepage 2</a>
    <br>
    <br>
    <?php if($problem_with_rankings){ ?>
    <p style="color: red;">Looks like you did not enter in a valid score</p>
    <?php }elseif($vote_submitted){ ?>
        <p style="color: green;">Vote Registered</p>
    <?php }?>
    <form method="POST" id="poll_form">
    Voting! Again, the score is out of 10, with 10 being amazing<br>
    Homepage 1 <input type="number" name="h_1"><br>
    Homepage 2 <input type="number" name="h_2"><br>
        Comments  <textarea name="comments" form="poll_form"></textarea><br>
    <input type="submit" name="submit" value="submit">
    </form>

    <p>
        Thanks for taking some time to look this over, and I appreciate any feedback you can give.
    </p>
    <p>
        One final note, Neither of these sites are guaranteed to work on mobile (want to figure out which one to use before I put in that time)
    </p>
</div>
</body>
</html>