<?php
session_start();
include 'conn.php';

$user_id = $_SESSION['userid'];

// Fetch ENUM values for dropdown
$expense_enum_query = "SHOW COLUMNS FROM recurring_expense LIKE 'expense_name'";
$expense_enum_result = $conn->query($expense_enum_query);
$row = $expense_enum_result->fetch_assoc();
preg_match_all("/'([^']+)'/", $row['Type'], $expense_options);

// Fetch filtered expenses
$selected_expense = isset($_GET['expense_name']) ? $_GET['expense_name'] : null;
$query = "SELECT * FROM recurring_expense WHERE user_id = ? " . ($selected_expense ? "AND expense_name = ?" : "");
$stmt = $conn->prepare($query);
if ($selected_expense) {
    $stmt->bind_param("is", $user_id, $selected_expense);
} else {
    $stmt->bind_param("i", $user_id);
}
$stmt->execute();
$result = $stmt->get_result();
$expenses = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://unpkg.com/@tailwindcss/browser@4"></script>
    <title>View Recurring Expenses</title>
    <style>
        body {
    color: white;
    background-color:rgb(55, 55, 55);
    font-family: Arial, sans-serif;
}

h2, h3 {
    color: white;
}

form {
    margin-bottom: 20px;
}

select {
    padding: 8px;
    background-color: transparent;
    color: white;
    border: 1px solid white;
}

table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
}

th, td {
    padding: 10px;
    border: 1px solid #ddd;
    text-align: left;
}

th {
    background-color: transparent;
    color: white;
}

td {
    color: white;
}
    </style>
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
</script>
</head>
<body class="p-10"><br><br>
    <h2 class="mb-6">View Recurring Expenses</h2>

    <form method="get">
        <label>Select Expense Name:</label>
        <select name="expense_name" onchange="this.form.submit()">
            <option value="" >All</option>
            <?php foreach ($expense_options[1] as $option) { ?>
                <option class="text-zinc-600" value="<?= $option ?>" <?= ($selected_expense == $option) ? 'selected' : '' ?>>
                    <?= ucfirst($option) ?>
                </option>
            <?php } ?>
        </select>
    </form>

    <h3>Expense Records</h3>
    <table border="1">
        <tr>
            <th>Expense Name</th>
            <th>Sub Name</th>
            <th>Amount</th>
            <th>Due Date</th>
            <th>Frequency</th>
        </tr>
        <?php foreach ($expenses as $expense): ?>
        <tr>
            <td><?= htmlspecialchars($expense['expense_name']) ?></td>
            <td><?= htmlspecialchars($expense['expense_sub_name']) ?></td>
            <td>₹<?= htmlspecialchars($expense['amount']) ?></td>
            <td><?= htmlspecialchars($expense['due_date']) ?></td>
            <td><?= htmlspecialchars($expense['frequency']) ?></td>
        </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>
