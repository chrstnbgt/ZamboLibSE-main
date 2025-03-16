<?php
session_start();
// Check if the user is logged in
if (!isset($_SESSION['librarianID'])) {      
    // Redirect to the login page or another page as needed
    header("Location: ../index.php");
    exit();
} 

// Fetch information for the logged-in user
$librarianID = $_SESSION['librarianID'];

// Fetch the eventID from the URL
if (isset($_GET['eventID'])) {
    $eventID = $_GET['eventID'];
} else {
    // Redirect or handle the case when eventID is not set
    header("Location: #");
    exit();
}

require_once '../classes/database.php';
require_once '../classes/eventslist.class.php';

$database = new Database();
$conn = $database->connect();

$event = new Event($conn);
$eventDetails = $event->getEventDetails($eventID);

// Check if event attendance data already exists for the event
$eventAttendance = new EventList($conn);
if (!$eventAttendance->attendanceExists($eventID)) {
    // If attendance data does not exist, add attendance days
    $eventAttendance->addAttendanceDays($eventID);
}

// Fetch event attendance data for the specific event ID
$attendanceData = $eventAttendance->getAttendanceData($eventID);
?>

<!DOCTYPE html>
<html lang="en">

<?php
$title = 'Event Details';
require_once('../include/head.php');
?>

<body>
    <?php
    require_once('../include/nav-panel.php');
    ?>

    <div class="main min-vh-100 container d-flex align-items-baseline">
        <div class="row">
            <div class="col-12 text-center"> <!-- Center the content -->
                <h3 class="my-3"><?php echo $eventDetails['eventTitle']; ?></h3>
            </div>

            <div class="col-12 table-div  mb-5"> <!-- Center the content -->
                <?php 
                $dayNumber = 1; // Initialize day number
                foreach ($attendanceData as $attendance): ?>
                    <a href="./attendance-list.php?eventAttendanceID=<?php echo $attendance['eventAttendanceID']; ?>" class="day-card d-flex align-items-center justify-content-around">
                        <p class="day-label me-3">Day &nbsp;<?php echo $dayNumber++; ?></p> <!-- Increment day number for each iteration -->
                        <p class="date-label align-items-center"><?php echo date('M d, Y', strtotime($attendance['eaDate'])); ?></p> <!-- Format date as Month Day, Year -->
                        <p class="go-label d-flex justify-content-end"><i class='bx bx-chevron-right right-icon' ></i></p>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <?php
    require_once('../include/js.php');
    ?>

</body>
</html>
