<?php
  session_start();
  /*
      if user is not login then redirect to login page,
      this is to prevent users from accessing pages that requires
      authentication such as the dashboard
  */
  if (!isset($_SESSION['user']) || $_SESSION['user'] != 'user'){
      header('location: ../index.php');
  }
?>


<!DOCTYPE html>
<html lang="en">
<?php
  $title = 'Profile';
  $courses = 'active';
  require_once('../include/head.php');
?>

<body class="profile-main" >
<?php
    require_once('../include/nav-panel.php');
    require_once('../classes/eventattendance.class.php');

?>

      <section class="overlay"></section>

    <div class="main min-vh-100">
        <div class="container-fluid">
            <div class="row">
                <!-- Profile DIV -->
                <div class="col-12 col-lg-5 mt-3">
                    <div class="profile-dashboard-card">
                        <div class="row align-content-center align-items-center  justify-content-center mb-lg-3 profile-container">
                          <div class="row flex-lg-column align-content-center align-items-center">
                            <div class="col-4 mt-3 d-flex justify-content-center">
                                  <!-- Profile Picture -->
                                  <img src="<?php echo isset($user->userImage) ? $user->userImage : '../images/profile_pic/default-profile.png'; ?>" alt="Profile Picture" class="img-fluid rounded-circle profile-picture-dashboard">
                              </div>
                              
                              <div class="col-8 mt-3 ps-3 d-flex align-items-lg-center flex-column justify-content-center align-items-sm-center">
                                  <h3 class="d-flex align-items-center"><?php echo $user->userFirstName . ' ' . $user->userMiddleName . ' ' . $user->userLastName ?></h3>
                                  <h5 class="email-display"><?php echo $user->userEmail;?></h5>

                                  <!-- Edit Profile -->
                                  <a href="account-settings.php" class="edit-profile d-flex align-items-center"><i class='bx bx-edit icon2'></i>Edit Profile</a>
                              </div>
                          </div>
                  
                            <div class="row d-flex flex-wrap flex-column align-content-around py-4">
                                <p class="heading-label ps-4">My QR Code</p>
                                <div class="row d-flex justify-content-around qr-buttons">
                                  <button class="col-5 qr-btn-view d-flex justify-content-center align-items-center txt-theme-white" data-bs-toggle="modal" data-bs-target="#qrCodeModal">
                                      <i class='bx bx-qr icon-qr'></i><span class="nav-org-label ps-2">View</span>
                                  </button>
                                  <!-- HTML -->
                                  <img id="qrCodeImage" src="<?php echo isset($user->userIDCard) ? $user->userIDCard : '/'; ?>" alt="userIDCard" style="display: none;">
                                  <button id="downloadButton" class="col-5 qr-btn-download justify-content-center align-items-center txt-theme-white">
                                      <i class='bx bxs-download icon-qr'></i><span class="nav-org-label ps-2">Download</span>
                                  </button>
                                </div>
                            </div>
                            <!-- Modal -->
                            <div class="modal fade" id="qrCodeModal" tabindex="-1" aria-labelledby="qrCodeModalLabel" aria-hidden="true">
                              <div class="modal-dialog modal-dialog-centered qr-show-modal">
                                <div class="modal-content">
                                  <div class="modal-header">
                                    <h5 class="modal-title" id="qrCodeModalLabel">QR Code</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                  </div>
                                  <div class="modal-body text-center">
                                    <?php if(isset($user->userIDCard)): ?>
                                      <img src="<?php echo $user->userIDCard; ?>" alt="QR Code" style="max-width: 100%;">
                                    <?php else: ?>
                                      <p>No QR code available</p>
                                    <?php endif; ?>
                                  </div>
                                </div>
                              </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Dashboard DIV -->
                <div class="col-12 col-lg-7 mt-lg-3 d-flex flex-wrap flex-row justify-content-around align-content-start ps-3">
                <?php
                  require_once '../classes/clubmembership.class.php'; // Include the ClubMembership class

                  // Create an instance of the ClubMembership class
                  $clubMembership = new ClubMembership();

                  // Count the clubs joined by the user
                  $clubsJoined = $clubMembership->countClubsByUserID($userID);
                  ?>

                  <div class="col-4 px-2">
                    <div class="card-dashboard d-flex">
                        <div class="content col-4 col-md-7">
                            <p class="para txt-green">
                                <?php echo $clubsJoined; ?>
                            </p>
                            <p class="heading"># Clubs Joined</p>
                        </div>
                        <div class="dash-icon col-5 d-flex justify-content-center">
                            <img src="../images/club.png" alt="Profile Picture" class="dashboard-icon">
                        </div>
                    </div>
                  </div>

                    <?php
                      require_once '../classes/event_registration.class.php';

                      $eventRegistration = new EventRegistration();

                      $totalEventRegistrations = $eventRegistration->countEventRegistrationsByUserID($userID);
                    ?>
                    <div class="col-4 px-2">
                      <div class="card-dashboard d-flex">
                          <div class="content col-4 col-md-7">
                              <p class="para txt-theme-red">
                                  <?php echo $totalEventRegistrations; ?>
                              </p>
                              <p class="heading">Events Registered</p>
                          </div>
                          <div class="dash-icon col-5 d-flex justify-content-center">
                              <img src="../images/registered.png" alt="Registered Icon" class="dashboard-icon">
                          </div>
                      </div>
                    </div>


                  <?php
                    require_once '../classes/eventcertificate.class.php';

                    $eventCertificate = new EventCertificate();

                    $totalCertificates = $eventCertificate->countCertificatesByUserID($userID);
                    ?>


                  <div class="col-4 px-2">
                    <div class="card-dashboard d-flex">
                        <div class="content col-4 col-md-7">
                            <p class="para txt-theme-dblue">
                                <?php echo $totalCertificates; ?>
                            </p>
                            <p class="heading">Certificates Received</p>
                        </div>
                        <div class="dash-icon col-5 d-flex justify-content-center">
                            <img src="../images/certificate.png" alt="Certificate Icon" class="dashboard-icon">
                        </div>
                    </div>
                  </div>


                 <!-- ATTENDANCE -->
                <div class="col-12 col-lg-12 mt-3">
                <div class="card-profile2 attendance-table mx-2">
                      <!-- Events Registered DIV -->
                      <h4 class="events-label mb-4">My Events</h4>
                      <?php
                      require_once '../classes/event_registration.class.php';

                      $eventRegistration = new EventRegistration();
                      $eventRegistrations = $eventRegistration->getEventRegistrationsByUserID($userID);
                      ?>

                          <table id="userTable" class="table table-striped">
                              <thead>
                                  <tr>
                                      <th style="width: 10%;">ID</th>
                                      <th style="width: 40%;">Event</th>
                                      <th class="date-col" style="width: 25%;">Date Registered</th>
                                      <th style="width: 25%;">Status</th>
                                  </tr>
                              </thead>
                              <tbody>
                              <?php foreach ($eventRegistrations as $eventRegistration): ?>
                                  <tr onclick="window.location='./event-details.php?id=<?= $eventRegistration['eventID'] ?>';" style="cursor:pointer;">
                                      <td><?= $eventRegistration['eventID'] ?></td>
                                      <td><?= $eventRegistration['eventTitle'] ?></td>
                                      <td class="date-col"><?= date('M d, Y h:i a', strtotime($eventRegistration['erCreatedAt'])) ?></td>
                                      <td><?= $eventRegistration['erStatus'] ?></td>
                                  </tr>
                              <?php endforeach; ?>

                              </tbody>
                          </table>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script>
        $(document).ready(function() {
            // Initialize DataTable
            var table = $('#userTable').DataTable();

            // Search functionality
            $('#search').on('keyup', function() {
                table.search($(this).val()).draw();
            });
        });
    </script>
    
  <?php
    require_once('../include/footer.php');
  ?>
      
  <?php
    require_once('../include/js.php');
  ?>
</body>
</html>