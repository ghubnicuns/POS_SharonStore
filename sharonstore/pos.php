<?php
session_start();
require 'db_connect.php';

// Security check
if (!isset($_SESSION['UserID'])) {
    header("Location: login.php");
    exit();
}

// Handle AJAX Checkout Payload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'checkout') {
    $data = json_decode($_POST['payload'], true);
    $total_amount = $data['total'];
    $cart = $data['cart'];

    try {
        $pdo->beginTransaction();
        
        // 1. Insert Master Transaction
        $stmt = $pdo->prepare("INSERT INTO tbl_transactions (UserID, Total_Amount) VALUES (?, ?)");
        $stmt->execute([$_SESSION['UserID'], $total_amount]);
        $transaction_id = $pdo->lastInsertId();

        // 2. Insert Details and Deduct Inventory
        $detail_stmt = $pdo->prepare("INSERT INTO tbl_transaction_details (TransactionID, ProductID, Quantity_Sold, Sub_Total) VALUES (?, ?, ?, ?)");
        $update_stock_stmt = $pdo->prepare("UPDATE tbl_inventory SET Stock_Quantity = Stock_Quantity - ? WHERE ProductID = ?");

        foreach ($cart as $item) {
            $subtotal = $item['qty'] * $item['price'];
            $detail_stmt->execute([$transaction_id, $item['id'], $item['qty'], $subtotal]);
            $update_stock_stmt->execute([$item['qty'], $item['id']]);
        }

        $pdo->commit();
        echo json_encode(['success' => true]);
    } catch (Exception $e) {
        $pdo->rollBack();
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
    exit();
}

// Fetch Inventory Data for the UI
$stmt = $pdo->query("SELECT ProductID as id, Item_Name as name, Category as category, Selling_Price as price, Stock_Quantity as stock, 'Pieces' as unit, 20 as reorderLevel FROM tbl_inventory");
$inventory_data = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Point of Sale – Sharon Store</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="app.css">
    <style>
        .pos-layout { display:flex; gap:0; height:calc(100vh - var(--topbar-h)); overflow:hidden; }
        .pos-left { flex:1; display:flex; flex-direction:column; padding:20px; overflow:hidden; }
        .pos-right { width:360px; background:var(--surface); border-left:1px solid var(--border); display:flex; flex-direction:column; flex-shrink:0; }
        .cat-pills { display:flex; gap:8px; flex-wrap:wrap; margin-bottom:16px; }
        .cat-pill { padding:7px 16px; border-radius:20px; border:1px solid var(--border); background:var(--surface2); color:var(--muted); font-size:12px; font-weight:600; cursor:pointer; transition:all .2s; font-family:'Inter',sans-serif; }
        .cat-pill.active { background:linear-gradient(135deg,var(--pink),var(--purple)); color:#fff; border-color:transparent; box-shadow:0 4px 12px rgba(233,30,140,.3); }
        .prod-grid { display:grid; grid-template-columns:repeat(auto-fill,minmax(110px,1fr)); gap:4px; overflow-y:auto; flex:1; align-content:start; }
        .prod-card { background:var(--surface2); border:1px solid var(--border); border-radius:6px; padding:8px 6px; cursor:pointer; transition:background .15s,box-shadow .15s; text-align:center; aspect-ratio: 1 / 1; display:flex; flex-direction:column; align-items:center; justify-content:center; overflow:hidden; }
        .prod-card:hover { background:rgba(233,30,140,0.08); box-shadow:inset 0 0 0 2px rgba(233,30,140,.35); }
        .prod-card.out { opacity:.5; cursor:not-allowed; }
        .prod-emoji { font-size:22px; margin-bottom:6px; }
        .prod-name { font-size:11px; font-weight:700; color:var(--text); margin-bottom:3px; line-height:1.3; }
        .prod-price { font-size:13px; font-weight:800; color:var(--success); margin-bottom:4px; }
        .prod-stock { font-size:10px; color:var(--muted); }
        /* Right panel */
        .cart-header { padding:16px 18px; border-bottom:1px solid var(--border); font-size:15px; font-weight:700; color:var(--text); display:flex; align-items:center; justify-content:space-between; }
        .cart-clear { font-size:11px; color:var(--danger); cursor:pointer; border:none; background:none; font-family:'Inter',sans-serif; font-weight:600; }
        .cart-items { flex:1; overflow-y:auto; padding:12px; }
        .cart-empty { text-align:center; padding:40px 20px; color:var(--muted); font-size:13px; }
        .cart-item { background:var(--surface2); border:1px solid var(--border); border-radius:10px; padding:12px; margin-bottom:8px; }
        .ci-name { font-size:13px; font-weight:700; color:var(--text); margin-bottom:4px; }
        .ci-price { font-size:11px; color:var(--muted); }
        .ci-controls { display:flex; align-items:center; justify-content:space-between; margin-top:8px; }
        .ci-qty { display:flex; align-items:center; gap:6px; }
        .qty-btn { width:26px; height:26px; border-radius:6px; border:1px solid var(--border); background:rgba(255,255,255,.06); color:var(--text); font-size:13px; font-weight:700; cursor:pointer; display:flex; align-items:center; justify-content:center; transition:background .2s; }
        .qty-btn:hover { background:rgba(233,30,140,.2); border-color:rgba(233,30,140,.4); }
        .qty-val { font-size:14px; font-weight:700; color:var(--text); min-width:20px; text-align:center; }
        .ci-subtotal { font-size:13px; font-weight:800; color:var(--success); }
        .ci-remove { background:none; border:none; color:var(--danger); cursor:pointer; font-size:13px; }
        .cart-footer { padding:16px 18px; border-top:1px solid var(--border); }
        .total-row { display:flex; justify-content:space-between; align-items:center; margin-bottom:14px; }
        .total-label { font-size:14px; color:var(--muted); font-weight:600; }
        .total-val { font-size:24px; font-weight:800; color:var(--pink-light); }
        .checkout-btn { width:100%; padding:15px; background:linear-gradient(135deg,var(--pink),var(--purple)); border:none; border-radius:12px; color:#fff; font-size:16px; font-weight:700; cursor:pointer; font-family:'Inter',sans-serif; box-shadow:0 6px 20px rgba(233,30,140,.35); transition:transform .15s,box-shadow .15s; }
        .checkout-btn:hover { transform:translateY(-2px); box-shadow:0 10px 28px rgba(233,30,140,.45); }
        .checkout-btn:disabled { opacity:.5; cursor:not-allowed; transform:none; }
        /* Receipt modal */
        .receipt { background:#fff; color:#222; border-radius:12px; padding:24px; font-family:'Courier New',monospace; font-size:12px; max-height:70vh; overflow-y:auto; }
        .receipt-title { text-align:center; font-size:16px; font-weight:bold; margin-bottom:4px; }
        .receipt-sub { text-align:center; font-size:11px; color:#666; margin-bottom:12px; }
        .receipt-divider { border:none; border-top:1px dashed #ccc; margin:10px 0; }
        .receipt-row { display:flex; justify-content:space-between; margin-bottom:4px; }
        .receipt-total { display:flex; justify-content:space-between; font-weight:bold; font-size:14px; margin-top:6px; }
        /* Stock alert banner */
        .stock-alert-bar { display: flex; align-items: flex-start; gap: 10px; background: rgba(15,7,21,.7); border: 1px solid rgba(239,68,68,.28); border-radius: 12px; padding: 10px 14px; margin-bottom: 14px; font-size: 12px; backdrop-filter: blur(8px); animation: alertSlideIn .35s cubic-bezier(.34,1.56,.64,1) both; position: relative; }
        .stock-alert-bar.hidden { display: none; }
        @keyframes alertSlideIn { from { opacity:0; transform:translateY(-8px); } to { opacity:1; transform:none; } }
        .alert-icon-wrap { width: 32px; height: 32px; border-radius: 9px; display:flex; align-items:center; justify-content:center; background: rgba(239,68,68,.15); color:#ef4444; font-size:14px; flex-shrink:0; margin-top:1px; }
        .alert-body { flex:1; min-width:0; }
        .alert-title { font-weight:700; color:var(--text); margin-bottom:6px; display:flex; align-items:center; gap:8px; }
        .alert-title .a-count { background:linear-gradient(135deg,#ef4444,#f97316); color:#fff; font-size:10px; font-weight:700; padding:1px 7px; border-radius:20px; letter-spacing:.3px; }
        .alert-chips { display:flex; flex-wrap:wrap; gap:6px; }
        .alert-chip { display:inline-flex; align-items:center; gap:5px; padding:3px 10px; border-radius:20px; font-size:11px; font-weight:600; border:1px solid; white-space:nowrap; cursor:default; transition: transform .15s; }
        .alert-chip:hover { transform:scale(1.05); }
        .chip-out  { background:rgba(239,68,68,.12); color:#ef4444; border-color:rgba(239,68,68,.3); }
        .chip-low  { background:rgba(245,158,11,.12); color:#f59e0b; border-color:rgba(245,158,11,.3); }
        .chip-dot  { width:5px; height:5px; border-radius:50%; background:currentColor; flex-shrink:0; }
        .alert-toggle { background:none; border:none; color:var(--muted); cursor:pointer; font-size:11px; font-weight:600; font-family:'Inter',sans-serif; display:flex; align-items:center; gap:4px; flex-shrink:0; padding:4px 8px; border-radius:7px; transition:background .2s, color .2s; margin-top:1px; }
        .alert-toggle:hover { background:rgba(255,255,255,.08); color:var(--text); }
        .alert-dismiss { position:absolute; top:8px; right:8px; background:none; border:none; color:var(--muted); cursor:pointer; font-size:12px; padding:2px 5px; border-radius:5px; transition:color .2s; }
        .alert-dismiss:hover { color:var(--danger); }
        .chips-wrap { overflow:hidden; transition: max-height .3s ease, opacity .3s; max-height:200px; opacity:1; }
        .chips-wrap.collapsed { max-height:0; opacity:0; }
        @media(max-width:768px){ .pos-right{ width:100%; position:fixed; bottom:0; left:0; right:0; max-height:50vh; z-index:50; } .pos-layout{ flex-direction:column; } }
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
        <a href="pos.php" class="nav-item active" id="nav-pos"><i class="fa fa-cash-register"></i><span>Point of Sale</span></a>
        <a href="inventory.php" class="nav-item" id="nav-inventory"><i class="fa fa-boxes-stacked"></i><span>Inventory</span></a>
        <a href="forecasting.php" class="nav-item" id="nav-forecasting"><i class="fa fa-chart-line"></i><span>Forecasting & Restock</span></a>
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
<main class="main-content" style="overflow:hidden;">
    <header class="topbar">
        <button class="menu-toggle" id="menuToggle"><i class="fa fa-bars"></i></button>
        <div class="topbar-title">Point of Sale</div>
        <div class="topbar-right">
            <div class="search-wrap" style="max-width:220px;">
                <i class="fa fa-magnifying-glass"></i>
                <input type="text" class="search-input" id="posSearch" placeholder="Search products...">
            </div>
        </div>
    </header>
    <div class="pos-layout">
        <div class="pos-left">
            <div class="stock-alert-bar hidden" id="stockAlertBar">
                <div class="alert-icon-wrap"><i class="fa fa-triangle-exclamation"></i></div>
                <div class="alert-body">
                    <div class="alert-title">
                        Stock Alerts
                        <span class="a-count" id="alertCount">0</span>
                    </div>
                    <div class="chips-wrap" id="alertChipsWrap">
                        <div class="alert-chips" id="alertChips"></div>
                    </div>
                </div>
                <button class="alert-toggle" id="alertToggle" title="Collapse / Expand">
                    <i class="fa fa-chevron-up" id="alertToggleIcon"></i>
                </button>
                <button class="alert-dismiss" id="alertDismiss" title="Dismiss until refresh">✖</button>
            </div>
            <div class="cat-pills" id="catPills"></div>
            <div class="prod-grid" id="prodGrid"></div>
        </div>
        <div class="pos-right">
            <div class="cart-header">
                <span><i class="fa fa-cart-shopping" style="color:var(--pink-light);margin-right:8px;"></i>Cart <span id="cartCount" style="background:var(--pink);color:#fff;font-size:10px;padding:1px 7px;border-radius:20px;margin-left:4px;">0</span></span>
                <button class="cart-clear" id="clearCartBtn">Clear All</button>
            </div>
            <div class="cart-items" id="cartItems">
                <div class="cart-empty"><i class="fa fa-cart-shopping fa-2x" style="margin-bottom:10px;display:block;opacity:.3;"></i>Cart is empty<br><small>Tap a product to add</small></div>
            </div>
            <div class="cart-footer">
                <div class="total-row">
                    <span class="total-label">Total</span>
                    <span class="total-val">₱<span id="cartTotal">0.00</span></span>
                </div>
                <button class="checkout-btn" id="checkoutBtn" disabled>Process Transaction</button>
            </div>
        </div>
    </div>
</main>
<!-- Payment Modal -->
<div class="modal-overlay" id="paymentModal">
    <div class="modal" style="max-width:400px;">
        <div class="modal-header">
            <h3>💳 Process Payment</h3>
            <button class="modal-close" onclick="closeModal('paymentModal')"><i class="fa fa-xmark"></i></button>
        </div>
        <div class="modal-body">
            <div style="text-align:center; margin-bottom:20px;">
                <div style="font-size:13px;color:var(--muted);margin-bottom:4px;">Amount Due</div>
                <div style="font-size:36px;font-weight:800;color:var(--pink-light);">₱<span id="modalTotal">0.00</span></div>
            </div>
            <div class="mf-group">
                <label class="mf-label">Cash Tendered (₱)</label>
                <input type="number" class="mf-input" id="cashInput" placeholder="0.00" min="0" step="0.01">
            </div>
            <div style="background:rgba(34,197,94,.08);border:1px solid rgba(34,197,94,.2);border-radius:10px;padding:14px;margin-top:8px;">
                <div style="display:flex;justify-content:space-between;font-size:13px;color:var(--muted);margin-bottom:6px;"><span>Amount Due</span><span style="color:var(--text);">₱<span id="changeAmtDue">0.00</span></span></div>
                <div style="display:flex;justify-content:space-between;font-size:15px;font-weight:700;color:var(--success);"><span>Change</span><span>₱<span id="changeAmt">0.00</span></span></div>
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn btn-outline" onclick="closeModal('paymentModal')">Cancel</button>
            <button class="btn btn-pink" id="confirmPayBtn" disabled>Confirm Payment</button>
        </div>
    </div>
</div>
<!-- Receipt Modal -->
<div class="modal-overlay" id="receiptModal">
    <div class="modal" style="max-width:380px;">
        <div class="modal-header">
            <h3>🧾 Receipt</h3>
            <button class="modal-close" onclick="closeModal('receiptModal');newTransaction()"><i class="fa fa-xmark"></i></button>
        </div>
        <div class="modal-body" style="padding:16px;">
            <div class="receipt" id="receiptContent"></div>
        </div>
        <div class="modal-footer">
            <button class="btn btn-outline" onclick="window.print()"><i class="fa fa-print"></i> Print</button>
            <button class="btn btn-pink" onclick="closeModal('receiptModal');newTransaction()">New Transaction</button>
        </div>
    </div>
</div>
<script src="app.js"></script>
<script>
// Load inventory from DB securely
let inventory = <?php echo json_encode($inventory_data, JSON_NUMERIC_CHECK); ?>;
window.getInventory = function() { return inventory; };

initNav('nav-pos');
const CATEGORIES = ['All','Canned Goods','Dry Commodities','Household Essentials','Condiments'];
const EMOJIS = { 'Canned Goods':'🥫','Dry Commodities':'🍚','Household Essentials':'🧼','Condiments':'🍶' };
let currentCat = 'All', cart = [];

const pillsEl = document.getElementById('catPills');
CATEGORIES.forEach(cat => {
    const btn = document.createElement('button');
    btn.className = 'cat-pill' + (cat==='All'?' active':'');
    btn.textContent = (EMOJIS[cat]||'🏷️') + ' ' + cat;
    btn.onclick = () => {
        currentCat = cat;
        document.querySelectorAll('.cat-pill').forEach(p=>p.classList.remove('active'));
        btn.classList.add('active');
        renderProducts();
    };
    pillsEl.appendChild(btn);
});

function renderProducts() {
    const q = document.getElementById('posSearch').value.toLowerCase();
    const items = inventory.filter(i=>{
        const matchCat = currentCat==='All'||i.category===currentCat;
        const matchQ = i.name.toLowerCase().includes(q)||i.category.toLowerCase().includes(q);
        return matchCat && matchQ;
    });
    const grid = document.getElementById('prodGrid');
    grid.innerHTML = items.map(item=>{
        const out = item.stock<=0;
        return `<div class="prod-card${out?' out':''}" onclick="${out?`alert('Out of stock!')`:`addToCart(${item.id})`}">
            <div class="prod-emoji">${EMOJIS[item.category]||'📦'}</div>
            <div class="prod-name">${item.name}</div>
            <div class="prod-price">₱${item.price.toFixed(2)}</div>
            <div class="prod-stock">${out?'<span style="color:#ef4444;">Out of Stock</span>':item.stock+' '+item.unit+' left'}</div>
        </div>`;
    }).join('') || '<div style="grid-column:1/-1;text-align:center;padding:40px;color:var(--muted);">No products found.</div>';
}

document.getElementById('posSearch').addEventListener('input', renderProducts);

window.addToCart = function(id) {
    const prod = inventory.find(p=>p.id===id);
    if(!prod||prod.stock<=0) return;
    const existing = cart.find(c=>c.id===id);
    const curQty = existing?existing.qty:0;
    if(curQty+1>prod.stock){ alert('Stock limit reached!'); return; }
    if(existing) existing.qty++;
    else cart.push({id:prod.id,name:prod.name,price:prod.price,qty:1,unit:prod.unit});
    renderCart();
};

function renderCart() {
    const el = document.getElementById('cartItems');
    const total = cart.reduce((s,c)=>s+c.price*c.qty,0);
    document.getElementById('cartTotal').textContent = total.toFixed(2);
    document.getElementById('cartCount').textContent = cart.reduce((s,c)=>s+c.qty,0);
    document.getElementById('checkoutBtn').disabled = cart.length===0;
    if(!cart.length){
        el.innerHTML='<div class="cart-empty"><i class="fa fa-cart-shopping fa-2x" style="margin-bottom:10px;display:block;opacity:.3;"></i>Cart is empty<br><small>Tap a product to add</small></div>';
        return;
    }
    el.innerHTML = cart.map((c,i)=>`
        <div class="cart-item">
            <div class="ci-name">${c.name}</div>
            <div class="ci-price">₱${c.price.toFixed(2)} each</div>
            <div class="ci-controls">
                <div class="ci-qty">
                    <button class="qty-btn" onclick="changeQty(${i},-1)">-</button>
                    <span class="qty-val">${c.qty}</span>
                    <button class="qty-btn" onclick="changeQty(${i},1)">+</button>
                </div>
                <span class="ci-subtotal">₱${(c.price*c.qty).toFixed(2)}</span>
                <button class="ci-remove" onclick="removeItem(${i})"><i class="fa fa-trash"></i></button>
            </div>
        </div>`).join('');
}

window.changeQty = function(i, d) {
    const prod = inventory.find(p=>p.id===cart[i].id);
    if(d>0 && cart[i].qty+1>(prod?prod.stock:Infinity)){ alert('Stock limit reached!'); return; }
    cart[i].qty += d;
    if(cart[i].qty<=0) cart.splice(i,1);
    renderCart();
};

window.removeItem = function(i){ cart.splice(i,1); renderCart(); };
document.getElementById('clearCartBtn').addEventListener('click',()=>{ if(cart.length&&confirm('Clear all items?')){ cart=[]; renderCart(); } });

// Checkout Handling
document.getElementById('checkoutBtn').addEventListener('click', ()=>{
    const total = cart.reduce((s,c)=>s+c.price*c.qty,0);
    document.getElementById('modalTotal').textContent = total.toFixed(2);
    document.getElementById('changeAmtDue').textContent = total.toFixed(2);
    document.getElementById('changeAmt').textContent = '0.00';
    document.getElementById('cashInput').value = '';
    document.getElementById('confirmPayBtn').disabled = true;
    openModal('paymentModal');
});

document.getElementById('cashInput').addEventListener('input', function(){
    const due = cart.reduce((s,c)=>s+c.price*c.qty,0);
    const cash = parseFloat(this.value)||0;
    const change = cash - due;
    document.getElementById('changeAmt').textContent = Math.max(0,change).toFixed(2);
    document.getElementById('changeAmt').style.color = change<0?'#ef4444':'#22c55e';
    document.getElementById('confirmPayBtn').disabled = cash<due;
});

// Database Integration via Fetch API
document.getElementById('confirmPayBtn').addEventListener('click', ()=>{
    document.getElementById('confirmPayBtn').disabled = true;
    const due = cart.reduce((s,c)=>s+c.price*c.qty,0);
    const cash = parseFloat(document.getElementById('cashInput').value)||0;
    const change = cash - due;

    const payload = JSON.stringify({ total: due, cart: cart });
    const formData = new FormData();
    formData.append('action', 'checkout');
    formData.append('payload', payload);

    fetch('pos.php', { method: 'POST', body: formData })
    .then(res => res.json())
    .then(data => {
        if(data.success) {
            // Build receipt visually
            const now = new Date();
            document.getElementById('receiptContent').innerHTML = `
                <div class="receipt-title">🛒 SHARON STORE</div>
                <div class="receipt-sub">Point of Sale Receipt<br>${now.toLocaleString('en-PH')}</div>
                <hr class="receipt-divider">
                ${cart.map(c=>`<div class="receipt-row"><span>${c.name} x${c.qty}</span><span>₱${(c.price*c.qty).toFixed(2)}</span></div>`).join('')}
                <hr class="receipt-divider">
                <div class="receipt-row"><span>Cash</span><span>₱${cash.toFixed(2)}</span></div>
                <div class="receipt-total"><span>TOTAL</span><span>₱${due.toFixed(2)}</span></div>
                <div class="receipt-row" style="color:green;font-weight:bold;"><span>Change</span><span>₱${change.toFixed(2)}</span></div>
                <hr class="receipt-divider">
                <div style="text-align:center;font-size:11px;color:#888;">Thank you for shopping at Sharon Store!<br>Please come again. 🙏</div>`;
            
            closeModal('paymentModal');
            openModal('receiptModal');
        } else {
            alert("Checkout failed: " + data.error);
            document.getElementById('confirmPayBtn').disabled = false;
        }
    });
});

function newTransaction(){
    // Hard reload the page to refresh the database stock numbers
    window.location.reload();
}

// Close modals
document.querySelectorAll('.modal-overlay').forEach(o=>{
    o.addEventListener('click',e=>{ if(e.target===o){ o.classList.remove('open'); if(o.id==='receiptModal') newTransaction(); } });
});

// Stock alert banner
let alertDismissed = false;
let alertCollapsed = false;
function renderStockAlerts() {
    if (alertDismissed) return;
    const outItems = inventory.filter(i => i.stock === 0);
    const lowItems = inventory.filter(i => i.stock > 0 && i.stock <= (i.reorderLevel || 20));
    const total = outItems.length + lowItems.length;
    const bar   = document.getElementById('stockAlertBar');
    const chips = document.getElementById('alertChips');
    const count = document.getElementById('alertCount');

    if (total === 0) { bar.classList.add('hidden'); return; }
    bar.classList.remove('hidden');
    count.textContent = total + ' item' + (total > 1 ? 's' : '');

    const iconWrap = bar.querySelector('.alert-icon-wrap');
    if (outItems.length > 0) {
        iconWrap.style.background = 'rgba(239,68,68,.18)';
        iconWrap.style.color      = '#ef4444';
        bar.style.borderColor     = 'rgba(239,68,68,.28)';
    } else {
        iconWrap.style.background = 'rgba(245,158,11,.18)';
        iconWrap.style.color      = '#f59e0b';
        bar.style.borderColor     = 'rgba(245,158,11,.28)';
    }

    chips.innerHTML = [
        ...outItems.map(i => `<span class="alert-chip chip-out" title="Out of Stock"><span class="chip-dot"></span>${i.name}</span>`),
        ...lowItems.map(i => `<span class="alert-chip chip-low" title="Low Stock – ${i.stock} ${i.unit} left"><span class="chip-dot"></span>${i.name} <em style="font-style:normal;opacity:.7;">(${i.stock})</em></span>`)
    ].join('');
}

document.getElementById('alertToggle').addEventListener('click', function () {
    alertCollapsed = !alertCollapsed;
    document.getElementById('alertChipsWrap').classList.toggle('collapsed', alertCollapsed);
    document.getElementById('alertToggleIcon').className = alertCollapsed ? 'fa fa-chevron-down' : 'fa fa-chevron-up';
});

document.getElementById('alertDismiss').addEventListener('click', function () {
    alertDismissed = true;
    document.getElementById('stockAlertBar').classList.add('hidden');
});

renderProducts();
renderStockAlerts();
</script>
</body>
</html>