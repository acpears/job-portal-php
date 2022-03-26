<?php
require_once("../configs/sessions.php");
require_once("../configs/db_config.php");

if (!isset($_SESSION['use']) || $_SESSION['userType'] != 'employer') {
    header("Location: ../index.php");
}

// Current session id
$userId = $_SESSION['use'];
$walletId = $_SESSION['walletId'];

// Query to load the current employer from the database
$query_getUser = "SELECT *, e.name AS cname FROM Employer e JOIN Employer_Plan ep ON e.plan_name = ep.name WHERE id = '$userId'";
$user = mysqli_fetch_assoc(mysqli_query($db, $query_getUser));
$_SESSION['enabled'] = $user['enabled'];

// Query to load the e methods of employer
$query_paymentMethods = "SELECT pm.id, type, card_number, expiry_date, account_number, ba.owner_name AS ba_owner_name, cc.owner_name AS cc_owner_name FROM Employer e
                        JOIN Wallet w ON e.wallet_id = w.id
                        JOIN Registered_Payment_Method rpm ON w.id = rpm.wallet_id
                        JOIN Payment_Method pm ON rpm.payment_method_id = pm.id
                        LEFT JOIN Bank_Account ba on pm.id = ba.id 
                        LEFT JOIN Credit_Card cc ON pm.id = cc.id
                        WHERE e.id = $userId";

$result_paymentMethods = mysqli_query($db, $query_paymentMethods);

$query_plans = "SELECT * FROM Employer_Plan ORDER BY monthly_cost";
$result_plans = mysqli_query($db, $query_plans);

$query_wallet = "SELECT * FROM Wallet WHERE id = $walletId";
$wallet = mysqli_fetch_assoc(mysqli_query($db, $query_wallet));

$query_usedCatergories = "SELECT DISTINCT category_name AS name FROM Job_Posting jp
                            INNER JOIN Job_Category jc ON jc.name = jp.category_name";
$query_unusedCategories = "SELECT DISTINCT name FROM Job_Posting jp
                            RIGHT JOIN Job_Category jc ON jc.name = jp.category_name
                            WHERE jp.category_name IS NULL";
$result_usedCategories = mysqli_query($db, $query_usedCatergories);
$result_unusedCategories = mysqli_query($db, $query_unusedCategories);

$time = strtotime(date('Y-m-01'));
$paymentDate = date("Y-m-d", strtotime("+1 month", $time));

?>

<!DOCTYPE html>
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
    <div class="container">
        <div class="container-fluid">
            <div class="row my-3">
                <div class="col-8">
                    <h1 class="mb-0">Settings</h1>
                </div>
                <div class="col text-right">
                    <form action="./employer_scripts/settings_scripts.php" method="post">
                        <button class="btn btn-danger" name="delete" value="<?= $userId ?>" onclick="return confirm('Are you sure you want to delete this account');">DELETE ACCOUNT</button>
                    </form>
                </div>
            </div>

        </div>

        <div class="card overflow-hidden">
            <div class="row no-gutters row-bordered row-border-light">
                <div class="col-md-3 pt-0">
                    <div class="list-group list-group-flush account-settings-links">
                        <a class="list-group-item list-group-item-action active" data-toggle="list" href="#account-general">General</a>
                        <a class="list-group-item list-group-item-action" data-toggle="list" href="#account-change-password">Security</a>
                        <a class="list-group-item list-group-item-action" data-toggle="list" href="#account-payment">Payments</a>
                        <a class="list-group-item list-group-item-action" data-toggle="list" href="#account-plan">Account</a>
                        <a class="list-group-item list-group-item-action" data-toggle="list" href="#account-categories">Job Categories</a>
                    </div>
                </div>
                <div class="col-md-9">
                    <div class="tab-content">
                        <div class="tab-pane fade active show" id="account-general">
                            <div class="card-body">
                                <div class="container">
                                    <div class="row">
                                        <div class="form-group col-md-6">
                                            <h2>General</h2>
                                        </div>
                                    </div>

                                    <form action="./employer_scripts/settings_scripts.php" method="post">
                                        <div class="form-group">
                                            <label class="form-label">Company Name</label>
                                            <input type="text" class="form-control" name="cname" value="<?= $user['cname'] ?>"">
                                            </div>

                                            <div class=" form-group">
                                            <label class="form-label">Email</label>
                                            <input type="email" class="form-control" name="email" value="<?= $user['email'] ?>">
                                        </div>

                                        <div class="text-right mt-3">
                                            <button type="submit" class="btn btn-primary">Save changes</button>&nbsp;
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="account-change-password">
                            <div class="card-body pb-2">
                                <div class="container">
                                    <div class="row">
                                        <div class="form-group col-md-6">
                                            <h2>Reset Password</h2>
                                        </div>
                                    </div>

                                    <form action="./employer_scripts/settings_scripts.php" method="post">
                                        <div class="form-group">
                                            <label class="form-label">Current password</label>
                                            <input type="password" name="oldPassword" class="form-control" required>
                                        </div>

                                        <div class="form-group">
                                            <label class="form-label">New password</label>
                                            <input type="password" name="password" class="form-control" required>
                                        </div>

                                        <div class="form-group">
                                            <label class="form-label">Confirm New password</label>
                                            <input type="password" name="confirmPassword" class="form-control" required>
                                        </div>

                                        <div class="text-right mt-3">
                                            <button type="submit" class="btn btn-primary">Save changes</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <!-- Payments Tab -->

                        <div class="tab-pane fade" id="account-payment">
                            <div class="card-body pb-4">
                                <!-- Container for listing payment methods -->
                                <div class="container">
                                    <div class="row">
                                        <div class="form-group col-md-6">
                                            <h2>Payment Methods</h2>
                                        </div>
                                    </div>
                                    <div class="list-group">

                                        <!-- Loop through payments methods and print on screen -->
                                        <?php while ($row = mysqli_fetch_assoc($result_paymentMethods)) {
                                            if ($row['type'] == "Credit Card") { ?>
                                                <div class="list-group-item list-group-item-secondary mb-3 pb-0">
                                                    <div class="row">
                                                        <h5 class="col-3">CREDIT CARD</h5>
                                                        <form action="./employer_scripts/settings_scripts.php" method="post" class="col">
                                                            <button name="remove" value="<?= $row['id'] ?>" class="btn btn-danger btn-sm">Remove</button>
                                                        </form>
                                                        <?php if ($wallet['default_payment_id'] == $row['id']) { ?>
                                                            <form action="./employer_scripts/settings_scripts.php" method="post" class="col">
                                                                <button name="defaultPayment" value="<?= $row['id'] ?>" class="btn btn-warning btn-sm" disabled>Current Payment Method</button>
                                                            </form>
                                                        <?php } else { ?>
                                                            <form action="./employer_scripts/settings_scripts.php" method="post" class="col">
                                                                <button name="defaultPayment" value="<?= $row['id'] ?>" class="btn btn-success btn-sm">Select As Payment Method</button>
                                                            </form>
                                                        <?php } ?>
                                                    </div>
                                                    <hr>
                                                    <div class="row">
                                                        <p class="col-4">Card Number: <?= $row['card_number'] ?> </p>
                                                        <p class="col-4">Expiration Date: <?= $row['expiry_date'] ?> </p>
                                                        <p class="col-4">Card Owner: <?= $row['cc_owner_name'] ?></p>
                                                    </div>
                                                </div>
                                            <?php } elseif ($row['type'] == "Bank Account") { ?>
                                                <div class="list-group-item list-group-item-secondary mb-3 pb-0">
                                                    <div class="row">
                                                        <h5 class="col-3">BANK ACCOUNT</h5>
                                                        <form action="./employer_scripts/settings_scripts.php" method="post" class="col">
                                                            <button name="remove" value="<?= $row['id'] ?>" class="btn btn-danger btn-sm">Remove</button>
                                                        </form>
                                                        <?php if ($wallet['default_payment_id'] == $row['id']) { ?>
                                                            <form action="./employer_scripts/settings_scripts.php" method="post" class="col">
                                                                <button name="defaultPayment" value="<?= $row['id'] ?>" class="btn btn-warning btn-sm" disabled>Current Payment Method</button>
                                                            </form>
                                                        <?php } else { ?>
                                                            <form action="./employer_scripts/settings_scripts.php" method="post" class="col">
                                                                <button name="defaultPayment" value="<?= $row['id'] ?>" class="btn btn-success btn-sm">Select As Payment Method</button>
                                                            </form>
                                                        <?php } ?>
                                                    </div>
                                                    <hr>
                                                    <div class="row">
                                                        <p class="col-4">Account Number: <?= $row['account_number'] ?> </p>
                                                        <!-- <p class="col-4">Expiration Date: <?= $row['expiry_date'] ?> </p> -->
                                                        <p class="col-4">Account Owner: <?= $row['ba_owner_name'] ?></p>
                                                    </div>
                                                </div>
                                        <?php }
                                        } ?>
                                    </div>

                                </div>
                                <hr>
                                <!-- Container for adding payment methods -->
                                <div class="container">
                                    <div class="row">
                                        <div class="form-group col-md-6">
                                            <h2>Add Payment Method</h2>
                                        </div>
                                        <div class="form-group col-md-6">
                                            <select id="paymentType" class="custom-select mr-sm-2" name="paymentType" required>
                                                <option value="" selected disabled>Choose...</option>
                                                <option value="cc">Credit Card</option>
                                                <option value="ba">Bank Account</option>
                                            </select>
                                        </div>

                                    </div>
                                    <!-- Credit Card From -->
                                    <div id="creditCard" style="display: none;">
                                        <form action="./employer_scripts/settings_scripts.php" id="payment-form" method="post">
                                            <div class="row">
                                                <div class="col-4">
                                                    <div class="form-group">
                                                        <label>CARD NUMBER</label>
                                                        <div class="input-group">
                                                            <input type="text" class="form-control" name="cardNumber" placeholder="16-digit Card Number" minlength="16" maxlength="16" required>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-8">
                                                    <div class="form-group">
                                                        <label>NAME OF CARD</label>
                                                        <input type="text" class="form-control" name="cardName" placeholder="First and Last Name" required>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-xs-4 col-md-4">
                                                    <div class="form-group">
                                                        <label>EXPIRATION DATE</label>
                                                        <div class="input-group">
                                                            <input type="number" placeholder="YYYY" name="year" class="form-control" min="1900" max="2030" required>
                                                            <input type="number" placeholder="MM" name="month" class="form-control" min="1" max="12" required>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-xs-4 col-md-4 pull-right">
                                                    <div class="form-group">
                                                        <label>CVC CODE</label>
                                                        <input type="text" class="form-control" name="cvc" placeholder="CVC" required="">
                                                    </div>
                                                </div>
                                                <div class="col-xs-4 col-md-4 pull-right">
                                                    <div class="text-right mt-3">
                                                        <button type="submit" class="btn btn-primary">Add</button>
                                                    </div>
                                                </div>
                                            </div>

                                        </form>
                                    </div>
                                    <!-- Bank Account Form -->
                                    <div id="bankAccount" style="display: none;">
                                        <form action="./employer_scripts/settings_scripts.php" id="payment-form" method="post">
                                            <div class="row">
                                                <div class="col-4">
                                                    <div class="form-group">
                                                        <label>ACCOUNT NUMBER</label>
                                                        <div class="input-group">
                                                            <input type="text" class="form-control" name="accountNumber" placeholder="10-12 Account Number" minlength="10" maxlength="12" required>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-8">
                                                    <div class="form-group">
                                                        <label>NAME ON ACCOUNT</label>
                                                        <input type="text" class="form-control" name="accountName" placeholder="First and Last Name" required>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-4">
                                                    <div class="form-group">
                                                        <label>BRANCH</label>
                                                        <input type="text" class="form-control" name="branch" placeholder="" required>
                                                    </div>
                                                </div>
                                                <div class="col-6">
                                                    <div class="form-group">
                                                        <label>BANK NAME</label>
                                                        <input type="text" class="form-control" name="bankName" placeholder="" required>
                                                    </div>
                                                </div>
                                                <div class="col-2">
                                                    <div class="text-right mt-3">
                                                        <button type="submit" class="btn btn-primary">Add</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Account plan -->

                        <div class="tab-pane fade" id="account-plan">
                            <div class="card-body pb-4">

                                <div class="container">
                                    <div class="row">
                                        <div class="form-group col-md-6">
                                            <h2>Plan</h2>
                                        </div>
                                    </div>
                                    <form action="./employer_scripts/settings_scripts.php" method="post">
                                        <div class="row">
                                            <div class="col-8">
                                                <?php while ($row = mysqli_fetch_assoc($result_plans)) { ?>
                                                    <div class="form-check my-2">
                                                        <?php if ($row['name'] == $user['plan_name']) { ?>
                                                            <input class="form-check-input" type="radio" name="plans" value="<?= $row['name'] ?>" checked>
                                                        <?php } else { ?>
                                                            <input class="form-check-input" type="radio" name="plans" value="<?= $row['name'] ?>">
                                                        <?php } ?>
                                                        <label class="form-check-label" for="<?= $row['name'] ?>">
                                                            <?= ucfirst($row['name']) ?>: <?= ucfirst($row['description']) ?> (Cost: <?= ucfirst($row['monthly_cost']) ?>$/month)
                                                        </label>
                                                    </div>
                                                <?php } ?>
                                            </div>
                                            <div class="col-4">
                                                <div class="text-right mt-3">
                                                    <button type="submit" class="btn btn-primary">Update Plan</button>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>

                                <hr>

                                <div class="container">
                                    <div class="row">
                                        <div class="col-12 mb-2">
                                            <h2>Account Balance</h2>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col">
                                            <div class="form-group form-inline">

                                                <div class="input-group-prepend">
                                                    <span class="input-group-text">$</span>
                                                </div>
                                                <input type="text" class="form-control" aria-label="" value="<?= round($wallet['balance'], 2) ?>" disabled>
                                                <h6 class="ml-2 mb-0"> as of <?php echo date("Y-m-d") ?></h6>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <hr>
                                <div class="container">
                                    <div class="row mb-2">
                                        <div class="col">
                                            <h6 class=""> Plan cost of <strong><?= $user['monthly_cost'] ?>$</strong> due before <?= $paymentDate ?> </h6>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-9">
                                            <div class="container-fluid">
                                                <div class="row">
                                                    <div class="form-group form-inline">
                                                        <form action="./employer_scripts/settings_scripts.php" method="post" class="form-inline">
                                                            <button type="submit" name="makePayment" value="<?= $wallet['id'] ?>" class="btn btn-primary mr-3">Make Payment</button>
                                                            <div class="input-group">
                                                                <div class="input-group-prepend">
                                                                    <span class="input-group-text">$</span>
                                                                </div>
                                                                <input type="number" class="form-control" name="amount" min="0" step="0.01" value="0.00" pattern="/^\d+\.\d{2,2}$/">
                                                            </div>
                                                        </form>

                                                    </div>
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col">
                                            <div class="form-group form-inline">
                                                <?php if ($wallet['automatic']) { ?>
                                                    <form action="./employer_scripts/settings_scripts.php" method="post">
                                                        <button name="automaticNo" value="<?= $wallet['id'] ?>" class="btn btn-danger">Disable Automatic Payments</button>
                                                    </form>
                                                    <?php
                                                    
                                                    ?>
                                                    <h6 class="ml-2 mb-0">Next payment date: <?= $paymentDate ?></h6>
                                                <?php } else { ?>
                                                    <form action="./employer_scripts/settings_scripts.php" method="post">
                                                        <button name="automaticYes" value="<?= $wallet['id'] ?>" class="btn btn-success">Enable Automatic Payments</button>
                                                    </form>

                                                <?php } ?>


                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>

                        <div class="tab-pane fade" id="account-categories">
                            <div class="card-body">
                                <div class="container">
                                    <div class="row mb-2">
                                        <div class="col">
                                            <h2>Categories</h2>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col">
                                            <h4>Currently Used</h1>
                                                <ul class="list-group">
                                                    <?php while ($row = mysqli_fetch_assoc($result_usedCategories)) { ?>
                                                        <li class="list-group-item"><?= $row['name'] ?></li>

                                                    <?php } ?>
                                                </ul>
                                        </div>
                                        <div class="col">
                                            <h4>Unused</h1>
                                                <ul class="list-group">
                                                    <?php while ($row = mysqli_fetch_assoc($result_unusedCategories)) { ?>
                                                        <li class="list-group-item"><?= $row['name'] ?>
                                                            <div class="float-right">
                                                                <form action="./employer_scripts/settings_scripts.php" method="post">
                                                                    <button name="deleteCategory" value="<?= $row['name'] ?>" class="badge badge-danger">DEL</button>
                                                                </form>
                                                            </div>
                                                        </li>
                                                    <?php } ?>
                                                </ul>
                                        </div>
                                    </div>
                                </div>
                                <hr>
                                <div class="container">
                                    <div class="row">
                                        <div class="col">
                                            <div class="form-group form-inline">
                                                <form action="./employer_scripts/settings_scripts.php" method="post">

                                                    <!-- <label for="addCategory" class="form-label mr-2">NEW CATEGORY</label> -->
                                                    <input type="text" class="form-control mr-2" name="addCategory" placeholder="New Category">
                                                    <button type="submit" class="btn btn-primary">Add</button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
            <!-- Flash messages on bottom of container -->
            <?php if (!empty($_SESSION['err'])) : ?>
                <div class="alert alert-danger my-0" role="alert">
                    <?= $_SESSION['err'] ?>
                </div>
            <?php $_SESSION['err'] = "";
            endif ?>
            <?php if (!empty($_SESSION['flash'])) : ?>
                <div class="alert alert-success my-0" role="alert">
                    <?= $_SESSION['flash'] ?>
                </div>
            <?php $_SESSION['flash'] = "";
            endif ?>
        </div>
    </div>
    <script>
        let selector = document.getElementById("paymentType");
        let cc = document.getElementById("creditCard");
        let ba = document.getElementById("bankAccount");
        selector.addEventListener('change', test);

        function test() {
            if (selector.value == 'cc') {
                cc.style.display = 'inline';
                ba.style.display = 'none';
            } else if (selector.value == 'ba') {
                cc.style.display = 'none';
                ba.style.display = 'inline';
            }
        }
    </script>
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>

</body>

</html>