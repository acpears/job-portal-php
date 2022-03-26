<?php
require_once("../configs/sessions.php");
require_once("../configs/db_config.php");

if (!isset($_SESSION['use']) || $_SESSION['userType'] != 'admin') {
    header("Location: ../index.php");
}

$query_getAllUserInfo = "SELECT *, s.id AS userId FROM Seeker s JOIN (SELECT id, balance FROM Wallet) w ON s.wallet_id = w.id";
$result_getAllUserInfo = mysqli_query($db, $query_getAllUserInfo);

$outstandingBalances = array(); // store users that have balances

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
    <div class="container-fluid">
    <div class="container-fluid">
            <div class="row my-3">
                <div class="col-4">
                    <h1 class="mb-0">Registered Job Seekers</h1>
                </div>
                <div class="col-8 text-right">
                    <?php if (!empty($_SESSION['err'])) : ?>
                        <div class="alert alert-danger my-0" role="alert" style="display:inline-block;">
                            <?= $_SESSION['err'] ?>
                        </div>
                    <?php $_SESSION['err'] = ""; endif ?>
                    <?php if (!empty($_SESSION['flash'])) : ?>
                        <div class="alert alert-success my-0" role="alert" style="display:inline-block;">
                            <?= $_SESSION['flash'] ?>
                        </div>
                    <?php $_SESSION['flash'] = ""; endif ?>
                </div>
            </div>
        </div>
        <hr>
        <table class="table table-bordered table-hover">
            <thead class="thead-light">
                <tr>
                    <th style="width: 50px;">ID</th>
                    <th style="width: 30%;">First Name</th>
                    <th style="width: 30%">Last Name</th>
                    <th style="width: 30%;">Email</th>
                    <th style="width: 10%;">Plan</th>
                    <th style="width: 100px;">Balance</th>
                    <th style="width: 100px;">Applications</th>
                    <th style="width: 100px;">Status</th>
                    <th style="width: 100px;">Delete</th>

                </tr>
            </thead>
            <tbody>
                <?php while ($row = mysqli_fetch_assoc($result_getAllUserInfo)) {
                    if($row['balance'] >= 0.01){
                        array_push($outstandingBalances, $row);
                    }
                    $userId = $row['userId'];
                    $query_numberOfApplications = "SELECT COUNT(*) AS value FROM Job_Application WHERE seeker_id = $userId";
                    $numberOfApplications = mysqli_fetch_assoc(mysqli_query($db, $query_numberOfApplications));
                ?>
                    <tr>
                        <th class="text-center"><?= $row['id'] ?></th>
                        <th><?= $row['first_name'] ?></th>
                        <th><?= $row['last_name'] ?></th>
                        <th><?= $row['email'] ?></th>
                        <th><?= $row['plan_name'] ?></th>
                        <th class="text-center"><?= round($row['balance'], 2) ?>$</th>
                        <th class="text-center"><?= $numberOfApplications['value'] ?></th>
                        <th>
                            <form action="./admin_scripts/seeker_scripts.php" method="post">
                                <?php if ($row['enabled'] == true) { ?>
                                    <button name="disable" value="<?= $row['userId'] ?>" class="btn btn-success">ENABLED</button>
                                <?php } else { ?>
                                    <button name="enable" value="<?= $row['userId'] ?>" class="btn btn-danger">DISABLED</button>
                                <?php } ?>
                            </form>
                        </th>
                        <th>
                            <form action="./admin_scripts/seeker_scripts.php" method="post">
                                <button name="delete" value="<?= $row['userId'] ?>" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this account');">DELETE</button>
                            </form>
                        </th>
                    </tr>
                <?php } ?>
            </tbody>
        </table>

    </div>
    <hr>
    <div class="container-fluid">
        <div class="container-fluid">
            <div class="row my-3">
                <div class="col-4">
                    <h1 class="mb-0">Outstanding Balances</h1>
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
        <table class="table table-bordered table-hover">
            <thead class="thead-light">
                <tr>
                    <th style="width: 50px;">ID</th>
                    <th style="width: 20%;">First Name</th>
                    <th style="width: 20%">Last Name</th>
                    <th style="width: 30%;">Email</th>
                    <th style="width: 10%;">Plan</th>
                    <th style="width: 100px;">Balance</th>
                    <th style="width: 300px;">Send Email</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($outstandingBalances as $row) {
                    $userId = $row['id'];


                ?>
                    <tr>
                        <th class="text-center"><?= $row['id'] ?></th>
                        <th><?= $row['first_name'] ?></th>
                        <th><?= $row['last_name'] ?></th>
                        <th><?= $row['email'] ?></th>
                        <th><?= $row['plan_name'] ?></th>
                        <th class="text-center"><?= round($row['balance'], 2) ?>$</th>
                        <th>
                            <form action="./admin_scripts/seeker_scripts.php" method="post">
                                <input name='email' value="<?= $row['email']?>" hidden>
                                <input name='balance' value="<?= round($row['balance'], 2)?>" hidden>
                                <input name='fname' value="<?= $row['first_name']?>" hidden>
                                <input name='lname' value="<?= $row['last_name']?>" hidden>
                                <button name="emailBalanceDue" value="<?= $row['userId'] ?>" class="btn btn-primary">SEND EMAIL</button>
                            </form>
                        </th>
                    </tr>
                <?php } ?>
            </tbody>
        </table>

    </div>

    <!-- <ul class="list-group">
            <?php foreach ($htmlUserArray as $item) { ?>
                <li class="list-group-item"><?= $item ?></li>
            <?php } ?>
        </ul> -->

    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>

</body>

</html>