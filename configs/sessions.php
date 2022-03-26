<?php 
    require_once("db_config.php");
    session_start();
    if(isset($_SESSION['use'])){
        if($_SESSION['userType'] == 'seeker'){
            $userId = $_SESSION['use'];
            $query_enabled = "SELECT enabled FROM Seeker WHERE id = $userId";
            $enabled = mysqli_query($db, $query_enabled);
            $_SESSION['enabled'] = $enabled;
        } elseif($_SESSION['userType'] == 'employer'){
            $userId = $_SESSION['use'];
            $query_enabled = "SELECT enabled FROM Employer WHERE id = $userId";
            $enabled = mysqli_query($db, $query_enabled);
            $_SESSION['enabled'] = $enabled;
           
        } 
    }
?>