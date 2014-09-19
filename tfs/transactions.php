<?php
require_once('db_config.php');

// TODO when users are set up, check for login. If not, send them to login page

$account_id = intval($_GET['account_id']);

// first, make sure that the user does own that account, if they do not, we are done
$query = "SELECT * FROM saving_accounts WHERE user_id = :user_id AND id = :account_id";
$stmt = $db->prepare($query);
$stmt->execute(array('user_id' => $user_id, 'account_id'=>$account_id));

$accounts = array();
foreach($stmt as $row){
    // if there are no results, they should not be here. If there are 2+ results, something went wrong
    $accounts[] = $row;
}

if(count($accounts) == 0){
    echo"You do not have access to this page";
    die();
}

if(count($accounts) > 1){
    echo "something odd happened, and this page could not load properly. Feel free to try again";
    die();
}
$account = $accounts[0]; //no need to have the extra "layer" of arrays, since we are dealing with a singular account


if(isset($_POST['transaction-amount']) && isset($_POST['transaction-type']) && isset($_POST['transaction-date'])){

    $amount = 100*$_POST['transaction-amount'];
    $is_withdrawal = $_POST['transaction-type'] == 'withdrawal' ? 1: 0;
    $is_deposit = $_POST['transaction-type'] == 'deposit' ? 1: 0;
    $note = isset($_POST['transaction-note']) ? $_POST['transaction-note'] : '';

    $transaction_date = $_POST['transaction-date'];

    $query = "INSERT INTO transactions (user_id, account_id, is_withdrawal, is_deposit, amount, transaction_date, note)
    VALUES (:user_id, :account_id, :is_withdrawal, :is_deposit, :amount, :transaction_date, :note)";
    $stmt = $db->prepare($query);
    $stmt->execute(
        array(
            'account_id'=>$account_id,
            'user_id'=>$user_id,
            'is_withdrawal'=>$is_withdrawal,
            'is_deposit'=>$is_deposit,
            'amount'=>$amount,
            'transaction_date'=>$transaction_date,
            'note'=>$note,
        )
    );



    $account_balance = $account['balance'];
    if($is_withdrawal){
        $account_balance -= $amount;
    } else {
        $account_balance += $amount;
    }
    $query = "UPDATE saving_accounts SET balance = :balance WHERE id = :account_id";
    $stmt = $db->prepare($query);
    $stmt->execute(array('balance' => $account_balance, 'account_id'=>$account_id));

}





$query = "SELECT * FROM transactions WHERE account_id = :account_id";
$stmt = $db->prepare($query);
$stmt->execute(array('account_id'=>$account_id));

$transactions = array();
foreach($stmt as $row){
    $transactions[] = $row;
}

$today = date('Y-m-d');


?>

<html>
<body>

<p>
    <a href="index.php">Back to main page</a>
</p>

<table style="text-align: center; margin-bottom: 50px;">
    <thead>
    <tr>
        <td>Date</td>
        <td>Amount</td>
        <td>withdrawal/deposit</td>
        <td>note</td>
    </tr>

    </thead>
    <tbody>
    <?php
    foreach($transactions as $transaction){
        echo "<tr><td>";

        echo $transaction['transaction_date'];
        echo "</td><td>";

        echo $transaction['amount']/100;

        echo "</td><td>";
        if($transaction['is_withdrawal']){
            echo "Withdrawal";
        }else{
            echo "Deposit";
        }
        echo "</td><td>";
        echo $transaction['note'];

        echo "</td></tr>";
    }
    ?>

    </tbody>

</table>

<form method="post">
    <label>AMOUNT:</label><input type="number" name="transaction-amount"><br>
    <label>DEPOSIT:</label><input type="radio" name="transaction-type" value="deposit"><label>OR WITHDRAWAL:</label><input type="radio" name="transaction-type" value="withdrawal"><br>
    <label>DEPOSIT:</label><input type="date" name="transaction-date" value="<?=$today?>"><br>

    <label>NAME: </label><input type="text" placeholder="Optional Note here" name="transaction-note"><br>

    <input type="submit" value="Add Transaction">
</form>
</body>


</html>