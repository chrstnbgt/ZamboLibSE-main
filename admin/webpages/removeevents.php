<?php
//resume session here to fetch session values
session_start();
/*
    if the user is not logged in, then redirect to the login page,
    this is to prevent users from accessing pages that require
    authentication such as the dashboard
*/
if (!isset($_SESSION['user']) || $_SESSION['user'] != 'admin') {
    header('location: ./index.php');
}
//if the above code is false then the HTML below will be displayed

// Check if the service ID is provided in the URL
if (isset($_GET['id'])) {
    $eventID = $_GET['id'];

    // Include necessary files and classes
    require_once '../classes/events.class.php';

    // Create a new instance of the Service class
    $events = new Events();

    // Call a method to delete the service by ID
    if ($events->delete($eventID)) {
        // Service deleted successfully, redirect to the services page
        header('location: events.php');
        exit();
    } else {
        // An error occurred during deletion
        echo 'Error deleting event.';
        exit();
    }
} else {
    // Service ID is not provided in the URL
    echo 'Invalid request.';
    exit();
}
?>
