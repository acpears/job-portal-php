<?php 
require_once("../../configs/db_config.php");
require_once("../../configs/sessions.php");

$userId = $_SESSION['use'];


if($_SERVER["REQUEST_METHOD"] == "POST"){

    if(isset($_POST['acceptOffer'])){
        $jobId = mysqli_real_escape_string($db, $_POST['acceptOffer']);
        $seekerId = mysqli_real_escape_string($db, $_POST['seekerId']);
        $query_updateApplicationStatus = "UPDATE Job_Application SET status = 'offer_pending' WHERE seeker_id = $seekerId AND job_posting_id = $jobId";

        mysqli_query($db, $query_updateApplicationStatus);

    }

    if(isset($_POST['rejectOffer'])){
        $jobId = mysqli_real_escape_string($db, $_POST['rejectOffer']);
        $seekerId = mysqli_real_escape_string($db, $_POST['seekerId']);
        $query_updateApplicationStatus = "UPDATE Job_Application SET status = 'employer_denied' WHERE seeker_id = $seekerId AND job_posting_id = $jobId";

        mysqli_query($db, $query_updateApplicationStatus);

    }


}
header("location: ../employer_applications.php");
?>