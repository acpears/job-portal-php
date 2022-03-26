<?php 
require_once("../../configs/db_config.php");
require_once("../../configs/sessions.php");

$userId = $_SESSION['use'];

$query_getUser = "SELECT * FROM Seeker WHERE id = '$userId'";
$user = mysqli_fetch_assoc(mysqli_query($db, $query_getUser));


if($_SERVER["REQUEST_METHOD"] == "POST"){

    if(isset($_POST['applied'])){
        $jobId = mysqli_real_escape_string($db, $_POST['applied']);
        $currentDate = date('Y-m-d');
        $query_applyJob = "INSERT INTO Job_Application (seeker_id,job_posting_id,date_applied,status) VALUES 
            ($userId, $jobId, '$currentDate', 1)";
        $result_applyJob = mysqli_query($db, $query_applyJob);

    }

}
header("location: ../seeker_jobs.php");
?>