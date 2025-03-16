<?php
session_start();
// Check if the user is logged in
if (!isset($_SESSION['librarianID'])) {      
    // Redirect to the login page or another page as needed
    header("Location: ../index.php");
    exit();
} 

// Fetch the eventAttendanceID from the URL
if (isset($_GET['eventAttendanceID'])) {
    $eventAttendanceID = $_GET['eventAttendanceID'];
    
} else {
    // Redirect or handle the case when eventAttendanceID is not set
    header("Location: #");
    exit();
}

require_once '../classes/database.php';
require_once '../classes/eventattendance.class.php';

$database = new Database();
$conn = $database->connect();

$attendance = new EventAttendance();
$attendanceDetails = $attendance->showEventAttendance($eventAttendanceID);
?>

<!DOCTYPE html>
<html lang="en">

<?php
$title = 'Attendance Details';
require_once('../include/head.php');
?>

<body>
    <?php
    require_once('../include/nav-panel.php');
    ?>

    <div class="main min-vh-100 container d-flex align-items-baseline">
        <div class="row">
            <div class="col-12 d-flex align-items-center text-center  my-3">
                <a class="ps-2" href="./homepage.php"><i class='bx bx-chevron-left icon-function'></i></a>
                <h3 class="ps-3 text-center">Attendance Details</h3>
            </div>

            <div class="col-12 table-div  mb-5"> <!-- Center the content -->
                <div class="row d-flex py-2">
                    <div class="label-2">Name</div>
                    <div class="label-4">Time Entered</div>
                </div>
                <div class="accordion" id="accordionExample">
                    <?php foreach ($attendanceDetails as $attendance): ?>
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="heading<?= $attendance['userID'] ?>">
                            <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapse<?= $attendance['userID'] ?>" aria-expanded="true" aria-controls="collapse<?= $attendance['userID'] ?>">
                                <div class="attendant-name"><?= $attendance['fullName'] ?></div>
                                <div class="time me-4"><?= $attendance['timeEntered'] ?></div>
                            </button>
                        </h2>
                        <div id="collapse<?= $attendance['userID'] ?>" class="accordion-collapse collapse show" aria-labelledby="heading<?= $attendance['userID'] ?>" data-bs-parent="#accordionExample">
                            <div class="accordion-body">
                                <div class="row py-2 d-flex">
                                    <div class="label-3">Date Entered:</div>
                                    <div class="dataLabel"><?= $attendance['dateEntered'] ?></div>
                                </div>

                                <div class="row py-2 d-flex">
                                    <div class="label-3">Registered:</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>

                <div class="container-btn mx-4 fixed-bottom text-center">
                    <button id="start-scan-btn" class="scan-btn d-flex justify-content-center align-items-center" data-bs-toggle="modal" data-bs-target="#qrScannerModal">
                        <i class='bx bx-scan icon'></i>START SCANNING
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal for Scanner -->
    <div class="modal fade" id="qrScannerModal" tabindex="-1" aria-labelledby="qrScannerModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="qrScannerModalLabel">Scan QR Code</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="qr-data d-flex text-center flex-column py-4 mb-4">
                    <p class="qr-data-label">Event Attendance ID: <span id="eventAttendanceID"><?php echo $eventAttendanceID; ?></span></p>
                    <h4 class="qr-data-label-header mb-4">QR Code User Information</h4>
                    <p class="qr-data-label">User ID: <span id="userID"></span></p>
                    <p class="qr-data-label">Name: <span id="userName"></span></p>
                </div>
                <video id="scanner" width="100%" height="auto"></video>
            </div>
            <div class="pb-4 d-flex justify-content-center">
                <button id="save-attendance-btn" type="button" data-bs-dismiss="modal">Save Attendance</button>
            </div>
        </div>
    </div>
</div>


    <?php
    require_once('../include/js.php');
    ?>

    <script>
        $(document).ready(function() {
            let scanner = new Instascan.Scanner({ video: document.getElementById('scanner') });
            let eventAttendanceID = '<?php echo $eventAttendanceID; ?>';

            $('#qrScannerModal').on('hidden.bs.modal', function () {
                resetModalData();
            });

            $('#start-scan-btn').on('click', function() {
                $('#qrScannerModal').modal('show');
                Instascan.Camera.getCameras().then(function(cameras) {
                    if (cameras.length > 0) {
                        scanner.start(cameras[0]);
                    } else {
                        console.error('No cameras found.');
                        alert('No cameras found.');
                    }
                }).catch(function(e) {
                    console.error(e);
                    alert('Error accessing cameras.');
                });
            });

            $('#save-attendance-btn').on('click', function() {
                scanner.stop();
                let content = $('#scanner').attr('data-content');
                console.log('Scanned: ' + content);
                // Send the scanned data and eventAttendanceID to the server
                $.ajax({
                    type: 'POST',
                    url: '../classes/process_qr.php',
                    data: { data: content, eventAttendanceID: eventAttendanceID }, // Include eventAttendanceID
                    success: function(response) {
                        console.log(response);
                        alert(response);
                    },
                    error: function(xhr, status, error) {
                        console.error(xhr.responseText);
                        alert('Error scanning QR code. Please try again.');
                    }
                });
            });

            scanner.addListener('scan', function(content) {
                console.log('Scanned: ' + content);
                updateModalContent(content);
            });
            
            // Function to update modal content with scanned data
            function updateModalContent(data) {
                // Extract user information from the QR code (assuming it's pipe-separated)
                let userData = data.split('|');
                let userID = userData[0];
                let userName = userData[1] + ' ' + userData[2] + ' ' + userData[3]; // Concatenate first name, middle name, and last name

                // Update modal content with user details
                $('#userID').text(userID);
                $('#userName').text(userName);
                $('#scanner').attr('data-content', data);
            }

            // Function to reset modal data
            function resetModalData() {
                $('#userID').text('');
                $('#userName').text('');
                $('#scanner').attr('data-content', '');
            }
        });
    </script>
</body>
</html>
