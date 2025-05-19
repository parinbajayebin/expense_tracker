<?php
session_start();
include 'conn.php'; // Database connection

$transaction_id = $_SESSION['transaction_id'];
$email = $_SESSION['email'];
$mobile = $_SESSION['mono'];
$amount = $_SESSION['amount']; // Fixed session variable name
$user_id = $_SESSION['userid'];

$_SESSION['otp'] = 1234; // Static OTP for testing; should be dynamically generated in production

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['verify_otp'])) {
    $entered_otp = $_POST['otp'];
    if ($entered_otp == $_SESSION['otp']) {
        $date = date('Y-m-d H:i:s');

        // Insert transaction into the upi_transaction table
        $stmt = $conn->prepare("INSERT INTO upi_transaction (transaction_id, user_id, amount, date) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("sids", $transaction_id, $user_id, $amount, $date);

        if ($stmt->execute()) {
            // Insert into the expense table
            $expense_stmt = $conn->prepare("INSERT INTO expense (user_id, amount, cat_types, date, description) VALUES (?, ?, ?, ?, ?)");
            $cat_type = "Miscellaneous";
            $description = "UPI";
            $expense_stmt->bind_param("idsss", $user_id, $amount, $cat_type, $date, $description);
            $expense_stmt->execute();
            $expense_stmt->close();

            echo "<script>alert('Payment Successful! Transaction ID: $transaction_id'); window.location.href='dashboard.php';</script>";
        } else {
            echo "<script>alert('Transaction failed! Try again.');</script>";
        }
        $stmt->close();
    } else {
        echo "<script>alert('Invalid OTP! Please try again.');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirm Payment</title>
    <style>
        /* Background - Zinc 700 */
        body {
            font-family: Arial, sans-serif;
            text-align: center;
            margin-top: 50px;
            background-color: #3f3f46; /* Zinc 700 */
            color: white;
        }

        /* Smooth animation for OTP section */
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

        /* Countdown Timer */
        .countdown {
            font-size: 20px;
            color: red;
            margin-bottom: 10px;
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

        /* QR Code Styling */
        .qr-container img {
            width: 150px;
            height: 150px;
            border-radius: 5px;
            margin: 10px 0;
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

        let countdown = 30;
        function startCountdown() {
            let timer = setInterval(function () {
                document.getElementById("countdown").innerText = countdown;
                countdown--;
                if (countdown < 0) {
                    clearInterval(timer);
                    document.getElementById("otp-section").innerHTML = "<p style='color:red;'>Time expired! Please refresh to retry.</p>";
                }
            }, 1000);
        }
        window.onload = startCountdown;
    </script>
</head>
<body>

    <!-- Floating back button -->
    <button id="backBtn" class="back-button" onclick="goBack()">←</button>

    <br><br>
    <h2>Confirm Payment</h2>
    <p><b>Transaction ID:</b> <?php echo $transaction_id; ?></p>
    <p><b>Amount:</b> ₹<?php echo number_format($amount, 2); ?></p>

    <div class="qr-container">
        <img src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=upi://pay?pa=your_upi_id&pn=Your%20Merchant%20Name&mc=1234&tid=<?php echo $transaction_id; ?>&tr=<?php echo $transaction_id; ?>&am=<?php echo $amount; ?>&cu=INR" alt="QR Code">
        <p>Scan & Pay via UPI</p>
    </div>

    <p class="countdown">Time left: <span id="countdown">30</span> seconds</p>

    <div class="container">
        <form method="POST" id="otp-section">
            <label>Enter OTP:</label>
            <input type="text" name="otp" pattern="[0-9]{4}" required><br>
            <button type="submit" name="verify_otp">Confirm Payment</button>
        </form>
    </div>

</body>
</html>
