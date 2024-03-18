<?php
session_start();

// Check if user is logged in as admin

// Your database connection details
$servername = 'localhost';
$username = 'root';
$password = '';
$dbname = 'hospital';

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_division'])) {
        $division_name = $_POST['division_name'];

        // Insert into divisions table
        $insert_division = "INSERT INTO divisions (division_name) VALUES ('$division_name')";
        $conn->query($insert_division);
    }

    // Your code for inserting into areas
    if (isset($_POST['add_area'])) {
        $area_name = $_POST['area_name'];
        $division_id = $_POST['division_id'];
    
        // Check if the provided division_id exists in divisions table
        $check_division = "SELECT * FROM divisions WHERE division_id = '$division_id'";
        $result = $conn->query($check_division);
    
        if ($result->num_rows > 0) {
            // Insert into areas table
            $insert_area = "INSERT INTO areas (area_name, division_id) VALUES ('$area_name', '$division_id')";
            if ($conn->query($insert_area) === TRUE) {
                echo "<script>alert('New area added successfully!');</script>";
            } else {
                echo "Error: " . $insert_area . "<br>" . $conn->error;
            }
        } else {
            echo "Error: Division ID does not exist";
        }
    }
    

    // Adding Hospital based on Area
    // Check if hospital data is provided
    // Check if hospital data is provided
    if (isset($_POST['add_hospital'])) {
        // Retrieve form data
        $hospital_name = $_POST['hospital_name'];
        $area_id = $_POST['area_id'];
        $bed_availability = $_POST['bed_availability'];
        $bed_cost = $_POST['bed_cost'];
        $hospital_location = $_POST['hospital_location'];

        // Retrieve division_id associated with the selected area_id
        $division_id = null;
        $fetch_division = "SELECT division_id FROM areas WHERE area_id = '$area_id'";
        $result_division = $conn->query($fetch_division);

        if ($result_division && $result_division->num_rows > 0) {
            $row_division = $result_division->fetch_assoc();
            $division_id = $row_division['division_id'];

            // Insert the hospital data including division_id and area_id into the hospitals table
            $insert_hospital = "INSERT INTO hospitals (hospital_name, division_id, area_id, bed_availability, bed_cost, hospital_location) VALUES ('$hospital_name', '$division_id', '$area_id', '$bed_availability', '$bed_cost', '$hospital_location')";
        
            if ($conn->query($insert_hospital) === TRUE) {
                echo "Hospital added successfully!";
            } else {
                echo "Error: " . $insert_hospital . "<br>" . $conn->error;
            }
        } else {
            echo "Error: Division for the selected area not found.";
        }
    }


    header("Location: admin_dashboard.php");
    exit();
}

// Delete Division
// Logic for deleting division
// Logic for deleting division
if (isset($_POST['delete_division'])) {
    try {
        $division_id = $_POST['division_id'];

        // Delete associated hospitals linked through areas
        $delete_hospitals = "DELETE hospitals FROM hospitals 
                             JOIN areas ON hospitals.area_id = areas.area_id 
                             WHERE areas.division_id = '$division_id'";
        if ($conn->query($delete_hospitals) === TRUE) {
            // Delete associated areas
            $delete_areas = "DELETE FROM areas WHERE division_id = '$division_id'";
            if ($conn->query($delete_areas) === TRUE) {
                // Delete division itself
                $delete_division = "DELETE FROM divisions WHERE division_id = '$division_id'";
                if ($conn->query($delete_division) === TRUE) {
                    echo "Division deleted successfully!";
                } else {
                    throw new Exception("Error deleting division: " . $conn->error);
                }
            } else {
                throw new Exception("Error deleting associated areas: " . $conn->error);
            }
        } else {
            throw new Exception("Error deleting associated hospitals: " . $conn->error);
        }
    } catch (Exception $e) {
        echo 'Caught exception: ',  $e->getMessage(), "\n";
    }
}


// Delete Area
if (isset($_POST['delete_area'])) {
    $area_id = $_POST['area_id'];

    // Delete associated hospitals
    $delete_hospitals = "DELETE FROM hospitals WHERE area_id = '$area_id'";
    if ($conn->query($delete_hospitals) === TRUE) {
        // If hospitals associated with the area are deleted successfully, proceed to delete the area
        $delete_area = "DELETE FROM areas WHERE area_id = '$area_id'";
        if ($conn->query($delete_area) === TRUE) {
            echo "Area deleted successfully!";
        } else {
            echo "Error deleting area: " . $conn->error;
        }
    } else {
        echo "Error deleting associated hospitals: " . $conn->error;
    }
}

// Delete Hospital
if (isset($_POST['delete_hospital'])) {
    $hospital_id = $_POST['hospital_id'];

    // Delete hospital itself
    $delete_hospital = "DELETE FROM hospitals WHERE hospital_id = '$hospital_id'";
    if ($conn->query($delete_hospital) === TRUE) {
        echo "Hospital deleted successfully!";
    } else {
        echo "Error deleting hospital: " . $conn->error;
    }
}


// Fetch divisions for area form
$fetch_divisions = "SELECT * FROM divisions";
$result = $conn->query($fetch_divisions);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">

    <title>Admin_Add Division,Area,and Hospital</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f5f5f5;
        }
        h1 {
            font-family: Arial, sans-serif;
            font-size: 36px;
            color: #333; /* Change color as desired */
            text-align: center; /* Adjust alignment as needed */
            margin-top: 20px; /* Adjust margins as needed */
        }

        h2 {
            margin-bottom: 10px;
        }

        form {
            background-color: #fff;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        input[type="text"],
        select {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box;
        }

        input[type="submit"] {
            background-color: #3498db;
            color: #fff;
            padding: 10px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        input[type="submit"]:hover {
            background-color: #2980b9;
        }
    </style>
</head>
<body>
    <h1>Admin Dashboard</h1>
    <h2>Add Division</h2>
    <form action="admin_dashboard.php" method="post">
        <input type="text" name="division_name" placeholder="Division Name" required>
        <input type="submit" name="add_division" value="Add Division">
    </form>

    <h2>Add Area related to Division</h2>
    <form action="admin_dashboard.php" method="post">
        <input type="text" name="area_name" placeholder="Area Name" required>
        <select name="division_id" required>
            <?php
            $fetch_divisions = "SELECT division_id, division_name FROM divisions";
            $result = $conn->query($fetch_divisions);

            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<option value='" . $row['division_id'] . "'>" . $row['division_name'] . "</option>";
                }
            }
            ?>
        </select>

        <input type="submit" name="add_area" value="Add Area">
    </form>

    <h2>Add Hospital related to Area</h2>
    <form action="admin_dashboard.php" method="post">
    <input type="text" name="hospital_name" placeholder="Hospital Name" required>

    <!-- Select dropdown for areas -->
    <select name="area_id" required>
        <?php
        // Fetch existing areas from the database
        $fetch_areas = "SELECT area_id, area_name, division_id FROM areas";
        $result_areas = $conn->query($fetch_areas);

        if ($result_areas->num_rows > 0) {
            while ($row_area = $result_areas->fetch_assoc()) {
                echo "<option value='" . $row_area['area_id'] . "' data-division='" . $row_area['division_id'] . "'>" . $row_area['area_name'] . "</option>";
            }
        } else {
            echo "<option value=''>No areas found</option>";
        }
        ?>
    </select>

    <input type="text" name="bed_availability" placeholder="Bed Availability" required>
    <input type="text" name="bed_cost" placeholder="Bed Cost" required>
    <input type="text" name="hospital_location" placeholder="Hospital Location" required>

    <input type="submit" name="add_hospital" value="Add Hospital">
</form>

<h2>Delete Division</h2>
    <form action="admin_dashboard.php" method="post">
        <select name="division_id" required>
            <?php
            // Fetch divisions for deletion
            $fetch_divisions = "SELECT division_id, division_name FROM divisions";
            $result = $conn->query($fetch_divisions);

            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<option value='" . $row['division_id'] . "'>" . $row['division_name'] . "</option>";
                }
            }
            ?>
        </select>
        <input type="submit" name="delete_division" value="Delete Division">
    </form>

<h2>Delete Area</h2>
<form action="admin_dashboard.php" method="post">
    <select name="area_id" required>
        <?php
        $fetch_areas = "SELECT area_id, area_name FROM areas";
        $result_areas = $conn->query($fetch_areas);

        if ($result_areas->num_rows > 0) {
            while ($row_area = $result_areas->fetch_assoc()) {
                echo "<option value='" . $row_area['area_id'] . "'>" . $row_area['area_name'] . "</option>";
            }
        } else {
            echo "<option value=''>No areas found</option>";
        }
        ?>
    </select>
    <input type="submit" name="delete_area" value="Delete Area">
</form>

<h2>Delete Hospital</h2>
<form action="admin_dashboard.php" method="post">
    <select name="hospital_id" required>
        <?php
        $fetch_hospitals = "SELECT hospital_id, hospital_name FROM hospitals";
        $result_hospitals = $conn->query($fetch_hospitals);

        if ($result_hospitals->num_rows > 0) {
            while ($row_hospital = $result_hospitals->fetch_assoc()) {
                echo "<option value='" . $row_hospital['hospital_id'] . "'>" . $row_hospital['hospital_name'] . "</option>";
            }
        } else {
            echo "<option value=''>No hospitals found</option>";
        }
        ?>
    </select>
    <input type="submit" name="delete_hospital" value="Delete Hospital">
</form>


</body>
</html>

<?php
$conn->close();
?>
