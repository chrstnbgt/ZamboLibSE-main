<?php
  // Start session
  session_start();

  // Check if user is already logged in
  if (isset($_SESSION['userID']) && isset($_SESSION['userEmail'])) {
    header('location: ./webpages/homepage.php');
    exit();
  }

  // Require the account class
  require_once('./classes/account.class.php');

  // Check if login form is submitted
  if (isset($_POST['login'])) {
    $account = new Account();
    $account->userEmail = htmlentities($_POST['userEmail']);
    $account->userPassword = htmlentities($_POST['userPassword']);
    if ($account->sign_in_users()){
      $userID =  $account->userID; 
      $userEmail = $account->userEmail; 
      $_SESSION['user'] = 'user'; 
      $_SESSION['userID'] = $userID;
      $_SESSION['userEmail'] = $userEmail;
      header('location: ./webpages/homepage.php');
      exit(); // Always exit after a header redirect
    } else {
      // $error = 'Invalid username/password. Try again.';
      echo "<script>alert('Invalid username/password. Try again.');</script>"; // JavaScript alert
    }
  }
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="../vendor/bootstrap-5.0.2/css/bootstrap.min.css" rel="stylesheet">
    <!-- Your custome css goes here -->
    <link rel="stylesheet" href="./css/stylesheet.css">
    <link rel="icon" href="./images/zc_lib_seal.png" type="image/x-icon">
    <link href="https://unpkg.com/boxicons@2.1.2/css/boxicons.min.css" rel="stylesheet"/>

    <title>Home</title>
    
</head>

<body>

  <?php 
    require_once('./tools/functions.php');
  ?>
    <nav>
        <div class="logo">
          <img src="./images/zc_lib_seal.png" alt="Logo Text" id="seal">
          <span class="logo-name" id="logo-seal nav-top">Zamboanga City Library</span>
        </div>
        
        <!-- BUTTONS -->
        <div class="access-btn">
            <a class="me-3 login-btn nav-sign-in d-inline-block" data-bs-toggle="modal" data-bs-target="#exampleModal">Sign in</a>
            <button class="signup-btn d-inline-block">
                <span class="signup-content"><a class="text-decoration-none" href="./webpages/signup.php">Create Account</a></span>
            </button>
        </div>

        
        <!-- Modal -->
        <div class="modal fade z-index-3" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog ">
                <div class="modal-content login-modal-size">
                    <div class="modal-body login-modal-size">
                        <!-- Modal body content -->
                        <div class="row">

                            <div class="login--field col-12">
                                <div class="header-login d-flex justify-content-between align-items-center mb-5">
                                    <h4 class="sign-in-header ps-2 pt-2">Sign in</h4>
                                    <button class="exit btn turn_btn" data-bs-dismiss="modal"><i class='bx bx-x icon3'></i></button>
                                </div>
                                <form method="post" action="">
                                    <!-- Form Fields -->
                                    <div class="mb-3">
                                        <label for="userEmail" class="form-label d-flex"><i class="bx bxs-user icon4"></i>Email</label>
                                        <input type="email" class="form-control input-design" id="userEmail" placeholder="Email"  name="userEmail" value="<?php if(isset($_POST['userEmail'])){ echo $_POST['userEmail']; } ?>">
                                    </div>
                
                                    <div class="mb-3">
                                        <label for="userPassword" class="form-label d-flex"><i class='bx bxs-key icon4'></i>Password</label>
                                        <input type="password" class="form-control input-design" id="userPassword" placeholder="Password" name="userPassword" value="<?php if(isset($_POST['userPassword'])){ echo $_POST['userPassword']; } ?>">
                                    </div>

                                    <div class="row forgot-password-link-row">
                                      <div class="col text-end">
                                        <a href="#" class="forgot-password-link">Forgot Password?</a>
                                      </div>
                                    </div>
                
                                    <!-- Add more form fields here -->
                                    <div class="d-flex justify-content-between">
                                        <button type="submit" name="login" id="login-button" class="btn mt-4">Login</button>
                                        <?php
                                          if (isset($_POST['login']) && isset($error)){
                                          ?>
                                              <p class="text-danger mt-3 text-center"><?= $error ?></p>
                                          <?php
                                          }
                                        ?>
                                    </div>
                                </form>
                            </div>

                            <div class="signup-option">
                              <p class="signup-para">
                                Don't have an account? 
                                <a href="signup.php" class="signup-link">Create Account</a>
                              </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
      </nav>

      <section class="overlay"></section>

      <div class="row landing-page align-items-center flex-column-reverse flex-lg-row justify-content-lg-start justify-content-center">
          <div class="col-12 col-lg-6">
              <h3 class="big-landing-header">Zamboanga City Library's <br><span class="events-hlg">Events</span> and <span class="clubs-hlg">Clubs</span></h3>
              <div class="col-12 d-flex justify-content-center justify-content-lg-start">
              <a class="text-decoration-none signup-btn d-inline-block mt-3 px-4 signup-content" href="./webpages/signup.php">Let's Get Started</a>
              </div>
          </div>

          <div class="col-12 col-lg-6">
            <div class="text-center">
            <img src="./images/books.png" alt="" class="img-fluid book-img">
            </div>
          </div>

      </div>

      <div class="row landing-dashboard ">
        <div class="col-6 col-lg-3">
          <?php
            require_once('./classes/user.class.php');

            $user = new User();
            $totalUsers = $user->getTotalUsers();
          ?>
            <h4 class="total-num"><?= $totalUsers ?></h4>
            <p class="total-label">Number of Users</p>
        </div>

        <div class="col-6 col-lg-3">
          <?php
            require_once('./classes/user.class.php');

            $eventTot = new User();
            $totalEvents = $eventTot->getTotalEvents();
          ?>
            <h4 class="total-num"><?= $totalEvents ?></h4>
            <p class="total-label">Number of Events</p>
        </div>

        <div class="col-6 col-lg-3">
          <?php
            require_once('./classes/user.class.php');

            $clubs = new User();
            $totalClubs = $clubs->getTotalClubs();
          ?>
            <h4 class="total-num"><?= $totalClubs ?></h4>
            <p class="total-label">Number of Clubs</p>
        </div>

        <div class="col-6 col-lg-3">
          <?php
            require_once('./classes/user.class.php');

            $incomingEvents = new User();
            $totalIncomingEvents = $incomingEvents->getTotalIncomingEvents();
          ?>
            <h4 class="total-num"><?= $totalIncomingEvents ?></h4>
            <p class="total-label">Incoming Events</p>
        </div>
      </div>


      <?php
        require_once('./classes/events.class.php');
        require_once ('./tools/functions.php');

        $events = new Events();

        $eventsArray = $events->show();
        $counter = 1;

      ?>
      

      <div class="landing-page-events">
      <div class="main ">
    <div class="container pt-5">
      <h4 class="landing-header-events mb-4 text-center ">Zamboanga City Library's Events</h4>
      <div id="carouselExampleIndicators" class="carousel slide" data-bs-ride="carousel">
        <div class="carousel-inner">
          <?php
          if ($eventsArray) {
            foreach ($eventsArray as $key => $item) {
          ?>
              <div class="carousel-item <?= ($key == 0) ? 'active' : '' ?>">
                <div class="row">
                  <div class="container col-12 col-md-10 col-lg-7 mb-3">
                    <!-- List View DIV -->
                    <div class="list_view_div landing-list d-flex flex-column mx-1 my-2">
                      <div class="d-flex justify-content-end">
                        <p class="status"><?= $item['eventStatus'] ?></p>
                      </div>
                      <div class="header_card d-flex align-items-center  mb-2">
                        <h4 class="event_title landing-title"><?= $item['eventTitle'] ?></h4>
                      </div>
                      <!-- Hold Data -->
                      <div class="class d-none">
                        <?= $item['eventGuessLimit'] ?>
                        <?= $item['eventRegion'] ?>
                        <?= $item['eventProvince'] ?>
                        <?= $item['eventCity'] ?>
                        <?= $item['eventBarangay'] ?>
                        <?= $item['eventStreetName'] ?>
                        <?= $item['eventBuildingName'] ?>
                        <?= $item['eventZipCode'] ?>
                      </div>
                      <p class="date_time landing-datetime d-flex align-items-center">
                        <i class="bx bx-calendar-plus icon pe-2"></i>
                        <span class="">
                          <?php
                          $formattedStartDate = date('F j, Y', strtotime($item['eventStartDate']));
                          $formattedEndDate = date('F j, Y', strtotime($item['eventEndDate']));
                          $formattedStartTime = date('h:ia', strtotime($item['eventStartTime']));
                          $formattedEndTime = date('h:ia', strtotime($item['eventEndTime']));

                          echo "{$formattedStartDate} - {$formattedEndDate} | {$formattedStartTime} - {$formattedEndTime}";
                          ?>
                        </span>
                      </p>
                      <p class="club-description landing-description"><?= strlen($item['eventDescription']) > 100 ? substr($item['eventDescription'], 0, 100) . '...' : $item['eventDescription'] ?></p>
                      <div class="btn-container d-flex justify-content-end">
                        <a class="view_more view_more_btn" href="#" data-bs-toggle="modal" data-bs-target="#joinUs">View More</a>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
          <?php
            }
          }
          ?>
        </div>
        <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide="prev">
          <span class="carousel-control-prev-icon" aria-hidden="true"></span>
          <span class="visually-hidden">Previous</span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide="next">
          <span class="carousel-control-next-icon" aria-hidden="true"></span>
          <span class="visually-hidden">Next</span>
        </button>
      </div>

       <!-- Join Us Modal -->
       <div class="modal fade" id="joinUs" tabindex="-1" role="dialog" aria-labelledby="joinUsModalLabel" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered application-modal" role="document">
                                        <div class="modal-content">
                                            <div class="modal-body mx-lg-4 mb-3">
                                                <div class="application-form-heading d-flex justify-content-between my-3">
                                                    <h3 class="club-name d-flex">Create Your Account Now</h3>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>

                                                <p>To keep updated with Zamboanga City Library's events, announcements, and many more!</p>

                                                <!-- BUTTONS -->
                                                <div class="access-btn d-flex justify-content-end align-items-center">
                                                    <a class="me-3 login-btn nav-sign-in" data-bs-toggle="modal" data-bs-target="#exampleModal">Sign in</a>
                                                    <button class="signup-btn">
                                                        <span class="signup-content"><a class="text-decoration-none" href="./webpages/signup.php">Create Account</a></span>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                          </div>
                          <?php
                                  $counter++;
                                  if ($counter % 3 == 0) {
                                      echo '</div><div class="row d-flex">';
                                  }
                          ?>
              <div class="col-12 d-flex justify-content-center pb-5">
                <a class="text-decoration-none signup-btn d-inline-block mt-3 px-5 signup-content" href="./webpages/signup.php">Let's Get Started</a>
              </div>


    </div>
      </div>



  <!-- </div> -->

      <div class="footer row d-flex justify-content-around">
        <div class="website-logo col-12 col-md-4 my-4">
            <div class="row container-fluid">
            </div>
            <div class="row align-items-center">
                <div class="col-3">
                    <img src="./images/zc_lib_seal.png" alt="">
                </div>
                <div class="col-9">
                    <h4 class="logo-name name-web">Zamboanga City Library</h4>
                </div>
            </div>
            <div class="address mt-4 text-center">
                Located at: Justice R.T. Lim Boulevard, Zamboanga City, 7000.
            </div>
        </div>

        <div class="website-navigation col-12 col-md-4 text-center align-items-start my-4">
            <p class="mb-0 pb-3">Explore</p>
            <ul class="list-unstyled mb-0">
                <li><a href="#">Home</a></li>
                <li><a href="#">About Us</a></li>
                <li><a href="#">Clubs</a></li>
            </ul>
        </div>

        <div class="website-navigation col-12 col-md-4 text-center my-4">
            <p>Contact Us</p>
            <ul class="list-unstyled">
                <li><a href="#"><img src="./images/facebook.png" alt="">Facebook</a></li>
                <li><a href="#"><img src="./images/instagram.png" alt="">Instagram</a></li>
                <li><a href="#"><img class="email-icon" src="./images/gmail.png" alt="">Email</a></li>
            </ul>
        </div>
    </div>

    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="./js/custom.js"></script>

</body>
</html>