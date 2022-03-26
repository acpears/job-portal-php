<?php
require_once("../configs/sessions.php");
require_once("../configs/db_config.php");

if(!isset($_SESSION['use']) || $_SESSION['userType'] != 'admin'){ 
    header("Location: ../index.php"); 
}

if($_SERVER["REQUEST_METHOD"] == "POST"){
    $jobId = mysqli_real_escape_string($db, $_POST['moreDetails']);
    $query_getJob = "SELECT *, jp.id AS postingId, e.id AS employerId FROM Job_Posting jp
                        JOIN Employer e ON e.id = jp.employer_id
                        WHERE jp.id = $jobId";
    $job = mysqli_fetch_assoc(mysqli_query($db, $query_getJob));

    $query_getApplicationsCount = "SELECT COUNT(*) as value FROM Job_Application ja
                                                JOIN Seeker s ON ja.seeker_id = s.id 
                                                WHERE ja.job_posting_id = $jobId";
    $result_getApplicationsCount = mysqli_query($db,$query_getApplicationsCount);
    $applicationsCount = mysqli_fetch_assoc($result_getApplicationsCount);
    $query_getApplications = "SELECT * FROM Job_Application ja JOIN Seeker s ON ja.seeker_id = s.id WHERE ja.job_posting_id = $jobId";
    $result_getApplications = mysqli_query($db,$query_getApplications);
 

}


?>
<!DOCTYPE html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]>      <html class="no-js"> <!--<![endif]-->
<html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title></title>
        <meta name="description" content="">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
        <link rel="stylesheet" href="../style.css">
    </head>
    <body>
    <?php include_once('./admin_navbar.php')?>
        <div class="container-fluid">
            <div class="row my-3">
                <div class="col">
                    <h1 class="mb-0">Job Details - ID: <?= $jobId?></h1>
                </div>
            </div>
            <hr>
            <div class="row my-3">
                <div class="col-4">
                    <div class="container-fluid">
                        <div class="row">
                            <h4>Job Title: <?=$job['title']?></h4>
                        </div>
                        <div class="row">
                            <h4>Company Name: <?=$job['name']?> </h4>
                        </div>
                        <div class="row">
                            <h4>Category: <?=$job['category_name']?></h4>
                        </div>
                        <div class="row">
                            <h4>Posted Date: <?=$job['date_posted']?> </h4>
                        </div>
                    </div>
                </div>
                <div class="col-8">
                    <div class="container-fluid">
                        <div class="row">
                            <h4>Job Description: </h4>
                            <p><?=$job['description']?></p>
                        </div>
                        
                    </div>
                </div>
            </div>
            <hr>
            <li class="list-group-item  mb-4">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-2">
                            <div class="row">
                                <p class="mb-2"><strong>Number of Applicants: </strong> <?= $applicationsCount['value']?> </p>
                            </div>
                            <div class="row">
                                <p class="mb-0"><strong>Positions filled: </strong> <?= $job['fill_qty']?> / <?= $job['max_fill_qty']?></p>
                            </div>
                        </div>          
                        <div class="col">
                            <?php if(mysqli_num_rows($result_getApplications) > 0) {?>
                        
                            <table class="table table-hover table-sm align-middle mb-0">
                                <thead class="">
                                    <tr>
                                        <th style="width:20%; color:#aaaaaa">First Name</th>
                                        <th style="width:20%;color:#aaaaaa">Last Name </th>
                                        <th style="width:20%;color:#aaaaaa">Date Applied</th>
                                        <th style="width:20%;color:#aaaaaa">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    
                                    while($row = mysqli_fetch_assoc($result_getApplications) ){ 
                                        if($row['status'] === 'accepted'){ ?>
                                                <tr class="table-success">
                                            <?php } else if($row['status'] === 'seeker_denied') { ?>
                                                <tr class="table-danger">   
                                            <?php } else { ?>
                                                <tr class="table">   
                                            <?php } ?>

                                            <th><?= $row['first_name'] ?></th>
                                            <th><?= $row['last_name'] ?></th>
                                            <th><?= $row['date_applied'] ?></th>
                                            <?php if($row['status'] === 'applied'){ ?>
                                            <th>Applied</th>
                                            
                                            <?php } elseif($row['status'] === 'employer_denied'){ ?>
                                            <th>Employer Rejected</th>
                                            
                                            <?php } elseif($row['status'] === 'seeker_denied'){ ?>
                                            <th>Offer Rejected</th>
                                            
                                            <?php } elseif($row['status'] === 'offer_pending'){ ?>
                                            <th>Offer Sent</th>
                                            
                                            <?php } elseif($row['status'] === 'accepted'){ ?>
                                            <th>Hired</th>
                                            
                                            <?php }?>   
                                            </div>
                                            </th>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                            <?php } else {?>
                                <h4>CURRENTLY NO APPLICATIONS</h4>
                            <?php }?>
                        </div>
                    </div> 
                </div>
            </li> 
        </div>
        <!-- <ul class="list-group">
            <?php foreach($htmlUserArray as $item) {?>
                <li class="list-group-item"><?= $item ?></li>
            <?php } ?>
        </ul> -->

        <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>

    </body>
</html>