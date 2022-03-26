<?php 
require_once("../../configs/sessions.php");
require_once("../../configs/db_config.php");

if($_SERVER["REQUEST_METHOD"] == "POST"){

    if(isset($_POST['automaticPayment'])){

        $query_getWalletsAutomaticPayment = "SELECT w.id, w.balance, j.monthly_cost, j.email, j.category_type FROM Wallet w 
        JOIN ((SELECT email, wallet_id, monthly_cost, category_type FROM Employer e JOIN Employer_Plan ep ON e.plan_name = ep.name) UNION
        (SELECT email, wallet_id, monthly_cost, category_type FROM Seeker s JOIN Seeker_Plan sp ON s.plan_name = sp.name)) j ON j.wallet_id = w.id
        WHERE automatic = 1";
        $result_getWalletsAutomaticPayment = mysqli_query($db, $query_getWalletsAutomaticPayment);
        
        if(mysqli_num_rows($result_getWalletsAutomaticPayment) == 0){
            $_SESSION['err'] = "No accounts with automatic payments enabled";
        } else{
            while($row = mysqli_fetch_assoc($result_getWalletsAutomaticPayment)){
                $walletId = $row['id'];
                $monthlyCost = $row['monthly_cost'];
                $balance = $row['balance'];
                $newBalance = $balance - $monthlyCost;
                $query_makePayment = "UPDATE Wallet SET balance = $newBalance WHERE id = $walletId";

                if(mysqli_query($db, $query_makePayment)){
                    $payment_method_id = $row['default_payment_id'];     
                    $query_transaction = "INSERT INTO Transaction (amount, wallet_id, payment_method_id) VALUES ($monthlyCost, $walletId, $payment_method_id)";
                    mysqli_query($db, $query_transaction);

                    $sender = "accounts@bjc55311.encs.concordia.ca";
                    $receiver = $row['email'];
                    $subject = "Confirmation of automatic payment made to " . $row['category_type'] . " account " . $row['email'];
                    $body = "Hello, this is a confirmation of your automatic payment of ". $monthlyCost . "$." . " Your account balance is now ". $newBalance . "$." . " Thank you.";

                    $query_sendEmail = "INSERT INTO Outbound_Email (sender, receiver, subject, body) VALUES
                                        ('$sender', '$receiver', '$subject', '$body')"; 

                    mysqli_query($db, $query_sendEmail);

                    if($newBalance <= 0){
                        mysqli_query($db,"UPDATE Employer SET enabled = 1 WHERE wallet_id = $walletId");
                        mysqli_query($db,"UPDATE Seeker SET enabled = 1 WHERE wallet_id = $walletId");
                        $_SESSION['enabled'] = 1;
                    }

                    $_SESSION['flash'] = "Automatic payments triggered";
                } else{
                    $_SESSION['err'] = mysqli_error($db);
                }
            }
        }
    } elseif(isset($_POST['monthlyCharge'])){
        $query_getWalletsMonthlyCost = "SELECT w.id, w.balance, j.monthly_cost FROM Wallet w 
        JOIN ((SELECT wallet_id, monthly_cost FROM Employer e JOIN Employer_Plan ep ON e.plan_name = ep.name) UNION
        (SELECT wallet_id, monthly_cost FROM Seeker s JOIN Seeker_Plan sp ON s.plan_name = sp.name)) j ON j.wallet_id = w.id";
        $result_getWalletsMonthlyCost = mysqli_query($db, $query_getWalletsMonthlyCost);

        while($row = mysqli_fetch_assoc($result_getWalletsMonthlyCost)){
            $walletId = $row['id'];
            $balance = $row['balance'];
            $monthlyCost = $row['monthly_cost'];
            $newBalance = $balance + $monthlyCost;
            $query_chargeAccounts = "UPDATE Wallet SET balance = $newBalance WHERE id = $walletId";

            if(mysqli_query($db, $query_chargeAccounts)){
                if($newBalance > $monthlyCost){
                    mysqli_query($db,"UPDATE Employer SET enabled = 0 WHERE wallet_id = $walletId");
                    mysqli_query($db,"UPDATE Seeker SET enabled = 0 WHERE wallet_id = $walletId");
                    $_SESSION['enabled'] = 0;
                }
                
                $_SESSION['flash'] = "Monthly charges triggered to all accounts";
            } else{
                $_SESSION['err'] = "Error";
            }
        }
    
    } elseif(isset($_POST['balanceEmail'])){
        $query_getWalletsOustandingBalance = "SELECT w.id, w.balance, j.monthly_cost, j.email, j.category_type FROM Wallet w 
        JOIN ((SELECT wallet_id, monthly_cost, email, category_type FROM Employer e JOIN Employer_Plan ep ON e.plan_name = ep.name) UNION
        (SELECT wallet_id, monthly_cost, email, category_type FROM Seeker s JOIN Seeker_Plan sp ON s.plan_name = sp.name)) j ON ((j.wallet_id = w.id) AND (w.balance > j.monthly_cost))";
        $result_getWalletsOustandingBalance = mysqli_query($db, $query_getWalletsOustandingBalance);

        while($row = mysqli_fetch_assoc($result_getWalletsOustandingBalance)){
            $sender = "accounts@bjc55311.encs.concordia.ca";
            $receiver = $row['email'];
            $subject = "Balance due for " . $row['category_type'] . " account " . $row['email'];
            $body = "Hello. You have a outstanding blance due of ". $row['balance'] ."$.". " Please login to your account to make a manual payment or setup automatic payments. Thank you.";

            $query_sendEmail = "INSERT INTO Outbound_Email (sender, receiver, subject, body) VALUES
                                ('$sender', '$receiver', '$subject', '$body')";
            if(mysqli_query($db, $query_sendEmail)){
                $_SESSION['flash'] = "Emails have been sent";
            } else{
                $_SESSION['err'] = "Error";
            } 
        }
    }       
}
header("location: ../admin_controls.php");
