<?php
session_start();
include 'conn.php';

$user_id = $_SESSION['userid'];
$today = date('Y-m-d');
$reminder_date = date('Y-m-d', strtotime('+7 days'));

// Fetch upcoming recurring expenses
$query = "SELECT expense_name, expense_sub_name, amount, due_date FROM recurring_expense 
          WHERE user_id = ? AND due_date BETWEEN ? AND ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("iss", $user_id, $today, $reminder_date);
$stmt->execute();
$result = $stmt->get_result();
$upcoming_expenses = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Fetch user's name
$query = "SELECT name FROM user WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($username);
$stmt->fetch();
$stmt->close();

// Fetch total expenses (expense + recurring_expense)
$expenseQuery = "SELECT SUM(amount) AS total_expense FROM expense WHERE user_id = ?";
$stmt = $conn->prepare($expenseQuery);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($expense_amount);
$stmt->fetch();
$stmt->close();

$recurringQuery = "SELECT SUM(amount) AS recurring_expense FROM recurring_expense WHERE user_id = ?";
$stmt = $conn->prepare($recurringQuery);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($recurring_amount);
$stmt->fetch();
$stmt->close();

$total_expense = ($expense_amount ?? 0) + ($recurring_amount ?? 0);

// Fetch total earnings (income + savings)
$incomeQuery = "SELECT SUM(amount) AS total_income FROM income WHERE user_id = ?";
$stmt = $conn->prepare($incomeQuery);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($total_income);
$stmt->fetch();
$stmt->close();

$savingQuery = "SELECT SUM(current_saving) AS total_savings FROM saving WHERE user_id = ?";
$stmt = $conn->prepare($savingQuery);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($total_savings);
$stmt->fetch();
$stmt->close();

$total_earnings = ($total_income ?? 0) + ($total_savings ?? 0);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Finance Dashboard</title>
    <script src="https://unpkg.com/@tailwindcss/browser@4"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script type="text/javascript">
        google.charts.load("current", {packages:["corechart"]});
        google.charts.setOnLoadCallback(drawChart);

        function drawChart() {
            var data = google.visualization.arrayToDataTable([
                ['Category', 'Amount'],
                ['Total Expenses', <?php echo $total_expense; ?>],
                ['Total Earnings', <?php echo $total_earnings; ?>]
            ]);

            var options = {
                title: 'Expense vs Savings',
                is3D: true,
                pieSliceText: 'value',
                colors: ['#ff4c4c', '#4caf50']
            };

            var chart = new google.visualization.PieChart(document.getElementById('piechart'));
            chart.draw(data, options);
        }
    </script>
    <style>
        @keyframes slideIn {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @keyframes softBlink {
            0% { background-color: #ffcc00; }
            50% { background-color: #ffe680; }
            100% { background-color: #ffcc00; }
        }

        .reminder-box {
            background: #ffcc00;
            padding: 15px;
            border-radius: 10px;
            width: 80%;
            margin: 20px auto;
            color: #333;
            animation: slideIn 0.8s ease-out, softBlink 2s infinite alternate;
        }
        .reminder-box h3 {
            margin-bottom: 10px;
            text-align: center;
        }
        .reminder-box ul {
            list-style-type: none;
            padding: 0;
        }
        .reminder-box li {
            background: #fff;
            margin: 5px 0;
            padding: 10px;
            border-radius: 5px;
            box-shadow: 2px 2px 5px rgba(0,0,0,0.1);
            font-weight: bold;
        }

        <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            width: 100vw;
            height: 100vh;
            background-image: linear-gradient(15deg , rgb(100, 100, 100) 20% , rgba(255, 255, 255, 0.899) 80%);
            color: white;
        }
        .navbar { width: 100vw; padding: 20px; font-size: 20px; letter-spacing: 2.5px; }
        .nav-items { list-style-type: none; display: flex; align-items: center; justify-content: center; gap: 20px; }
        .categories { width: 100vw; margin-top: 8px; padding: 18px; display: flex; gap: 12px; justify-content: center; }
        .cat1 { width: 20%; padding: 20px; border-radius: 10px; text-align: center; font-size: 22px; }
        .info { align-items: center; transition: 0.3s; display: flex; justify-content: center; gap: 10px; }
        .info:hover { color: blue; }
        #trans-arrow:hover { transform: translateX(14px); }
        .person { margin: 20px; padding-left: 20px; color: black; font-size: 32px; font-weight: bold; }
        .chart-container { width: 100%; display: flex; justify-content: center; margin-top: 30px; }
    </style>
    </style>
</head>
<body>
<script>
function goBack() {
    window.location.href = "logout.php";
}
</script>

    <div class="absolute top-4 right-4">
        <button onclick="goBack()" class="px-4 py-2 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition duration-300">
            Log Out
        </button>
    </div>
    <div class="navbar">
        <ul class="nav-items font-bold text-4xl">
            <li class="text-orange-600">खुद</li>
            <li class="text-blue-700">का</li>
            <li class="text-emerald-700">Fin@ncer</li>
        </ul>
    </div>

    <div class="person">
        <p>Welcome, <?= htmlspecialchars($username) ?> 👋</p>
    </div>

    <div class="categories">
            <div class="cat1 bg-green-600">
                <div class="title font-semibold text-3xl">AamDani</div>
                <div class="info">
                    <a href="income.php" class="info">Kitna Kamaya!</a>
                    <i class="fa-solid fa-arrow-right"></i>
                </div>
            </div>
            <div class="cat1 bg-red-600">
                <div class="title font-semibold text-3xl">Kharcha</div>
                <div class="info">
                    <a href="add_expenses.php" class="info">Kha Gawaya!?</a>
                    <i class="fa-solid fa-arrow-right"></i>
                </div>
            </div>            
            <div class="cat1 bg-gray-600">
                <div class="title font-semibold text-3xl">Dekhle bhai</div>
                <div class="info">
                    <a href="rec_exp.php" class="info">kya sachi!</a>
                    <i class="fa-solid fa-arrow-right"></i>
                </div>
            </div>            
            <div class="cat1 bg-yellow-600">
                <div class="title font-semibold text-3xl">Bacha Hua</div>
                <div class="info">
                    <a href="savings.php" class="info">paissa Hu Mein!</a>
                    <i class="fa-solid fa-arrow-right"></i>
                </div>
            </div>
            <div class="cat1 bg-pink-600">
                <div class="title font-semibold text-3xl">UPI</div>
                <div class="info">
                    <a href="upi_payment_sim.php" class="info">Asani seh kharach kre</a>
                    <i class="fa-solid fa-arrow-right"></i>
                </div>
            </div>
        </div>

<div class="chat-footer w-4/4 flex align-center justify-around pl-10 text-2xl">
<div class="reminder-box w-3/4 mt-4">
        <h3>Upcoming Payments</h3>
        <?php if (!empty($upcoming_expenses)) { ?>
            <ul>
                <?php foreach ($upcoming_expenses as $expense) { ?>
                    <li><b><?= htmlspecialchars($expense['expense_name']) ?> (<?= htmlspecialchars($expense['expense_sub_name']) ?>):</b> ₹<?= htmlspecialchars($expense['amount']) ?> due on <?= htmlspecialchars($expense['due_date']) ?></li>
                <?php } ?>
            </ul>
        <?php } else { ?>
            <p>No upcoming payments within the next 7 days.</p>
        <?php } ?>
    </div>
    
    <div class="chart-container w-1/4">
        <div id="piechart" style="width: 400px; height: 300px; background: white; padding: 10px; border-radius: 10px; size: 30px;"></div>
    </div>
</div>

</body>
</html>
