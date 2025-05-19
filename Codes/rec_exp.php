<?php
session_start();
include 'conn.php';

$user_id = $_SESSION['userid'];

// Fetch ENUM values for expense_name
$expense_enum_query = "SHOW COLUMNS FROM recurring_expense LIKE 'expense_name'";
$expense_enum_result = $conn->query($expense_enum_query);
$row = $expense_enum_result->fetch_assoc();
preg_match_all("/'([^']+)'/", $row['Type'], $expense_options);

// Fetch ENUM values for frequency
$freq_enum_query = "SHOW COLUMNS FROM recurring_expense LIKE 'frequency'";
$freq_enum_result = $conn->query($freq_enum_query);
$row_freq = $freq_enum_result->fetch_assoc();
preg_match_all("/'([^']+)'/", $row_freq['Type'], $frequency_options);

// Insert Recurring Expense
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_expense'])) {
    $expense_name = $_POST['expense_name'];
    $expense_sub_name = $_POST['expense_sub_name'];
    $amount = $_POST['amount'];
    $due_date = $_POST['due_date'];
    $frequency = $_POST['frequency'];

    $query = "INSERT INTO recurring_expense (user_id, expense_name, expense_sub_name, amount, due_date, frequency) 
              VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("issdss", $user_id, $expense_name, $expense_sub_name, $amount, $due_date, $frequency);
    $stmt->execute();
    $stmt->close();

    echo "<script>alert('Recurring Expense Added Successfully!');</script>";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Recurring Expense</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="signUp.css">
</head>
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
<body class="bg-zinc-700 text-white-800 flex justify-center items-center w-full h-screen">

    <div class="bg-zinc-600 p-8 rounded-lg shadow-lg w-full max-w-md">
        <h2 class="text-3xl font-bold text-center mb-6">Add Recurring Expense</h2>

        <form method="post" class="space-y-4">

<div>
    <label class="block text-sm font-semibold mb-2">Expense Name:</label>
    <select name="expense_name" required class="w-full p-3 border border-gray-300 rounded-lg text-gray  bg-transparent">
        <?php foreach ($expense_options[1] as $option) { ?>
            <option value="<?= $option ?>"><?= ucfirst($option) ?></option>
        <?php } ?>
    </select>
</div>

<div>
    <label class="block text-sm font-semibold mb-2">Expense Sub-Name:</label>
    <input type="text" name="expense_sub_name" required class="w-full p-3 border border-gray-300 rounded-lg text-white bg-transparent" />
</div>

<div>
    <label class="block text-sm font-semibold mb-2">Amount:</label>
    <input type="number" name="amount" required class="w-full p-3 border border-gray-300 rounded-lg text-white bg-transparent" />
</div>

<div>
    <label class="block text-sm font-semibold mb-2">Due Date:</label>
    <input type="date" name="due_date" required class="w-full p-3 border border-gray-300 rounded-lg text-white bg-transparent" />
</div>

<div>
    <label class="block text-sm font-semibold mb-2">Frequency:</label>
    <select name="frequency" required class="w-full p-3 border border-gray-300 rounded-lg text-gray bg-transparent">
        <?php foreach ($frequency_options[1] as $option) { ?>
            <option value="<?= $option ?>"><?= ucfirst($option) ?></option>
        <?php } ?>
    </select>
</div>
            <div class="flex justify-between items-center">
                <button type="submit" name="add_expense" class="px-6 py-3 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition duration-300">
                    Add Expense
                </button>
                <button type="button" onclick="window.location.href='view_recurring.php'" class="px-6 py-3 bg-gray-600 text-white font-semibold rounded-lg hover:bg-gray-700 transition duration-300">
                    View Expenses
                </button>
            </div>

        </form>
    </div>
</body>
</html>