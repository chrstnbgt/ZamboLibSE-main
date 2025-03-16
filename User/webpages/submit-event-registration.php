<?php
session_start();

if (!isset($_SESSION['user']) || $_SESSION['user'] != 'user') {
    header('location: ../index.php');
    exit();
}

require_once('../classes/events.class.php');
require_once('../tools/functions.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $eventID = $_POST['eventID'];
    $userID = $_SESSION['userID'];

    // Insert event registration
    $events = new Events();
    $registrationID = $events->registerForEvent($eventID, $userID);

    if ($registrationID) {
        // Insert answers for registration questions
        foreach ($_POST['answer'] as $questionID => $answer) {
            $events->saveAnswer($registrationID, $questionID, $answer);
        }

        // Redirect to success page or display a success message
        header('location: registration-success.php');
        exit();
    } else {
        // Handle registration failure
        echo "Failed to register for the event.";
    }
} else {
    // Redirect to index page if accessed directly
    header('location: ../index.php');
    exit();
}
?>
