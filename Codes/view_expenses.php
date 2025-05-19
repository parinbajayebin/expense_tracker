<?php
session_start();
include 'conn.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);

$user_id = $_SESSION['userid'];

// Get ENUM values for categories
$query = "SHOW COLUMNS FROM expense LIKE 'cat_types'";
$result = $conn->query($query);
$row = $result->fetch_assoc();
preg_match("/^enum\((.*)\)$/", $row['Type'], $matches);
$categories = str_getcsv($matches[1], ",", "'");

// Handle AJAX request
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['fetch_data'])) {
    header('Content-Type: application/json');
    ob_clean();

    $category = $_POST['category'];
    $query = "SELECT description, amount, date FROM expense WHERE user_id = ? AND cat_types = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("is", $user_id, $category);
    $stmt->execute();
    $result = $stmt->get_result();

    $expenses = [];
    while ($row = $result->fetch_assoc()) {
        $expenses[] = $row;
    }
    $stmt->close();

    echo json_encode($expenses);
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Expenses</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://unpkg.com/@tailwindcss/browser@4"></script>

    <style>
        body {
    font-family: Arial, sans-serif;
    text-align: center;
    margin: 20px;
    color: white; /* Text color set to white */
    background-color:rgb(68, 68, 68); /* Zinc 800 background color */
}

.category-container {
    margin-top: 20px;
}

.category-btn {
    padding: 20px 25px;
    margin: 5px;
    cursor: pointer;
    border: none;
    background-color:rgb(53, 53, 53);
    color: white;
    border-radius: 24px;
}

.category-btn:hover {
    background-color:rgb(33, 33, 33);
}

.popup {
    display: none;
    position: fixed;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    background:rgb(16, 16, 16); /* Darker background for the popup */
    padding: 20px;
    border-radius: 10px;
    box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.3);
    z-index: 1000;
    width: 80%;
    max-width: 500px;
    color: white; /* White text inside the popup */
}

.close-btn {
    cursor: pointer;
    color: red;
    font-size: 20px;
    float: right;
}

.overlay {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5);
    z-index: 999;
}

table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 10px;
    color: white; /* White text color for table */
}

th, td {
    padding: 10px;
    border: 1px solid #ddd;
    text-align: left;
}

th {
    background-color: #2c3e50; /* Darker background for table headers */
}

.pagination {
    margin-top: 10px;
}

.pagination button {
    margin: 5px;
    padding: 5px 10px;
    cursor: pointer;
    background-color: #3498db;
    color: white;
    border: none;
    border-radius: 5px;
}

.pagination button:hover {
    background-color: #2980b9;
}

canvas {
    width: 100% !important;
    height: 300px !important;
}

.hidden {
    display: none;
}
    </style>
</head>
<body>
<script>
function goBack() {
    window.location.href = "dashboard.php";
}
</script>

    <div class="absolute top-4 left-4">
        <button onclick="goBack()" class="px-4 py-2 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition duration-300">
            Back
        </button>
    </div>
<div class="category-container" id="category-container">
    <h2>Select Expense Category</h2>
    <?php foreach ($categories as $category): ?>
        <button class="category-btn" onclick="openPopup('<?php echo $category; ?>')">
            <?php echo ucfirst($category); ?>
        </button>
    <?php endforeach; ?>
</div>

<div class="overlay" id="overlay"></div>
<div class="popup" id="popup">
    <span class="close-btn" onclick="closePopup()">✖</span>
    <h3 id="popup-title"></h3>
    <div id="message" class="hidden">No expenses found for this category.</div>
    <div id="table-container">
        <table id="expense-table">
            <thead>
                <tr><th>Description</th><th>Amount</th><th>Date</th></tr>
            </thead>
            <tbody></tbody>
        </table>
        <div class="pagination" id="pagination"></div>
    </div>
    <canvas id="expenseChart"></canvas>
</div>

<script>
let currentCategory = "";
let chartInstance = null;
let expensesData = [];
let currentPage = 1;
const rowsPerPage = 5;

function openPopup(category) {
    currentCategory = category;
    document.getElementById("popup-title").innerText = "Expenses for " + category;
    fetchExpenses(category);
}

function closePopup() {
    document.getElementById("popup").style.display = "none";
    document.getElementById("overlay").style.display = "none";
    document.getElementById("message").classList.add("hidden");
    document.getElementById("table-container").style.display = "none";
    document.getElementById("expenseChart").style.display = "none";
}

function fetchExpenses(category) {
    let xhr = new XMLHttpRequest();
    xhr.open("POST", "", true);
    xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");

    xhr.onload = function() {
        if (xhr.status == 200) {
            expensesData = JSON.parse(xhr.responseText);

            if (expensesData.length === 0) {
                document.getElementById("message").classList.remove("hidden");
                document.getElementById("table-container").style.display = "none";
                document.getElementById("expenseChart").style.display = "none";
            } else {
                document.getElementById("message").classList.add("hidden");
                document.getElementById("table-container").style.display = "block";
                document.getElementById("expenseChart").style.display = "block";
                updateTable();
                updateChart();
            }

            document.getElementById("popup").style.display = "block";
            document.getElementById("overlay").style.display = "block";
        }
    };
    xhr.send("fetch_data=1&category=" + category);
}

function updateTable() {
    let tbody = document.querySelector("#expense-table tbody");
    tbody.innerHTML = "";

    let start = (currentPage - 1) * rowsPerPage;
    let end = start + rowsPerPage;
    let paginatedExpenses = expensesData.slice(start, end);

    paginatedExpenses.forEach(expense => {
        let row = `<tr>
            <td>${expense.description}</td>
            <td>${expense.amount}</td>
            <td>${expense.date}</td>
        </tr>`;
        tbody.innerHTML += row;
    });

    updatePagination();
}

function updatePagination() {
    let paginationDiv = document.getElementById("pagination");
    paginationDiv.innerHTML = "";

    let totalPages = Math.ceil(expensesData.length / rowsPerPage);
    for (let i = 1; i <= totalPages; i++) {
        let btn = document.createElement("button");
        btn.innerText = i;
        btn.onclick = function() {
            currentPage = i;
            updateTable();
        };
        if (i === currentPage) btn.style.fontWeight = "bold";
        paginationDiv.appendChild(btn);
    }
}

function updateChart() {
    let ctx = document.getElementById("expenseChart").getContext("2d");

    if (chartInstance) {
        chartInstance.destroy();
    }

    let labels = expensesData.map(expense => expense.date);
    let amounts = expensesData.map(expense => expense.amount);

    // Create a gradient effect
    let gradient = ctx.createLinearGradient(0, 0, 0, 400);
    gradient.addColorStop(0, 'rgba(52, 152, 219, 0.5)'); // Light Blue
    gradient.addColorStop(1, 'rgba(52, 152, 219, 0)');   // Fade to transparent

    chartInstance = new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: "Amount Spent",
                data: amounts,
                borderColor: 'blue',
                borderWidth: 2,
                backgroundColor: gradient, // Gradient fill effect
                borderDash: [5, 5], // Dotted line effect
                tension: 0.3, // Smooth curve
                pointBackgroundColor: 'blue',
                pointBorderColor: 'black',
                pointRadius: 6
            }]
        },
        options: {
            responsive: true,
            plugins: {
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return `₹${context.raw}`;
                        }
                    }
                },
                datalabels: { // Show data values on points
                    color: 'black',
                    anchor: 'end',
                    align: 'top',
                    font: { weight: 'bold' }
                }
            },
            scales: {
                x: { title: { display: true, text: "Date" } },
                y: { title: { display: true, text: "Amount" } }
            }
        }
    });
}
</script>

</body>
</html>
