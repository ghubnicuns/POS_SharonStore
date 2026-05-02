<?php
session_start();
require 'db_connect.php';

if (!isset($_SESSION['UserID'])) {
    header("Location: login.php");
    exit();
}

$forecast_msg = '';

// Generate Forecast Logic (Admin Only)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['generate_forecast'])) {
    if ($_SESSION['Role'] === 'Admin') {
        $product_id = $_POST['product_id'];
        $target_date = date('Y-m-01', strtotime('+1 month')); 

        // Calculation Method: 3-Month Historical Average
        $stmt = $pdo->prepare("
            SELECT DATE_FORMAT(t.Transaction_Date, '%Y-%m') as month, SUM(td.Quantity_Sold) as total_sold
            FROM tbl_transactions t
            JOIN tbl_transaction_details td ON t.TransactionID = td.TransactionID
            WHERE td.ProductID = ? AND t.Transaction_Date >= DATE_SUB(NOW(), INTERVAL 3 MONTH)
            GROUP BY month ORDER BY month DESC
        ");
        $stmt->execute([$product_id]);
        $sales_data = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (count($sales_data) > 0) {
            $total_qty = 0;
            foreach ($sales_data as $data) {
                $total_qty += $data['total_sold'];
            }
            
            // Simple Moving Average
            $predicted_demand = ceil($total_qty / count($sales_data));

            $insert_stmt = $pdo->prepare("INSERT INTO tbl_forecasts (ProductID, Recommended_Restock_Qty, Forecast_Date) VALUES (?, ?, ?)");
            $insert_stmt->execute([$product_id, $predicted_demand, $target_date]);
            
            $forecast_msg = "<div class='toast toast-success'><span>✅</span><span>Projection calculated: Recommend restocking $predicted_demand units.</span></div>";
        } else {
            $forecast_msg = "<div class='toast toast-error'><span>⚠️</span><span>Insufficient historical POS data to project demand.</span></div>";
        }
    } else {
        $forecast_msg = "<div class='toast toast-error'><span>❌</span><span>Access Denied. Admins only.</span></div>";
    }
}

// Fetch drop-down products and table history
$products = $pdo->query("SELECT ProductID, Item_Name FROM tbl_inventory ORDER BY Item_Name ASC")->fetchAll(PDO::FETCH_ASSOC);
$recent_forecasts = $pdo->query("
    SELECT f.ForecastID, i.Item_Name, f.Forecast_Date, f.Recommended_Restock_Qty 
    FROM tbl_forecasts f JOIN tbl_inventory i ON f.ProductID = i.ProductID
    ORDER BY f.ForecastID DESC LIMIT 10
")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forecasting – Sharon Store</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="app.css">
    <style>
        .forecast-card { max-width: 500px; margin-bottom: 30px; }
    </style>
</head>
<body>
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-logo">
            <div class="logo-icon-sm">🛒</div>
            <span class="logo-text">Sharon<span class="logo-accent">Store</span></span>
        </div>
        <nav class="sidebar-nav">
            <a href="dashboard.php" class="nav-item" id="nav-dashboard"><i class="fa fa-house"></i><span>Dashboard</span></a>
            <a href="pos.php" class="nav-item" id="nav-pos"><i class="fa fa-cash-register"></i><span>Point of Sale</span></a>
            <a href="inventory.php" class="nav-item" id="nav-inventory"><i class="fa fa-boxes-stacked"></i><span>Inventory</span></a>
            <a href="forecasting.php" class="nav-item active" id="nav-forecasting"><i class="fa fa-chart-line"></i><span>Forecasting & Restock</span></a>
        </nav>
        <div class="sidebar-footer">
            <div class="user-pill">
                <div class="user-avatar" id="sidebarAvatar"><?php echo strtoupper(substr($_SESSION['Username'], 0, 1)); ?></div>
                <div class="user-info">
                    <div class="user-name" id="sidebarName"><?php echo htmlspecialchars($_SESSION['Username']); ?></div>
                    <div class="user-role" id="sidebarRole"><?php echo htmlspecialchars($_SESSION['Role']); ?></div>
                </div>
            </div>
            <button class="logout-btn" id="logoutBtn" title="Sign Out" onclick="window.location.href='logout.php'"><i class="fa fa-right-from-bracket"></i></button>
        </div>
    </aside>

    <main class="main-content">
        <header class="topbar">
            <button class="menu-toggle" id="menuToggle"><i class="fa fa-bars"></i></button>
            <div class="topbar-title">Forecasting & Restock</div>
        </header>

        <div class="page-body">
            <div class="section-header">
                <div>
                    <h2 class="section-title">Sales Data Analytics</h2>
                    <p class="section-sub">Calculate future demand based on 3-month historical averages.</p>
                </div>
            </div>

            <?php if ($_SESSION['Role'] === 'Admin'): ?>
            <div class="dash-card forecast-card">
                <div class="dash-card-header">
                    <span>Calculate Future Demand</span>
                </div>
                <form method="POST" action="forecasting.php">
                    <div class="mf-group">
                        <label class="mf-label">Select Inventory Item</label>
                        <select name="product_id" class="filter-select" style="width: 100%; margin-bottom: 15px;" required>
                            <option value="">-- Choose a Product --</option>
                            <?php foreach($products as $p): ?>
                                <option value="<?php echo $p['ProductID']; ?>"><?php echo htmlspecialchars($p['Item_Name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <button type="submit" name="generate_forecast" class="btn btn-pink" style="width: 100%; justify-content: center;">
                        <i class="fa fa-calculator"></i> Process Calculation
                    </button>
                </form>
            </div>
            <?php else: ?>
            <div class="dash-card forecast-card" style="border-left: 4px solid var(--warning);">
                <p style="color: var(--muted); font-size: 14px;"><strong>Note:</strong> You must be an Administrator to generate new forecasting projections.</p>
            </div>
            <?php endif; ?>

            <div class="table-wrap">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Item Name</th>
                            <th>Target Month</th>
                            <th>Recommended Restock Qty</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(count($recent_forecasts) > 0): ?>
                            <?php foreach($recent_forecasts as $row): ?>
                            <tr>
                                <td class="td-name"><?php echo htmlspecialchars($row['Item_Name']); ?></td>
                                <td><?php echo date('F Y', strtotime($row['Forecast_Date'])); ?></td>
                                <td style="color: var(--success); font-weight: bold;">
                                    <?php echo $row['Recommended_Restock_Qty']; ?> units
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="3" class="no-results">No historical projections found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Render Toast message if PHP generated one -->
        <?php if($forecast_msg): ?>
        <div class="toast-container" id="toastBox">
            <?php echo $forecast_msg; ?>
        </div>
        <script>
            setTimeout(() => { document.getElementById('toastBox').style.display = 'none'; }, 4000);
        </script>
        <?php endif; ?>

    </main>
    <script src="app.js"></script>
    <script>
        initNav('nav-forecasting');
    </script>
</body>
</html>