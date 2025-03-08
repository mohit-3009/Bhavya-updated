<?php
$servername = "localhost";
$username = "root";
$password = "";
$database = "project1";

// Create a connection
$conn = mysqli_connect($servername, $username, $password, $database);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Check if the ID is passed in the URL
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Fetch the data for the specific entry
    $sql = "SELECT * FROM admin_maintenance WHERE id = $id";
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
    } else {
        echo "No record found.";
        exit;
    }
}

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get updated values from the form
    $payment_duration = $_POST['payment_duration'];
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];
    $maintenance_amount = $_POST['maintenance_amount'];

    // Calculate the total amount before tax and discount (without tax)
    $total_amount = 0;

    // Calculate number of months between start and end dates
    $start_date_obj = new DateTime($start_date);
    $end_date_obj = new DateTime($end_date);
    $interval = $start_date_obj->diff($end_date_obj);
    $months = $interval->m + ($interval->y * 12); // Convert years to months and add remaining months

    // Calculate total amount
    $total_amount = $months * $maintenance_amount;

    // Update the record in the database
    $update_sql = "UPDATE admin_maintenance 
                   SET payment_duration = '$payment_duration', start_date = '$start_date', 
                       end_date = '$end_date', maintenance_amount = '$maintenance_amount', 
                       total_amount = '$total_amount' 
                   WHERE id = $id";

    if (mysqli_query($conn, $update_sql)) {
        echo "Record updated successfully.";
        header("Location: m_admin.php"); // Redirect to the maintenance history page
    } else {
        echo "Error updating record: " . mysqli_error($conn);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Maintenance Record</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            margin: 0;
            padding: 0;
            background: linear-gradient(135deg, #f4f7f6, #ffffff);
            color: #333;
            box-sizing: border-box;
        }

        .header {
            background: #3498db;
            color: white;
            padding: 10px;
            text-align: center;
            font-size: 20px;
            font-weight: 70;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
            border-radius: 8px 8px 0 0;
            margin-bottom: 40px;
        }

        .main-content {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 80vh;
            padding: 0 20px;
        }

        .form-container {
            background: #ffffff;
            border-radius: 10px;
            padding: 40px;
            box-shadow: 0 12px 24px rgba(0, 0, 0, 0.1);
            max-width: 500px;
            width: 100%;
            transform: translateY(-50px);
            transition: transform 0.3s ease-in-out;
        }

        .form-container:hover {
            transform: translateY(0);
        }

        .form-container h2 {
            text-align: center;
            color: #3498db;
            font-size: 28px;
            margin-bottom: 30px;
            font-weight: 600;
        }

        .form-container label {
            font-weight: bold;
            margin-bottom: 8px;
            display: block;
            color: #555;
        }

        .form-container input,
        .form-container select,
        .form-container button {
            width: 100%;
            padding: 14px;
            margin-bottom: 20px;
            border-radius: 10px;
            border: 1px solid #ddd;
            font-size: 16px;
            box-sizing: border-box;
            background-color: #f9f9f9;
            transition: all 0.3s ease;
        }

        .form-container input[type="number"] {
            -moz-appearance: textfield;
        }

        .form-container input:focus,
        .form-container select:focus {
            border-color: #3498db;
            background-color: #f3faff;
            outline: none;
        }

        .form-container button {
            background-color: #3498db;
            color: white;
            cursor: pointer;
            border: none;
            transition: background-color 0.3s ease;
            font-weight: 500;
            width:100%;
        }

        .form-container button:hover {
            background-color: #2980b9;
        }

        .form-container button:active {
            background-color: #1c6fa0;
        }

        .form-container input[readonly] {
            background-color: #f4f7f6;
        }

        .button-container {
            display: flex;
            justify-content: space-between;
            gap: 10px;
        }

        .back-button, .update-button {
            background-color: #2ecc71;
            color: white;
            padding: 12px 25px;
            border-radius: 5px;
            text-decoration: none;
            font-size: 16px;
            text-align: center;
            transition: background-color 0.3s ease;
            width: 48%;
        }

        .update-button {
            background-color: #3498db;
        }

        .back-button:hover, .update-button:hover {
            background-color: #27ae60;
        }

        .back-button:active, .update-button:active {
            background-color: #1e8449;
        }

        /* Adding media queries for better mobile responsiveness */
        @media (max-width: 768px) {
            .form-container {
                padding: 30px 20px;
            }

            .form-container h2 {
                font-size: 24px;
            }

            .main-content {
                padding: 20px;
            }
        }
    </style>
</head>
<body>

    <div class="header">
        <h1>Edit Maintenance Record</h1>
    </div>

    <div class="main-content">
        <div class="form-container">
            <form method="POST" onsubmit="return validateMaintenanceAmount()">
                <div class="input-container">
                    <label for="payment_duration">Payment Duration:</label>
                    <select name="payment_duration" id="payment_duration" required onchange="updateTotalAmount()">
                        <option value="1 month" <?php echo ($row['payment_duration'] == '1 month') ? 'selected' : ''; ?>>1 month</option>
                        <option value="3 months" <?php echo ($row['payment_duration'] == '3 months') ? 'selected' : ''; ?>>3 months</option>
                        <option value="6 months" <?php echo ($row['payment_duration'] == '6 months') ? 'selected' : ''; ?>>6 months</option>
                        <option value="1 year" <?php echo ($row['payment_duration'] == '1 year') ? 'selected' : ''; ?>>1 year</option>
                    </select>
                </div>

                <div class="input-container">
                    <label for="start_date">Start Date:</label>
                    <input type="date" name="start_date" value="<?php echo $row['start_date']; ?>" required onchange="updateTotalAmount()">
                </div>

                <div class="input-container">
                    <label for="end_date">End Date:</label>
                    <input type="date" name="end_date" value="<?php echo $row['end_date']; ?>" required onchange="updateTotalAmount()">
                </div>

                <div class="input-container">
                    <label for="maintenance_amount">Maintenance Amount (Per Month):</label>
                    <input type="number" name="maintenance_amount" id="maintenance_amount" value="<?php echo $row['maintenance_amount']; ?>" required onchange="updateTotalAmount()" min="0" step="0.01">
                </div>

                <div class="input-container">
                    <label for="total_amount">Total Amount:</label>
                    <input type="number" name="total_amount" id="total_amount" value="<?php echo $row['total_amount']; ?>" readonly>
                </div>

                <div class="button-container">
                    <button type="submit" class="update-button">Update Record</button>
                    <a href="m_admin.php"><button type="button" class="back-button">Back</button></a>
                </div>
            </form>
        </div>
    </div>

    <script>
        function updateTotalAmount() {
            var startDate = document.getElementById('start_date').value;
            var endDate = document.getElementById('end_date').value;
            var maintenanceAmount = document.getElementById('maintenance_amount').value;

            if (startDate && endDate && maintenanceAmount) {
                var startDateObj = new Date(startDate);
                var endDateObj = new Date(endDate);

                if (startDateObj > endDateObj) {
                    alert("Start date should be earlier than the end date.");
                    return;
                }

                var monthDifference = (endDateObj.getFullYear() - startDateObj.getFullYear()) * 12 + endDateObj.getMonth() - startDateObj.getMonth();
                if (endDateObj.getDate() < startDateObj.getDate()) {
                    monthDifference--;
                }

                var totalAmount = monthDifference * maintenanceAmount;
                document.getElementById('total_amount').value = totalAmount.toFixed(2);
            }
        }

        function validateMaintenanceAmount() {
            var maintenanceAmount = document.getElementById('maintenance_amount').value;

            if (maintenanceAmount < 0) {
                alert("Maintenance amount cannot be negative.");
                return false; // Prevent form submission
            }

            return true; // Allow form submission
        }

        window.onload = updateTotalAmount;
    </script>
</body>
</html>
