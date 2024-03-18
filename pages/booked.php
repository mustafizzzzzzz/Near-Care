<?php
// Database connection
$host = 'localhost';
$dbname = 'hospital';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}

// Fetch areas from the database
$statementAreas = $pdo->query("SELECT area_id, area_name FROM areas");
$areaNames = $statementAreas->fetchAll(PDO::FETCH_ASSOC);

// Fetch hospitals without filtering by area initially
$statementHospitals = $pdo->query("SELECT hospital_id, hospital_name, area_id, bed_cost, hospital_address FROM hospitals");
$hospitalsData = $statementHospitals->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve selected hospital name from the form
    $hospitalName = $_POST['hospital_name'];

    // Use prepared statements to prevent SQL injection
    $stmtHospital = $pdo->prepare("SELECT hospital_id, hospital_address, bed_cost FROM hospitals WHERE LOWER(TRIM(hospital_name)) = LOWER(:hospital_name)");
    $stmtHospital->bindParam(':hospital_name', $hospitalName);
    $stmtHospital->execute();
    $hospitalDetails = $stmtHospital->fetch(PDO::FETCH_ASSOC);

    if ($hospitalDetails) {
        $hospitalId = $hospitalDetails['hospital_id'];

        // Fetch available beds for the selected hospital
        $stmtBeds = $pdo->prepare("SELECT bed_id FROM bed_availability WHERE hospital_id = :hospital_id AND is_available = true");
        $stmtBeds->bindParam(':hospital_id', $hospitalId);
        $stmtBeds->execute();
        $availableBeds = $stmtBeds->fetchAll(PDO::FETCH_ASSOC);

        if ($availableBeds) {
            // Display the available beds
            echo "<h3>Available Beds:</h3>";
            echo "<ul>";
            foreach ($availableBeds as $bed) {
                echo "<li>Bed ID: " . $bed['bed_id'] . "</li>";
            }
            echo "</ul>";
        } else {
            echo "No available beds found for the hospital.";
        }
    } else {
        echo "Hospital details not found.";
    }
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Near Care-Hospital Bed Details</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="./style/booked.css">
</head>

<body>
    <div class="container">
        <h2>Book a Bed</h2>
        <form id="booking-form" method="post" action="receipt.php">
            <div class="form-group">
                <label for="patient-name">Patient Name:</label>
                <input type="text" id="patient-name" name="patient-name" required>
            </div>
            <div class="form-group">
                <label for="age">Age:</label>
                <input type="number" id="age" name="age" required>
            </div>
            <div class="form-group">
                <label for="disease">Disease:</label>
                <input type="text" id="disease" name="disease" required>
            </div>
            <div class="form-group">
                <label for="area">Area:</label>
                <select id="area" name="area" required>
                    <option value="">--Select an area--</option>
                    <?php foreach ($areaNames as $area) : ?>
                        <option value="<?php echo htmlspecialchars($area['area_id']); ?>">
                            <?php echo htmlspecialchars($area['area_name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="hospital_name">Hospital Name:</label>
                <select id="hospital_name" name="hospital_name" required>
                    <option value="">--Select a hospital--</option>
                    <?php foreach ($hospitalsData as $hospital) : ?>
                        <option value="<?php echo htmlspecialchars($hospital['hospital_name']); ?>">
                            <?php echo htmlspecialchars($hospital['hospital_name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <input type="hidden" id="hospital_id" name="hospital_id">
            </div>

            <div class="form-group">
                <label for="available_beds">Available Beds:</label>
                <select id="available_beds" name="available_beds" required>
                    <!-- Available beds will be dynamically populated here -->
                </select>
            </div>

            <div class="form-group">
                <label for="booking-date">Booking Date:</label>
                <input type="date" id="booking-date" name="booking-date" required>
            </div>

            <button type="submit" id="book-button"><a href="preceipt.php?hospital_id=<?php echo $hospitalId; ?>&selected_bed=<?php echo $selectedBed; ?>">Book Bed</a></button>
        </form>
    </div>

    <script>
        var ageInput = document.getElementById("age");

        ageInput.addEventListener("input", function () {
            var ageValue = parseInt(ageInput.value);
            if (ageValue < 0) {
                ageInput.value = "";
            }
        });

        document.getElementById('area').addEventListener('change', function () {
            var selectedArea = this.value;
            var hospitalsData = <?php echo json_encode($hospitalsData); ?>;
            var hospitalsSelect = document.getElementById('hospital_name');

            console.log("Selected Area ID:", selectedArea);

            hospitalsSelect.innerHTML = '<option value="">--Select a hospital--</option>';

            hospitalsData.forEach(function (hospital) {
                if (hospital.area_id == selectedArea) {
                    var option = document.createElement('option');
                    option.value = hospital.hospital_name;
                    option.textContent = hospital.hospital_name;
                    hospitalsSelect.appendChild(option);
                }
            });
        });

        document.getElementById('booking-form').addEventListener('submit', function () {
    var selectedHospitalId = document.getElementById('hospital_name').value;
    // Set the selected hospital_id in the hidden field
    document.getElementById('hospital_id').value = selectedHospitalId;

    if (!selectedHospitalId) {
        alert('Please select a hospital.');
        event.preventDefault(); // Prevent form submission if hospital is not selected
    }
});

        // Dynamically populate available beds based on the selected hospital
        document.getElementById('hospital_name').addEventListener('change', function () {
            var selectedHospital = this.value;
            var bedsSelect = document.getElementById('available_beds');

            fetch('get_available_beds.php?hospital_name=' + selectedHospital)
                .then(response => response.json())
                .then(data => {
                    bedsSelect.innerHTML = ''; // Clear the bed options

                    if (data.availableBeds && data.availableBeds.length > 0) {
                        data.availableBeds.forEach(bed => {
                            var option = document.createElement('option');
                            option.value = bed.bed_id;
                            option.textContent = 'Bed ID: ' + bed.bed_id;
                            bedsSelect.appendChild(option);
                        });
                    } else {
                        var option = document.createElement('option');
                        option.textContent = 'No available beds';
                        bedsSelect.appendChild(option);
                    }
                })
                .catch(error => {
                    console.error('Error fetching available beds:', error);
                });
        });
    </script>
</body>

</html>
