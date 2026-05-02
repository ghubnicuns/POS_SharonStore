<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Point of Sale – Sharon Store</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="style.css">
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
        <a href="forecasting.php" class="nav-item" id="nav-forecasting"><i class="fa fa-chart-line"></i><span>Forecasting & Restock</span></a>
    </nav>
    <div class="sidebar-footer">
        <div class="user-pill">
            <div class="user-avatar" id="sidebarAvatar">A</div>
            <div class="user-info">
                <div class="user-name" id="sidebarName">Admin</div>
                <div class="user-role" id="sidebarRole">Administrator</div>
            </div>
        </div>
        <button class="logout-btn" id="logoutBtn" title="Sign Out"><i class="fa fa-right-from-bracket"></i></button>
    </div>
</aside>

<main class="main-content overflow-hidden">
    <header class="topbar">
        <button class="menu-toggle" id="menuToggle"><i class="fa fa-bars"></i></button>
        <div class="topbar-title">Point of Sale</div>
        <div class="topbar-right">
            <div class="search-wrap compact">
                <i class="fa fa-magnifying-glass"></i>
                <input type="text" class="search-input" id="posSearch" placeholder="Search products…">
            </div>
        </div>
    </header>

    <div class="pos-layout">
        <div class="pos-left">
            <!-- Stock Alert Banner -->
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
                <button class="alert-dismiss" id="alertDismiss" title="Dismiss until refresh">✕</button>
            </div>

            <div class="cat-pills" id="catPills"></div>
            <div class="prod-grid" id="prodGrid"></div>
        </div>

        <div class="pos-right">
            <div class="cart-header">
                <span><i class="fa fa-cart-shopping cart-icon"></i>Cart <span id="cartCount" class="badge-pill">0</span></span>
                <button class="cart-clear" id="clearCartBtn">Clear All</button>
            </div>
            <div class="cart-items" id="cartItems">
                <div class="cart-empty"><i class="fa fa-cart-shopping fa-2x icon-block icon-muted"></i>Cart is empty<br><small>Tap a product to add</small></div>
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
    <div class="modal modal-large">
        <div class="modal-header">
            <h3>💳 Process Payment</h3>
            <button class="modal-close" onclick="closeModal('paymentModal')"><i class="fa fa-xmark"></i></button>
        </div>
        <div class="modal-body">
            <div class="text-center mb-lg">
                <div class="modal-small-label">Amount Due</div>
                <div class="modal-total">₱<span id="modalTotal">0.00</span></div>
            </div>
            <div class="mf-group">
                <label class="mf-label">Cash Tendered (₱)</label>
                <input type="number" class="mf-input" id="cashInput" placeholder="0.00" min="0" step="0.01">
            </div>
            <div class="info-card">
                <div class="info-row"><span>Amount Due</span><span class="text-default">₱<span id="changeAmtDue">0.00</span></span></div>
                <div class="info-row info-row-strong"><span>Change</span><span>₱<span id="changeAmt">0.00</span></span></div>
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
    <div class="modal modal-small">
        <div class="modal-header">
            <h3>🧾 Receipt</h3>
            <button class="modal-close" onclick="closeModal('receiptModal');newTransaction()"><i class="fa fa-xmark"></i></button>
        </div>
        <div class="modal-body modal-body-compact">
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
checkAuth();
initNav('nav-pos');

const CATEGORIES = ['All','Canned Goods','Dry Commodities','Household Essentials','Condiments'];
const EMOJIS = { 'Canned Goods':'🥫','Dry Commodities':'🌾','Household Essentials':'🧴','Condiments':'🫙' };
let currentCat = 'All', cart = [], inventory = getInventory();

// Build category pills
const pillsEl = document.getElementById('catPills');
CATEGORIES.forEach(cat => {
    const btn = document.createElement('button');
    btn.className = 'cat-pill' + (cat==='All'?' active':'');
    btn.textContent = (EMOJIS[cat]||'🛒') + ' ' + cat;
    btn.onclick = () => {
        currentCat = cat;
        document.querySelectorAll('.cat-pill').forEach(p=>p.classList.remove('active'));
        btn.classList.add('active');
        renderProducts();
    };
    pillsEl.appendChild(btn);
});

function renderProducts() {
    inventory = getInventory();
    const q = document.getElementById('posSearch').value.toLowerCase();
    const items = inventory.filter(i=>{
        const matchCat = currentCat==='All'||i.category===currentCat;
        const matchQ = i.name.toLowerCase().includes(q)||i.category.toLowerCase().includes(q);
        return matchCat && matchQ;
    });
    const grid = document.getElementById('prodGrid');
    grid.innerHTML = items.map(item=>{
        const out = item.stock<=0;
        return `<div class="prod-card${out?' out':''}" onclick="${out?`showToast('Out of stock!','error')`:`addToCart(${item.id})`}">
            <div class="prod-emoji">${EMOJIS[item.category]||'📦'}</div>
            <div class="prod-name">${item.name}</div>
            <div class="prod-price">₱${item.price.toFixed(2)}</div>
            <div class="prod-stock">${out?'<span class="text-danger font-bold">Out of Stock</span>':item.stock+' '+item.unit+' left'}</div>
        </div>`;
    }).join('') || '<div class="empty-state span-full">No products found.</div>';
    if(!prod||prod.stock<=0) return;
    const existing = cart.find(c=>c.id===id);
    const curQty = existing?existing.qty:0;
    if(curQty+1>prod.stock){ showToast('Stock limit reached!','error'); return; }
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
        el.innerHTML='<div class="cart-empty"><i class="fa fa-cart-shopping fa-2x icon-block icon-muted"></i>Cart is empty<br><small>Tap a product to add</small></div>';
        return;
    }
    el.innerHTML = cart.map((c,i)=>`
        <div class="cart-item">
            <div class="ci-name">${c.name}</div>
            <div class="ci-price">₱${c.price.toFixed(2)} each</div>
            <div class="ci-controls">
                <div class="ci-qty">
                    <button class="qty-btn" onclick="changeQty(${i},-1)">−</button>
                    <span class="qty-val">${c.qty}</span>
                    <button class="qty-btn" onclick="changeQty(${i},1)">+</button>
                </div>
                <span class="ci-subtotal">₱${(c.price*c.qty).toFixed(2)}</span>
                <button class="ci-remove" onclick="removeItem(${i})"><i class="fa fa-trash"></i></button>
            </div>
        </div>`).join('');
}

window.changeQty = function(i, d) {
    inventory = getInventory();
    const prod = inventory.find(p=>p.id===cart[i].id);
    if(d>0 && cart[i].qty+1>(prod?prod.stock:Infinity)){ showToast('Stock limit reached!','error'); return; }
    cart[i].qty += d;
    if(cart[i].qty<=0) cart.splice(i,1);
    renderCart();
};

window.removeItem = function(i){ cart.splice(i,1); renderCart(); };

document.getElementById('clearCartBtn').addEventListener('click',()=>{ if(cart.length&&confirm('Clear all items?')){ cart=[]; renderCart(); } });

// Checkout
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

document.getElementById('confirmPayBtn').addEventListener('click', ()=>{
    const due = cart.reduce((s,c)=>s+c.price*c.qty,0);
    const cash = parseFloat(document.getElementById('cashInput').value)||0;
    const change = cash - due;

    // Deduct stock
    inventory = getInventory();
    cart.forEach(c=>{ const p=inventory.find(i=>i.id===c.id); if(p) p.stock=Math.max(0,p.stock-c.qty); });
    saveInventory(inventory);

    // Save transaction
    addTransaction({ total: due, items: cart.map(c=>({name:c.name,qty:c.qty,price:c.price})) });

    // Build receipt
    const now = new Date();
    document.getElementById('receiptContent').innerHTML = `
        <div class="receipt-title">🛒 SHARON STORE</div>
        <div class="receipt-sub">Point of Sale Receipt<br>${now.toLocaleString('en-PH')}</div>
        <hr class="receipt-divider">
        ${cart.map(c=>`<div class="receipt-row"><span>${c.name} x${c.qty}</span><span>₱${(c.price*c.qty).toFixed(2)}</span></div>`).join('')}
        <hr class="receipt-divider">
        <div class="receipt-row"><span>Cash</span><span>₱${cash.toFixed(2)}</span></div>
        <div class="receipt-total"><span>TOTAL</span><span>₱${due.toFixed(2)}</span></div>
        <div class="receipt-row receipt-row--total"><span>Change</span><span>₱${change.toFixed(2)}</span></div>
        <hr class="receipt-divider">
        <div class="receipt-note">Thank you for shopping at Sharon Store!<br>Please come again. 💕</div>`;

    closeModal('paymentModal');
    openModal('receiptModal');
    renderProducts();
    renderStockAlerts();
});

function newTransaction(){ cart=[]; renderCart(); }

// Close on overlay click
document.querySelectorAll('.modal-overlay').forEach(o=>{
    o.addEventListener('click',e=>{ if(e.target===o){ o.classList.remove('open'); if(o.id==='receiptModal') newTransaction(); } });
});

// ── Stock alert banner ──────────────────────────────────────────────
let alertDismissed = false;
let alertCollapsed = false;

function renderStockAlerts() {
    if (alertDismissed) return;
    const inv = getInventory();
    const outItems = inv.filter(i => i.stock === 0);
    const lowItems = inv.filter(i => i.stock > 0 && i.stock <= (i.reorderLevel || 20));
    const total = outItems.length + lowItems.length;

    const bar   = document.getElementById('stockAlertBar');
    const chips = document.getElementById('alertChips');
    const count = document.getElementById('alertCount');

    if (total === 0) { bar.classList.add('hidden'); return; }

    bar.classList.remove('hidden');
    count.textContent = total + ' item' + (total > 1 ? 's' : '');

    // Shift icon colour: red if any out-of-stock, amber if only low
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
        ...outItems.map(i =>
            `<span class="alert-chip chip-out" title="Out of Stock">` +
            `<span class="chip-dot"></span>${i.name}</span>`),
        ...lowItems.map(i =>
            `<span class="alert-chip chip-low" title="Low Stock – ${i.stock} ${i.unit} left">` +
            `<span class="chip-dot"></span>${i.name} <em class="chip-note">(${i.stock})</em></span>`)
    ].join('');
}

// Collapse / expand chips
document.getElementById('alertToggle').addEventListener('click', function () {
    alertCollapsed = !alertCollapsed;
    document.getElementById('alertChipsWrap').classList.toggle('collapsed', alertCollapsed);
    document.getElementById('alertToggleIcon').className =
        alertCollapsed ? 'fa fa-chevron-down' : 'fa fa-chevron-up';
});

// Dismiss banner for this session
document.getElementById('alertDismiss').addEventListener('click', function () {
    alertDismissed = true;
    document.getElementById('stockAlertBar').classList.add('hidden');
});

renderProducts();
renderStockAlerts();
</script>
</body>
</html>
