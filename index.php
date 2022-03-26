<?php
require_once("./configs/sessions.php");

if (isset($_SESSION['use'])) {
    if ($_SESSION['userType'] == 'seeker') {
        header("Location: ./seeker_views/seeker_main.php");
    } elseif ($_SESSION['userType'] == 'employer') {
        header("Location: ./employer_views/employer_main.php");
    } elseif ($_SESSION['userType'] == 'admin') {
        header("Location: ./admin_views/admin_main.php");
    }
}

?>
<!doctype html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <link rel="stylesheet" href="style.css">
    <title></title>
</head>

<body>
    <?php include_once('./navbar.php') ?>
    <div class="mask rgba-black-strong">
        <div class="container-fluid d-flex align-items-center justify-content-center h-100">
            <div class="row d-flex justify-content-center text-center">
                <div class="col-md-10">
                    <!-- Heading -->
                    <h2 class="display-4 font-weight-bold white-text pt-5 mb-2">Awesome Web Career Portal</h2>
                    <!-- Divider -->
                    <hr class="hr-light">
                    <!-- Description -->
                    <h5 class="white-text my-4">Welcome to Web Career Portal. The online Job Portal website enables recruiters to post new job vacancies of any specialization for the hiring process. Also, it enables job seekers to search and apply for any number of jobs. </h5>
                </div>
            </div>
        </div>
    </div>
    <script>
        var login = document.getElementById('login');
        var register = document.getElementById('register');
        login.addEventListener('click', function() {
            console.log("login")
            document.location.href = '/login.php';
        });
        register.addEventListener('click', function() {
            console.log("register")
            document.location.href = '/register.php';
        });
    </script>
    <!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->

    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
</body>

</html>