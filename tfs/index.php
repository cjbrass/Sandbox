<?php
require_once('db_config.php');

// TODO when users are set up, check for login. If not, send them to login page

// if they dont already have a yearly account, then we send them to set up. If they are set up, we have all there accounts saved
$query = "SELECT * FROM saving_accounts WHERE user_id = :user_id";
$stmt = $db->prepare($query);

$stmt->execute(array('user_id' => $user_id));
$is_settup = false;
$accounts= array();


foreach($stmt as $row){
    $accounts[] = $row;
    if($row['is_yearly_addition']){
        $is_settup = true;
    }
}
//if the account does not have a yearly account, we send them to a page to make one
if(!$is_settup){
    header("Location: set_up_account.php");
    die();
}


// now we go through all the accounts, and display the info for each
// the primary info we want to grab is the amount we have left to contribute this year, as well as next year
// the estimated balance for each account would be nice as well
$deposit_available = 0;
$deposit_available_next_year = 0;

$query = "SELECT *, YEAR(transaction_date) as 'year' FROM transactions WHERE user_id = :user_id";
$stmt = $db->prepare($query);
$stmt->execute(array('user_id' => $user_id));
foreach($stmt as $row){
    if($row['is_withdrawal']){
        $deposit_available_next_year += $row['amount'];
        if($row['year'] < date('Y')){
            $deposit_available += $row['amount'];
        }
    }
    if($row['is_deposit']){
        $deposit_available -= $row['amount'];
        $deposit_available_next_year -= $row['amount'];

    }

}

$deposit_available = number_format($deposit_available/100, 2);
$deposit_available_next_year = number_format($deposit_available_next_year/100, 2);


?>


<html>
<body>
<table>
    <tbody>
        <tr>
            <td>Deposit availble:</td>
            <td>
                $<?=$deposit_available ?>
            </td>
        </tr>

        <tr>
            <td>Deposit availble next year:</td>
            <td>
                $<?=$deposit_available_next_year ?>
            </td>
        </tr>
    <tr style="height: 20px;">
        &nbsp;
    </tr>
    <tr>
        <td>Accounts</td>
    </tr>
    <?php foreach($accounts as $account){
        if($account['is_yearly_addition']){
            continue;
        }
        echo "<tr><td>";
        echo $account['NAME'];

        echo "</td><td>";

        echo "$".$account['balance']/100;

        echo "</td><td>";

        echo "<a href='transactions.php?account_id=".$account['id']."'>account transactions</a>";

        echo "</td></tr>";
    }
    ?>

    <tr>
        <td><a href="add_account.php">Add Account</a> </td>
    </tr>


    </tbody>
</table>
</body>


</html>