<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarTogglerDemo01" aria-controls="navbarTogglerDemo01" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
  </button>
  <div class="collapse navbar-collapse" id="navbarTogglerDemo01">

    <!-- SHOW NAV BAR BUTTONS FOR ADMINS -->
    <?php if(isset($_SESSION['use']) && $_SESSION['userType'] === "admin"): ?>
    <a class="navbar-brand" href="#">JOB PORTAL</a>
    <ul class="navbar-nav mr-auto mt-2 mt-lg-0">
        <li class="nav-item active">
            <a class="nav-link" href="admin_seekers.php">Seekers</a>
        </li>
        <li class="nav-item active">
            <a class="nav-link" href="admin_employers.php">Employers</a>
        </li>
        <li class="nav-item active">
            <a class="nav-link" href="admin_jobs.php">Jobs</a>
        </li>
        <li class="nav-item active">
            <a class="nav-link" href="admin_controls.php">Controls</a>
        </li>
        <li class="nav-item active">
            <a class="nav-link" href="../logout.php">Logout</a>
        </li>
    <?php endif; ?>
    </ul>
    <div class="border-left">
      <span class="navbar-text ml-2 py-0 mr-3">
      <p class="mb-0 small text-info"><em>Account: <?=$_SESSION['email'] ?></em></p>
      <p class="mb-0 small text-info"><em>Account Type: <?=ucfirst($_SESSION['userType']) ?></em></p>
      </span>  
    </div>
  </div>
</nav>