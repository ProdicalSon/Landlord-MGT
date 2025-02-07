<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" href="style.css">
    <meta charset="UTF-8">
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCt_lSl8p8Kjs5TZ34QG-F8u3BFelKun3U"></script>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Location</title>
</head>
<body>
    <button id="add-location-btn">Add location</button>
    <div class="search-map">
        <input type="search" placeholder="Search Location">
    </div>
    <div id="map"></div>
    <script>
       

let map;
let marker;

function initMap(latitude, longitude) {
  const userLocation = { lat: latitude, lng: longitude };

  
  map = new google.maps.Map(document.getElementById("map"), {
    center: userLocation,
    zoom: 15,
  });

 
  marker = new google.maps.Marker({
    position: userLocation,
    map: map,
    title: "You are here",
  });
}

document.getElementById("add-location-btn").addEventListener("click", function() {
  if (navigator.geolocation) {
    navigator.geolocation.getCurrentPosition(showPosition, showError);
  } else {
    alert("Geolocation is not supported by this browser.");
  }
});

function showPosition(position) {
  const latitude = position.coords.latitude;
  const longitude = position.coords.longitude;

 
  initMap(latitude, longitude);

  // Optionally, you can send the coordinates to a database
  // saveLocationToDatabase(latitude, longitude);
}

function showError(error) {
  switch (error.code) {
    case error.PERMISSION_DENIED:
      alert("User denied the request for Geolocation.");
      break;
    case error.POSITION_UNAVAILABLE:
      alert("Location information is unavailable.");
      break;
    case error.TIMEOUT:
      alert("The request to get user location timed out.");
      break;
    case error.UNKNOWN_ERROR:
      alert("An unknown error occurred.");
      break;
  }
}
    </script>
</body>
</html>