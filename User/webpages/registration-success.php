<?php
require_once('../include/head.php');
require_once('../classes/events.class.php');
require_once('../classes/userfull.class.php');
require_once('../tools/functions.php');
require_once('../classes/eventform.class.php');

session_start();

// Check if the user is logged in and has appropriate permissions
if (!isset($_SESSION['user']) || $_SESSION['user'] != 'user') {
    header('location: ../index.php');
    exit();
}

$title = 'Participant Registration';
$courses = 'active';

$eventID = isset($_GET['id']) ? $_GET['id'] : null;

if (!$eventID) {
    header('location: ../index.php');
    exit();
}

$events = new Events();
$eventsform = new EventForm();

$record = $events->fetch($eventID);

if (!$record) {
    header('location: ../index.php');
    exit();
}

$eventTitle = $record['eventTitle'];

$user = new User(); // Assuming you have a User class

// Check if the user is already registered for the event
$userID = $_SESSION['userID'];
$registeredData = $eventsform->fetchEventRegistrationAnswers($eventID, $userID);

if ($registeredData) {
    // User is already registered, display filled-up form data
    $isRegistered = true;
} else {
    // User is not registered, fetch registration questions
    $isRegistered = false;
    $eventRegistrationFormData = $eventsform->fetchEventRegistrationQuestions($eventID, $userID);
}

if (isset($_POST['submit-answers'])) {
    $answers = $_POST['longTextField'] ?? array();

    $success = $eventsform->insertEventRegistrationAnswers($eventID, $userID, $answers);

    if ($success) {
        header("Location: event-details.php?id=$eventID");
        exit();
    } else {
        $errorMessage = "Failed to submit registration answers.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<?php require_once('../include/head2.php'); ?>

<body>
    <?php require_once('../include/nav-panel.php'); ?>

    <section class="overlay"></section>

    <div class="main min-vh-100">
        <div class="row d-flex justify-content-center">
            <div class="form-card col-12 col-md-8 col-lg-6 mt-lg-4 mb-5">
                <div class="header-form mb-3">
                    <h3 class="form-header mb-3 ps-2 mt-2">Registration Form for Participant</h3>
                    <h4 class="event_title_form ps-2"><?php echo $eventTitle; ?></h4>
                    <?php if ($isRegistered) { ?>
                        <p class="note-already mt-3">You successfully apply for this event! We'll notify you when the registration is accepted.</p>
                    <?php } else { ?>
                        <p class="form-instructions mt-3 ps-2">Please fill in your information.</p>
                    <?php } ?>

                    <div class="row d-flex justify-content-end">
                        <a href="event-details.php?id=<?php echo $eventID ?>" class="submit-form mt-3">Return to Event Details</a>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <?php require_once('../include/footer.php'); ?>
    <?php require_once('../include/js.php'); ?>

</body>

</html>
