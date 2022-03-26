<?php
require_once("./configs/db_config.php");
require_once("./configs/sessions.php");
// require_once("./configs/security_questions.php");

if (isset($_SESSION['use'])) {
    if ($_SESSION['userType'] == 'seeker') {
        header("Location: ./seeker_views/seeker_main.php");
    } elseif ($_SESSION['userType'] == 'employer') {
        header("Location: ./employer_views/employer_main.php");
    } elseif ($_SESSION['userType'] == 'admin') {
        header("Location: ./admin_views/admin_main.php");
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $email = mysqli_real_escape_string($db, $_POST['email']);
    $password = mysqli_real_escape_string($db, $_POST['password']);
    $confirmPassword = mysqli_real_escape_string($db, $_POST['confirmPassword']);
    $securityQuestionId = mysqli_real_escape_string($db, $_POST['securityQuestionId']);
    $securityAnswer = strtolower(mysqli_real_escape_string($db, $_POST['securityAnswer']));
    $userType = mysqli_real_escape_string($db, $_POST['userType']);
    $fname = mysqli_real_escape_string($db, $_POST['fname']);
    $lname = mysqli_real_escape_string($db, $_POST['lname']);
    $cname = mysqli_real_escape_string($db, $_POST['cname']);
    $planSeeker = mysqli_real_escape_string($db, $_POST['planSeeker']);
    $planEmployer = mysqli_real_escape_string($db, $_POST['planEmployer']);

    #sql query to check if user exists
    if ($userType === "seeker") {
        $query_findUser = "SELECT * FROM Seeker WHERE email = '$email' LIMIT 1";
    } else if ($userType === "employer") {
        $query_findUser = "SELECT * FROM Employer WHERE email = '$email' LIMIT 1";
    }
    $result_findUser = mysqli_query($db, $query_findUser);

    if (!$result_findUser) {
        $_SESSION['err'] ="Database Error. Please visit our help page.";
    }

    $user = mysqli_fetch_assoc($result_findUser);
    #check if email is already used and passwords match
    if ($user) {
        $_SESSION['err'] = "Email is already in use";
    } else if ($password != $confirmPassword) {
        $_SESSION['err'] = "Passwords do not match";
    }

    if (empty($_SESSION['err'])) {
        #hash password and security answer
        $passwordEncrypted = password_hash($password, PASSWORD_DEFAULT);
        $securityAnswerEncrypted = password_hash($securityAnswer, PASSWORD_DEFAULT);

        if ($userType === "seeker") {
            $query_addSeeker = "INSERT INTO Seeker (first_name, last_name, email, password, plan_name, security_question_id, security_answer) VALUES 
                                ('$fname', '$lname', '$email', '$passwordEncrypted','$planSeeker', $securityQuestionId, '$securityAnswerEncrypted') ";

            if (mysqli_query($db, $query_addSeeker)) {
                $_SESSION['flash'] = "Registration Complete";
                header("location: ../login.php");
                exit();
            } else {
                $_SESSION['err'] = "Employer Database Error. Please call our helpline";
            }
        } else if ($userType === "employer") {

            $query_addEmployer = "INSERT INTO Employer (name, email, password, plan_name, security_question_id, security_answer) VALUES
                                ('$cname', '$email', '$passwordEncrypted', '$planEmployer',$securityQuestionId, '$securityAnswerEncrypted') ";

            if (mysqli_query($db, $query_addEmployer)) {
                $_SESSION['flash'] = "Registration Complete";
                header("location: ../login.php");
                exit();
            } else {
                echo $query_addEmployer;
                $_SESSION['err'] = "<p>Employer Database Error. Please call our helpline!!</p>";
            }
        }
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
    <?php include_once('./navbar.php') ?>
    <div class="row">
        <div class="col">
            <div class="container p-2">
                <div class="container-fluid">
                    <div class="row my-3">
                        <div class="col-4">
                            <h1 class="display-2 mb-0">Register</h1>
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

                <form action="register.php" method="post">
                    <div class="form-group">
                        <label for="email">EMAIL</label>
                        <input type="email" class="form-control" name="email" id="email" aria-describedby="emailHelpId" required>
                    </div>
                    <div class="form-group">
                        <label for="password">PASSWORD</label>
                        <input type="password" class="form-control" name="password" id="password" required>
                    </div>
                    <div class="form-group">
                        <label for="confirmPassword">CONFIRM PASSOWRD</label>
                        <input type="password" class="form-control" name="confirmPassword" id="confirmPassword" required>
                    </div>

                    <!-- security question -->

                    <div class="form-row">
                        <div class="form-group col-md-8">
                            <label class="mr-sm-2" for="inlineFormCustomSelect">SECURITY QUESTION</label>
                            <select class="custom-select mr-sm-2" name="securityQuestionId" required>
                                <option value="" selected disabled>Choose...</option>

                                <!-- Questions from security_question query -->
                                <?php
                                $query_getQuestion = "SELECT * FROM Security_Question";
                                $result_getQuestion = mysqli_query($db, $query_getQuestion);
                                while ($row = mysqli_fetch_assoc($result_getQuestion)) { ?>
                                    <option value="<?= $row['id'] ?>"><?= $row['question'] ?></option>
                                <?php } ?>

                            </select>
                        </div>
                        <div class="form-group col-md-4">
                            <label for="answer">ANSWER</label>
                            <input type="text" class="form-control" name="securityAnswer" id="securityAnswer" required>
                        </div>
                    </div>


                    <div id="userTypeForm" class="form-row">
                        <div class="form-group col-md-2">
                            <label for="userType">ACCOUNT TYPE</label>
                            <select id="userType" class="custom-select mr-sm-2" name="userType" required>
                                <option value="" selected disabled>Choose...</option>
                                <option value="seeker">Job Seeker</option>
                                <option value="employer">Employer</option>
                            </select>
                        </div>
                        <div id="fname" class="form-group col-md-3" style="display: none;">
                            <label for="fname">FIRST NAME</label>
                            <input type="text" class="form-control" name="fname" id="inputFName" required>
                        </div>
                        <div id="lname" class="form-group col-md-3" style="display: none;">
                            <label for="lname">LAST NAME</label>
                            <input type="text" class="form-control" name="lname" id="inputLName" required>
                        </div>
                        <div id="cname" class="form-group col-md-6" style="display: none;">
                            <label for="cname">COMPANY NAME</label>
                            <input type="text" class="form-control" name="cname" id="inputCName" required>
                        </div>
                        <div id="planSeeker" class="form-group col-md-4" style="display: none;">
                            <label for="planSeeker">PLAN</label>
                            <select id="inputPlanSeeker" class="custom-select mr-sm-2" name="planSeeker" required>
                                <option value="" selected disabled>Choose...</option>
                                <?php
                                $query_getSeekerPlan = "SELECT * FROM Seeker_Plan ORDER BY monthly_cost";
                                $result_getSeekerPlan = mysqli_query($db, $query_getSeekerPlan);
                                while ($row = mysqli_fetch_assoc($result_getSeekerPlan)) { ?>
                                    <option value="<?= $row['name'] ?>"><?= ucfirst($row['name']) . " (" . $row['monthly_cost'] . "$/month)" ?></option>
                                <?php } ?>
                            </select>
                        </div>
                        <div id="planEmployer" class="form-group col-md-4" style="display: none;">
                            <label for="planEmployer">PLAN</label>
                            <select id="inputPlanEmployer" class="custom-select mr-sm-2" name="planEmployer" required>
                                <option value="" selected disabled>Choose...</option>
                                <?php
                                $query_getEmployerPlan = "SELECT * FROM Employer_Plan ORDER BY monthly_cost";
                                $result_getEmployerPlan = mysqli_query($db, $query_getEmployerPlan);
                                while ($row = mysqli_fetch_assoc($result_getEmployerPlan)) { ?>
                                    <option value="<?= $row['name'] ?>"><?= ucfirst($row['name']) . " (" . $row['monthly_cost'] . "$/month)" ?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                    <p class="lead">
                        <button class="btn btn-primary btn-lg" type="submit">Submit</button>
                    </p>
                </form>
            </div>
        </div>
    </div>
    <script>
        const userType = document.getElementById('userType');
        userType.addEventListener('change', addAdminNip);

        let cname = document.getElementById('cname');
        let fname = document.getElementById('fname');
        let lname = document.getElementById('lname');
        let planSeeker = document.getElementById('planSeeker');
        let planEmployer = document.getElementById('planEmployer');

        let incname = document.getElementById('inputCName');
        let infname = document.getElementById('inputFName');
        let inlname = document.getElementById('inputLName');
        let inPlanSeeker = document.getElementById('inputPlanSeeker');
        let inPlanEmployer = document.getElementById('inputPlanEmployer');

        function addAdminNip() {
            if (userType.value == "seeker") {
                fname.style.display = "inline";
                lname.style.display = "inline";
                cname.style.display = "none";
                planSeeker.style.display = "inline";
                planEmployer.style.display = "none";

                infname.setAttribute("required", "");
                inlname.setAttribute("required", "");
                incname.removeAttribute("required");
                inPlanSeeker.setAttribute("required", "");
                inPlanEmployer.removeAttribute("required");

            } else if (userType.value == "employer") {
                fname.style.display = "none";
                lname.style.display = "none";
                cname.style.display = "inline";
                planSeeker.style.display = "none";
                planEmployer.style.display = "inline";

                infname.removeAttribute("required");
                inlname.removeAttribute("required");
                incname.setAttribute("required", "");
                inPlanSeeker.removeAttribute("required");
                inPlanEmployer.setAttribute("required", "");
            } else {
                fname.style.display = "none";
                lname.style.display = "none";
                cname.style.display = "none";
                planSeeker.style.display = "none";
                planEmployer.style.display = "none";

                infname.removeAttribute("required");
                inlname.removeAttribute("required");
                incname.removeAttribute("required");
                inPlanSeeker.removeAttribute("required");
                inPlanEmployer.removeAttribute("required");
            }
        }
    </script>
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>

</body>

</html>