<?php
session_start();
require 'db_connect.php';

// Security check
if (!isset($_SESSION['UserID'])) {
    header("Location: login.php");
    exit();
}

// Fetch Inventory Data for JavaScript integration
$inv_stmt = $pdo->query("SELECT ProductID as id, Item_Name as name, Category as category, Selling_Price as price, Stock_Quantity as stock, 'Pieces' as unit FROM tbl_inventory");
$inventory_data = $inv_stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch Transactions Data for JavaScript integration
$tx_stmt = $pdo->query("SELECT TransactionID as id, Transaction_Date as date, Total_Amount as total FROM tbl_transactions ORDER BY Transaction_Date DESC");
$tx_data = $tx_stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover, maximum-scale=5, user-scalable=yes">
    <title>Dashboard – Sharon Store</title>
    <meta name="theme-color" content="#198754">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="app.css">
</head>
<body>
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-logo">
            <div class="logo-icon-sm">🛒</div>
            <span class="logo-text">Sharon<span class="logo-accent">Store</span></span>
        </div>
        <nav class="sidebar-nav">
            <a href="dashboard.php" class="nav-item active" id="nav-dashboard">
                <i class="fa fa-house"></i><span>Dashboard</span>
            </a>
            <a href="pos.php" class="nav-item" id="nav-pos">
                <i class="fa fa-cash-register"></i><span>Point of Sale</span>
            </a>
            <a href="inventory.php" class="nav-item" id="nav-inventory">
                <i class="fa fa-boxes-stacked"></i><span>Inventory</span>
            </a>
            <a href="forecasting.php" class="nav-item" id="nav-forecasting">
                <i class="fa fa-chart-line"></i><span>Forecasting & Restock</span>
            </a>
        </nav>
        <div class="sidebar-footer">
            <div class="user-pill">
                <div class="user-avatar" id="sidebarAvatar"><?php echo strtoupper(substr($_SESSION['Username'], 0, 1)); ?></div>
                <div class="user-info">
                    <div class="user-name" id="sidebarName"><?php echo htmlspecialchars($_SESSION['Username']); ?></div>
                    <div class="user-role" id="sidebarRole"><?php echo htmlspecialchars($_SESSION['Role']); ?></div>
                </div>
            </div>
            <button class="logout-btn" id="logoutBtn" title="Sign Out">
                <i class="fa fa-right-from-bracket"></i>
            </button>
        </div>
    </aside>
    <main class="main-content">
        <header class="topbar">
            <button class="menu-toggle" id="menuToggle"><i class="fa fa-bars"></i></button>
            <div class="topbar-title">Dashboard</div>
            <div class="topbar-right">
                <span class="topbar-date" id="topbarDate"></span>
            </div>
        </header>
        <div class="page-body">
            <div class="page-hero">
                <h1>Welcome back, <span id="heroName"><?php echo htmlspecialchars($_SESSION['Username']); ?></span> 👋</h1>
                <p>Here's what's happening with your store today.</p>
            </div>
            <div class="stats-grid">
                <div class="stat-card" style="--accent:#e91e8c">
                    <div class="stat-icon"><i class="fa fa-peso-sign"></i></div>
                    <div class="stat-info">
                        <div class="stat-value" id="statRevenue">₱0.00</div>
                        <div class="stat-label">Total Revenue (Today)</div>
                    </div>
                </div>
                <div class="stat-card" style="--accent:#7c3aed">
                    <div class="stat-icon"><i class="fa fa-receipt"></i></div>
                    <div class="stat-info">
                        <div class="stat-value" id="statTransactions">0</div>
                        <div class="stat-label">Transactions Today</div>
                    </div>
                </div>
                <div class="stat-card" style="--accent:#0ea5e9">
                    <div class="stat-icon"><i class="fa fa-boxes-stacked"></i></div>
                    <div class="stat-info">
                        <div class="stat-value" id="statProducts">0</div>
                        <div class="stat-label">Total Products</div>
                    </div>
                </div>
                <div class="stat-card" style="--accent:#f59e0b">
                    <div class="stat-icon"><i class="fa fa-triangle-exclamation"></i></div>
                    <div class="stat-info">
                        <div class="stat-value" id="statLowStock">0</div>
                        <div class="stat-label">Low Stock Alerts</div>
                    </div>
                </div>
            </div>
            <div class="dash-grid">
                <div class="dash-card">
                    <div class="dash-card-header">
                        <span>Recent Transactions</span>
                        <a href="pos.php" class="dash-link">Go to POS →</a>
                    </div>
                    <div id="recentTransactions" class="empty-state">
                        <i class="fa fa-clock-rotate-left fa-2x" style="color:var(--muted); margin-bottom:8px;"></i>
                        <p>No transactions yet today</p>
                    </div>
                </div>
                <div class="dash-card">
                    <div class="dash-card-header">
                        <span>Low Stock Items</span>
                        <a href="inventory.php" class="dash-link">View Inventory →</a>
                    </div>
                    <div id="lowStockList"></div>
                </div>
            </div>
        </div>
    </main>
    <script src="app.js"></script>
    <script>
        // Override localStorage with live database arrays
        const dbInventory = <?php echo json_encode($inventory_data, JSON_NUMERIC_CHECK); ?>;
        const dbTransactions = <?php echo json_encode($tx_data, JSON_NUMERIC_CHECK); ?>;
        window.getInventory = function() { return dbInventory; };
        window.getTransactions = function() { return dbTransactions; };

        initNav('nav-dashboard');

        // Render dashboard stats seamlessly using existing logic
        const inventory = getInventory();
        document.getElementById('statProducts').textContent = inventory.length;
        const lowStock = inventory.filter(i => i.stock <= 20);
        document.getElementById('statLowStock').textContent = lowStock.length;
        const transactions = getTransactions();
        const todayStr = new Date().toDateString();
        const todayTx = transactions.filter(t => new Date(t.date).toDateString() === todayStr);
        document.getElementById('statTransactions').textContent = todayTx.length;
        const todayRevenue = todayTx.reduce((sum, t) => sum + t.total, 0);
        document.getElementById('statRevenue').textContent = '₱' + todayRevenue.toFixed(2);

        // Low stock list rendering
        const lowStockEl = document.getElementById('lowStockList');
        if (lowStock.length === 0) {
            lowStockEl.innerHTML = '<div class="empty-state"><i class="fa fa-circle-check fa-2x" style="color:#22c55e;margin-bottom:8px;"></i><p>All items are well stocked!</p></div>';
        } else {
            lowStockEl.innerHTML = lowStock.map(item => `
                <div class="low-stock-row">
                    <div>
                        <div class="ls-name">${item.name}</div>
                        <div class="ls-cat">${item.category}</div>
                    </div>
                    <span class="ls-badge ${item.stock === 0 ? 'badge-danger' : 'badge-warn'}">${item.stock === 0 ? 'Out of Stock' : item.stock + ' left'}</span>
                </div>
            `).join('');
        }

        // Recent transactions rendering
        const recentEl = document.getElementById('recentTransactions');
        if (todayTx.length > 0) {
            recentEl.innerHTML = todayTx.slice(0, 5).map(t => `
                <div class="tx-row">
                    <div>
                        <div class="tx-id">TX #${t.id}</div>
                        <div class="tx-time">${new Date(t.date).toLocaleTimeString()}</div>
                    </div>
                    <span class="tx-amount">₱${t.total.toFixed(2)}</span>
                </div>
            `).join('');
        }

        document.getElementById('topbarDate').textContent = new Date().toLocaleDateString('en-PH', { weekday:'long', year:'numeric', month:'long', day:'numeric' });

        document.getElementById('logoutBtn').addEventListener('click', () => {
            window.location.href = 'logout.php';
        });
    </script>
</body>
</html>