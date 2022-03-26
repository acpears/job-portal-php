<?php
require_once("../configs/sessions.php");
require_once("../configs/db_config.php");

if( !isset($_SESSION['use']) || $_SESSION['userType'] != 'employer'){ 
    header("Location: ../index.php"); 
}

// Current session id
$userId = $_SESSION['use'];

// Query to load the current employer from the database
$query_getUser = "SELECT * FROM Employer WHERE id = '$userId'";
$user = mysqli_fetch_assoc(mysqli_query($db, $query_getUser));
$_SESSION['enabled'] = $user['enabled'];
if($_SESSION['enabled'] == false){ 
    header("Location: ./employer_main.php"); 
}

$query_getAllJobs = "SELECT *, jp.id AS jobId FROM Job_Posting jp JOIN Employer e ON jp.employer_id = e.id WHERE e.id = $userId ORDER BY e.name";
$result_getAllJobs = mysqli_query($db, $query_getAllJobs);

$query_getJobCategories = "SELECT * FROM Job_Category";
$result_getJobCategories = mysqli_query($db, $query_getJobCategories);

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
        <link rel="stylesheet" href="style.css">
    </head>
    <body>
    <?php include_once('./employer_navbar.php');?>
    <div class="container-fluid">
        <div class="container-fluid">
            <div class="row mt-3">
                <div class="col">
                    <h1>Job Applicants</h1>
                </div>
            </div>
        </div>

        <hr>

        <?php if (mysqli_num_rows($result_getAllJobs) == 0) {?>
            <div class="container-fluid">
                <h4>YOU CURRENTLY HAVE NO JOB POSTINGS</h4>
            </div>
            
        <?php } ?>

        <ul class="list-group">
            <?php while($row = mysqli_fetch_assoc($result_getAllJobs) ){
                $jobId = $row['jobId'];
                $query_getApplicationsCount = "SELECT COUNT(*) as value FROM Job_Application ja
                                                JOIN Seeker s ON ja.seeker_id = s.id 
                                                WHERE ja.job_posting_id = $jobId";
                $result_getApplicationsCount = mysqli_query($db,$query_getApplicationsCount);
                $applicationsCount = mysqli_fetch_assoc($result_getApplicationsCount);
                $query_getApplications = "SELECT * FROM Job_Application ja JOIN Seeker s ON ja.seeker_id = s.id WHERE ja.job_posting_id = $jobId";
                $result_getApplications = mysqli_query($db,$query_getApplications);

            ?>
            <li class="list-group-item  mb-4 bg-light">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-2">
                            <div class="row">
                                <p class="mb-2"><strong>Job Title:</strong>  <?= $row['title']?></p>
                            </div>
                            <div class="row">
                                <p class="mb-2"><strong>Date Posted:</strong>  <?= $row['date_posted']?></p>
                            </div>

                            <div class="row">
                                <p class="mb-2"><strong>Number of Applicants: </strong> <?= $applicationsCount['value']?> </p>
                            </div>
                            <div class="row">
                                <p class="mb-0"><strong>Positions filled: </strong> <?= $row['fill_qty']?> / <?= $row['max_fill_qty']?></p>
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
                                        <th style="width:20%;color:#aaaaaa">Options</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    
                                    while($row2 = mysqli_fetch_assoc($result_getApplications) ){ 
                                        if($row2['status'] === 'accepted'){ ?>
                                                <tr class="table-success">
                                            <?php } else if($row2['status'] === 'seeker_denied') { ?>
                                                <tr class="table-danger">   
                                            <?php } else { ?>
                                                <tr class="table">   
                                            <?php } ?>

                                            <th><?= $row2['first_name'] ?></th>
                                            <th><?= $row2['last_name'] ?></th>
                                            <th><?= $row2['date_applied'] ?></th>
                                            

                                            
                                                <?php if($row2['status'] === 'applied'){ ?>
                                                <th>Applied</th>
                                                <th> 
                                                    <div class="form-group form-inline mb-0">
                                                        <form action="./employer_scripts/applications_scripts.php" method="post" class="px-1">
                                                            <input type="hidden" name="seekerId" value="<?=$row2['id']?>"/>
                                                            <button name="acceptOffer" value="<?=$jobId?>" class="btn btn-success btn-sm">Accept</button>
                                                            <button name="rejectOffer" value="<?=$jobId?>" class="btn btn-danger btn-sm">Reject</button>
                                                        </form>  
                                                    </div>
                                                </th>
                                                <?php } elseif($row2['status'] === 'employer_denied'){ ?>
                                                <th>Rejected</th>
                                                <th></th>
                                                <?php } elseif($row2['status'] === 'seeker_denied'){ ?>
                                                <th>Offer Rejected</th>
                                                <th></th>
                                                <?php } elseif($row2['status'] === 'offer_pending'){ ?>
                                                <th>Offer Sent</th>
                                                <th></th>
                                                <?php } elseif($row2['status'] === 'accepted'){ ?>
                                                <th>Accepted</th>
                                                <th></th>
                                                <?php }?>   
                                                </div>
                                        
                                            </th>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                            <?php } else {?>
                                <h4>THIS POSTING CURRENTLY HAS NO APPLICANTS</h4>
                            <?php }?>
                        </div>
                    </div> 
                </div>
            </li>     
            <?php } ?>
        </ul>

    </div>
    
        <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>

    </body>
</html>


