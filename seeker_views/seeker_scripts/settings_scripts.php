<?php 
require_once("../../configs/db_config.php");
require_once("../../configs/sessions.php");

$userId = $_SESSION['use'];
$walletId = $_SESSION['walletId'];

$query_getUser = "SELECT * FROM Seeker WHERE id = '$userId'";
$user = mysqli_fetch_assoc(mysqli_query($db, $query_getUser));

function sendManualTransactionEmail($email,$amount,$balance){
    global $db;
    $sender = "accounts@bjc55311.encs.concordia.ca";
    $receiver = $email;
    $subject = "Confirmation of manual payment made to job seeker account " . $email;
    $body = "Hello, this is a confirmation of your manual payment of ". $amount . "$." . " Your account balance is now ". $balance . "$." . " Thank you.";

    $query_sendEmail = "INSERT INTO Outbound_Email (sender, receiver, subject, body) VALUES
                        ('$sender', '$receiver', '$subject', '$body')"; 

    mysqli_query($db, $query_sendEmail);
}

// POST VARIABLES
if($_SERVER["REQUEST_METHOD"] == "POST"){

    if(isset($_POST['fname'])){
        $id = $_SESSION['use'];
        $fname = mysqli_real_escape_string($db, $_POST['fname']);
        $lname = mysqli_real_escape_string($db, $_POST['lname']);
        $email = mysqli_real_escape_string($db, $_POST['email']);

        $query_updateDetails = "UPDATE Seeker SET first_name = '$fname', last_name = '$lname', email = '$email' WHERE id = $id";
        
        if(mysqli_query($db, $query_updateDetails)){
            $_SESSION['flash'] = "Changes have been saved!!!";
        } else {
            if(mysqli_errno($db) == 1062){
                $_SESSION['err'] = "Error: Email is already being used";
                header("location: ../seeker_settings.php");
                exit();  
            }
            $_SESSION['err'] = "Error! ". mysqli_errno($db);
        }
    }

    if(isset($_POST['oldPassword'])){
        $oldPassword = mysqli_real_escape_string($db, $_POST['oldPassword']);
        $password = mysqli_real_escape_string($db, $_POST['password']);
        $confirmPassword = mysqli_real_escape_string($db, $_POST['confirmPassword']);

        if(password_verify($oldPassword, $user['password'])){
            if($password != $confirmPassword){
                $_SESSION['err'] = "Passwords do not match";
            } else{
                $passwordEncrypted = password_hash($password,PASSWORD_DEFAULT);

                $query_updatePassword = "UPDATE Seeker SET password = '$passwordEncrypted' WHERE id = $userId";
                if(mysqli_query($db, $query_updatePassword)){
                    $_SESSION['flash'] = "Password has been changed successfuly";
                } else {
                    $_SESSION['err'] = "Database Error! Please contact our help line";
                }
            } 
        } else {
            $_SESSION['err'] = "Incorrect password";
        }
    } 

    if(isset($_POST['remove'])){
        $id = mysqli_real_escape_string($db, $_POST['remove']);
        $query_deletePayment = "DELETE FROM Payment_Method WHERE id = '$id'";
        if(mysqli_query($db, $query_deletePayment)){
            $_SESSION['flash'] = "Payment method removed";
        }else if(mysqli_errno($db) == 1644){
            $_SESSION['err'] = "Cant delete payment method. Please chose another default payment method or remove automatic payments";
        }else{
            $_SESSION['err'] = mysqli_error($db);
        }
    }

    if(isset($_POST['cardNumber'])){
        $cardNumber = mysqli_real_escape_string($db, $_POST['cardNumber']);
        $cardName = mysqli_real_escape_string($db, $_POST['cardName']);
        $year = mysqli_real_escape_string($db, $_POST['year']);
        $month = mysqli_real_escape_string($db, $_POST['month']);
        $cvc = mysqli_real_escape_string($db, $_POST['cvc']);

        // Create date string from year month
        $expirationDate = date($year."-".$month."-1");

        // Query to insert new card
        $query_insertCreditCard = "INSERT INTO Credit_Card (card_number, expiry_date, owner_name) VALUES ('$cardNumber', '$expirationDate','$cardName')";
        mysqli_query($db, $query_insertCreditCard);

        // Query to register new payment method to wallet
        $query_paymentId = "SELECT id FROM Credit_Card WHERE card_number = '$cardNumber'";
        $paymentID = mysqli_fetch_assoc(mysqli_query($db, $query_paymentId))['id'];
        
        $query_registerPaymentMethod = "INSERT INTO Registered_Payment_Method (payment_method_id, wallet_id) VALUES ('$paymentID','$walletId')";
        if(mysqli_query($db, $query_registerPaymentMethod)){
            $_SESSION['flash'] = "Payment method added";
        }else{
            $_SESSION['err'] = "Database Error! Please contact our help line";
        }
    }

    if(isset($_POST['accountNumber'])){
        $accountNumber = mysqli_real_escape_string($db, $_POST['accountNumber']);
        $accountName = mysqli_real_escape_string($db, $_POST['accountName']);
        $branch = mysqli_real_escape_string($db, $_POST['branch']);
        $bankName = mysqli_real_escape_string($db, $_POST['bankName']);

        // Query to insert new bank account
        $query_insertBankAccount = "INSERT INTO Bank_Account (account_number, owner_name) VALUES ('$accountNumber','$accountName')";
        mysqli_query($db, $query_insertBankAccount);

        // Query to register new payment method to seeker wallet
        $query_paymentId = "SELECT id FROM Bank_Account WHERE account_number = '$accountNumber'";
        $paymentID = mysqli_fetch_assoc(mysqli_query($db, $query_paymentId))['id'];
    
        $query_registerPaymentMethod = "INSERT INTO Registered_Payment_Method (payment_method_id, wallet_id) VALUES ('$paymentID','$walletId')";
        if(mysqli_query($db, $query_registerPaymentMethod)){
            $_SESSION['flash'] = "Payment method added";
        }else{
            $_SESSION['err'] = "Database Error! Please contact our help line";
        }
    }
    
    if(isset($_POST['defaultPayment'])){
        $id = mysqli_real_escape_string($db, $_POST['defaultPayment']);
        $query_makeDefault = "UPDATE Wallet SET default_payment_id = $id WHERE id = $walletId";

        if(mysqli_query($db, $query_makeDefault)){
            $_SESSION['flash'] = "Default payment method updated";
        }else{
            $_SESSION['err'] = "Database Error! Please contact our help line";
        }
        
    }

    if(isset($_POST['plans'])){
        $plan = mysqli_real_escape_string($db, $_POST['plans']);
        
        // Query to update seeker plan
        $query_changePlan= "UPDATE Seeker SET plan_name = '$plan' WHERE id = $userId";

        if(mysqli_query($db, $query_changePlan)){
            $_SESSION['flash'] = "Account plan update";
        }else{
            $_SESSION['err'] = "Database Error! Please contact our help line";
        }
    }

    if(isset($_POST['makePayment'])){
        $id = mysqli_real_escape_string($db, $_POST['makePayment']);
        $amount = mysqli_real_escape_string($db, $_POST['amount']);

        $query_wallet = "SELECT default_payment_id, balance FROM Wallet WHERE id = $walletId";
        $wallet = mysqli_fetch_assoc(mysqli_query($db, $query_wallet));
        $balance = $wallet['balance'];
        $balance = $balance - $amount;

        $query_makePayment = "UPDATE Wallet SET balance = $balance WHERE id = $walletId";

        if($wallet['default_payment_id']){
            if(mysqli_query($db, $query_makePayment)){
                
                // insert transaction

                $payment_method_id = $wallet['default_payment_id'];
                $query_transaction = "INSERT INTO Transaction (amount, wallet_id, payment_method_id) VALUES ($amount, $walletId, $payment_method_id)";
                mysqli_query($db, $query_transaction);

                sendManualTransactionEmail($user['email'],$amount,$balance);

                // enable account if balance is less then monthly cost
                if($balance <= 0){
                    mysqli_query($db,"UPDATE Seeker SET enabled = 1 WHERE id = $userId");
                    $_SESSION['enabled'] = 1;
                }

                $_SESSION['flash'] = "Payment sucessfull";
            }else{
                $_SESSION['err'] = "Payment error";
            }
        } else{
            $_SESSION['err'] = "Please selecte a payment method before making a payment";
        }
    }

    if(isset($_POST['automaticYes'])){
        $id = mysqli_real_escape_string($db, $_POST['automatic']);
        $query_automatic = "UPDATE Wallet SET automatic = 1 WHERE id = $walletId";

        if(mysqli_query($db, $query_automatic)){
            $_SESSION['flash'] = "Automatic Payment Enabled";
        }else if(mysqli_errno($db) == 1644){
            $_SESSION['err'] = "Must have selected method of payment for automatic payments ";
        }else{
            $_SESSION['err'] = mysqli_error($db);
        }
    }

    if(isset($_POST['automaticNo'])){
        $id = mysqli_real_escape_string($db, $_POST['automatic']);
        $query_automatic = "UPDATE Wallet SET automatic = 0 WHERE id = $walletId";

        if(mysqli_query($db, $query_automatic)){
            $_SESSION['flash'] = "Automatic Payment Disabled";
        }else{
            $_SESSION['err'] = mysqli_error($db);
        }
    }

    if(isset($_POST['delete'])){
        $id = mysqli_real_escape_string($db, $_POST['delete']);
        $query_deleteUser = "DELETE FROM Seeker WHERE id = $id";

        if(mysqli_query($db,$query_deleteUser)){
            header("location: ../../logout.php");
            exit();
        }
    }  
}
header("location: ../seeker_settings.php");
?>