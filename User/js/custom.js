const navBar = document.querySelector("nav"),
       menuBtns = document.querySelectorAll(".menu-icon"),
       overlay = document.querySelector(".overlay");

     menuBtns.forEach((menuBtn) => {
       menuBtn.addEventListener("click", () => {
         navBar.classList.toggle("open");
       });
     });

     overlay.addEventListener("click", () => {
       navBar.classList.remove("open");
     });


  // EVENT STATUS TEXT COLOR CONDITION
    // EVENT STATUS TEXT COLOR CONDITION
// Get all elements with the status class
var statusElements = document.querySelectorAll('.status');

// Iterate over each status element
statusElements.forEach(function(statusElement) {
    // Check the text content of the status element
    var statusText = statusElement.textContent.trim();
    if (statusText === 'Ongoing') {
        // If it's a finished event, add the "finished" class
        statusElement.classList.add('Ongoing');
    } else if (statusText === 'Completed') {
        // If it's a completed event, add the "completed" class
        statusElement.classList.add('completed');
    }
});


    // SHORTEN THE TEXT
    document.addEventListener('DOMContentLoaded', function() {
      const clubDescriptions = document.querySelectorAll('.club-description');
      const maxCharacters = 80;
  
      clubDescriptions.forEach(clubDescription => {
          if (clubDescription.textContent.length > maxCharacters) {
              clubDescription.textContent = clubDescription.textContent.substring(0, maxCharacters) + '...';
          }
      });
  });
  
  // DATE TIME
  $(function() {
    $('input[name="datetimes"]').daterangepicker({
      timePicker: true,
      startDate: moment().startOf('hour'),
      endDate: moment().startOf('hour').add(32, 'hour'),
      locale: {
        format: 'M/DD hh:mm A'
      }
    });
  });

// For QR
// JavaScript function to trigger download
function downloadQR() {
  var qrCodeImg = document.querySelector('.qr-code');
  var downloadLink = document.createElement('a');
  downloadLink.href = qrCodeImg.src;
  downloadLink.download = 'user_qr_code.png';
  downloadLink.click();
}

// JavaScript
document.getElementById('downloadButton').addEventListener('click', function() {
  var qrCodeImage = document.getElementById('qrCodeImage');
  var downloadLink = document.createElement('a');
  downloadLink.href = qrCodeImage.src;
  downloadLink.download = 'qr_code.png';
  document.body.appendChild(downloadLink);
  downloadLink.click();
  document.body.removeChild(downloadLink);
});




