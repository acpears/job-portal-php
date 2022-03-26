<?php
    session_start();

    if(isset($_SESSION['use'])){ 
        session_destroy();

    }
    
      // function that Destroys Session 
    header("Location: ../index.php");
