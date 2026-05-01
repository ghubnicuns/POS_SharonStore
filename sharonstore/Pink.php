<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sharon Store - Point of Sale & Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="container-fluid pos-container d-flex flex-column">
    <div class="row flex-grow-1 h-100">
        
        <div class="col-md-7 p-4 h-100 d-flex flex-column">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h2 class="text-success fw-bold m-0">Sharon Store POS</h2>
                <button class="btn btn-outline-dark btn-sm fw-bold" onclick="openAdminPanel()">⚙️ Admin Setup</button>
            </div>
            
            <div class="mb-3 d-flex gap-2">
                <button class="btn btn-outline-success active" onclick="filterProducts('All')">All Items</button>
                <button class="btn btn-outline-secondary" onclick="filterProducts('Canned Goods')">Canned Goods</button>
                <button class="btn btn-outline-secondary" onclick="filterProducts('Dry Commodities')">Dry Commodities</button>
                <button class="btn btn-outline-secondary" onclick="filterProducts('Household Essentials')">Household Essentials</button>
            </div>

            <div class="product-scroll-area">
                <div class="row g-3" id="productGrid">
                </div>
            </div>
        </div>

        <div class="col-md-5 p-4 cart-section h-100 d-flex flex-column shadow-sm">
            <h4 class="mb-3 text-dark border-bottom pb-2">Current Cart</h4>
            
            <div class="cart-scroll-area flex-grow-1">
                <table class="table table-hover align-middle">
                    <thead class="table-light sticky-top">
                        <tr>
                            <th>Item</th>
                            <th class="text-center">Quantity</th>
                            <th class="text-end">Subtotal</th>
                            <th class="text-center"></th>
                        </tr>
                    </thead>
                    <tbody id="cartTableBody">
                    </tbody>
                </table>
            </div>
            
            <div class="mt-auto pt-3 border-top bg-white">
                <div class="d-flex justify-content-between mb-3">
                    <h4 class="m-0 text-muted">Total:</h4>
                    <h4 class="m-0 text-danger fw-bold">₱<span id="cartTotal">0.00</span></h4>
                </div>
                
                <div class="d-flex gap-2">
                    <button id="clearAllBtn" class="btn btn-outline-danger btn-lg w-25 fw-bold shadow-sm py-3">Clear All</button>
                    <button id="checkoutBtn" class="btn btn-success btn-lg w-75 fw-bold shadow-sm py-3">Process Transaction</button>
                </div>
            </div>
        </div>

    </div>
</div>

<div class="modal fade" id="checkoutModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title fw-bold">Confirm Transaction</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center py-5">
                <h5 class="text-muted mb-3">Total Amount Due:</h5>
                <h1 class="text-danger fw-bold display-4 mb-4">₱<span id="modalTotalAmount">0.00</span></h1>
                <p class="text-muted mb-0">Do you want to finalize this transaction?</p>
            </div>
            <div class="modal-footer justify-content-center border-top-0 pt-0 pb-4">
                <button type="button" class="btn btn-outline-secondary btn-lg px-4" data-bs-dismiss="modal">Cancel / Edit</button>
                <button type="button" class="btn btn-success btn-lg px-4 shadow-sm fw-bold" id="confirmPaymentBtn">Confirm Payment</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="adminPanelModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title fw-bold">Inventory Management Panel</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body bg-light">
                <div class="d-flex justify-content-between mb-3">
                    <h5 class="text-secondary fw-bold">Active Products</h5>
                    <button class="btn btn-success fw-bold shadow-sm" onclick="openProductForm()">+ Add New Product</button>
                </div>
                <div class="table-responsive bg-white rounded shadow-sm border">
                    <table class="table table-hover align-middle m-0">
                        <thead class="table-light">
                            <tr>
                                <th>Name</th>
                                <th>Category</th>
                                <th>Price</th>
                                <th>Stock</th>
                                <th>Unit</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="adminTableBody">
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="productFormModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title fw-bold" id="productModalTitle">Add Product</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="productForm" onsubmit="saveProduct(event)">
                <div class="modal-body p-4">
                    <input type="hidden" id="editProductId">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Product Name</label>
                        <input type="text" class="form-control" id="prodName" required>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Category</label>
                            <select class="form-select" id="prodCategory" required>
                                <option value="Canned Goods">Canned Goods</option>
                                <option value="Dry Commodities">Dry Commodities</option>
                                <option value="Household Essentials">Household Essentials</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Unit</label>
                            <select class="form-select" id="prodUnit" required>
                                <option value="Pieces">Pieces</option>
                                <option value="Packs">Packs</option>
                                <option value="Kilograms">Kilograms</option>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Price (₱)</label>
                            <input type="number" class="form-control" id="prodPrice" step="0.01" min="0" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Stock Level</label>
                            <input type="number" class="form-control" id="prodStock" min="0" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-outline-secondary fw-bold" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary fw-bold px-4">Save Product</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
    let inventoryItems = [
        { id: 1, name: "Century Tuna", category: "Canned Goods", price: 35.00, stock: 150, unit: "Pieces" },
        { id: 2, name: "Corned Beef", category: "Canned Goods", price: 45.00, stock: 100, unit: "Pieces" },
        { id: 3, name: "Jasmine Rice 1kg", category: "Dry Commodities", price: 55.00, stock: 80, unit: "Kilograms" },
        { id: 4, name: "Brown Sugar 1kg", category: "Dry Commodities", price: 60.00, stock: 40, unit: "Kilograms" },
        { id: 5, name: "Tide Powder", category: "Household Essentials", price: 15.00, stock: 200, unit: "Packs" },
        { id: 6, name: "Dishwashing Liquid", category: "Household Essentials", price: 25.00, stock: 90, unit: "Pieces" }
    ];

    let cart = [];
    let currentCategory = 'All';
    let nextProductId = 7;
    let adminPanelModal, productFormModal, checkoutModal;

    document.addEventListener('DOMContentLoaded', () => {
        renderProducts('All');
        adminPanelModal = new bootstrap.Modal(document.getElementById('adminPanelModal'));
        productFormModal = new bootstrap.Modal(document.getElementById('productFormModal'));
        checkoutModal = new bootstrap.Modal(document.getElementById('checkoutModal'));
    });

    function renderProducts(categoryFilter) {
        const grid = document.getElementById('productGrid');
        grid.innerHTML = ''; 
        
        const filteredItems = categoryFilter === 'All' 
            ? inventoryItems 
            : inventoryItems.filter(item => item.category === categoryFilter);

        filteredItems.forEach(item => {
            const outOfStock = item.stock <= 0;
            const cardClass = outOfStock ? 'opacity-50' : 'product-card';
            const clickAction = outOfStock ? `alert('Out of stock!')` : `addToCart(${item.id})`;

            grid.innerHTML += `
                <div class="col-sm-6 col-md-4 col-lg-3">
                    <div class="card h-100 ${cardClass}" onclick="${clickAction}">
                        <div class="card-body text-center d-flex flex-column justify-content-between">
                            <h6 class="card-title fw-bold text-dark mb-1">${item.name}</h6>
                            <small class="text-muted mb-2 d-block">${item.category}</small>
                            <h5 class="text-success fw-bold mb-2">₱${item.price.toFixed(2)}</h5>
                            <span class="badge ${outOfStock ? 'bg-danger' : 'bg-light text-dark border'}">Stock: ${item.stock} ${item.unit}</span>
                        </div>
                    </div>
                </div>
            `;
        });
    }

    window.filterProducts = function(category) {
        currentCategory = category;
        document.querySelectorAll('.btn-outline-secondary, .btn-outline-success').forEach(btn => {
            btn.classList.remove('active', 'btn-outline-success');
            btn.classList.add('btn-outline-secondary');
            if (btn.textContent.includes(category)) {
                btn.classList.add('active', 'btn-outline-success');
                btn.classList.remove('btn-outline-secondary');
            }
        });
        renderProducts(category);
    }

    window.addToCart = function(productId) {
        const product = inventoryItems.find(p => p.id === productId);
        const existingItem = cart.find(item => item.product_id === productId);
        const currentQty = existingItem ? existingItem.quantity : 0;

        if (currentQty + 1 > product.stock) {
            alert(`Cannot add more. Only ${product.stock} available.`);
            return;
        }

        if (existingItem) {
            existingItem.quantity += 1;
            existingItem.subtotal = existingItem.quantity * product.price;
        } else {
            cart.push({ product_id: product.id, name: product.name, price: product.price, quantity: 1, subtotal: product.price });
        }
        updateCartUI();
    }

    window.changeQuantity = function(index, amount) {
        const item = cart[index];
        const product = inventoryItems.find(p => p.id === item.product_id);

        if (amount > 0 && item.quantity + amount > product.stock) {
            alert(`Cannot add more. Stock limit reached for ${product.name}.`);
            return;
        }

        item.quantity += amount;
        if (item.quantity <= 0) cart.splice(index, 1);
        else item.subtotal = item.quantity * item.price;
        updateCartUI();
    }

    window.setQuantity = function(index, newQuantity) {
        const item = cart[index];
        const product = inventoryItems.find(p => p.id === item.product_id);
        let parsedQty = parseInt(newQuantity);

        if (isNaN(parsedQty) || parsedQty <= 0) {
            parsedQty = 1;
        } else if (parsedQty > product.stock) {
            alert(`Cannot add ${parsedQty}. Only ${product.stock} units of ${product.name} are available.`);
            parsedQty = product.stock;
        }

        item.quantity = parsedQty;
        item.subtotal = item.quantity * item.price;
        updateCartUI();
    }

    window.removeFromCart = function(index) {
        cart.splice(index, 1);
        updateCartUI();
    }

    function updateCartUI() {
        const tbody = document.getElementById('cartTableBody');
        let total = 0;
        tbody.innerHTML = '';

        cart.forEach((item, index) => {
            total += item.subtotal;
            tbody.innerHTML += `
                <tr>
                    <td class="align-middle fw-semibold">${item.name}<br><small class="text-muted fw-normal">₱${item.price.toFixed(2)}</small></td>
                    <td class="align-middle text-center">
                        <div class="d-flex align-items-center justify-content-center">
                            <button class="btn btn-outline-danger qty-btn" onclick="changeQuantity(${index}, -1)">-</button>
                            <input type="number" class="form-control qty-input" value="${item.quantity}" min="1" onchange="setQuantity(${index}, this.value)">
                            <button class="btn btn-outline-success qty-btn" onclick="changeQuantity(${index}, 1)">+</button>
                        </div>
                    </td>
                    <td class="align-middle text-end fw-bold">₱${item.subtotal.toFixed(2)}</td>
                    <td class="align-middle text-center">
                        <button class="btn btn-danger remove-btn" onclick="removeFromCart(${index})">X</button>
                    </td>
                </tr>
            `;
        });
        document.getElementById('cartTotal').textContent = total.toFixed(2);
    }

    document.getElementById('clearAllBtn').addEventListener('click', function() {
        if (cart.length > 0 && confirm("Clear all items from the cart?")) {
            cart = [];
            updateCartUI();
        }
    });

    document.getElementById('checkoutBtn').addEventListener('click', function() {
        if (cart.length === 0) {
            alert("The cart is empty! Please click a product to add items.");
            return;
        }
        
        let finalTotal = cart.reduce((sum, item) => sum + item.subtotal, 0);
        document.getElementById('modalTotalAmount').textContent = finalTotal.toFixed(2);
        
        checkoutModal.show();
    });

    document.getElementById('confirmPaymentBtn').addEventListener('click', function() {
        let finalTotal = cart.reduce((sum, item) => sum + item.subtotal, 0);
        
        cart.forEach(cartItem => {
            const product = inventoryItems.find(p => p.id === cartItem.product_id);
            if(product) product.stock -= cartItem.quantity;
        });

        alert(`Transaction Successful!\nTotal Paid: ₱${finalTotal.toFixed(2)}`);
        
        cart = [];
        updateCartUI();
        renderProducts(currentCategory); 
        
        checkoutModal.hide();
    });

    window.openAdminPanel = function() {
        renderAdminTable();
        adminPanelModal.show();
    }

    function renderAdminTable() {
        const tbody = document.getElementById('adminTableBody');
        tbody.innerHTML = '';
        
        inventoryItems.forEach(item => {
            tbody.innerHTML += `
                <tr>
                    <td class="fw-bold text-dark">${item.name}</td>
                    <td><span class="badge bg-secondary">${item.category}</span></td>
                    <td class="fw-bold text-success">₱${item.price.toFixed(2)}</td>
                    <td><span class="badge bg-dark">${item.stock}</span></td>
                    <td class="text-muted">${item.unit}</td>
                    <td class="text-center">
                        <button class="btn btn-sm btn-primary fw-bold" onclick="openProductForm(${item.id})">Edit</button>
                    </td>
                </tr>
            `;
        });
    }

    window.openProductForm = function(id = null) {
        const form = document.getElementById('productForm');
        form.reset();
        
        if (id) {
            const item = inventoryItems.find(p => p.id === id);
            document.getElementById('productModalTitle').textContent = "Edit Product";
            document.getElementById('editProductId').value = item.id;
            document.getElementById('prodName').value = item.name;
            document.getElementById('prodCategory').value = item.category;
            document.getElementById('prodPrice').value = item.price;
            document.getElementById('prodStock').value = item.stock;
            document.getElementById('prodUnit').value = item.unit;
        } else {
            document.getElementById('productModalTitle').textContent = "Add New Product";
            document.getElementById('editProductId').value = "";
        }
        productFormModal.show();
    }

    window.saveProduct = function(e) {
        e.preventDefault();
        
        const id = document.getElementById('editProductId').value;
        const name = document.getElementById('prodName').value;
        const category = document.getElementById('prodCategory').value;
        const price = parseFloat(document.getElementById('prodPrice').value);
        const stock = parseInt(document.getElementById('prodStock').value);
        const unit = document.getElementById('prodUnit').value;

        if (id) {
            const index = inventoryItems.findIndex(p => p.id == id);
            inventoryItems[index] = { id: parseInt(id), name, category, price, stock, unit };
        } else {
            inventoryItems.push({ id: nextProductId++, name, category, price, stock, unit });
        }

        renderAdminTable(); 
        renderProducts(currentCategory); 
        productFormModal.hide();
    }
</script>
</body>
</html>