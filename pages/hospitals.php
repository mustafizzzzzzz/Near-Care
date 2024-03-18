<?php
require('db_connect.php');

// Start the session
session_start();
// Check if the user is not logged in, redirect to signin.php
if (!isset($_SESSION['user_id'])) {
    header("Location: signin.php");
    exit();
}

// Function to fetch divisions from the database
function getDivisions($conn) {
    $divisions = array();

    $sql = "SELECT * FROM divisions";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $divisions[] = $row;
        }
    }

    return $divisions;
}

// Function to fetch areas based on a division from the database
function getAreas($conn, $divisionId) {
    $areas = array();

    $sql = "SELECT * FROM areas WHERE division_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $divisionId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $areas[] = $row;
        }
    }

    return $areas;
}

// Function to fetch hospitals based on a division and area from the database
// Function to fetch hospitals based on a division and area from the database
function getHospitals($conn, $divisionId, $areaId) {
    $hospitals = array();

    $sql = "SELECT hospital_name FROM hospitals WHERE division_id = ? AND area_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $divisionId, $areaId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $hospitals[] = $row['hospital_name'];
        }
    }

    return $hospitals;
}


// Fetch divisions from the database
$divisions = getDivisions($conn);

// Get the selected division and area from the client
$division = isset($_GET['division']) ? $_GET['division'] : null;
$area = isset($_GET['area']) ? $_GET['area'] : null;

// Fetch areas if a division is selected
$areas = array();
if ($division !== null) {
    $divisionId = 0;
    // Find the division ID based on the selected division name
    foreach ($divisions as $div) {
        if ($div['division_name'] === $division) {
            $divisionId = $div['division_id'];
            break;
        }
    }

    // Fetch areas based on the selected division
    $areas = getAreas($conn, $divisionId);
}
// Fetch hospitals if both division and area are selected
$hospitals = array();
if ($division !== null && $area !== null) {
    $areaId = 0;
    // Find the area ID based on the selected area name and division ID
    $sql = "SELECT area_id FROM areas WHERE division_id = ? AND area_name = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("is", $divisionId, $area);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $areaId = $row['area_id'];
    }

    // Fetch hospitals based on the selected division and area
    $hospitals = getHospitals($conn, $divisionId, $areaId);
}

// Close the database connection
$conn->close();
if (!empty($hospitals)) : ?>
    <div class="hospital-list">
        <h3>Hospitals in selected area:</h3>
        <ul>
            <?php foreach ($hospitals as $hospital) : ?>
                <li><a href="bed.php?division=<?php echo urlencode($division); ?>&area=<?php echo urlencode($area); ?>&hospital=<?php echo urlencode($hospital); ?>"><?php echo $hospital; ?></a></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<!DOCTYPE html>
<html lang="en">

<head>
<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Near Care-Hospitals Page</title>
    <link rel="stylesheet" href="./style/hospitals.css">
</head>

<body>
    <nav class="navbar">
        <div class="head">
            <a href="index.php">
                <div id="circle"></div>
                <img src="../images/+.png" id="logo">
                <span id="cn">Near Care</span>
            </a>
        </div>
        <div class="menu-toggle" id="menu-toggle">
            <div class="bar"></div>
            <div class="bar"></div>
            <div class="bar"></div>
        </div>
        <ul class="nav-links" id="nav-links">
            <li><a href="index.php">Home</a></li>
            <li><a href="hospitals.php" style="color:rgb(75, 122, 242);">Hospitals</a></li>
            <!-- <li><a href="confirmation.php">Confirmation</a></li> -->
            <li><a href="receipt.php">Receipt</a></li>
            <?php
            // Check if the user is logged in
            if (isset($_SESSION['user_id'])) {
                echo '<li id="log"><a href="logout.php">Sign Out</a></li>';
            } else {
                echo '<li id="log"><a href="signin.php">Sign In</a></li>';
                echo '<li id="sign"><a href="signup.php">Sign Up</a></li>';
            }
            ?>
        </ul>
    </nav>

    <main>
        <div class="division">
            <?php foreach ($divisions as $div) : ?>
                <a href="?division=<?php echo urlencode($div['division_name']); ?>"><?php echo $div['division_name']; ?></a>
            <?php endforeach; ?>
        </div>

        <?php if (!empty($areas)) : ?>
            <div class="area" id="<?php echo strtolower($division); ?>" style="display: none;">
                <label for="<?php echo strtolower($division); ?>Areas">Select Area in <?php echo $division; ?>:</label>
                <select id="<?php echo strtolower($division); ?>Areas" onchange="showHospitals(this.value)">
                    <?php foreach ($areas as $area) : ?>
                        <option value="<?php echo $area['area_name']; ?>"><?php echo $area['area_name']; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        <?php endif; ?>

    <div class="hospital-list">
        <ul id="hospitalUl">
            
        </ul>
    </div>


    </main>
    
      <script>
function showAreas(division) {
    // Hide all areas
    const areas = document.querySelectorAll('.area');
    areas.forEach(area => area.style.display = 'none');

    // Show the selected division's areas
    const selectedArea = document.getElementById(`${division.toLowerCase()}`);
    if (selectedArea) {
        selectedArea.style.display = 'block';
    }
}
function showHospitals(area) {
    const hospitalUl = document.getElementById('hospitalUl');

    // Check if the element is found
    if (!hospitalUl) {
        console.error('Error: hospitalUl element not found.');
        return;
    }

    // Clear previous list
    hospitalUl.innerHTML = '';

    // Get the selected division
    const division = "<?php echo isset($_GET['division']) ? $_GET['division'] : ''; ?>";

    // Use AJAX to fetch hospitals from the server
    $.ajax({
        url: 'fetch_hospitals.php',
        type: 'POST',
        data: {
            division: division,
            area: area
        },
        success: function (response) {
            console.log('Response from fetch_hospitals.php:', response);

            try {
                const hospitals = JSON.parse(response);

                const uniqueHospitals = [...new Set(hospitals)]; // Filter unique hospitals

                if (uniqueHospitals.length > 0) {
                    uniqueHospitals.forEach(hospital => {
                        const li = document.createElement('li');
                        const a = document.createElement('a');
                        a.href = `bed.php?division=${encodeURIComponent(division)}&area=${encodeURIComponent(area)}&hospital=${encodeURIComponent(hospital)}`;
                        a.textContent = hospital;
                        li.appendChild(a);
                        hospitalUl.appendChild(li);
                    });
                }
            } catch (error) {
                console.error('Error parsing JSON:', error);
            }
        },
        error: function (error) {
            console.error('Error fetching hospitals:', error);
        }
    });
}

// Event listener for division selection
document.addEventListener('DOMContentLoaded', function () {
    const division = "<?php echo $division; ?>";
    if (division !== "") {
        showAreas(division);
    }
});

    </script>
</body>

