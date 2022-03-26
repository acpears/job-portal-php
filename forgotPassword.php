<?php 
require_once("./configs/db_config.php");
require_once("./configs/sessions.php");

if(isset($_SESSION['use'])){
    if($_SESSION['userType'] == 'seeker'){
        header("Location: ./seeker_views/seeker_main.php"); 
    } elseif($_SESSION['userType'] == 'employer'){
        header("Location: ./employer_views/employer_main.php"); 
    } elseif($_SESSION['userType'] == 'admin'){
        header("Location: ./admin_views/admin_main.php"); 
    }
}

$type = "";
$email = "";

if($_SERVER["REQUEST_METHOD"] == "POST"){

    $type = mysqli_real_escape_string($db, $_POST['accounttype']);
    $email = mysqli_real_escape_string($db, $_POST['email']);


    #sql query to check if user exists
    if($type === "seeker"){
        $query_findUser = "SELECT * FROM Seeker WHERE email = '$email' LIMIT 1";
    } else if ($type === "employer"){
        $query_findUser = "SELECT * FROM Employer WHERE email = '$email' LIMIT 1";
    } 
    
    $user = mysqli_fetch_assoc(mysqli_query($db, $query_findUser));

    if($user) {
        if($user['email'] === $email ) {
            $_SESSION['recoveryId'] = $user['id'];
            $_SESSION['recoveryEmail'] = $user['email'];
            $_SESSION['recoveryQuestionId'] = $user['security_question_id'];
            $_SESSION['recoveryQuestionAnswer'] = $user['security_answer'];
            $_SESSION['type'] = $type;
            header("location: ./passwordRecovery.php");
        }
    } else{
        $error .= "<p>Email doest not exist</p>";
    }
    
    mysqli_close($db);
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
                    <h2 class="display-3">Password Recovery</h2>
                    <?php  if (!empty($error)) : ?>
                        <div class="alert alert-danger" role="alert">
                            <?php echo $error?>
                        </div>
                    <?php  endif ?>
                    <form action="forgotPassword.php" method="post">
                        <div class="form-group">
                            <label for="accounttype">ACCOUNT TYPE</label>
                            <select id="accounttype" class="custom-select mr-sm-2" name="accounttype" required>
                                <option value="" selected disabled>Choose...</option>
                                <option value="seeker">Job Seeker</option>
                                <option value="employer">Employer</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="email">EMAIL</label>
                            <input type="email" class="form-control" name="email" id="email" aria-describedby="emailHelpId" required>
                        </div>
                        <p class="lead">
                            <button class="btn btn-primary btn-lg" type="submit">Submit</button>
                        </p>
                    </form>
                </div>
            </div> 
        </div>
        <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>

    </body>
</html>


