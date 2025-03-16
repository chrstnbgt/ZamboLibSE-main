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

    // Update the eventAttendanceID span element when the document is ready
    $('#eventAttendanceID').text(eventAttendanceID);
});
