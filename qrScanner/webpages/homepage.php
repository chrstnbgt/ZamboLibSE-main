<?php
session_start();

if (!isset($_SESSION['librarianID'])) {      
    header("Location: ../index.php");
    exit();
} 

require_once '../classes/database.php';
require_once '../classes/eventslist.class.php'; // Change this to events.class.php

$database = new Database();
$conn = $database->connect();

$librarianID = $_SESSION['librarianID'];

$event = new Event($conn); // Change EventList to Event

// Fetch events for the logged-in librarian
$events = $event->getEventsForLibrarian($librarianID);

?>

<!DOCTYPE html>
<html lang="en">
<?php
$title = 'Zamboanga City Library';
require_once('../include/head.php');
?>
<body>
<?php
require_once('../include/nav-panel.php');
?>

<div class="main min-vh-100 container ">
    <div class="row">
        <div class="col-12 text-center">
            <h3 class="label-homepage mb-4">Check Attendance</h3>
        </div>

        <div class="col-12 choose-div text-center mb-5">
            <p class="label mb-3">Choose Event</p>
            <div class="dropdown">
                <button class="save-attendance-btn dropdown-toggle" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                    Select Events
                </button>
                <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                    <?php foreach($events as $event): ?>
                        <li><a class="dropdown-item" href="./event-details.php?eventID=<?php echo $event['eventID']; ?>"><?php echo $event['eventTitle']; ?></a></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
    </div>
</div>

<?php
require_once('../include/js.php');
?>

</body>
</html>
