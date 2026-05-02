<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventory – Sharon Store</title>
    <meta name="description" content="Manage your Sharon Store product inventory – add, edit, and track stock levels.">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="app.css">
    <style>
        .stock-chip {
            display: inline-flex; align-items: center; gap: 5px;
            padding: 4px 10px; border-radius: 20px;
            font-size: 12px; font-weight: 600;
        }
        .stock-dot { width: 6px; height: 6px; border-radius: 50%; }
        .stock-ok   { background: rgba(34,197,94,0.12); color: #22c55e; }
        .stock-ok .stock-dot { background: #22c55e; }
        .stock-low  { background: rgba(245,158,11,0.12); color: #f59e0b; }
        .stock-low .stock-dot { background: #f59e0b; }
        .stock-out  { background: rgba(239,68,68,0.12); color: #ef4444; }
        .stock-out .stock-dot { background: #ef4444; }

        .cat-badge {
            padding: 3px 10px; border-radius: 20px;
            font-size: 11px; font-weight: 600;
            background: rgba(124,58,237,0.15); color: #a855f7;
        }

        .table-wrap { background: var(--surface2); border: 1px solid var(--border); border-radius: var(--radius); overflow: auto; }

        .bulk-bar {
            display: none;
            align-items: center;
            gap: 12px;
            padding: 10px 16px;
            background: rgba(233,30,140,0.08);
            border: 1px solid rgba(233,30,140,0.2);
            border-radius: 10px;
            margin-bottom: 14px;
            font-size: 13px;
            color: var(--pink-light);
        }
        .bulk-bar.visible { display: flex; }

        .sort-th { cursor: pointer; user-select: none; }
        .sort-th:hover { color: var(--text); }
        .sort-th .sort-icon { margin-left: 4px; opacity: 0.4; }
        .sort-th.asc .sort-icon, .sort-th.desc .sort-icon { opacity: 1; color: var(--pink-light); }

        .no-results { text-align: center; padding: 48px; color: var(--muted); font-size: 14px; }
        .import-export { display: flex; gap: 8px; }
    </style>
</head>
<body>
    <!-- Sidebar -->
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

    <main class="main-content">
        <header class="topbar">
            <button class="menu-toggle" id="menuToggle"><i class="fa fa-bars"></i></button>
            <div class="topbar-title">Inventory Management</div>
            <div class="topbar-right">
                <span class="topbar-date" id="topbarDate"></span>
            </div>
        </header>

        <div class="page-body">
            <!-- Stats row -->
            <div class="stats-grid" style="margin-bottom:20px;">
                <div class="stat-card" style="--accent:#22c55e">
                    <div class="stat-icon"><i class="fa fa-boxes-stacked"></i></div>
                    <div class="stat-info"><div class="stat-value" id="invTotal">0</div><div class="stat-label">Total Products</div></div>
                </div>
                <div class="stat-card" style="--accent:#f59e0b">
                    <div class="stat-icon"><i class="fa fa-triangle-exclamation"></i></div>
                    <div class="stat-info"><div class="stat-value" id="invLow">0</div><div class="stat-label">Low Stock</div></div>
                </div>
                <div class="stat-card" style="--accent:#ef4444">
                    <div class="stat-icon"><i class="fa fa-ban"></i></div>
                    <div class="stat-info"><div class="stat-value" id="invOut">0</div><div class="stat-label">Out of Stock</div></div>
                </div>
                <div class="stat-card" style="--accent:#0ea5e9">
                    <div class="stat-icon"><i class="fa fa-peso-sign"></i></div>
                    <div class="stat-info"><div class="stat-value" id="invValue">₱0</div><div class="stat-label">Total Stock Value</div></div>
                </div>
            </div>

            <!-- Controls row -->
            <div class="page-controls">
                <div class="search-wrap">
                    <i class="fa fa-magnifying-glass"></i>
                    <input type="text" class="search-input" id="searchInput" placeholder="Search products…">
                </div>
                <select class="filter-select" id="catFilter">
                    <option value="">All Categories</option>
                    <option value="Canned Goods">Canned Goods</option>
                    <option value="Dry Commodities">Dry Commodities</option>
                    <option value="Household Essentials">Household Essentials</option>
                    <option value="Condiments">Condiments</option>
                </select>
                <select class="filter-select" id="stockFilter">
                    <option value="">All Stock</option>
                    <option value="ok">In Stock</option>
                    <option value="low">Low Stock</option>
                    <option value="out">Out of Stock</option>
                </select>
                <div style="margin-left:auto; display:flex; gap:8px;">
                    <button class="btn btn-outline" id="exportBtn"><i class="fa fa-download"></i> Export CSV</button>
                    <button class="btn btn-pink" id="addProductBtn"><i class="fa fa-plus"></i> Add Product</button>
                </div>
            </div>

            <!-- Bulk bar -->
            <div class="bulk-bar" id="bulkBar">
                <i class="fa fa-check-circle"></i>
                <span id="bulkCount">0 selected</span>
                <button class="btn btn-danger btn-sm" id="bulkDeleteBtn"><i class="fa fa-trash"></i> Delete Selected</button>
            </div>

            <!-- Table -->
            <div class="table-wrap">
                <table class="data-table" id="inventoryTable">
                    <thead>
                        <tr>
                            <th style="width:40px;"><input type="checkbox" id="selectAll" style="accent-color:var(--pink);"></th>
                            <th class="sort-th" data-col="name">Product Name <i class="fa fa-sort sort-icon"></i></th>
                            <th class="sort-th" data-col="category">Category <i class="fa fa-sort sort-icon"></i></th>
                            <th class="sort-th" data-col="price">Price <i class="fa fa-sort sort-icon"></i></th>
                            <th class="sort-th" data-col="stock">Stock <i class="fa fa-sort sort-icon"></i></th>
                            <th>Unit</th>
                            <th>Reorder Level</th>
                            <th>Status</th>
                            <th style="text-align:center;">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="inventoryBody"></tbody>
                </table>
                <div class="no-results" id="noResults" style="display:none;">
                    <i class="fa fa-search fa-2x" style="margin-bottom:8px; display:block;"></i>
                    No products match your search.
                </div>
            </div>

            <!-- Pagination -->
            <div class="pagination" id="pagination"></div>
        </div>
    </main>

    <!-- Add/Edit Modal -->
    <div class="modal-overlay" id="productModal">
        <div class="modal">
            <div class="modal-header">
                <h3 id="modalTitle">Add Product</h3>
                <button class="modal-close" onclick="closeModal('productModal')"><i class="fa fa-xmark"></i></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="editId">
                <div class="mf-group">
                    <label class="mf-label">Product Name *</label>
                    <input type="text" class="mf-input" id="mfName" placeholder="e.g. Century Tuna Flakes" required>
                </div>
                <div class="mf-row">
                    <div class="mf-group">
                        <label class="mf-label">Category *</label>
                        <select class="mf-input" id="mfCategory">
                            <option value="Canned Goods">Canned Goods</option>
                            <option value="Dry Commodities">Dry Commodities</option>
                            <option value="Household Essentials">Household Essentials</option>
                            <option value="Condiments">Condiments</option>
                        </select>
                    </div>
                    <div class="mf-group">
                        <label class="mf-label">Unit *</label>
                        <select class="mf-input" id="mfUnit">
                            <option value="Pieces">Pieces</option>
                            <option value="Packs">Packs</option>
                            <option value="Kilograms">Kilograms</option>
                            <option value="Bottles">Bottles</option>
                            <option value="Liters">Liters</option>
                            <option value="Grams">Grams</option>
                        </select>
                    </div>
                </div>
                <div class="mf-row">
                    <div class="mf-group">
                        <label class="mf-label">Price (₱) *</label>
                        <input type="number" class="mf-input" id="mfPrice" placeholder="0.00" step="0.01" min="0">
                    </div>
                    <div class="mf-group">
                        <label class="mf-label">Stock Level *</label>
                        <input type="number" class="mf-input" id="mfStock" placeholder="0" min="0">
                    </div>
                </div>
                <div class="mf-group">
                    <label class="mf-label">Reorder Level <span style="color:var(--muted); font-weight:400;">(alert threshold)</span></label>
                    <input type="number" class="mf-input" id="mfReorder" placeholder="20" min="0">
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-outline" onclick="closeModal('productModal')">Cancel</button>
                <button class="btn btn-pink" id="saveProductBtn">Save Product</button>
            </div>
        </div>
    </div>

    <!-- Delete Confirm Modal -->
    <div class="modal-overlay" id="deleteModal">
        <div class="modal" style="max-width:380px;">
            <div class="modal-header">
                <h3>Delete Product</h3>
                <button class="modal-close" onclick="closeModal('deleteModal')"><i class="fa fa-xmark"></i></button>
            </div>
            <div class="modal-body" style="text-align:center; padding:28px 24px;">
                <div style="font-size:40px; margin-bottom:12px;">🗑️</div>
                <p style="font-size:14px; color:var(--text2); margin-bottom:6px;">Are you sure you want to delete</p>
                <p style="font-size:16px; font-weight:700; color:var(--text);" id="deleteProductName">"Product Name"</p>
                <p style="font-size:12px; color:var(--muted); margin-top:8px;">This action cannot be undone.</p>
            </div>
            <div class="modal-footer">
                <button class="btn btn-outline" onclick="closeModal('deleteModal')">Cancel</button>
                <button class="btn btn-danger" id="confirmDeleteBtn">Delete</button>
            </div>
        </div>
    </div>

    <script src="app.js"></script>
    <script>
        checkAuth();
        requireRole(['Admin', 'Manager']);
        initNav('nav-inventory');
        document.getElementById('topbarDate').textContent = new Date().toLocaleDateString('en-PH', { weekday:'long', year:'numeric', month:'long', day:'numeric' });

        const ITEMS_PER_PAGE = 10;
        let currentPage = 1;
        let sortCol = 'name';
        let sortDir = 'asc';
        let deleteTargetId = null;
        let inventory = getInventory();

        function getStockStatus(item) {
            if (item.stock === 0) return 'out';
            if (item.stock <= (item.reorderLevel || 20)) return 'low';
            return 'ok';
        }

        function stockChip(item) {
            const s = getStockStatus(item);
            const labels = { ok: 'In Stock', low: 'Low Stock', out: 'Out of Stock' };
            return `<span class="stock-chip stock-${s}"><span class="stock-dot"></span>${labels[s]}</span>`;
        }

        function updateStats() {
            inventory = getInventory();
            document.getElementById('invTotal').textContent = inventory.length;
            document.getElementById('invLow').textContent = inventory.filter(i => getStockStatus(i) === 'low').length;
            document.getElementById('invOut').textContent = inventory.filter(i => getStockStatus(i) === 'out').length;
            const val = inventory.reduce((s, i) => s + i.price * i.stock, 0);
            document.getElementById('invValue').textContent = '₱' + val.toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        }

        function getFiltered() {
            inventory = getInventory();
            const q = document.getElementById('searchInput').value.toLowerCase();
            const cat = document.getElementById('catFilter').value;
            const stock = document.getElementById('stockFilter').value;
            return inventory.filter(item => {
                const matchQ = item.name.toLowerCase().includes(q) || item.category.toLowerCase().includes(q);
                const matchCat = !cat || item.category === cat;
                const matchStock = !stock || getStockStatus(item) === stock;
                return matchQ && matchCat && matchStock;
            }).sort((a, b) => {
                const va = a[sortCol]; const vb = b[sortCol];
                if (typeof va === 'number') return sortDir === 'asc' ? va - vb : vb - va;
                return sortDir === 'asc' ? String(va).localeCompare(String(vb)) : String(vb).localeCompare(String(va));
            });
        }

        function renderTable() {
            const filtered = getFiltered();
            const tbody = document.getElementById('inventoryBody');
            const noResults = document.getElementById('noResults');
            const start = (currentPage - 1) * ITEMS_PER_PAGE;
            const page = filtered.slice(start, start + ITEMS_PER_PAGE);

            if (filtered.length === 0) {
                tbody.innerHTML = '';
                noResults.style.display = 'block';
            } else {
                noResults.style.display = 'none';
                tbody.innerHTML = page.map(item => `
                    <tr>
                        <td><input type="checkbox" class="row-check" data-id="${item.id}" style="accent-color:var(--pink);"></td>
                        <td class="td-name">${item.name}</td>
                        <td><span class="cat-badge">${item.category}</span></td>
                        <td style="font-weight:700; color:var(--success);">₱${item.price.toFixed(2)}</td>
                        <td style="font-weight:700;">${item.stock} <small style="color:var(--muted);">${item.unit}</small></td>
                        <td style="color:var(--muted);">${item.unit}</td>
                        <td style="color:var(--muted);">${item.reorderLevel || 20}</td>
                        <td>${stockChip(item)}</td>
                        <td style="text-align:center;">
                            <button class="btn btn-outline btn-sm" onclick="editProduct(${item.id})"><i class="fa fa-pen"></i></button>
                            <button class="btn btn-danger btn-sm" onclick="deleteProduct(${item.id})" style="margin-left:4px;"><i class="fa fa-trash"></i></button>
                        </td>
                    </tr>
                `).join('');
            }

            renderPagination(filtered.length);
            updateBulkBar();
            updateStats();
        }

        function renderPagination(total) {
            const pages = Math.ceil(total / ITEMS_PER_PAGE);
            const pg = document.getElementById('pagination');
            pg.innerHTML = '';
            if (pages <= 1) return;
            for (let i = 1; i <= pages; i++) {
                const btn = document.createElement('button');
                btn.className = `pg-btn${i === currentPage ? ' active' : ''}`;
                btn.textContent = i;
                btn.onclick = () => { currentPage = i; renderTable(); };
                pg.appendChild(btn);
            }
        }

        // Sorting
        document.querySelectorAll('.sort-th').forEach(th => {
            th.addEventListener('click', () => {
                const col = th.dataset.col;
                if (sortCol === col) sortDir = sortDir === 'asc' ? 'desc' : 'asc';
                else { sortCol = col; sortDir = 'asc'; }
                document.querySelectorAll('.sort-th').forEach(t => { t.classList.remove('asc','desc'); t.querySelector('.sort-icon').className = 'fa fa-sort sort-icon'; });
                th.classList.add(sortDir);
                th.querySelector('.sort-icon').className = `fa fa-sort-${sortDir === 'asc' ? 'up' : 'down'} sort-icon`;
                currentPage = 1;
                renderTable();
            });
        });

        // Filters
        ['searchInput','catFilter','stockFilter'].forEach(id => {
            document.getElementById(id).addEventListener('input', () => { currentPage = 1; renderTable(); });
        });

        // Select all
        document.getElementById('selectAll').addEventListener('change', function() {
            document.querySelectorAll('.row-check').forEach(c => c.checked = this.checked);
            updateBulkBar();
        });
        document.addEventListener('change', e => {
            if (e.target.classList.contains('row-check')) updateBulkBar();
        });

        function updateBulkBar() {
            const checked = document.querySelectorAll('.row-check:checked');
            const bar = document.getElementById('bulkBar');
            document.getElementById('bulkCount').textContent = `${checked.length} selected`;
            if (checked.length > 0) bar.classList.add('visible'); else bar.classList.remove('visible');
        }

        // Bulk delete
        document.getElementById('bulkDeleteBtn').addEventListener('click', () => {
            const ids = [...document.querySelectorAll('.row-check:checked')].map(c => parseInt(c.dataset.id));
            if (!ids.length) return;
            if (!confirm(`Delete ${ids.length} product(s)?`)) return;
            let inv = getInventory().filter(i => !ids.includes(i.id));
            saveInventory(inv);
            showToast(`${ids.length} product(s) deleted.`, 'success');
            renderTable();
        });

        // Add product
        document.getElementById('addProductBtn').addEventListener('click', () => {
            document.getElementById('modalTitle').textContent = 'Add Product';
            document.getElementById('editId').value = '';
            document.getElementById('mfName').value = '';
            document.getElementById('mfCategory').value = 'Canned Goods';
            document.getElementById('mfUnit').value = 'Pieces';
            document.getElementById('mfPrice').value = '';
            document.getElementById('mfStock').value = '';
            document.getElementById('mfReorder').value = '20';
            openModal('productModal');
        });

        // Edit product
        window.editProduct = function(id) {
            const item = getInventory().find(i => i.id === id);
            if (!item) return;
            document.getElementById('modalTitle').textContent = 'Edit Product';
            document.getElementById('editId').value = id;
            document.getElementById('mfName').value = item.name;
            document.getElementById('mfCategory').value = item.category;
            document.getElementById('mfUnit').value = item.unit;
            document.getElementById('mfPrice').value = item.price;
            document.getElementById('mfStock').value = item.stock;
            document.getElementById('mfReorder').value = item.reorderLevel || 20;
            openModal('productModal');
        };

        // Save product
        document.getElementById('saveProductBtn').addEventListener('click', () => {
            const name = document.getElementById('mfName').value.trim();
            const cat = document.getElementById('mfCategory').value;
            const unit = document.getElementById('mfUnit').value;
            const price = parseFloat(document.getElementById('mfPrice').value);
            const stock = parseInt(document.getElementById('mfStock').value);
            const reorder = parseInt(document.getElementById('mfReorder').value) || 20;

            if (!name || isNaN(price) || isNaN(stock)) {
                showToast('Please fill in all required fields.', 'error'); return;
            }

            let inv = getInventory();
            const editId = document.getElementById('editId').value;

            if (editId) {
                const idx = inv.findIndex(i => i.id === parseInt(editId));
                inv[idx] = { ...inv[idx], name, category: cat, unit, price, stock, reorderLevel: reorder };
                showToast('Product updated successfully.', 'success');
            } else {
                const newId = Math.max(...inv.map(i => i.id), 0) + 1;
                inv.push({ id: newId, name, category: cat, unit, price, stock, reorderLevel: reorder });
                showToast('Product added successfully.', 'success');
            }

            saveInventory(inv);
            closeModal('productModal');
            renderTable();
        });

        // Delete product
        window.deleteProduct = function(id) {
            const item = getInventory().find(i => i.id === id);
            deleteTargetId = id;
            document.getElementById('deleteProductName').textContent = `"${item.name}"`;
            openModal('deleteModal');
        };

        document.getElementById('confirmDeleteBtn').addEventListener('click', () => {
            if (!deleteTargetId) return;
            let inv = getInventory().filter(i => i.id !== deleteTargetId);
            saveInventory(inv);
            showToast('Product deleted.', 'success');
            closeModal('deleteModal');
            deleteTargetId = null;
            renderTable();
        });

        // Export CSV
        document.getElementById('exportBtn').addEventListener('click', () => {
            const inv = getInventory();
            const headers = ['ID','Name','Category','Price','Stock','Unit','Reorder Level','Status'];
            const rows = inv.map(i => [i.id, i.name, i.category, i.price.toFixed(2), i.stock, i.unit, i.reorderLevel || 20, getStockStatus(i)]);
            const csv = [headers, ...rows].map(r => r.join(',')).join('\n');
            const a = document.createElement('a');
            a.href = 'data:text/csv;charset=utf-8,' + encodeURIComponent(csv);
            a.download = 'sharon_inventory_' + new Date().toISOString().slice(0,10) + '.csv';
            a.click();
            showToast('Inventory exported as CSV.', 'info');
        });

        // Close modals on overlay click
        document.querySelectorAll('.modal-overlay').forEach(o => {
            o.addEventListener('click', e => { if (e.target === o) o.classList.remove('open'); });
        });

        renderTable();
    </script>
</body>
</html>
