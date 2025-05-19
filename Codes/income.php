<?php
session_start();
include 'conn.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);

$user_id = $_SESSION['userid'];

// Add Income
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_income'])) {
    $amount = $_POST['amount'];
    $source = $_POST['source'];
    $date = $_POST['date'];
    $description = $_POST['description'];

    $query = "INSERT INTO income (user_id, amount, source, date, description) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("issss", $user_id, $amount, $source, $date, $description);
    $stmt->execute();
    $stmt->close();
}

// Delete Income
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_income'])) {
    $id = $_POST['id'];
    $query = "DELETE FROM income WHERE id=? AND user_id=?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ii", $id, $user_id);
    $stmt->execute();
    $stmt->close();
}

// Fetch Income Records
$query = "SELECT * FROM income WHERE user_id=? ORDER BY date ASC";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$income_records = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Income</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://unpkg.com/@tailwindcss/browser@4"></script>
    <style>
       body {
    font-family: Arial, sans-serif;
    color: white; /* Text color set to white */
    /* background-color: #334155; Zinc 800 background color */
    background-color:rgba(92, 93, 91, 0.72);
    text-align: center;
    margin: 20px;
}

h2, h3 {
    color: white; /* Ensure headings are white */
}

form {
    margin-bottom: 20px;
    background-color:rgb(95, 95, 95); /* Darker background for the form */
    padding: 20px;
    border-radius: 8px;
}

input, select, button {
    padding: 10px;
    margin: 5px;
    background-color:rgb(61, 61, 61); /* Blue button background */
    color: white; /* White text on buttons */
    border: 1px solid #2980b9; /* Border color */
    border-radius: 5px;
}

input[type="text"], input[type="date"], select {
    width: 200px;
}

button:hover {
    background-color: #2980b9; /* Darker blue when hovering */
}

table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
    color: white; /* White text for the table */
}

th, td {
    padding: 10px;
    border: 1px solid #ddd;
    text-align: left;
}

th {
    background-color: #2c3e50; /* Darker background for table headers */
}

td {
    background-color: #34495e; /* Darker background for table rows */
}

form button[type="submit"] {
    background-color:rgb(56, 143, 32); /* Green button for submission */
}

form button[type="submit"]:hover {
    background-color:rgb(80, 160, 55); /* Lighter green on hover */
}

canvas {
    width: 100% !important;
    height: 300px !important;
    display: none; /* Initially hide the chart */
}

select {
    padding: 10px;
    background-color:rgb(61, 61, 61);
    color: white;
    border: 1px solid #3498db;
    border-radius: 5px;
}
    </style>
    <script>
    function goBack() {
    window.location.href = "dashboard.php";
}

    </script>
        <div class="btn w-20 flex">
        <button onclick="goBack()" class="flex justify-end w-20">Back</button>
    </div>
</head>
<body>

    <h2 class="text-4xl font-semibold">Manage Income</h2>
    <center><div class="divider h-1 w-100 bg-zinc-600 m-10"></div></center>

    <form method="post">
        <input type="number" name="amount" class="bg-transparent text-white" placeholder="Amount" required>
        <select name="source" required>
            <option value="job">Job</option>
            <option value="gift">Gift</option>
            <option value="allowance">Allowance</option>
            <option value="extra">Extra</option>
        </select>
        <input type="date" name="date" required>
        <input type="text" name="description" placeholder="Description">
        <button type="submit" name="add_income">Add Income</button>
    </form>

    <h3 class="text-2xl m-3">Your Income Records</h3>
    <table border="1">
        <tr>
            <th>Amount</th>
            <th>Source</th>
            <th>Date</th>
            <th>Description</th>
            <th>Actions</th>
        </tr>
        <?php foreach ($income_records as $income): ?>
        <tr>
            <td>₹<?= htmlspecialchars($income['amount']) ?></td>
            <td><?= htmlspecialchars($income['source']) ?></td>
            <td><?= htmlspecialchars($income['date']) ?></td>
            <td><?= htmlspecialchars($income['description']) ?></td>
            <td>
                <form method="post" style="display:inline;">
                    <input type="hidden" name="id" value="<?= $income['id'] ?>">
                    <button type="submit" name="delete_income">Delete</button>
                </form>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>

    <h3 class="text-2xl m-3">Analyze Income Progress</h3>
    <select id="incomeSource" onchange="updateChart()">
        <option value="">Select Source</option>
        <option value="job">Job</option>
        <option value="gift">Gift</option>
        <option value="allowance">Allowance</option>
        <option value="extra">Extra</option>
    </select>

    <canvas id="incomeChart" class="text-white"></canvas>

    <script>
        function updateChart() {
            var source = document.getElementById('incomeSource').value;
            var ctx = document.getElementById('incomeChart').getContext('2d');
            var incomeData = <?php echo json_encode($income_records); ?>;
            var filteredData = incomeData.filter(i => i.source === source);
            
            if (filteredData.length === 0) {
                document.getElementById('incomeChart').style.display = 'none';
                return;
            }

            document.getElementById('incomeChart').style.display = 'block';
            
            var chartData = {
                labels: filteredData.map(i => i.date),
                datasets: [{
                    label: source + ' Income Progress',
                    data: filteredData.map(i => i.amount),
                    borderColor: 'rgba(75, 192, 192, 1)',
                    backgroundColor: 'rgba(75, 192, 192, 0.5)',
                    fontSize:'20px',
                    fill: true,
                    tension: 0.3,
                }]
            };

            if (window.incomeChartInstance) {
                window.incomeChartInstance.destroy();
            }
            
            window.incomeChartInstance = new Chart(ctx, {
                type: 'line',
                data: chartData,
                options: {
                    responsive: false,
                    maintainAspectRatio: false,
                    scales: {
                        x: { title: { display: true, text: 'Date' }},
                        y: { title: { display: true, text: 'Amount' }, beginAtZero: true }
                    }
                }
            });
            
        }
        
    </script>
</body>
</html>