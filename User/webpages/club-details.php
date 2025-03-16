<?php

require_once '../classes/clubAnnouncement.class.php';
//resume session here to fetch session values
session_start();
// Check if the user is logged in
if (!isset($_SESSION['userID'])) {
    // Redirect to the login page or another page as needed
    header("Location: ../index.php");
    exit();
} 

// Fetch information for the logged-in user
$userID = $_SESSION['userID'];

if(isset($_GET['id'])) {
    // Get the club ID from the URL parameter
    $clubID = $_GET['id'];
    $_SESSION['clubID'] = $clubID; // Store club ID in session
} else {
    // Check if club ID is stored in session
    if(isset($_SESSION['clubID'])) {
        $clubID = $_SESSION['clubID'];
    } else {
        // Handle the case where the 'id' parameter is not set and not stored in session
        echo "Club ID not provided in the URL";
        exit(); // exit the script if club ID is not provided
    }
}

?>


<!DOCTYPE html>
<html lang="en">
<?php
  $title = 'Club Details';
  $courses = 'active';
  require_once('../include/head.php');
?>

<body>
  <?php
    require_once('../include/nav-panel.php');
    require_once('../classes/datetime.php');
    require_once('../classes/club.class.php');
    

  // Check if the user confirms leaving the club
  if(isset($_GET['leave'])) {
    $leaveClubID = $_GET['leave'];
    if($leaveClubID == $clubID) {
        $club = new Clubs();
        $club->deleteMembership($userID, $clubID);
        // Redirect back to the club details page after leaving the club
        echo "<script>window.location.href='clubs.php';</script>";
        exit();
    } else {
        echo "Invalid club ID for leaving.";
        exit();
    }
  }

  ?>

  <section class="overlay"></section>
      
  <div class="main mb-5">
    <div class="row d-flex justify-content-center">
      <div class="content-feed col-md-9 col-lg-9 min-vh-100">           
        <!-- Friends of the Library -->
        <div class="row club-header">
          <div class="container">
            <div class="card-club-detail-header">
              <a class="club-more-functions" href="#" id="dropdownMenuLink" data-bs-toggle="dropdown" aria-expanded="false">
                <i class='bx bx-dots-vertical-rounded icon-default2'  aria-hidden="true"></i>
                <ul class="dropdown-menu" aria-labelledby="dropdownMenuLink">
                  <li>
                    <a class="dropdown-item option-li" href="?leave=<?= $clubID ?>" onclick="return confirm('Are you sure you want to leave club?')">
                      <div class="d-flex align-items-center text-danger">
                        <i class='bx bx-exit action-icon me-2 text-danger' aria-hidden="true"></i> Leave Club
                      </div>
                    </a>
                  </li>
                </ul>
              </a>
              <?php $clubModel = new Clubs();

                if(isset($_GET['id'])) {
                    // Get the club ID from the URL parameter
                    $clubID = $_GET['id'];

                    // Fetch club details
                    $clubDetails = $clubModel->fetch($clubID);

                    // Check if the club ID exists
                    if ($clubDetails) {
                        // Display club details
                        echo '<div class="club-title-header">' . $clubDetails['clubName'] . '<span class="total-member">' . $clubDetails['total_members'] . ' members</span></div>';
                        echo '<div class="club-description-header mt-2">' . $clubDetails['clubDescription'] . '</div>';
                    } else {
                        // Handle the case where the club ID does not exist
                        echo "Club not found";
                    }
                } else {
                    // Handle the case where the 'id' parameter is not set
                    echo "Club ID not provided in the URL";
                } ?>

            </div>
          </div>
        </div>

        <div class="container mt-3">
          
          <ul class="nav nav-tabs" id="myTab" role="tablist">
            <li class="nav-item" role="presentation">
              <button class="nav-link active" id="clubAnnouncements-tab" data-bs-toggle="tab" data-bs-target="#clubAnnouncements" type="button" role="tab" aria-controls="clubAnnouncements" aria-selected="true">Announcements</button>
            </li>

            <li class="nav-item" role="presentation">
              <button class="nav-link" id="clubEventList-tab" data-bs-toggle="tab" data-bs-target="#clubEventList" type="button" role="tab" aria-controls="clubEventList" aria-selected="false">Events</button>
            </li>
          </ul>

          <div class="tab-content" id="myTabContent">
            <div class="tab-pane fade show active" id="clubAnnouncements" role="tabpanel" aria-labelledby="clubAnnouncements-tab">
              <!-- Update Cards -->
              <?php 
                $announcements = new ClubAnnouncement();
                $announcementsArray = $announcements->fetchAnnouncements($clubID);
                foreach ($announcementsArray as $announcement) { ?>
                  <div class="row announcement-container d-flex align-items-baseline flex-nowrap px-3 py-3">
                    <div class="col datetime-card">
                      <p class="datetime-created"><?= formatDate($announcement['caCreatedAt']) ?></p>
                    </div>
                    <div class="col card-body d-flex flex-column align-items-start ps-4">
                      <h5 class="card-title"><?= $announcement['caTitle'] ?><span class="condition-text px-3 fs-6" style="color: <?= ($announcement['caCondition'] == 'Required') ? 'red' : 'green' ?>"><?= $announcement['caCondition'] ?></span></h5>
                      <?php
                          $startFormatted = formatTime($announcement['caStartTime']);
                          $endFormatted = formatTime($announcement['caEndTime']);
                          $dateFormatted = formatDay($announcement['caStartDate']);

                          // Check if the start and end dates are the same
                          if ($announcement['caStartDate'] === $announcement['caEndDate']) {
                              $timeText = "$startFormatted - $endFormatted";
                              $dateText = $dateFormatted;
                          } else {
                              $timeText = "$startFormatted - $endFormatted";
                              $dateText = "$dateFormatted - " . formatDay($announcement['caEndDate']);
                          }
                          ?>
                          <p class="card-text"><?= $timeText ?>, <?= $dateText ?></p>

                      <p class="card-text"><?= $announcement['caDescription'] ?></p>
                    </div>
                  </div>
                <?php } ?>
            </div>


            <div class="tab-pane fade" id="clubEventList" role="tabpanel" aria-labelledby="clubEventList-tab">
                <!-- Events Cards -->
                <?php
                require_once '../classes/events.class.php';

                $clubEvents2 = new Events();
                $clubEvents2Array = $clubEvents2->showClubEvents($clubID); // Fetch club events

                // Check if the events array is empty
                if (empty($clubEvents2Array)) {
                    echo "<div class='text-center py-5'><p class='text-muted'>No events available for this club at the moment.</p></div>";
                } else {
                    foreach ($clubEvents2Array as $clubEvents) :
                ?>
                    <div class="row announcement-container d-flex align-items-baseline flex-nowrap px-3 py-3">
                        <div class="col datetime-card">
                            <p class="datetime-created"><?= formatDate($clubEvents['eventCreatedAt']) ?></p>
                            <div class="row justify-content-start align-content-center">
                                <div class="col-auto">
                                <a href="./event-details.php?id=<?php echo $clubEvents['eventID']; ?>" class="signup-btn px-3 w-100">View More</a>
                                </div>
                            </div>
                        </div>
                        <div class="col card-body d-flex flex-column align-items-start ps-4">
                            <h5 class="card-title">
                                <?= $clubEvents['eventTitle'] ?>
                                <span class="condition-text px-3 fs-6" style="color: <?= ($clubEvents['eventStatus'] == 'Complete') ? 'blue' : 'green' ?>">
                                    <?= $clubEvents['eventStatus'] ?>
                                </span>
                            </h5>
                            <?php
                            $startFormatted = formatTime($clubEvents['eventStartTime']);
                            $endFormatted = formatTime($clubEvents['eventEndTime']);
                            $dateFormatted = formatDay($clubEvents['eventStartDate']);

                            // Check if the start and end dates are the same
                            if ($clubEvents['eventStartDate'] === $clubEvents['eventEndDate']) {
                                $timeText = "$startFormatted - $endFormatted";
                                $dateText = $dateFormatted;
                            } else {
                                $timeText = "$startFormatted - $endFormatted";
                                $dateText = "$dateFormatted - " . formatDay($clubEvents['eventEndDate']);
                            }
                            ?>
                            <p class="card-text"><?= $timeText ?>, <?= $dateText ?></p>
                            <p class="card-text"><?= $clubEvents['eventDescription'] ?></p>
                        </div>
                    </div>
                <?php 
                    endforeach;
                }
                ?>
</div>


          </div>
        </div>

      </div>
    </div>              
  </div>

  

    <?php
    require_once('../include/footer.php');
    ?>

    <?php
    require_once('../include//js.php');

    
    ?>
</body>
</html>
