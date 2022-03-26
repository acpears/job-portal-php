<?php
require_once("../configs/sessions.php");
require_once("../configs/db_config.php");

if( !isset($_SESSION['use']) || $_SESSION['userType'] != 'admin'){ 
    header("Location: ../index.php"); 
}

$query_getAllJobs = "SELECT *, jp.id AS jobId FROM Job_Posting jp
                    JOIN Employer e ON jp.employer_id = e.id
                    ORDER BY e.name";
$result = mysqli_query($db, $query_getAllJobs);

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
                    <h1 class="mb-0">Job Postings</h1>
                </div>
            </div>
            <hr>
            <table class="table table-bordered table-hover">
                <thead class="thead-light">
                    <tr>
                        <th>Job ID</th>
                        <th>Company Name</th>
                        <th>Job Title</th>
                        <th>Job Category</th>
                        <th>Number Applicants</th>
                        <th>Max Positions</th>
                        <th>Filled</th>
        
                        <th>Date Posted</th>
                        <th>Details</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($row = mysqli_fetch_assoc($result) ){?>
                    
                        <th><?= $row['jobId'] ?></th>    
                        <th><?= $row['name'] ?></th>
                        <th><?= $row['title'] ?></th>
                        <th><?= $row['category_name'] ?></th>
                        <th class="text-center"><?= $row['candidate_qty'] ?></th>
                        <th class="text-center"><?= $row['max_fill_qty'] ?></th>
                        <th><?php if($row['max_fill_qty'] == $row['fill_qty']){ ?>
                            Filled
                            <?php }else{?>
                            Not Filled
                            <?php }?>
                        
                        </th>
                        <th><?= $row['date_posted'] ?></th>
                        <th>
                            <form action="./admin_details.php" method="post">
                                <button name="moreDetails" value="<?=$row['jobId']?>" class="btn btn-primary">More Details</button>
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