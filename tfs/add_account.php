<?php
require_once('db_config.php');

// TODO when users are set up, check for login. If not, send them to login page


if(isset($_POST['account-name'])){

    $name = $_POST['account-name'];
    $interest_rate = 0;

    if(isset($_POST['account-interest-rate'])){
        $interest_rate = 100*$_POST['account-interest-rate'];
    }


    $query = "INSERT INTO saving_accounts (user_id, name, interest_rate, balance, is_yearly_addition)
    VALUES (:user_id, :name, :interest_rate, 0, 0)";

    $stmt = $db->prepare($query);
    $stmt->execute(array('user_id' => $user_id, 'name'=>$name, 'interest_rate'=>$interest_rate));

    header("Location: add_account.php");
    die();
}




?>


<html>
<body>

<p>
    <a href="index.php">Back to main page</a>
</p>
<form method="post">
    <label>NAME: </label><input type="text" placeholder="Name of the account here" name="account-name"><br>
    <label>INTREST RATE (optional):</label><input type="number" name="account-interest-rate"><br>
    <input type="submit" value="Add Account">
</form>
</body>


</html>