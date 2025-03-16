<?php
// Database connection
$host = 'localhost';
$username = 'root';
$password = '';
$database = 'dbzambocitylib2';

try {
    $conn = new PDO("mysql:host=$host;dbname=$database", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Get the scanned data and eventAttendanceID from the frontend
    $data = isset($_POST['data']) ? $_POST['data'] : null;
    $eventAttendanceID = isset($_POST['eventAttendanceID']) ? $_POST['eventAttendanceID'] : null;

    if ($data && $eventAttendanceID) {
        // Extract user information from the QR code (assuming it's pipe-separated)
        list($userID, $userLastName, $userFirstName, $userMiddleName) = explode('|', $data);

        // Check if the user already exists in the event_attendanceuser table
        $stmt = $conn->prepare("SELECT COUNT(*) FROM event_attendanceuser WHERE userID = :userID AND eventAttendanceID = :eventAttendanceID");
        $stmt->bindParam(':userID', $userID);
        $stmt->bindParam(':eventAttendanceID', $eventAttendanceID);
        $stmt->execute();
        $count = $stmt->fetchColumn();

        if ($count == 0) {
            // Insert data into the database if the user does not exist
            $sql = "INSERT INTO event_attendanceuser (userID, eventAttendanceID) 
                    VALUES(:userID, :eventAttendanceID)";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':userID', $userID);
            $stmt->bindParam(':eventAttendanceID', $eventAttendanceID);
            $stmt->execute();
            echo "Attendance saved successfully!";
        } else {
            echo "Attendance already exists for this user!";
        }
    } else {
        echo "No data detected!";
    }

} catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
}

$conn = null;
?>
