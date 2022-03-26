<?php 
require_once("../../configs/db_config.php");
require_once("../../configs/sessions.php");

$userId = $_SESSION['use'];


if($_SERVER["REQUEST_METHOD"] == "POST"){

    if(isset($_POST['title'])){
        $title = mysqli_real_escape_string($db, $_POST['title']);
        $category = mysqli_real_escape_string($db, $_POST['category']);
        $qty = mysqli_real_escape_string($db, $_POST['qty']);
        $description = mysqli_real_escape_string($db, $_POST['description']);
        $currentDate = date('Y-m-d');
        $query_addJob = "INSERT INTO Job_Posting (title, description, max_fill_qty, date_posted,category_name,employer_id) VALUES 
                        ('$title', '$description', $qty, '$currentDate', '$category', $userId)";
        mysqli_query($db, $query_addJob);
    }

    if(isset($_POST['newTitle'])){
        $jobId = mysqli_real_escape_string($db, $_POST['id']);
        $title = mysqli_real_escape_string($db, $_POST['newTitle']);
        $category = mysqli_real_escape_string($db, $_POST['category']);
        $qty = mysqli_real_escape_string($db, $_POST['qty']);
        $description = mysqli_real_escape_string($db, $_POST['description']);
        $query_updateJob = "UPDATE Job_Posting SET title = '$title', description = '$description', max_fill_qty = $qty, category_name = '$category' 
                            WHERE id = $jobId";

        mysqli_query($db, $query_updateJob);

    }

    if(isset($_POST['deleteJob'])){
        $jobId = mysqli_real_escape_string($db, $_POST['deleteJob']);
        $query_deleteJob = "DELETE FROM Job_Posting WHERE id = $jobId";

        mysqli_query($db, $query_deleteJob);

    }


}
header("location: ../employer_jobs.php");
?>