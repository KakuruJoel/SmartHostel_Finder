<?php
session_start();
require_once 'db_config.php';
require_once 'auth_check.php';

// 1. Authentication Check
if (!isset($_SESSION['landlord_id'])) {
    header("Location: login.php");
    exit();
}

$landlord_id = $_SESSION['landlord_id'];

// 2. Fetch Financial Summary using Prepared Statements
// We calculate Gross, Expenses, and NOI in one go
$sql_summary = "SELECT 
    SUM(CASE WHEN type = 'income' THEN amount ELSE 0 END) as total_income,
    SUM(CASE WHEN type = 'expense' THEN amount ELSE 0 END) as total_expense
    FROM transactions 
    WHERE landlord_id = ? AND booking_status = 'completed'";

$stmt = $pdo->prepare($sql_summary);
$stmt->execute([$landlord_id]);
$data = $stmt->fetch();

$noi = $data['total_income'] - $data['total_expense'];
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <script src="https://cdn.tailwindcss.com"></script>
    <title>Revenue Dashboard</title>
</head>

<body class="bg-gray-50">

    <?php include 'navbar.php'; ?>
    <div class="max-w-7xl mx-auto px-4 py-8">
        <h1 class="text-3xl font-bold text-gray-800 mb-8">Financial Overview</h1>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                <p class="text-sm font-medium text-gray-500 uppercase">Gross Revenue</p>
                <p class="text-2xl font-bold text-green-600">$<?= number_format($data['total_income'], 2) ?></p>
            </div>
            <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                <p class="text-sm font-medium text-gray-500 uppercase">Total Expenses</p>
                <p class="text-2xl font-bold text-red-600">$<?= number_format($data['total_expense'], 2) ?></p>
            </div>
            <div class="bg-white p-6 rounded-xl shadow-sm border border-blue-100 bg-blue-50">
                <p class="text-sm font-medium text-blue-600 uppercase">Net Operating Income</p>
                <p class="text-2xl font-bold text-blue-900">$<?= number_format($noi, 2) ?></p>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm overflow-hidden">
            <div class="p-6 border-b border-gray-100 flex justify-between items-center">
                <h2 class="text-lg font-semibold text-gray-800">Recent Transactions</h2>
                <button class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm">Download Report (CSV)</button>
            </div>
            <table class="w-full text-left">
                <thead class="bg-gray-50 text-gray-600 text-sm uppercase">
                    <tr>
                        <th class="px-6 py-4">Date</th>
                        <th class="px-6 py-4">Property</th>
                        <th class="px-6 py-4">Category</th>
                        <th class="px-6 py-4 text-right">Amount</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php
                    $sql_list = "SELECT t.*, p.property_name 
                                FROM transactions t 
                                JOIN properties p ON t.property_id = p.id 
                                WHERE t.landlord_id = ? 
                                ORDER BY t.date_created DESC LIMIT 10";
                    $stmt_list = $pdo->prepare($sql_list);
                    $stmt_list->execute([$landlord_id]);
                    while ($row = $stmt_list->fetch()): ?>
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4 text-gray-600"><?= $row['date_created'] ?></td>
                            <td class="px-6 py-4 font-medium text-gray-800"><?= htmlspecialchars($row['property_name']) ?></td>
                            <td class="px-6 py-4 uppercase text-xs font-bold text-gray-400"><?= $row['category'] ?></td>
                            <td class="px-6 py-4 text-right <?= $row['type'] == 'income' ? 'text-green-600' : 'text-red-600' ?>">
                                <?= $row['type'] == 'income' ? '+' : '-' ?> $<?= number_format($row['amount'], 2) ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php include 'footer.php'; ?>
    <script src="js/bootstrap.bundle.min.js"></script>
</body>

</html>