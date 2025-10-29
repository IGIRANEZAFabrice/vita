/**
 * Cart Page JavaScript
 * Handles cart display and interactions for guest users
 */

// Update cart quantity
async function updateCartQuantity(cartItemId, newQuantity) {
    newQuantity = parseInt(newQuantity);
    
    if (newQuantity < 1) {
        if (confirm('Remove this item from cart?')) {
            await removeCartItem(cartItemId);
        }
        return;
    }
    
    const result = await cartManager.updateQuantity(cartItemId, newQuantity);
    
    if (result.success) {
        // Reload page to show updated cart
        location.reload();
    } else {
        alert(result.message);
    }
}

// Remove cart item
async function removeCartItem(cartItemId) {
    if (!confirm('Remove this item from cart?')) {
        return;
    }
    
    const result = await cartManager.removeFromCart(cartItemId);
    
    if (result.success) {
        showNotification('Item removed from cart', 'success');
        // Reload page to show updated cart
        setTimeout(() => {
            location.reload();
        }, 500);
    } else {
        showNotification(result.message, 'error');
    }
}

// Load guest cart (for non-logged-in users)
function loadGuestCart() {
    const guestContainer = document.getElementById('guest-cart-container');
    if (!guestContainer) return;
    
    const cart = cartManager.getLocalCart();
    
    if (cart.length === 0) {
        guestContainer.innerHTML = `
            <div class="empty-cart">
                <div class="empty-icon">
                    <i class="fas fa-shopping-cart"></i>
                </div>
                <h2>Your Cart is Empty</h2>
                <p>Add some products to get started!</p>
                <a href="../../index.php?page=product" class="btn-browse">
                    <i class="fas fa-box-open"></i> Browse Products
                </a>
                <div class="login-prompt">
                    <p><i class="fas fa-info-circle"></i> Login to save your cart and checkout</p>
                    <a href="../../index.php?page=login" class="btn-login">
                        <i class="fas fa-sign-in-alt"></i> Login
                    </a>
                </div>
            </div>
        `;
        return;
    }
    
    // Fetch product details for cart items
    fetchCartProductDetails(cart).then(cartItems => {
        renderGuestCart(cartItems);
    });
}

// Fetch product details from database
async function fetchCartProductDetails(cart) {
    const productIds = cart.map(item => item.product_id).join(',');
    
    try {
        const response = await fetch(`../../api/get-products.php?ids=${productIds}`);
        const products = await response.json();
        
        // Merge cart data with product data
        return cart.map(cartItem => {
            const product = products.find(p => p.product_id == cartItem.product_id);
            if (product) {
                return {
                    ...cartItem,
                    ...product,
                    subtotal: product.price * cartItem.quantity
                };
            }
            return null;
        }).filter(item => item !== null);
    } catch (error) {
        console.error('Error fetching product details:', error);
        return [];
    }
}

// Render guest cart
function renderGuestCart(cartItems) {
    const guestContainer = document.getElementById('guest-cart-container');
    if (!guestContainer) return;
    
    const total = cartItems.reduce((sum, item) => sum + item.subtotal, 0);
    const totalItems = cartItems.reduce((sum, item) => sum + item.quantity, 0);
    
    let html = `
        <div class="cart-content">
            <!-- Cart Items -->
            <div class="cart-items-section">
                <div class="cart-items-header">
                    <h2>Cart Items (${totalItems})</h2>
                </div>

                <div class="cart-items-list">
    `;
    
    cartItems.forEach(item => {
        const imageUrl = item.image_url ? `../${item.image_url}` : '../images/placeholder.jpg';
        
        html += `
            <div class="cart-item" data-product-id="${item.product_id}">
                <div class="cart-item-image">
                    <img src="${imageUrl}" alt="${item.product_name}">
                </div>

                <div class="cart-item-details">
                    <h3>${item.product_name}</h3>
                    <p class="item-category">${item.category_name || 'Uncategorized'}</p>
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
                        <button class="qty-btn qty-decrease" onclick="updateGuestQuantity(${item.product_id}, ${item.quantity - 1})">
                            <i class="fas fa-minus"></i>
                        </button>
                        <input type="number" 
                               class="qty-input" 
                               value="${item.quantity}" 
                               min="1" 
                               max="${item.stock_quantity}"
                               onchange="updateGuestQuantity(${item.product_id}, this.value)">
                        <button class="qty-btn qty-increase" onclick="updateGuestQuantity(${item.product_id}, ${item.quantity + 1})">
                            <i class="fas fa-plus"></i>
                        </button>
                    </div>
                </div>

                <div class="cart-item-subtotal">
                    <span class="subtotal-label">Subtotal:</span>
                    <span class="subtotal">$${item.subtotal.toFixed(2)}</span>
                </div>

                <div class="cart-item-actions">
                    <button class="btn-remove" onclick="removeGuestItem(${item.product_id})" title="Remove">
                        <i class="fas fa-trash-alt"></i>
                    </button>
                </div>
            </div>
        `;
    });
    
    html += `
                </div>
            </div>

            <!-- Cart Summary -->
            <div class="cart-summary">
                <h2>Order Summary</h2>
                
                <div class="summary-row">
                    <span>Subtotal (${totalItems} items):</span>
                    <span class="summary-value">$${total.toFixed(2)}</span>
                </div>
                
                <div class="summary-row">
                    <span>Shipping:</span>
                    <span class="summary-value">Calculated at checkout</span>
                </div>
                
                <div class="summary-row">
                    <span>Tax:</span>
                    <span class="summary-value">Calculated at checkout</span>
                </div>
                
                <div class="summary-divider"></div>
                
                <div class="summary-row summary-total">
                    <span>Total:</span>
                    <span class="summary-value">$${total.toFixed(2)}</span>
                </div>
                
                <div class="login-required">
                    <i class="fas fa-info-circle"></i>
                    <p>Please login to proceed with checkout</p>
                </div>
                
                <a href="../../index.php?page=login" class="btn-checkout">
                    <i class="fas fa-sign-in-alt"></i> Login to Checkout
                </a>
                
                <a href="../../index.php?page=product" class="btn-continue">
                    <i class="fas fa-arrow-left"></i> Continue Shopping
                </a>
                
                <div class="security-badges">
                    <i class="fas fa-shield-alt"></i>
                    <span>Secure Checkout</span>
                </div>
            </div>
        </div>
    `;
    
    guestContainer.innerHTML = html;
}

// Update guest cart quantity
function updateGuestQuantity(productId, newQuantity) {
    newQuantity = parseInt(newQuantity);
    
    if (newQuantity < 1) {
        if (confirm('Remove this item from cart?')) {
            removeGuestItem(productId);
        }
        return;
    }
    
    const result = cartManager.updateInLocalStorage(productId, newQuantity);
    
    if (result.success) {
        loadGuestCart(); // Reload cart display
    } else {
        alert(result.message);
    }
}

// Remove guest cart item
function removeGuestItem(productId) {
    if (!confirm('Remove this item from cart?')) {
        return;
    }
    
    const result = cartManager.updateInLocalStorage(productId, 0);
    
    if (result.success) {
        showNotification('Item removed from cart', 'success');
        loadGuestCart(); // Reload cart display
    } else {
        showNotification(result.message, 'error');
    }
}

// Show notification
function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `cart-notification cart-notification-${type}`;
    notification.innerHTML = `
        <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'}"></i>
        <span>${message}</span>
    `;
    
    document.body.appendChild(notification);
    
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

// Initialize guest cart on page load
document.addEventListener('DOMContentLoaded', function() {
    const isLoggedIn = document.body.dataset.loggedIn === 'true';
    
    if (!isLoggedIn) {
        loadGuestCart();
    }
});

