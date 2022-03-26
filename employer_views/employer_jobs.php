<?php
require_once("../configs/sessions.php");
require_once("../configs/db_config.php");

if (!isset($_SESSION['use']) || $_SESSION['userType'] != 'employer') {
    header("Location: ../index.php");
}


// Current session id
$userId = $_SESSION['use'];

// Query to load the current employer from the database
$query_getUser = "SELECT * FROM Employer WHERE id = '$userId'";
$user = mysqli_fetch_assoc(mysqli_query($db, $query_getUser));
$_SESSION['enabled'] = $user['enabled'];
if ($_SESSION['enabled'] == false) {
    header("Location: ./employer_main.php");
}

$query_postingCount = "SELECT COUNT(*) AS value FROM Job_Posting WHERE employer_id = $userId";
$postingCount = mysqli_fetch_assoc(mysqli_query($db, $query_postingCount));

$query_maxPosting = "SELECT posting_qty AS value FROM Employer e
                        JOIN Employer_Plan ep ON e.plan_name = ep.name 
                        WHERE e.id = $userId";
$maxPosting = mysqli_fetch_assoc(mysqli_query($db, $query_maxPosting));

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $startDate = mysqli_real_escape_string($db, $_POST['startDate']);
    $endDate = mysqli_real_escape_string($db, $_POST['endDate']);
    $category = mysqli_real_escape_string($db, $_POST['category']);
    if (isset($_POST['category']) && empty($_POST['startDate'])) {

        $query_getAllJobs = "SELECT *, jp.id AS jobId FROM Job_Posting jp JOIN Employer e ON jp.employer_id = e.id 
                                WHERE e.id = $userId
                                AND category_name = '$category' 
                                ORDER BY jp.date_posted DESC";
    } else if (!isset($_POST['category']) && !empty($_POST['startDate'])) {
        $query_getAllJobs = "SELECT *, jp.id AS jobId FROM Job_Posting jp JOIN Employer e ON jp.employer_id = e.id 
                                WHERE e.id = $userId
                                AND jp.date_posted >= '$startDate'
                                AND jp.date_posted <= '$endDate'
                                ORDER BY jp.date_posted DESC";
    } else if (isset($_POST['category']) && !empty($_POST['startDate'])) {

        $query_getAllJobs = "SELECT *, jp.id AS jobId FROM Job_Posting jp JOIN Employer e ON jp.employer_id = e.id 
                                WHERE e.id = $userId
                                AND category_name = '$category' 
                                AND jp.date_posted >= '$startDate'
                                AND jp.date_posted <= '$endDate'
                                ORDER BY jp.date_posted DESC";
    } else if (isset($_POST['search'])) {
        $searchValue = mysqli_real_escape_string($db, $_POST['search']);
        $query_getAllJobs = "SELECT *, jp.id AS jobId FROM Job_Posting jp JOIN Employer e ON jp.employer_id = e.id 
                            WHERE e.id = $userId 
                            AND CONCAT(title, '|', description, '|', category_name) LIKE '%$searchValue%' 
                            ";
    } else {
        $query_getAllJobs = "SELECT *, jp.id AS jobId FROM Job_Posting jp JOIN Employer e ON jp.employer_id = e.id 
                            WHERE e.id = $userId 
                            ORDER BY jp.date_posted DESC";
    }
    $result_getAllJobs = mysqli_query($db, $query_getAllJobs);
} else {
    $query_getAllJobs = "SELECT *, jp.id AS jobId FROM Job_Posting jp JOIN Employer e ON jp.employer_id = e.id 
                        WHERE e.id = $userId 
                        ORDER BY jp.date_posted DESC";
    $result_getAllJobs = mysqli_query($db, $query_getAllJobs);
}


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
    <?php include_once('./employer_navbar.php'); ?>
    <div class="container-fluid">
        <div class="container-fluid">
            <div class="row my-3">
                <div class="col-4">
                    <h1 class="mb-0">My Job Postings</h1>
                </div>
                <div class="col-8 text-right">
                    <?php if ($maxPosting['value'] === null || $postingCount['value'] < $maxPosting['value']) { ?>
                        <a href="./employer_add_job.php" class="btn btn-success " role="button">Add New Job Posting</a>
                    <?php } else { ?>
                        <div class="alert alert-danger" style="display:inline-block;">
                            <strong>WARNING!</strong> Maximumg postings reached for current plan. Go to settings to upgrade your plan.
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>
        <hr>
        <table class="table table-bordered table-hover">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-8">
                        <div class="form-group form-inline mt-1 ">
                            <form action="employer_jobs.php" method="post" class="form-inline">
                                <label class="mr-sm-2" for="inlineFormCustomSelect"></label>
                                <select class="custom-select mr-sm-2" name="category">
                                    <option style="color:lightgray" selected disabled>Choose Category...</option>
                                    <?php
                                    while ($row = mysqli_fetch_assoc($result_getJobCategories)) {
                                        if ($_POST['category'] == $row['name']) { ?>
                                            <option value="<?= $row['name'] ?>" selected><?= $row['name'] ?></option>
                                        <?php } else { ?>
                                            <option value="<?= $row['name'] ?>"><?= $row['name'] ?></option>
                                    <?php }
                                    } ?>

                                </select>


                                <input class="form-control" type="date" id="startDate" name="startDate" value="<?= $_POST['startDate'] ?>" min="2018-01-01" max="<?= date('Y-m-d') ?>">
                                <label class="mr-sm-2 ml-2" for="start">to</label>

                                <input class="form-control" type="date" id="endDate" name="endDate" value="<?= $_POST['endDate'] ?>" min="2018-01-01" max="<?= date('Y-m-d') ?>">
                                <button type="submit" class="btn btn-primary ml-2">Apply Filter</button>

                            </form>
                            <form action="employer_jobs.php" method="post" class="form-inline">
                                <button class="btn btn-primary ml-2" name="clear" value="1">Clear Filter</button>
                            </form>
                        </div>
                    </div>
                    <div class="col-4 text-right">
                        <div style="display:inline-block">
                            <div class="form-group form-inline mt-1" >
                                <form action="employer_jobs.php" method="post" class="form-inline">
                                    <input class="form-control" type="text" id="search" name="search" value = <?= $searchValue?>>
                                    <button type="submit" class="btn btn-primary ml-2" >Search</button>
                                </form>
                                <form action="employer_jobs.php" method="post" class="form-inline">
                                    <button class="btn btn-primary ml-2" name="clear" value="1">Clear</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


            <thead class="thead-light">
                <tr>
                    <th style="width:10%;">Job Title</th>
                    <th style="width:10%;">Category</th>
                    <th style="width:45%;">Description</th>
                    <th style="width:10%;">Date Posted</th>
                    <th style="width:5%;">Positions Filled</th>
                    <th style="width:5%;">Max Positions</th>
                    <th style="width:15%;">Options</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = mysqli_fetch_assoc($result_getAllJobs)) {
                    $jobId = $row['jobId'];
                ?>
                    <tr>
                        <th><?= $row['title'] ?></th>
                        <th><?= $row['category_name'] ?></th>
                        <th>
                            <div><?= $row['description'] ?></div>
                        </th>
                        <th><?= $row['date_posted'] ?></th>
                        <th><?= $row['fill_qty'] ?></th>
                        <th><?= $row['max_fill_qty'] ?></th>
                        <th>
                            <div class="form-group form-inline">
                                <form action="./employer_edit_job.php" method="post" class="px-1">
                                    <button name="editJob" value="<?= $row['jobId'] ?>" class="btn btn-warning btn-sm">Edit</button>

                                </form>
                                <form action="./employer_scripts/jobs_scripts.php" method="post" class="px-1">

                                    <button name="deleteJob" value="<?= $row['jobId'] ?>" class="btn btn-danger btn-sm">Remove</button>
                                </form>
                            </div>

                        </th>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>

    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>

</body>

</html>