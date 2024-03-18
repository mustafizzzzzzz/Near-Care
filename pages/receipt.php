<?php
require('db_connect.php');
session_start();

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
// Retrieve session variables
$bedCost = $_SESSION['bedCost'] ?? 0; // Default to 0 if not set
$serviceCost = $_SESSION['serviceCost'] ?? 0; // Default to 0 if not set
$totalCost = $_SESSION['totalCost'] ?? 0; // Default to 0 if not set

// Fixed service charge
$fixedServiceCharge = 300;

// Calculate total cost including the fixed service charge
$totalCost = $bedCost + $fixedServiceCharge;


// Calculate total cost including the fixed service charge
$totalCost = $bedCost+$fixedServiceCharge;
    } else {
        echo "No hospital data found for ID '$hospitalId'.";
        exit();
    }
} else {
    echo "Error fetching hospital data: " . $conn->error;
    exit();
}


//$hospitalName = $_SESSION['hospitalName'] ?? 'Unknown Hospital'; // Default to a default value if not set
?>



<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1" />

		<title>Near Care- Receipt</title>
        <link rel="stylesheet" href="./style/receipt.css">
		<!-- Favicon -->
		<link rel="icon" href="./images/favicon.png" type="image/x-icon" />

		<!-- Invoice styling -->
		<style>
			
			body {
				font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif;
				text-align: center;
				color: #777;
			}

			body h1 {
				font-weight: 300;
				margin-bottom: 0px;
				padding-bottom: 0px;
				color: #000;
			}

			body h3 {
				font-weight: 300;
				margin-top: 10px;
				margin-bottom: 20px;
				font-style: italic;
				color: #555;
			}

			body a {
				color: #06f;
			}

			.invoice-box {
				max-width: 1000px;
				margin: auto;
				padding: 30px;
				border: 1px solid #eee;
				box-shadow: 0 0 10px rgba(0, 0, 0, 0.15);
				font-size: 16px;
				line-height: 24px;
				font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif;
				color: #555;
				position: relative;
				top:80px;
			}

			.invoice-box table {
				width: 100%;
				line-height: inherit;
				text-align: left;
				border-collapse: collapse;
			}

			.invoice-box table td {
				padding: 5px;
				vertical-align: top;
			}

			.invoice-box table tr td:nth-child(2) {
				text-align: right;
			}

			.invoice-box table tr.top table td {
				padding-bottom: 20px;
			}

			.invoice-box table tr.top table td.title {
				font-size: 35px;
				line-height: 45px;
				color: #333;
			}

			.invoice-box table tr.information table td {
				padding-bottom: 40px;
			}

			.invoice-box table tr.heading td {
				background: #eee;
				border-bottom: 1px solid #ddd;
				font-weight: bold;
			}

			.invoice-box table tr.details td {
				padding-bottom: 20px;
			}

			.invoice-box table tr.item td {
				border-bottom: 1px solid #eee;
			}

			.invoice-box table tr.item.last td {
				border-bottom: none;
			}

			.invoice-box table tr.total td:nth-child(2) {
				border-top: 2px solid #eee;
				font-weight: bold;
			}

			@media only screen and (max-width: 600px) {
				.invoice-box table tr.top table td {
					width: 100%;
					display: block;
					text-align: center;
				}

				.invoice-box table tr.information table td {
					width: 100%;
					display: block;
					text-align: center;
				}
			}
            .print-button {
            display: inline-block;
            padding: 10px 40px;
            font-size: 16px;
            text-align: center;
            text-decoration: none;
            color: #fff;
            background-color: #3498db;
            border-radius: 5px;
            border: none;
            transition: background-color 0.3s ease;
            cursor: pointer;
			position: relative;
			top:100px;
        }

        .print-button:hover {
            background-color: #2980b9;
        }
		</style>
	</head>

	<body>
	<div class="container">
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
                <li><a href="hospitals.php" >Hospitals</a></li>
                <li><a href="confirmation.php">Confirmation</a></li>
                <li><a href="receipt.php" style="color:rgb(75, 122, 242);">Receipt</a></li>
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
		<div class="invoice-box">
			<table>
				<tr class="top">
					<td colspan="2">
						<table>
							<tr>
								<td class="title">
								
								<?php echo $hospitalName; ?><br>

									<!-- <img src="../images/nearcarelogo.png" alt="Company logo" style="width: 100%; max-width: 250px" /> -->
								</td>

								<td>
									<br/>
									NearCare@gmail.com
								</td>
							</tr>
						</table>
					</td>
				</tr>

				<tr class="information">
					<td colspan="2">
						<table>
							<tr>
								<td>
						
                                <?php echo $hospitalAddress; ?><br />
								</td>
							</tr>
						</table>
					</td>
				</tr>
                <tr class="heading">
					<td>Room</td>

					<td>Bed no</td>
				</tr>

				<tr class="details">
					<td>Bed</td>
				<td><?php echo $selectedBed; ?></td>
				</tr>
				<tr class="heading">
					<td>Cost</td>

					<td>Price</td>
				</tr>

                <tr class="item">
    				<td>Bed Cost</td>
    				<td><?php echo $bedCost; ?></td>
				</tr>
				<tr class="item">
					<td>Service Charge</td>

					<td><?php echo $fixedServiceCharge; ?></td>
				</tr>


				<tr class="total">
					<td></td>

					<td>Total: <?php echo $totalCost; ?></td>
				</tr>
			</table>
		</div>
		</div>
		</div>
		<script>
        function printInvoice() {
            window.print();
        }
    </script>
    <!-- Button to trigger print -->
    <button onclick="printInvoice()" class="print-button">Print receipt</button>
	</body>
</html>
