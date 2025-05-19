<?php
session_start();
include 'conn.php'; // Ensure this file correctly establishes a database connection

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['process_payment'])) {
    $amount = htmlspecialchars($_POST['amount']);
    $upi_id = htmlspecialchars($_POST['upi_id']);

    $transaction_id = 'TXN' . rand(100000, 999999);

    $_SESSION['transaction_id'] = $transaction_id;
    $_SESSION['amount'] = $amount;
    $_SESSION['upi_id'] = $upi_id; // User-defined UPI ID

    header('Location: confirm_payment.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UPI Payment</title>
    <style>
        /* Background - Zinc 700 */
        body {
            font-family: Arial, sans-serif;
            text-align: center;
            margin-top: 50px;
            background-color: #3f3f46; /* Zinc 700 */
            color: white;
        }

        /* Smooth animation for form container */
        .container {
            opacity: 0;
            transform: translateY(-20px);
            animation: fadeIn 1s forwards;
            display: inline-block;
            text-align: left;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 2px 2px 10px rgba(0, 0, 0, 0.2);
            background: #52525b; /* Zinc 600 */
            width: 350px;
        }

        @keyframes fadeIn {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Input & Button Styles */
        input, button {
            width: 90%;
            padding: 10px;
            margin: 5px 0;
            font-size: 16px;
            border-radius: 5px;
            border: none;
            transition: all 0.3s ease-in-out;
        }

        input {
            background: #27272a; /* Darker zinc */
            color: white;
            border: 1px solid #52525b;
        }

        input:focus {
            border-color: #10b981;
            outline: none;
            box-shadow: 0 0 5px rgba(16, 185, 129, 0.5);
        }

        button {
            background-color: #10b981; /* Emerald */
            color: white;
            cursor: pointer;
            font-weight: bold;
        }

        button:hover {
            background-color: #059669;
            transform: scale(1.05);
        }

        button:active {
            transform: scale(0.95);
        }

        /* Floating Back Button */
        .back-button {
            position: fixed;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            width: 40px;
            height: 40px;
            background: #007BFF;
            color: white;
            border: none;
            border-radius: 50%;
            cursor: pointer;
            font-size: 18px;
            font-weight: bold;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 2px 2px 5px rgba(0,0,0,0.2);
            transition: all 0.3s ease;
        }

        .back-button:hover {
            background: #0056b3;
            transform: translateY(-50%) scale(1.1);
        }

        .back-button:active {
            transform: translateY(-50%) scale(0.95);
        }
    </style>
    <script>
        function goBack() {
            let btn = document.getElementById("backBtn");
            btn.style.transform = "translateY(-50%) scale(0.9)";
            setTimeout(() => {
                window.location.href = "dashboard.php";
            }, 200);
        }
    </script>
</head>
<body>

    <!-- Floating back button -->
    <button id="backBtn" class="back-button" onclick="goBack()">←</button>

    <br><br>
    <h2>Enter Payment Details</h2>
    <div class="container">
        <form method="POST">
            <label>UPI ID:</label>
            <input type="text" name="upi_id" required><br>
            
            <label>Amount:</label>
            <input type="number" name="amount" min="1" required><br>
            <button type="submit" name="process_payment">Generate QR</button>
        </form>
    </div>

</body>
</html>