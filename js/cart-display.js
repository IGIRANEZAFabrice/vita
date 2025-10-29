/**
 * Cart Display - Renders cart items on cart page
 */

// Load and display cart on page load
document.addEventListener('DOMContentLoaded', function() {
    loadCartDisplay();
    
    // Setup event listeners
    setupEventListeners();
});

// Listen for cart updates
window.addEventListener('cartUpdated', function() {
    loadCartDisplay();
});

/**
 * Setup event listeners
 */
function setupEventListeners() {
    // Clear cart button
    const clearBtn = document.getElementById('clear-cart-btn');
    if (clearBtn) {
        clearBtn.addEventListener('click', function() {
            if (confirm('Are you sure you want to clear your entire cart?')) {
                simpleCart.clearCart();
                showNotification('Cart cleared successfully', 'success');
            }
        });
    }
}

/**
 * Load and display cart
 */
async function loadCartDisplay() {
    const cart = simpleCart.getCart();
    
    const loadingEl = document.getElementById('loading-cart');
    const contentEl = document.getElementById('cart-content');
    const emptyEl = document.getElementById('empty-cart');
    
    if (cart.length === 0) {
        // Show empty cart
        loadingEl.style.display = 'none';
        contentEl.style.display = 'none';
        emptyEl.style.display = 'block';
        
        // Update header count
        updateHeaderCount(0);
        return;
    }
    
    // Fetch product details from database
    const cartWithDetails = await fetchProductDetails(cart);
    
    // Render cart
    renderCart(cartWithDetails);
    
    // Hide loading, show content
    loadingEl.style.display = 'none';
    contentEl.style.display = 'block';
    emptyEl.style.display = 'none';
}

/**
 * Fetch product details from database
 */
async function fetchProductDetails(cart) {
    const productIds = cart.map(item => item.product_id).join(',');
    
    try {
        const response = await fetch(`api/get-products.php?ids=${productIds}`);
        const products = await response.json();
        
        // Merge cart data with product data
        return cart.map(cartItem => {
            const product = products.find(p => p.product_id == cartItem.product_id);
            if (product) {
                return {
                    ...cartItem,
                    product_name: product.product_name,
                    price: parseFloat(product.price),
                    sku: product.sku,
                    stock_quantity: product.stock_quantity,
                    image_url: product.image_url,
                    category_name: product.category_name,
                    short_description: product.short_description,
                    subtotal: parseFloat(product.price) * cartItem.quantity
                };
            }
            return cartItem;
        });
    } catch (error) {
        console.error('Error fetching product details:', error);
        // Return cart with existing data
        return cart.map(item => ({
            ...item,
            subtotal: item.price * item.quantity
        }));
    }
}

/**
 * Render cart items
 */
function renderCart(cartItems) {
    const listEl = document.getElementById('cart-items-list');
    const total = cartItems.reduce((sum, item) => sum + item.subtotal, 0);
    const totalItems = cartItems.reduce((sum, item) => sum + item.quantity, 0);
    
    // Update counts
    updateHeaderCount(totalItems);
    document.getElementById('total-items-count').textContent = totalItems;
    document.getElementById('summary-items-count').textContent = totalItems;
    document.getElementById('summary-subtotal').textContent = '$' + total.toFixed(2);
    document.getElementById('summary-total').textContent = '$' + total.toFixed(2);
    
    // Render items
    let html = '';
    
    cartItems.forEach(item => {
        const imageUrl = item.image_url ? item.image_url : 'images/placeholder.jpg';
        
        html += `
            <div class="cart-item" data-product-id="${item.product_id}">
                <div class="cart-item-image">
                    <img src="${imageUrl}" alt="${item.product_name}" onerror="this.src='images/placeholder.jpg'">
                </div>

                <div class="cart-item-details">
                    <h3>${item.product_name}</h3>
                    ${item.category_name ? `<p class="item-category">${item.category_name}</p>` : ''}
                    <p class="item-sku">SKU: ${item.sku}</p>
                    
                    ${item.stock_quantity > 0 ? `
                        <span class="stock-status in-stock">
                            <i class="fas fa-check-circle"></i> In Stock (${item.stock_quantity} available)
                        </span>
                    ` : `
                        <span class="stock-status out-of-stock">
                            <i class="fas fa-times-circle"></i> Out of Stock
                        </span>
                    `}
                </div>

                <div class="cart-item-price">
                    <span class="price-label">Price:</span>
                    <span class="price">$${parseFloat(item.price).toFixed(2)}</span>
                </div>

                <div class="cart-item-quantity">
                    <label>Quantity:</label>
                    <div class="quantity-controls">
                        <button class="qty-btn qty-decrease" onclick="updateCartItemQuantity(${item.product_id}, ${item.quantity - 1})">
                            <i class="fas fa-minus"></i>
                        </button>
                        <input type="number" 
                               class="qty-input" 
                               value="${item.quantity}" 
                               min="1" 
                               max="${item.stock_quantity || 999}"
                               onchange="updateCartItemQuantity(${item.product_id}, this.value)">
                        <button class="qty-btn qty-increase" onclick="updateCartItemQuantity(${item.product_id}, ${item.quantity + 1})">
                            <i class="fas fa-plus"></i>
                        </button>
                    </div>
                </div>

                <div class="cart-item-subtotal">
                    <span class="subtotal-label">Subtotal:</span>
                    <span class="subtotal">$${item.subtotal.toFixed(2)}</span>
                </div>

                <div class="cart-item-actions">
                    <button class="btn-remove" onclick="removeCartItem(${item.product_id})" title="Remove">
                        <i class="fas fa-trash-alt"></i>
                    </button>
                </div>
            </div>
        `;
    });
    
    listEl.innerHTML = html;
}

/**
 * Update header cart count
 */
function updateHeaderCount(count) {
    const headerCount = document.getElementById('header-cart-count');
    if (headerCount) {
        headerCount.textContent = count;
    }
}

/**
 * Update cart item quantity
 */
window.updateCartItemQuantity = function(productId, newQuantity) {
    newQuantity = parseInt(newQuantity);
    
    if (newQuantity < 1) {
        if (confirm('Remove this item from cart?')) {
            removeCartItem(productId);
        }
        return;
    }
    
    const result = simpleCart.updateQuantity(productId, newQuantity);
    
    if (result.success) {
        // Cart will auto-reload via event listener
    } else {
        showNotification(result.message, 'error');
    }
};

/**
 * Remove cart item
 */
window.removeCartItem = function(productId) {
    if (!confirm('Remove this item from cart?')) {
        return;
    }
    
    const result = simpleCart.removeFromCart(productId);
    
    if (result.success) {
        showNotification('Item removed from cart', 'success');
        // Cart will auto-reload via event listener
    } else {
        showNotification(result.message, 'error');
    }
};

/**
 * Show notification
 */
function showNotification(message, type = 'info') {
    const container = document.getElementById('notification-container') || document.body;
    
    const notification = document.createElement('div');
    notification.className = `cart-notification cart-notification-${type}`;
    notification.innerHTML = `
        <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'}"></i>
        <span>${message}</span>
    `;
    
    container.appendChild(notification);
    
    setTimeout(() => {
        notification.classList.add('show');
    }, 10);
    
    setTimeout(() => {
        notification.classList.remove('show');
        setTimeout(() => {
            notification.remove();
        }, 300);
    }, 3000);
}

