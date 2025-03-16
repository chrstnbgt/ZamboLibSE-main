<?php
session_start();
require_once '../classes/events.class.php';
require_once '../classes/database.php';

if (!isset($_SESSION['user']) || $_SESSION['user'] != 'librarian') {
    header('location: ./index.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $eventID = $_POST['eventID'];
    $userID = $_POST['userID'];
    $ecName = $_POST['ecName'];
    $ecImage = $_POST['certificateImageData'];

 // Decode the image data
$ecImage = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $ecImage));

// Generate a unique file name
$fileName = 'certificate_' . uniqid('', true) . '.png'; // Added more entropy for uniqueness

// Define the upload directory
$uploadDir = __DIR__ . '/../../certificate_images/'; // Adjust the path as needed

// Ensure the directory exists
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true); // Create the directory if it doesn't exist
}

// Save the image to the server
$filePath = $uploadDir . $fileName;
file_put_contents($filePath, $ecImage);

// Prepare to save to the database
$db = new Database();
$conn = $db->connect();

// Store the relative path in the database
$relativePath = 'certificate_images/' . $fileName; // Relative to the project root

$stmt = $conn->prepare('INSERT INTO event_certificate (eventID, userID, ecName, ecImage) VALUES (:eventID, :userID, :ecName, :ecImage)');
$stmt->bindParam(':eventID', $eventID);
$stmt->bindParam(':userID', $userID);
$stmt->bindParam(':ecName', $ecName);
$stmt->bindParam(':ecImage', $relativePath); // Store the relative path

// Execute the query
$stmt->execute();

    if ($stmt->execute()) {
        echo '<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>';
        echo '<script>
            Swal.fire({
                title: "Certificate uploaded successfully!",
                text: "",
                icon: "success"
            }).then(() => {
                window.location.href = "event-overview.php?librarianID=' . $_SESSION['librarianID'] . '&eventID=' . $eventID . '";
            });
        </script>';
    } else {
        echo '<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>';
        echo '<script>
            Swal.fire({
                title: "Oops!",
                text: "Failed to upload certificate.",
                icon: "error"
            }).then(() => {
                window.history.back();
            });
        </script>';
    }
}
?>
