<?php
session_start();
include('conn.php'); // Ensure you include your database connection file

$success_message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $amount = $_POST['amount'];
    $category = $_POST['category'];
    $description = $_POST['description'];
    $user_id = $_SESSION['userid']; // Assuming the user is logged in

    // Insert into the database
    $sql = "INSERT INTO Expense (user_id, amount, cat_types, description, date) 
            VALUES ('$user_id', '$amount', '$category', '$description', NOW())";

    if (mysqli_query($conn, $sql)) {
        $success_message = "Expense added successfully!";
    } else {
        $success_message = "Error: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Expense</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-zinc-700 text-white-800 flex justify-center items-center w-full h-screen relative">
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

    <div class="bg-zinc-600 p-8 rounded-lg shadow-lg w-full max-w-md">
        <h2 class="text-3xl font-bold text-center mb-6">Add Expense</h2>

        <?php if (!empty($success_message)): ?>
            <div class="mb-4 p-3 bg-green-600 text-white text-center rounded-lg">
                <?= $success_message; ?>
            </div>
        <?php endif; ?>

        <form method="POST" class="space-y-4">
            <div>
                <label for="amount" class="block text-sm font-semibold mb-2">Amount:</label>
                <input type="number" name="amount" required class="w-full p-3 border border-gray-300 rounded-lg bg-transparent text-white" />
            </div>

            <div>
                <label for="category" class="block text-sm font-semibold mb-2">Category:</label>
                <select name="category" required class="w-full p-3 border border-gray-300 rounded-lg bg-transparent text-white text-zinc-900">
                    <option value="food">Food</option>
                    <option value="transport">Transport</option>
                    <option value="education">Education</option>
                    <option value="entertainment">Entertainment</option>
                    <option value="miscellaneous">Miscellaneous</option>
                </select>
            </div>

            <div>
                <label for="description" class="block text-sm font-semibold mb-2">Description:</label>
                <textarea name="description" required class="w-full p-3 border border-gray-300 rounded-lg bg-transparent text-white"></textarea>
            </div>

            <div class="flex justify-between items-center">
                <button type="submit" class="px-6 py-3 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition duration-300">
                    Add Expense
                </button>
                <button type="button" onclick="window.location.href='view_expenses.php'" class="px-6 py-3 bg-gray-600 text-white font-semibold rounded-lg hover:bg-gray-700 transition duration-300">
                    View Expenses
                </button>
            </div>
        </form>
    </div>

</body>
</html>
