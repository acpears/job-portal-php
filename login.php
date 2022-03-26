<?php
require_once("./configs/db_config.php");
require_once("./configs/sessions.php");
require_once("./configs/functions.php");

if(isset($_SESSION['use'])){
    if($_SESSION['userType'] == 'seeker'){
        header("Location: ./seeker_views/seeker_main.php"); 
    } elseif($_SESSION['userType'] == 'employer'){
        header("Location: ./employer_views/employer_main.php"); 
    } elseif($_SESSION['userType'] == 'admin'){
        header("Location: ./admin_views/admin_main.php"); 
    }
}

$userType = "";
$email = "";
$password = "";

//login logic
if($_SERVER["REQUEST_METHOD"] == "POST"){

    $userType = mysqli_real_escape_string($db, $_POST['userType']);
    $email = mysqli_real_escape_string($db, $_POST['email']);
    $password = mysqli_real_escape_string($db, $_POST['password']);
    $error = "";

    #sql query to check if user exists
    if($userType === "seeker"){
        $query_findUser = "SELECT * FROM Seeker WHERE email = '$email' LIMIT 1";
    } else if ($userType === "employer"){
        $query_findUser = "SELECT * FROM Employer WHERE email = '$email' LIMIT 1";
    } else if ($userType === "admin"){
        $query_findUser = "SELECT * FROM Admin WHERE email = '$email' LIMIT 1";
    }
    
    $user = mysqli_fetch_assoc(mysqli_query($db, $query_findUser));
    $userId = $user['id'];

    if($user) {
        if($user['email'] === $email ) {
            if(password_verify($password, $user['password'])) {
                $_SESSION['use'] = $user['id'];
                $_SESSION['userType'] = $userType;
                
                if($userType === "seeker"){
                    $_SESSION['email'] = $user['email'];
                    $_SESSION['name'] = $user['first_name'] . " " . $user['last_name'];
                    $_SESSION['walletId'] = $user['wallet_id'];
                    $_SESSION['enabled'] = $user['enabled'];
                    //disable account if balance is greater then monthly due
                    if(disactivateAccount($userId,$db, $userType)){
                        mysqli_query($db,"UPDATE Seeker SET enabled = 0 WHERE id = $userId");
                        $_SESSION['enabled'] = 0;
                    }
                    
                    setTime($userId,$userType,$db);

                    header("location: ./seeker_views/seeker_main.php");
                } else if ($userType === "employer"){
                    $_SESSION['email'] = $user['email'];
                    $_SESSION['cname'] = $user['name'];
                    $_SESSION['walletId'] = $user['wallet_id'];
                    $_SESSION['enabled'] = $user['enabled'];

                    //disable account if balance is greater then monthly due
                    if(disactivateAccount($userId,$db, $userType)){
                        mysqli_query($db,"UPDATE Employer SET enabled = 0 WHERE id = $userId");
                        $_SESSION['enabled'] = 0;
                    }
                    
                    setTime($userId,$userType,$db);

                    header("location: ./employer_views/employer_main.php");
                } else if ($userType === "admin"){
                    $_SESSION['email'] = $user['email'];
                    header("location: ./admin_views/admin_main.php");
                }
                exit();
            } else{
                $_SESSION['err']= "Password is incorrect";
            }
        }
    } else{
        $_SESSION['err'] = "Email doest not exist";
    }
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
        <link rel="stylesheet" href="style.css">
    </head>
    <body>
        <?php include_once('./navbar.php')?>
        <div class="row">
            <div class="col">
                <div class="container p-2">
                    <div class="container-fluid">
                        <div class="row my-3">
                            <div class="col-4">
                                <h1 class="display-2 mb-0">Login</h1>
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
                    <form action="login.php" method="post">
                        <div class="form-group">
                            <label for="userType">ACCOUNT TYPE</label>
                            <select id="userType" class="custom-select mr-sm-2" name="userType" required>
                                <option value="" selected disabled>Choose...</option>
                                <option value="seeker">Job Seeker</option>
                                <option value="employer">Employer</option>
                                <option value="admin">Admin</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="email">EMAIL</label>
                            <input type="text" class="form-control" name="email" id="email" aria-describedby="emailHelpId" required>
                        </div>
                        <div class="form-group">
                            <label for="password">PASSWORD</label>
                            <input type="password" class="form-control" name="password" id="password" required>
                        </div>
                        <div class="row">
                            <div class="col-md-1">
                                <button class="btn btn-primary btn-lg" type="submit">Submit</button>
                            </div>
                            <div class="col-md-3 px-4" >
                                <a  href="forgotPassword.php">Forgot your password?</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div> 
        </div>
        <script>

        </script>
        <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>

    </body>
</html>


