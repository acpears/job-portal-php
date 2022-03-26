<?php 
require_once("../../configs/sessions.php");
require_once("../../configs/db_config.php");

if($_SERVER["REQUEST_METHOD"] == "POST"){

    if(isset($_POST['disable'])){
        $id = mysqli_real_escape_string($db, $_POST['disable']);
        $query_disableSeeker = "UPDATE Seeker SET enabled = false WHERE id = $id";
        mysqli_query($db, $query_disableSeeker);

    } elseif(isset($_POST['enable'])){
        $id = mysqli_real_escape_string($db, $_POST['enable']);
        $query_enableSeeker = "UPDATE Seeker SET enabled = true WHERE id = $id";
        mysqli_query($db, $query_enableSeeker);
        
    } elseif(isset($_POST['delete'])){
        $id = mysqli_real_escape_string($db, $_POST['delete']);
        $query_deleteSeeker = "DELETE FROM Seeker WHERE id = '$id'";
        
        if(mysqli_query($db, $query_deleteSeeker)){
            $_SESSION['flash'] = "Employer $id deleted";
        }else{
            $_SESSION['err'] = "Error";
        }
        
    } elseif(isset($_POST['emailBalanceDue'])){
        $id = mysqli_real_escape_string($db, $_POST['emailBalanceDue']);
        $email = mysqli_real_escape_string($db, $_POST['email']);
        $balance = mysqli_real_escape_string($db, $_POST['balance']);
        $fname = mysqli_real_escape_string($db, $_POST['fname']);
        $lname = mysqli_real_escape_string($db, $_POST['lname']);

        $sender = "accounts@bjc55311jobportal.ca";
        $receiver = $email;
        $subject = "Balance due for job seeker account " . $email;
        $body = "Hello $fname $lname. You have a blance due of ". $balance ."$.". " Please login to your account to make a manual payment or setup automatic payment. Thank you.";

        $query_sendEmail = "INSERT INTO Outbound_Email (sender, receiver, subject, body) VALUES
                            ('$sender', '$receiver', '$subject', '$body')"; 

        if(mysqli_query($db, $query_sendEmail)){
            $_SESSION['flash'] = "Email has been sent";
        } else{
            $_SESSION['err'] = "Error";
        }
    }
}
header("location: ../admin_seekers.php");
?>