<?php
session_start();
/*
    if the user is not logged in then redirect to the login page,
    this is to prevent users from accessing pages that require
    authentication such as the dashboard
*/
if (!isset($_SESSION['user']) || $_SESSION['user'] != 'user') {
    header('location: ../index.php');
    exit(); // Make sure to exit after a redirect
}

$title = 'Event Details';
$courses = 'active';

require_once('../include/head.php');
require_once('../classes/events.class.php');
require_once('../tools/functions.php');

$userID = $_SESSION['userID'];



if (isset($_GET['id'])) {
    $events = new Events();
    $record = $events->fetch($_GET['id']);

    $eventID = isset($_GET['id']) ? $_GET['id'] : null;

    if ($record) {
        $id = $record['eventID'];
        $eventTitle = $record['eventTitle'];
        $eventDescription = $record['eventDescription'];
        $eventStartDate = $record['eventStartDate'];
        $eventEndDate = $record['eventEndDate'];
        $eventStartTime = $record['eventStartTime'];
        $eventEndTime = $record['eventEndTime'];
        $eventStatus = $record['eventStatus'];
        $eventGuestLimit = $record['eventGuestLimit'];
        $eventRegion = $record['eventRegion'];
        $eventProvince = $record['eventProvince'];
        $eventCity = $record['eventCity'];
        $eventBarangay = $record['eventBarangay'];
        $eventStreetName = $record['eventStreetName'];
        $eventBuildingName = $record['eventBuildingName'];
        $eventZipCode = $record['eventZipCode'];

    } else {
        // Handle the case where event information couldn't be retrieved
        // You might want to redirect to an error page or handle it in another way
        // For now, let's redirect to index.php
        header('location: ../index.php');
        exit();
    }
}

// add_volunteer.php

if (isset($_POST['apply'])) {
    // Include your database connection here
    require_once('../classes/database.php');
    require_once('../classes/events.class.php'); // Adjust the path as per your file structure

    // Get userID and eventID from the form
    $userID = $_POST['userID'];
    $eventID = $_POST['eventID'];

    // Create an instance of your event class
    $eventVolunteer = new Events(); // Adjust the class name as per your implementation

    // Call the addVolunteer method
    if ($eventVolunteer->addVolunteer($userID, $eventID)) {
        // Redirect back to the page with success message
        header("Location: event-details.php?id=$eventID&volunteer_success=true");
        exit();
    } else {
        echo "Failed to apply as volunteer.";
    }
}

// Cancel volunteer application
if (isset($_POST['cancel'])) {
    // Include your database connection here
    require_once('../classes/database.php');
    require_once('../classes/events.class.php'); // Adjust the path as per your file structure

    // Get userID and eventID from the form
    $userID = $_POST['userID'];
    $eventID = $_POST['eventID'];

    // Create an instance of your event class
    $eventVolunteer = new Events(); // Adjust the class name as per your implementation

    // Call the cancelVolunteer method
    if ($eventVolunteer->cancelVolunteer($userID, $eventID)) {
        // Redirect back to the page with success message
        header("Location: event-details.php?id=$eventID&cancel_success=true");
        exit();
    } else {
        echo "Failed to cancel volunteer application.";
    }
}

// event-details.php

// Check if volunteer application succeeded
if (isset($_GET['volunteer_success']) && $_GET['volunteer_success'] === 'true') {
    echo '<script>alert("Apply as Volunteer Succeed");</script>';
}

// Check if volunteer application was cancelled
if (isset($_GET['cancel_success']) && $_GET['cancel_success'] === 'true') {
    echo '<script>alert("Volunteer application cancelled");</script>';
}

// Check if the user has an event certificate
$eventCertificate = new Events();
$userHasCertificate = $eventCertificate->checkUserCertificate($userID, $eventID);

// Handle feedback submission
if (isset($_POST['submitFeedback'])) {
    $ratings = $_POST['rate'];
    $feedback = $_POST['feedback'];

    $eventFeedback = new Events();
    $eventFeedback->submitFeedback($userID, $eventID, $ratings, $feedback);
}




?>

<!DOCTYPE html>
<html lang="en">
<?php require_once('../include/head.php'); ?>

<body>
    <?php require_once('../include/nav-panel.php'); ?>

    <section class="overlay"></section>

    <div class="main">
        <div class="event-information row d-flex justify-content-center my-3">
            <!-- Event Information -->
            <div class="event-card col-12 col-md-4 mt-4 mx-2">
                <?php if ($record) { ?>
                    <!-- <div class="event-images">
                        <img src="../images/cyber2.png" alt="">
                    </div> -->
                    <h4 class="event_title event_title-header-2 mt-3"><?php echo $eventTitle ?></h4>
                    <!-- <h5 class="collaboration_name event-collab mt-3">Collaborated with DICT</h5> -->
                     
                    <p class="status event-collab">
                        <?php
                        // Get current date and time
                        $currentDateTime = new DateTime(); // Current date and time

                        // Format start and end datetime
                        $eventStartDateTime = new DateTime($eventStartDate . ' ' . $eventStartTime); // Event start datetime
                        $eventEndDateTime = new DateTime($eventEndDate . ' ' . $eventEndTime); // Event end datetime

                        // Determine the status
                        if ($currentDateTime < $eventStartDateTime) {
                            echo "Upcoming"; // If current time is before the event starts
                        } elseif ($currentDateTime >= $eventStartDateTime && $currentDateTime <= $eventEndDateTime) {
                            echo "Ongoing"; // If current time is within the event start and end time range
                        } elseif ($currentDateTime > $eventEndDateTime) {
                            echo "Completed"; // If current time is after the event ends
                        }
                        ?>
                    </p>

                    <p class="datetime-label d-flex align-content-center"><i class='bx bx-calendar icon-default me-2'></i> Date & Time of Event</p>
                    <p class="date-time d-flex align-items-center">
                        <span>
                        <?php
                            // Format start date
                            $formattedStartDate = date('M d, Y', strtotime($eventStartDate));
                            // Format end date
                            $formattedEndDate = date('M d, Y', strtotime($eventEndDate));

                            // Check if start and end date are the same
                            if ($formattedStartDate == $formattedEndDate) {
                                echo $formattedStartDate;
                            } else {
                                echo $formattedStartDate . ' - ' . $formattedEndDate;
                            }
                            ?>
                            <br>
                            <?php
                                // Format start time
                                $formattedStartTime = date('h:i a', strtotime($eventStartTime));
                                // Format end time
                                $formattedEndTime = date('h:i a', strtotime($eventEndTime));

                                echo $formattedStartTime . ' - ' . $formattedEndTime;
                            ?>
                        </span>
                    </p>
                    <p class="datetime-label d-flex align-content-center mt-4"><i class='bx bx-map icon-default me-2'></i> Place of Event</p>
                    <p class="event-address">
                        <?php
                        $eventLocation = '';

                        if (!empty($eventBuildingName)) {
                            $eventLocation .= ' ' . $eventBuildingName;
                        }

                        $eventLocation .= ', ' . $eventStreetName . ', ' . $eventBarangay . ', ' . $eventCity . ', ' . $eventProvince . ', ' . $eventRegion . ', ' . $eventZipCode;

                        // Remove leading comma if buildingName is empty
                        $eventLocation = ltrim($eventLocation, ', ');

                        echo $eventLocation;
                        ?>
                    </p>

                    <p class="datetime-label d-flex align-content-center mt-4"><i class='bx bx-male-female icon-default me-2'></i> Guess Limit</p>
                    <p class="guess-limit mt-2"><?php echo $eventGuestLimit ?></p>
            </div>

            <!-- Event Details -->
            <div class="event-details-card col-12 col-md-7 mt-4 mx-2">
                <div class="row">
                    <div class="col-12 col-lg-9 pe-2">
                        <h4 class="event_title">Event Description:</h4>
                        <p class="caption"><?php echo $eventDescription ?></p>
                        <?php } ?>
                    </div>
                    <div class="col-12 col-lg-3">
                        <?php
                            // Assuming $eventEndDate and $eventEndTime are the end date and time of the event respectively
                            // You may need to format these variables according to your database structure

                            // Convert event end date and time to DateTime objects
                            $eventEndDateTime = new DateTime("$eventEndDate $eventEndTime");
                            $currentDateTime = new DateTime();

                            // Check if the current date is equal to or after the event end date
                            if ($currentDateTime >= $eventEndDateTime) {
                                // If the event has ended, hide the buttons
                                $hideButtons = true;
                            } else {
                                $hideButtons = false;
                            }
                            ?>

                            <div  <?php if ($hideButtons) echo 'style="display: none;"' ?>>
                                    
                            <p class="d-block">Register for:</p>
                            </div>


                            <!-- Buttons will be hidden if $hideButtons is true -->
                            <button class="button my-1 type1 col-12" <?php if ($hideButtons) echo 'style="display: none;"' ?>>
                                <span class="btn-txt">
                                    <a href="registration-form-participant.php?id=<?php echo $id ?>">Participant</a>
                                </span>
                            </button>
                            <button class="button my-1 type2 col-12" <?php if ($hideButtons) echo 'style="display: none;"' ?>>
                                <span class="btn-txt">
                                    <a href="#" data-bs-toggle="modal" data-bs-target="#volunteer">Volunteer</a>
                                </span>
                            </button>


                            <?php
                                function getDateTimeFromString2($dateString) {
                                    // Assuming date format is 'Y-m-d H:i:s'
                                    return DateTime::createFromFormat('Y-m-d H:i:s', $dateString);
                                }

                                // Example usage
                                $eventEndDateTime = getDateTimeFromString2("$eventEndDate $eventEndTime");
                                $currentDateTime = new DateTime();

                                // Check if the current date is equal to or after the event end date
                                if ($currentDateTime <= $eventEndDateTime) {
                                    // If the event has ended, hide the buttons
                                    $hideButtons = true;
                                } else {
                                    $hideButtons = false;
                                }

                                // Check if the user has an event certificate
                                $eventCertificate = new Events();
                                $hasCertificate = $eventCertificate->checkUserCertificate($userID, $eventID);
                                $hasFeedback = false; // Assuming initially user hasn't given feedback

                                // Check if the user has given feedback
                                if ($hasCertificate) {
                                    $hasFeedback = $eventCertificate->checkFeedback($userID, $eventID);
                                }
                                ?>

                                <?php if ($hasCertificate) : ?>
                                    <a href="#" class="text-decoration-none signup-btn px-5 w-100 text-center <?php echo $hasFeedback ? '' : 'disabled'; ?>" <?php if ($hideButtons) echo 'style="display: none;"'; ?> <?php echo $hasFeedback ? 'data-bs-toggle="modal" data-bs-target="#certificate"' : 'onclick="return openFeedbackModal();"'; ?>>Certificate</a>
                                    <a href="#" class="text-decoration-none signup-btn px-5 w-100 text-center mt-2" data-bs-toggle="modal" data-bs-target="#eventFeedback" <?php if ($hideButtons) echo 'style="display: none;"'; ?>>Feedback</a>
                                <?php endif; ?>

                                <script>
                                    function openFeedbackModal() {
                                        alert("You need to submit feedback first.");
                                        document.querySelector('[data-bs-target="#eventFeedback"]').click();
                                        return false; // Prevent default link behavior
                                    }
                                </script>
                        </div>
                    </div>

                    <div class="horizontal-line mt-lg-4"></div>
                    <div class="documentation mt-3">
                        <div id="carouselExampleFade" class="carousel slide" data-bs-ride="carousel">
                            <div class="carousel-inner">
                                <?php
                                require_once '../classes/events.class.php'; // Adjust the path as per your file structure

                                $eventImg = new Events();

                                // Fetch event images for the specific event ID
                                $eventImages = $eventImg->getEventImages($eventID);

                                if ($eventImages) {
                                    $counter = 0;
                                    foreach ($eventImages as $key => $image) {
                                ?>
                                        <div class="carousel-item <?= ($counter == 0) ? 'active' : '' ?>">
                                            <img src="<?php echo $image['eventImage']; ?>" class="d-block w-100" alt="Event Image">
                                        </div>
                                <?php
                                        $counter++;
                                    }
                                } else {
                                    echo "<p>No images found for this event.</p>";
                                }
                                ?>
                            </div>
                            <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleFade" data-bs-slide="prev">
                                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                <span class="visually-hidden">Previous</span>
                            </button>
                            <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleFade" data-bs-slide="next">
                                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                <span class="visually-hidden">Next</span>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Certificate Modal -->
                <div class="modal fade" id="certificate" tabindex="-1" role="dialog" aria-labelledby="certificateModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered application-modal" role="document">
                        <div class="modal-content">
                            <div class="modal-body mx-lg-4 mb-3">
                                <div class="application-form-heading d-flex justify-content-between my-3">
                                    <h3 class="club-name d-flex">Certificate of Participation</h3>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                             <?php
                                // Assuming $eventID and $userID are already defined
                                $eventsCertificate = new Events();
                                // Fetch certificate image based on eventID and userID
                                $certificateImage = $eventsCertificate->getCertificateImage($eventID, $userID);

                                if (!empty($certificateImage)) {
                                    // Ensure the path is correct relative to the project root
                                    echo '<img src="/zambolib/' . $certificateImage . '" class="certificate-image" alt="Certificate Image">';
                                } else {
                                    echo '<p>No certificate found.</p>';
                                }
                            ?>
                            </div>
                            <div class="modal-footer">
                                <button id="downloadCertificateBtn" class="signup-btn">Download Certificate</button>
                            </div>
                        </div>
                    </div>
                </div>

                <script>
                    document.getElementById('downloadCertificateBtn').addEventListener('click', function() {
                        var certificateImage = '<?php echo $certificateImage; ?>';
                        if (certificateImage) {
                            var link = document.createElement('a');
                            link.href = certificateImage;
                            link.download = 'Certificate';
                            document.body.appendChild(link);
                            link.click();
                            document.body.removeChild(link);
                        }
                    });
                </script>



                <?php
                    // Fetch feedback if it exists
                    $feedbackData = $eventCertificate->getFeedback($userID, $eventID);
                    if ($feedbackData) {
                        $feedback = $feedbackData['feedback'];
                        $ratings = $feedbackData['ratings'];
                    } else {
                        $feedback = ""; // If no feedback exists, set it to an empty string
                        $ratings = 0; // Default rating
                    }

                    $eventCertificate = new Events();
                    $hasFeedback = $eventCertificate->hasFeedback($userID, $eventID);
                    ?>


                    <!-- Event Feedback Modal -->
                    <div class="modal fade" id="eventFeedback" tabindex="-1" role="dialog" aria-labelledby="eventFeedbackModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered eventFeedback" role="document">
                            <div class="modal-content">
                                <form method="post" action="">
                                    <div class="modal-body mx-lg-4 mb-3">
                                        <div class="application-form-heading d-flex justify-content-between my-3">
                                            <h3 class="club-name d-flex">Feedback</h3>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="row">
                                        <div class="rating">
                                            <h3 class="event_title">Rate 1-5 stars:</h3>
                                            <?php for ($i = 5; $i >= 1; $i--) : ?>
                                                <input type="radio" id="star<?php echo $i; ?>" name="rate" value="<?php echo $i; ?>" <?php echo $i == $ratings ? 'checked' : ''; ?>/>
                                                <label for="star<?php echo $i; ?>" title="<?php echo ($i == 1) ? 'Very Poor' : ($i == 2 ? 'Poor' : ($i == 3 ? 'Average' : ($i == 4 ? 'Good' : 'Excellent'))); ?>">
                                                    <svg viewBox="0 0 576 512" height="1em" xmlns="http://www.w3.org/2000/svg" class="star-solid">
                                                        <path d="M316.9 18C311.6 7 300.4 0 288.1 0s-23.4 7-28.8 18L195 150.3 51.4 171.5c-12 1.8-22 10.2-25.7 21.7s-.7 24.2 7.9 32.7L137.8 329 113.2 474.7c-2 12 3 24.2 12.9 31.3s23 8 33.8 2.3l128.3-68.5 128.3 68.5c10.8 5.7 23.9 4.9 33.8-2.3s14.9-19.3 12.9-31.3L438.5 329 542.7 225.9c8.6-8.5 11.7-21.2 7.9-32.7s-13.7-19.9-25.7-21.7L381.2 150.3 316.9 18z"></path>
                                                    </svg>
                                                </label>
                                            <?php endfor; ?>
                                        </div>

                                        </div>
                                        <div class="input-group flex-column mb-3">
                                            <label for="feedback" class="label mb-2">Feedback</label>
                                            <textarea name="feedback" id="feedback" class="input-1" rows="5" placeholder="Please enter feedback on the event." required><?php echo $feedback; ?></textarea>
                                            <div></div>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <input type="hidden" name="userID" value="<?php echo $userID; ?>">
                                        <input type="hidden" name="eventID" value="<?php echo $eventID; ?>">
                                        <?php if (!$hasFeedback): ?>
                                            <button type="submit" name="submitFeedback" class="text-decoration-none signup-btn">Submit Feedback</button>
                                        <?php endif; ?>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>


<!-- Volunteer Application Modal -->
<div class="modal fade" id="volunteer" tabindex="-1" role="dialog" aria-labelledby="volunteerModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered application-modal" role="document">
        <div class="modal-content">
    <form method="post" action="">
        <div class="modal-body mx-lg-4 mb-3">
            <div class="application-form-heading d-flex justify-content-between my-3">
                <h3 class="club-name d-flex">Apply as Volunteer</h3>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <?php
            // Check if the user is already a volunteer
            require_once('../classes/events.class.php');
            $eventVolunteer = new Events();
            $isVolunteer = $eventVolunteer->isUserVolunteered($userID, $eventID);

            if ($isVolunteer) {
                echo "<p>You're already applied for volunteer.</p>";
            } else {
            ?>
            <p>Are you sure that you want to apply as a volunteer?</p>
            <?php } ?>
        </div>
        <div class="modal-footer">
            <input type="hidden" name="userID" value="<?php echo $userID; ?>">
            <input type="hidden" name="eventID" value="<?php echo $eventID; ?>">
            <?php if ($isVolunteer) { ?>
                <button type="submit" name="cancel" class="btn btn-secondary">Cancel Volunteer</button>
            <?php } else { ?>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" name="apply" class="btn btn-primary">Apply</button>
            <?php } ?>
        </div>
    </form>
</div>

    </div>
</div>


        </div>
    </div>

    <?php require_once('../include/footer.php'); ?>
    <?php require_once('../include/js.php'); ?>
</body>

</html>
