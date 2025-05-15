<?php
require_once '../config/database.php';
require_once '../includes/auth.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $latitude = $_POST['latitude'];
    $longitude = $_POST['longitude'];
    
    // Reverse geocoding to get city name
    $api_url = "https://nominatim.openstreetmap.org/reverse?format=json&lat=$latitude&lon=$longitude";
    $response = file_get_contents($api_url);
    $data = json_decode($response, true);
    
    $city = $data['address']['city'] ?? $data['address']['town'] ?? $data['address']['village'] ?? 'Unknown';
    $region = $data['address']['state'] ?? 'Unknown';
    $country = $data['address']['country'] ?? 'Unknown';
    
    $stmt = $pdo->prepare("UPDATE mahasiswa SET latitude=?, longitude=?, city=?, region=?, country=? WHERE user_id=?");
    $stmt->execute([$latitude, $longitude, $city, $region, $country, $_SESSION['user_id']]);
    
    echo json_encode([
        'success' => true,
        'redirect' => 'edit_profile.php',
        'city' => $city,
        'region' => $region,
        'country' => $country
    ]);
    exit();
}

// Get existing location data
$stmt = $pdo->prepare("SELECT latitude, longitude, city, region, country FROM mahasiswa WHERE user_id=?");
$stmt->execute([$_SESSION['user_id']]);
$location = $stmt->fetch();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Update Location</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
    <style>
        #map { height: 500px; }
        .location-info { margin: 20px 0; padding: 15px; background: #f8f9fa; border-radius: 5px; }
    </style>
</head>
<body>
    <?php include '../includes/header.php'; ?>
    
    <div class="container">
        <h1>Update Your Location</h1>
        
        <div class="location-info">
            <?php if ($location && $location['city']): ?>
                <h3>Current Location:</h3>
                <p><?= htmlspecialchars($location['city']) ?>, <?= htmlspecialchars($location['region']) ?>, <?= htmlspecialchars($location['country']) ?></p>
                <p>Coordinates: <?= $location['latitude'] ?>, <?= $location['longitude'] ?></p>
            <?php else: ?>
                <p>No location data available yet.</p>
            <?php endif; ?>
        </div>
        
        <button id="get-location" class="btn">Detect My Current Location</button>
        
        <div id="map"></div>
        
        <div id="new-location" style="display:none;">
            <h3>Detected Location:</h3>
            <p id="location-text"></p>
            <button id="save-location" class="btn">Save This Location</button>
        </div>
    </div>

    <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
    <script>
        let map, marker;
        const locationInfo = <?= json_encode($location) ?>;

        // Initialize map
        if (locationInfo && locationInfo.latitude) {
            initMap(locationInfo.latitude, locationInfo.longitude);
        } else {
            initMap(-6.2088, 106.8456); // Default to Jakarta
        }

        function initMap(lat, lng) {
            map = L.map('map').setView([lat, lng], 13);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
            }).addTo(map);
            
            if (locationInfo && locationInfo.latitude) {
                marker = L.marker([lat, lng]).addTo(map)
                    .bindPopup("Your saved location");
            }
        }

        document.getElementById('get-location').addEventListener('click', () => {
            if (!navigator.geolocation) {
                alert("Geolocation is not supported by your browser");
                return;
            }

            navigator.geolocation.getCurrentPosition(
                position => {
                    const lat = position.coords.latitude;
                    const lng = position.coords.longitude;
                    
                    // Update map
                    map.setView([lat, lng], 15);
                    if (marker) map.removeLayer(marker);
                    marker = L.marker([lat, lng]).addTo(map)
                        .bindPopup("Your current location")
                        .openPopup();
                    
                    // Get city name
                    fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}`)
                        .then(response => response.json())
                        .then(data => {
                            const city = data.address.city || data.address.town || data.address.village || 'Unknown';
                            const region = data.address.state || 'Unknown';
                            const country = data.address.country || 'Unknown';
                            
                            document.getElementById('location-text').textContent = 
                                `${city}, ${region}, ${country}`;
                            document.getElementById('new-location').style.display = 'block';
                            
                            // Save data for later
                            window.currentLocation = { lat, lng, city, region, country };
                        });
                },
                error => {
                    alert("Error getting location: " + error.message);
                }
            );
        });

        document.getElementById('save-location').addEventListener('click', () => {
            const { lat, lng, city, region, country } = window.currentLocation;
            
            fetch('location.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `latitude=${lat}&longitude=${lng}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert("Location saved successfully!");
                    window.location.href = data.redirect || 'user/edit_profile.php';
                } else {
                    alert("Error saving location");
                }
            });
        });
    </script>
</body>
</html>