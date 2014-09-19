<?php
require_once('db_config.php');

// TODO when users are set up, check for login. If not, send them to login page


// if they already have a yearly account, then we send them back
$query = "SELECT * FROM saving_accounts WHERE user_id = :user_id AND is_yearly_addition = 1";
$stmt = $db->prepare($query);
$stmt->execute(array('user_id' => $user_id));

foreach($stmt as $row){
    // if there are any results, they do not belong here
    header("Location: index.php");
}

// set up the account
$valid_years = array(2009, 2010, 2011, 2012, 2013, 2014);
if(isset($_POST['first_year']) && in_array($_POST['first_year'], $valid_years)){

    $year = $_POST['first_year'];

    $query = "INSERT INTO saving_accounts (user_id, name, interest_rate, balance, is_yearly_addition)
    VALUES (:user_id, 'yearly', 0, 0, 1)";

    $stmt = $db->prepare($query);
    $stmt->execute(array('user_id' => $user_id));

    // grab the id of the row we just inserted
    $account_id = $db->lastInsertId();


    $query = "SELECT * FROM yearly_additions WHERE year >= :year";
    $stmt = $db->prepare($query);
    $stmt->execute(array('year' => $year));
    $years_to_add = array();
    foreach($stmt as $row){
        $years_to_add[] = $row;
    }

    foreach($years_to_add as $year_info){
        $amount = $year_info['amount'];
        $amount_year = $year_info['year'];
        $query = "INSERT INTO transactions (user_id, account_id, is_withdrawal, is_deposit, amount, transaction_date, note)
    VALUES (:user_id, :account_id, 1, 0, :amount, '1970-01-01 00:00:01', :note )";
        $stmt = $db->prepare($query);
        $stmt->execute(array('user_id' => $user_id, 'account_id'=>$account_id, 'amount'=>$amount, 'note'=>"yearly allotment for $amount_year"));
    }

    header("Location: index.php");
    die();
}

?>


<html>
<body>

<p>
    To set up your account, all we need to know if what year you were: 1) at least 18 2) living in Canada with a SIN
</p>
<form method="post">
<label>YEAR: </label><select name="first_year">
    <option value="2009">2009 or earlier</option>
    <option value="2010">2010</option>
    <option value="2011">2011</option>
    <option value="2012">2012</option>
    <option value="2013">2013</option>
    <option value="2014">2014</option>
</select>
    <input type="submit" value="submit">
</form>
</body>


</html>