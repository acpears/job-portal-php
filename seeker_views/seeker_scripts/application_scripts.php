<?php 
require_once("../../configs/db_config.php");
require_once("../../configs/sessions.php");

$userId = $_SESSION['use'];

$query_getUser = "SELECT * FROM Seeker WHERE id = '$userId'";
$user = mysqli_fetch_assoc(mysqli_query($db, $query_getUser));


if($_SERVER["REQUEST_METHOD"] == "POST"){

    if(isset($_POST['cancel'])){
        $jobId = mysqli_real_escape_string($db, $_POST['cancel']);
        $query_removeApplication = "DELETE FROM Job_Application WHERE seeker_id = $userId AND job_posting_id = $jobId";
        mysqli_query($db, $query_removeApplication);
    }

    if(isset($_POST['accept'])){
        $jobId = mysqli_real_escape_string($db, $_POST['accept']);
        $query_acceptApplication = "UPDATE Job_Application SET status = 'accepted' WHERE seeker_id = $userId AND job_posting_id = $jobId";
        mysqli_query($db, $query_acceptApplication);
        // Remove remaining job applications when job is filled
        $query_checkJobFilled = "SELECT max_fill_qty, fill_qty FROM Job_Posting WHERE id = $jobId";
        $checkJobFilled = mysqli_fetch_assoc(mysqli_query($db, $query_checkJobFilled));
        if($checkJobFilled['max_fill_qty'] == $checkJobFilled['fill_qty']){
            $query_removeNonAcceptedApplications = "DELETE FROM Job_Application WHERE job_posting_id = $jobId AND status != 'accepted'";
            mysqli_query($db, $query_removeNonAcceptedApplications);
        }
        

    }

    if(isset($_POST['deny'])){
        $jobId = mysqli_real_escape_string($db, $_POST['deny']);
        $query_denyApplication = "UPDATE Job_Application SET status = 'seeker_denied' WHERE seeker_id = $userId AND job_posting_id = $jobId";
        mysqli_query($db, $query_denyApplication);
    }
}
header("location: ../seeker_applications.php");
?>