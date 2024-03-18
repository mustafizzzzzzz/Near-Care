<!DOCTYPE html>
<html>

<head>
    <title>Find Nearby Hospitals</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
            margin-top: 70px;

        }

        h2 {
            color: #007BFF;
            margin: 20px 0;
        }

        #map-container {
            margin: 20px auto;
            width: 80%;
            max-width: 800px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        #map {
            height: 400px;
            border-radius: 8px 8px 0 0;
        }

        #location {
            width: 70%;
            padding: 10px;
            border: none;
            border-bottom: 2px solid #007BFF;
            font-size: 16px;
        }

        #search-button {
            padding: 10px 20px;
            background-color: #007BFF;
            color: #fff;
            border: none;
            cursor: pointer;
            border-radius: 0 0 8px 8px;
            font-size: 16px;
        }

        #results {
            text-align: left;
            padding: 20px;
        }
    </style>
</head>

<body>
    <h2>Find Nearby Hospitals</h2>
    <div id="map-container">
        <input type="text" id="location" placeholder="Enter location">
        <button id="search-button" onclick="searchHospitals()">Search Hospitals</button>
        <div id="map"></div>
        <div id="results"></div>
    </div>

    <script>
        let map;
        let service;

        function initMap() {
            map = new google.maps.Map(document.getElementById('map'), {
                center: { lat: 23.8103, lng: 90.4125 }, // Centered around Dhaka, Bangladesh
                zoom: 8 // Adjust the zoom level as needed
            });
            const bangladeshBounds = new google.maps.LatLngBounds(
                new google.maps.LatLng(20.7905, 88.9476), // Southwest corner of Bangladesh
                new google.maps.LatLng(26.8785, 92.6266)  // Northeast corner of Bangladesh
            );
            map.fitBounds(bangladeshBounds);

        }

        function searchHospitals() {
            const location = document.getElementById('location').value;

            // Make sure to update the 'location' variable to stay within Bangladesh boundaries

            const request = {
                location: bangladeshBounds.getCenter(), // Use the center of Bangladesh as a reference
                radius: 5000, // Adjust the radius as needed (in meters)
                keyword: 'hospital'
            };

            service = new google.maps.places.PlacesService(map);
            service.nearbySearch(request, showHospitals);
        }


        function showHospitals(results, status) {
            if (status === google.maps.places.PlacesServiceStatus.OK) {
                for (let i = 0; i < results.length; i++) {
                    createMarker(results[i]);
                }
            }
        }

        function createMarker(place) {
            const marker = new google.maps.Marker({
                map: map,
                position: place.geometry.location,
                title: place.name
            });
        }
    </script>

    <script
        src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDGGNGoC0SxbL2la2-JPj9UvKkuCkQXTMM&libraries=places&callback=initMap"
        async defer></script>
</body>
</html>