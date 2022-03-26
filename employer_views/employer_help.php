<?php
require_once("../configs/sessions.php");
require_once("../configs/db_config.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $name = mysqli_real_escape_string($db, $_POST['name']);
    $sender = mysqli_real_escape_string($db, $_POST['email']);
    $subject = mysqli_real_escape_string($db, $_POST['subject']);
    $body = mysqli_real_escape_string($db, $_POST['message']);

    $query_sendEmail = "INSERT INTO Outbound_Email (sender, receiver, subject, body) VALUES
                            ('$sender', 'tech_support@bjc5531.encs.concordia.caiver', '$subject', '$body')";

    if (mysqli_query($db, $query_sendEmail)) {
        $_SESSION['flash'] = "Message has been received. Thank you";
    } else {
        $_SESSION['err'] = "Error";
    }
}

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
    <div class="jumbotron">
        <div class="container">
            <div class="row my-3">
                <div class="col-6">
                    <h1 class="display-2 mb-0">Contact Us</h1>
                </div>
                <div class="col-6 text-right">
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
            <div class="col mb-md-0 mb-5">
                <form id="contact-form" name="contact-form" action="employer_help.php" method="POST">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="md-form mb-0">
                                <input type="text" id="name" name="name" class="form-control">
                                <label for="name" class="">Your name</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="md-form mb-0">
                                <input type="email" id="email" name="email" class="form-control">
                                <label for="email" class="">Your email</label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="md-form mb-0">
                                <input type="text" id="subject" name="subject" class="form-control">
                                <label for="subject" class="">Subject</label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="md-form">
                                <textarea type="text" id="message" name="message" rows="2" class="form-control md-textarea"></textarea>
                                <label for="message">Your message</label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 text-right">
                            <!--Grid column-->
                            <button type="submit" class="btn btn-primary">SUBMIT</button>
                        </div>
                    </div>
                </form>
            </div>

            <hr class="my-2">
            <p class="lead">For more information please email us at</p>
            <a href="mailto:tech_support@bjc5531.encs.concordia.ca" class="link-primary">tech_support@bjc5531.encs.concordia.ca</a>
            <p class="lead mt-5">or reach us by phone at</p>
            <a href="tel:514-929-3242" class="link-primary">514-929-3242</a>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>

</body>

</html>