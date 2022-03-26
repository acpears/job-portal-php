<?php
require_once("../configs/sessions.php");
require_once("../configs/db_config.php");

// Session checking
if (!isset($_SESSION['use']) || $_SESSION['userType'] != 'seeker') {
    header("Location: ../index.php");
}

$userId = $_SESSION['use'];

// Query to load the current seeker from the database
$query_getUser = "SELECT * FROM Seeker WHERE id = '$userId'";
$user = mysqli_fetch_assoc(mysqli_query($db, $query_getUser));
$_SESSION['enabled'] = $user['enabled'];
if ($_SESSION['enabled'] == false) {
    header("Location: ./seeker_main.php");
}

// Filtering logic and search
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $startDate = mysqli_real_escape_string($db, $_POST['startDate']);
    $endDate = mysqli_real_escape_string($db, $_POST['endDate']);
    $category = mysqli_real_escape_string($db, $_POST['category']);
    if (isset($_POST['category']) && empty($_POST['startDate'])) {

        $query_getAllJobs = "SELECT *, jp.id AS jobId FROM Job_Posting jp JOIN Employer e ON jp.employer_id = e.id 
                                WHERE category_name = '$category' 
                                ORDER BY jp.date_posted DESC";
    } else if (!isset($_POST['category']) && !empty($_POST['startDate'])) {
        $query_getAllJobs = "SELECT *, jp.id AS jobId FROM Job_Posting jp JOIN Employer e ON jp.employer_id = e.id 
                                WHERE jp.date_posted >= '$startDate'
                                AND jp.date_posted <= '$endDate'
                                ORDER BY jp.date_posted DESC";
    } else if (isset($_POST['category']) && !empty($_POST['startDate'])) {

        $query_getAllJobs = "SELECT *, jp.id AS jobId FROM Job_Posting jp JOIN Employer e ON jp.employer_id = e.id 
                                WHERE category_name = '$category' 
                                AND jp.date_posted >= '$startDate'
                                AND jp.date_posted <= '$endDate'
                                ORDER BY jp.date_posted DESC";
    } else if (isset($_POST['search'])) {
        $searchValue = mysqli_real_escape_string($db, $_POST['search']);
        $query_getAllJobs = "SELECT *, jp.id AS jobId FROM Job_Posting jp JOIN Employer e ON jp.employer_id = e.id 
                            WHERE CONCAT(title, '|', description, '|', category_name) LIKE '%$searchValue%' ";
    } else {
        $query_getAllJobs = "SELECT *, jp.id AS jobId FROM Job_Posting jp JOIN Employer e ON jp.employer_id = e.id ORDER BY jp.date_posted DESC";
    }
    $result_getAllJobs = mysqli_query($db, $query_getAllJobs);
} else {
    $query_getAllJobs = "SELECT *, jp.id AS jobId FROM Job_Posting jp JOIN Employer e ON jp.employer_id = e.id ORDER BY jp.date_posted DESC";
    $result_getAllJobs = mysqli_query($db, $query_getAllJobs);
}

$query_applicationCount = "SELECT COUNT(*) AS value FROM Job_Application WHERE seeker_id = $userId";
$applicationCount = mysqli_fetch_assoc(mysqli_query($db, $query_applicationCount));

$query_maxApplications = "SELECT application_qty AS value FROM Seeker s JOIN Seeker_Plan sp ON s.plan_name = sp.name WHERE s.id = $userId";
$maxApplications = mysqli_fetch_assoc(mysqli_query($db, $query_maxApplications));

$query_getJobCategories = "SELECT * FROM Job_Category";
$result_getJobCategories = mysqli_query($db, "SELECT * FROM Job_Category");

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
    <?php include_once('./seeker_navbar.php'); ?>
    <div class="container-fluid">
        <div class="container-fluid">
            <div class="row my-3">
                <div class="col">
                    <h1 class="mb-0">Job Postings</h1>
                </div>

                <div class="col text-right">
                    <?php if ($maxApplications['value'] === 0) { ?>
                        <div class="alert alert-danger mb-0" style="display:inline-block;">
                            <strong>WARNING!</strong> Current plan does not allow you to apply to jobs. Go to settings to upgrade your plan.
                        </div>
                    <?php } else if ($applicationCount['value'] >= $maxApplications['value'] && $maxApplications['value'] != null) { ?>
                        <div class="alert alert-danger mb-0" style="display:inline-block;">
                            <strong>WARNING!</strong> Maximum applications reached for current plan. Go to settings to upgrade your plan.
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
                            <form action="seeker_jobs.php" method="post" class="form-inline">
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
                            <form action="seeker_jobs.php" method="post" class="form-inline">
                                <button class="btn btn-primary ml-2" name="clear" value="1">Clear Filter</button>
                            </form>
                        </div>
                    </div>
                    <div class="col-4 text-right">
                        <div style="display:inline-block">
                            <div class="form-group form-inline mt-1">
                                <form action="seeker_jobs.php" method="post" class="form-inline">
                                    <input class="form-control" type="text" id="search" name="search" value=<?= $searchValue ?>>
                                    <button type="submit" class="btn btn-primary ml-2">Search</button>
                                </form>
                                <form action="seeker_jobs.php" method="post" class="form-inline">
                                    <button class="btn btn-primary ml-2" name="clear" value="1">Clear</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <thead class="thead-light">

                <tr>
                    <th style="width:10%;">Company Name</th>
                    <th style="width:10%;">Job Title</th>
                    <th style="width:10%;">Job Category</th>
                    <th style="width:45%;">Description</th>
                    <th style="width:10%;">Date Posted</th>
                    <th style="width:5%;">Positions Available</th>
                    <th style="width:15%;">Apply</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = mysqli_fetch_assoc($result_getAllJobs)) {
                    $jobId = $row['jobId'];
                    $query_jobApplied = "SELECT * FROM Job_Application WHERE seeker_id = $userId AND job_posting_id = $jobId";
                    $result_jobApplied = mysqli_query($db, $query_jobApplied);
                    $jobApplied = mysqli_fetch_assoc($result_jobApplied);
                ?>
                    <tr>
                        <th><?= $row['name'] ?></th>
                        <th><?= $row['title'] ?></th>
                        <th><?= $row['category_name'] ?></th>
                        <th><?= $row['description'] ?></th>
                        <th style="width: 125px;"><?= $row['date_posted'] ?></th>
                        <th><?= $row['max_fill_qty'] - $row['fill_qty'] ?></th>
                        <th>
                            <form action="./seeker_scripts/job_scripts.php" method="post">
                                <?php if ($applicationCount['value'] >= $maxApplications['value'] && $maxApplications['value'] != null) { ?>
                                    <?php if ($jobApplied) { ?>
                                        <button class="btn btn-warning btn-sm" disabled>Applied</button>
                                    <?php } else { ?>
                                        <button class="btn btn-secondary btn-sm" disabled>Disabled</button>
                                    <?php }
                                } else if ($row['max_fill_qty'] == $row['fill_qty']) { ?>
                                    <button class="btn btn-danger btn-sm" disabled>Filled</button>
                                <?php } else { ?>
                                    <?php if ($jobApplied) { ?>
                                        <button class="btn btn-warning btn-sm" disabled>Applied</button>
                                    <?php } else { ?>
                                        <button name="applied" value="<?= $row['jobId'] ?>" class="btn btn-success btn-sm">Apply</button>
                                <?php }
                                } ?>
                            </form>
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