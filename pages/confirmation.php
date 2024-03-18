<?php
require('db_connect.php');


// Start the session
session_start();


// Check if the user is not logged in, redirect to signin.php
if (!isset($_SESSION['user_id'])) {
    header("Location: signin.php");
    exit();
}


// Retrieve the hospital ID and selected bed from the query parameters
$hospitalId = $_GET['hospital_id'];
$selectedBed = $_GET['selected_bed'];


// Fetch hospital data
$hospitalQuery = $conn->prepare("SELECT hospital_name, hospital_address, bed_cost FROM hospitals WHERE hospital_id = ?");
$hospitalQuery->bind_param("i", $hospitalId);
if ($hospitalQuery->execute()) {
    $hospitalResult = $hospitalQuery->get_result();
    if ($hospitalResult->num_rows > 0) {
        $hospitalData = $hospitalResult->fetch_assoc();
        $hospitalName = $hospitalData['hospital_name'];
        $hospitalAddress = $hospitalData['hospital_address'];
        $bedCost = $hospitalData['bed_cost'];


        // Store bed cost in session
        $_SESSION['bedCost'] = $bedCost;
    } else {
        echo "No hospital data found for ID '$hospitalId'.";
        exit();
    }
} else {
    echo "Error fetching hospital data: " . $conn->error;
    exit();
}
?>


<!DOCTYPE html>
<html lang="en">


<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmation</title>
    <link rel="stylesheet" href="./style/confirmation.css">
</head>


<body>
    <div class="container">
        <nav class="navbar">
            <!-- Your navigation content here -->
        </nav>
        <div class="form-box">
            <h1><?php echo $hospitalName; ?></h1>
            <form>
                <div class="input-group">
                    
                    <div class="input-field">
                        <input type="text" placeholder="Room No" value="<?php echo $selectedBed; ?>" disabled>
                    </div>
                    <div class="input-group">
                        <div class="input-field">
                            <input type="text" placeholder="Bed Cost" value="<?php echo $bedCost; ?>" disabled>
                        </div>
                        <div class="checkbox">
                            <input type="checkbox" id="checkbox">
                            <span id="cb" style="opacity:0.5 ;">Ambulance</span>
                        </div>
                        <div class="input-group">
                            <div class="input-field">
                                <input type="text" placeholder="Service Cost" value="300" disabled>
                            </div>
                        </div>
                        <div class="btn-field">
                            <button type="button" onclick="redirectToReceipt()">Confirm</button>
                        </div>
            </form>
        </div>
        <img src="../images/Untitled-1.png" id="img">
    </div>


    <script>
        function redirectToReceipt() {
            // Construct the URL for receipt.php with the necessary parameters
            var url = "receipt.php?hospital_id=<?php echo $hospitalId; ?>&selected_bed=<?php echo $selectedBed; ?>&bedCost=<?php echo $bedCost; ?>";


            // Redirect the user to the receipt.php page
            window.location.href = url;
        }
    </script>
</body>


</html>