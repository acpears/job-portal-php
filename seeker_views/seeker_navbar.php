<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarTogglerDemo01" aria-controls="navbarTogglerDemo01" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
  </button>
  <div class="collapse navbar-collapse" id="navbarTogglerDemo01">

    <!-- SHOW NAV BAR BUTTONS FOR JOB_SEEKER -->
    <a class="navbar-brand" href="seeker_main.php">JOB PORTAL</a>
    <ul class="navbar-nav mr-auto mt-2 mt-lg-0">
        <?php if($_SESSION['enabled']){ ?>
          <li class="nav-item active">
              <a class="nav-link" href="seeker_jobs.php" >Jobs</a>
          </li>
          <li class="nav-item active">
              <a class="nav-link" href="seeker_applications.php">Job Applications</a>
          </li>
        <?php } else { ?>
          <li class="nav-item">
            <a class="nav-link disabled" href="seeker_jobs.php" >Jobs</a>
          </li>
          <li class="nav-item">
              <a class="nav-link disabled" href="seeker_applications.php">Job Applications</a>
          </li>
        <?php } ?>
        <li class="nav-item active">
            <a class="nav-link" href="seeker_settings.php">Settings</a>
        </li>
        <li class="nav-item active">
            <a class="nav-link" href="../logout.php">Logout</a>
        </li>
    </ul>
    
    <!-- HELP PAGE -->  
    <div class="mr-3">
      <a href="./seeker_help.php" class="navbar-text link-dark pd-4">Help?</a>
    </div>
    <div class="border-left">
      <span class="navbar-text ml-2 py-0 mr-3">
      <p class="mb-0 small text-info"><em><strong>Account:</strong> <?=$_SESSION['email'] ?></em></p>
      <p class="mb-0 small text-info"><em><strong>Account Type:</strong> <?=ucfirst($_SESSION['userType']) ?></em></p>
      </span>  
    </div>

  </div>
</nav>
<?php if(!$_SESSION['enabled']){?>
  <div class="alert alert-danger">
      <strong>WARNING!</strong> Account is currently blocked. Please make a payment to get your account reactivated or call our help line for assistance.
  </div>
<?php } ?>