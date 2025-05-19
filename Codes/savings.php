<?php
session_start();
include 'conn.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);

$user_id = $_SESSION['userid'];

// Add Savings Goal
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_saving'])) {
    $goal_name = $_POST['goal_name'];
    $target_amount = $_POST['target_amount'];
    $current_saving = $_POST['current_saving'];
    $goal_deadline = $_POST['goal_deadline'];

    $query = "INSERT INTO saving (user_id, goal_name, target_amount, current_saving, goal_deadline) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("issis", $user_id, $goal_name, $target_amount, $current_saving, $goal_deadline);
    $stmt->execute();
    $stmt->close();
}

// Update Savings Goal (Add Amount to Current Saving)
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_saving'])) {
    $id = $_POST['id'];
    $add_amount = $_POST['add_amount'];
    
    // Get current saving and target amount
    $query = "SELECT goal_name, current_saving, target_amount FROM saving WHERE id=? AND user_id=?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ii", $id, $user_id);
    $stmt->execute();
    $stmt->bind_result($goal_name, $current_saving, $target_amount);
    $stmt->fetch();
    $stmt->close();
    
    // Add the new amount to the current saving
    $new_saving = $current_saving + $add_amount;
    
    // Update the savings record
    $query = "UPDATE saving SET current_saving=? WHERE id=? AND user_id=?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("iii", $new_saving, $id, $user_id);
    $stmt->execute();
    $stmt->close();
    
    // Check if goal is reached
    if ($new_saving >= $target_amount) {
        echo "<script>alert('Congratulations! You have reached your savings goal: $goal_name');</script>";
    }
}

// Delete Savings Goal
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_saving'])) {
    $id = $_POST['id'];
    $query = "DELETE FROM saving WHERE id=? AND user_id=?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ii", $id, $user_id);
    $stmt->execute();
    $stmt->close();
}

// Fetch Savings Goals
$query = "SELECT * FROM saving WHERE user_id=?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$savings = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Savings</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://unpkg.com/@tailwindcss/browser@4"></script>

    <style>
        body {
            background-color: #2d3748; /* Zinc 700 color */
            color: white; /* Set text color to white */
            font-family: 'Arial', sans-serif;
        }

        h2, h3 {
            text-align: center;
            font-size: 24px;
            margin-top: 20px;
        }

        h3 {
            font-size: 20px;
        }

        table {
            width: 100%;
            margin: 20px auto;
            border-collapse: collapse;
        }

        table, th, td {
            border: 1px solid white;
        }

        th, td {
            padding: 10px;
            text-align: center;
        }

        button {
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        button:hover {
            background-color:rgb(57, 57, 57);
        }

        /* Form and input styling */
        input[type="text"], input[type="number"], input[type="date"] {
            padding: 8px;
            margin: 10px 0;
            width: 100%;
            border-radius: 5px;
            border: 1px solid white;
            background-color: #2d3748; /* Background color same as zinc 700 */
            color: white;
        }

        input[type="submit"], button {
            width: 100%;
        }

        .form-container {
            max-width: 600px;
            margin: 20px auto;
            background: rgba(255, 255, 255, 0.1);
            padding: 20px;
            border-radius: 10px;
        }

        .tips-container {
            margin: 40px auto;
            width: 80%;
            padding: 20px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 10px;
        }

        /* Chart styling */
        #savingsChart {
            width: 300px;
            height: 300px;
            margin: 20px auto;
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

</head>
<body>


<h2 class="text-4xl font-semibold m-8">Manage Savings Goals</h2>

<div class="form-containerd flex justify-center align-center">
    <form method="post" class="w-2/4">
        <input type="text" name="goal_name" placeholder="Goal Name" required>
        <input type="number" name="target_amount" placeholder="Target Amount" required>
        <input type="number" name="current_saving" placeholder="Current Saving" required>
        <input type="date" name="goal_deadline" placeholder="Deadline" required>
        <button type="submit" name="add_saving">Add Savings Goal</button>
    </form>
</div>

<h3>Your Savings Goals</h3>
<table>
    <tr>
        <th>Goal Name</th>
        <th>Target Amount</th>
        <th>Current Saving</th>
        <th>Goal Deadline</th>
        <th>Actions</th>
    </tr>
    <?php foreach ($savings as $saving): ?>
    <tr>
        <td><?= htmlspecialchars($saving['goal_name']) ?></td>
        <td>₹<?= htmlspecialchars($saving['target_amount']) ?></td>
        <td>₹<?= htmlspecialchars($saving['current_saving']) ?></td>
        <td><?= htmlspecialchars($saving['goal_deadline']) ?></td>
        <td>
            <form method="post" style="display:inline;">
                <input type="hidden" name="id" value="<?= $saving['id'] ?>">
                <button type="submit" name="delete_saving">Delete</button>
            </form>
            <button onclick="showUpdateForm('<?= $saving['id'] ?>', '<?= $saving['goal_name'] ?>')">Update</button>
            <button onclick="showChart('<?= $saving['id'] ?>')">Analyze</button>
        </td>
    </tr>
    <?php endforeach; ?>
</table>

<h3>Update Savings Goal</h3>
<form method="post" class="p-10" id="updateForm" style="display: none;">
    <input type="hidden" name="id" id="update_id">
    <p id="goal_name_display"></p>
    <input type="number" name="add_amount" id="update_add_amount" placeholder="Add Amount" required>
    <button type="submit" name="update_saving">Add to Savings</button>
</form>

<div class="analysis w-full flex align-center jusify-between">
<div class="tips-container w-3/4">
    <h2 style="text-align: center; font-size: 28px;">Tips to Save More & Cut Expenses</h2>
    <ul style="font-size: 24px; margin-top: 10px;">
        <li>Track your expenses regularly.</li>
        <li>Set a monthly budget and stick to it.</li>
        <li>Cut down on unnecessary subscriptions.</li>
        <li>Cook at home instead of eating out.</li>
        <li>Automate savings to set aside money consistently.</li>
        <li>Look for discounts and cashback offers.</li>
        <li>Reduce impulse purchases by waiting 24 hours before buying.</li>
    </ul>
</div>

<h3>Savings Progress Chart</h3>
<canvas id="savingsChart" class="w-1/4"></canvas>

</div>
<script>
    function showUpdateForm(id, goalName) {
        document.getElementById("update_id").value = id;
        document.getElementById("goal_name_display").innerText = "Updating Goal: " + goalName;
        document.getElementById("updateForm").style.display = "block";
    }
    
    function showChart(id) {
        var ctx = document.getElementById('savingsChart').getContext('2d');
        var goal = <?php echo json_encode($savings); ?>.find(g => g.id == id);
        
        if (window.savingsChartInstance) {
            window.savingsChartInstance.destroy();
        }
        
        window.savingsChartInstance = new Chart(ctx, {
            type: 'pie',
            data: {
                labels: ['Current Saving', 'Amount Left'],
                datasets: [{
                    label: goal.goal_name,
                    data: [goal.current_saving, goal.target_amount - goal.current_saving],
                    backgroundColor: ['rgba(54, 162, 235, 0.5)', 'rgba(255, 99, 132, 0.5)']
                }]
            },
            options: {
                responsive: false,
                maintainAspectRatio: false,
            }
        });
    }
</script>

</body>
</html>