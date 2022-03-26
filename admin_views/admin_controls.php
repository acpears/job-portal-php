<?php
require_once("../configs/sessions.php");
require_once("../configs/db_config.php");

if (!isset($_SESSION['use']) || $_SESSION['userType'] != 'admin') {
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
    <?php include_once('./admin_navbar.php') ?>
    <div class="container">
        <div class="container-fluid">
            <div class="row my-3">
                <div class="col-4">
                    <h1 class="mb-0">Admin Controls</h1>
                </div>
                <div class="col-8 text-right">
                    <?php if (!empty($_SESSION['err'])) : ?>
                        <div class="alert alert-danger my-0" role="alert" style="display:inline-block;">
                            <?= $_SESSION['err'] ?>
                        </div>
                    <?php $_SESSION['err'] = "";
                    endif ?>
                    <?php if (!empty($_SESSION['flash'])) : ?>
                        <div class="alert alert-success my-0" role="alert" style="display:inline-block;">
                            <?= $_SESSION['flash'] ?>
                        </div>
                    <?php $_SESSION['flash'] = "";
                    endif ?>
                </div>
            </div>
        </div>
        <hr>
        <form action="./admin_scripts/control_scripts.php" method="post">
            <div class="list-group form-group">
                <button type="submit" class="list-group-item list-group-item-action" name="automaticPayment">Trigger automatic payment</button>
                <button type="submit" class="list-group-item list-group-item-action" name="monthlyCharge">Trigger plan monthly charge</button>
                <button type="submit" class="list-group-item list-group-item-action" name="balanceEmail">Trigger oustanding balance email</button>
            </div>
        </form>

    </div>

    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>

</body>

</html>