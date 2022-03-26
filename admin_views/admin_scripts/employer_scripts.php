<?php 
require_once("../../configs/sessions.php");
require_once("../../configs/db_config.php");

if($_SERVER["REQUEST_METHOD"] == "POST"){

    if(isset($_POST['disable'])){
        $id = mysqli_real_escape_string($db, $_POST['disable']);
        $query_disableEmployer = "UPDATE Employer SET enabled = false WHERE id = $id";
        mysqli_query($db, $query_disableEmployer);

    } elseif(isset($_POST['enable'])){
        $id = mysqli_real_escape_string($db, $_POST['enable']);
        $query_enableEmployer = "UPDATE Employer SET enabled = true WHERE id = $id";
        mysqli_query($db, $query_enableEmployer);
        
    } elseif(isset($_POST['delete'])){
        $id = mysqli_real_escape_string($db, $_POST['delete']);
        $query_deleteEmployer = "DELETE FROM Employer WHERE id = '$id'";
        if(mysqli_query($db, $query_deleteEmployer)){
            $_SESSION['flash'] = "Employer $id deleted";
        }else{
            $_SESSION['err'] = "Error";
        }


    }  elseif(isset($_POST['emailBalanceDue'])){
        $id = mysqli_real_escape_string($db, $_POST['emailBalanceDue']);
        $email = mysqli_real_escape_string($db, $_POST['email']);
        $balance = mysqli_real_escape_string($db, $_POST['balance']);
        $cname = mysqli_real_escape_string($db, $_POST['cname']);

        $sender = "accounts@bjc55311jobportal.ca";
        $receiver = $email;
        $subject = "Balance due for employer account " . $email;
        $body = "Hello $cname. You have a blance due of ". $balance ."$.". " Please login to your account to make a manual payment or setup automatic payment. Thank you.";

        $query_sendEmail = "INSERT INTO Outbound_Email (sender, receiver, subject, body) VALUES
                            ('$sender', '$receiver', '$subject', '$body')"; 

        if(mysqli_query($db, $query_sendEmail)){
            $_SESSION['flash'] = "Email has been sent";
        } else{
            $_SESSION['err'] = "Error";
        }
    }
}
header("location: ../admin_employers.php");
?>