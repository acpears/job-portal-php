<?php
require_once("../configs/sessions.php");
require_once("../configs/db_config.php");

if( !isset($_SESSION['use']) || $_SESSION['userType'] != 'employer'){ 
    header("Location: ../index.php"); 
}
// Check if employer account permits a job posting

// Current session id
$userId = $_SESSION['use'];

// Query to load the current employer from the database
$query_getUser = "SELECT * FROM Employer WHERE id = '$userId'";
$user = mysqli_fetch_assoc(mysqli_query($db, $query_getUser));
$_SESSION['enabled'] = $user['enabled'];
if($_SESSION['enabled'] == false){ 
    header("Location: ./employer_main.php"); 
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
    <?php include_once('./employer_navbar.php');?>
    <div class="container">
        <div class="row mt-4">
            <div class="col">
                <h1>Add Job Posting</h1>
            </div>
        </div>

        <div class="card ">
            <div class ="card-body">
          
        
        <form action="./employer_scripts/jobs_scripts.php" method="post">
            <div class="row">
                <div class="form-group col-4">
                    <div class="container">
                        <div class="row mb-2">
                            <div class="col">
                                <label for="jobTitle" class="mb-0">JOB TITLE</label>
                                <input type="text" class="form-control" name="title" id="jobTitle" required>
                            </div>
                        </div>
                        <div class="row ">
                            <div class="col-8">
                                <label for="category" class="mb-0"> CATEGORY </label>
                                <select class="custom-select mr-sm-2" name="category" required>
                                    <option value="" selected disabled>Choose...</option>

                                    <?php 
                                    while($row = mysqli_fetch_assoc($result_getJobCategories)) { ?>
                                        <option value="<?= $row['name']?>"><?= $row['name']?></option>
                                    <?php } ?>

                                </select>
                            </div>
                            <div class="col-4">
                                <label for="qty" class="mb-0">QTY</label>
                                <input type="number" min="1" max="99" class="form-control" name="qty" id="qty" required>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-group col-8">
                    <label for="description" class="mb-0">JOB DESCRIPTION</label>
                    <textarea class="form-control" id="description" name="description" rows="4" style="resize:none" maxlength="1000"></textarea>
                </div>
            </div>
            <div class="row">
                <div class="col text-right">
                    <button type="submit" class="btn btn-primary" >Add</button> 
                </div>
            </div>     
        </form>
            </div>
        </div>
    </div>
    
        <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>

    </body>
</html>


