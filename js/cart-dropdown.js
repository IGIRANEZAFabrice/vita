/**
 * Cart Dropdown - Shows cart preview on hover
 */

/**
 * Update cart dropdown with current items
 */
async function updateCartDropdown() {
    const cart = simpleCart.getCart();
    const dropdownItems = document.getElementById('cart-dropdown-items');
    const dropdownEmpty = document.getElementById('cart-dropdown-empty');
    const dropdownFooter = document.getElementById('cart-dropdown-footer');
    const dropdownCount = document.querySelector('.cart-dropdown-count');
    
    if (!dropdownItems) return;
    
    // Update count
    const totalItems = simpleCart.getCartCount();
    if (dropdownCount) {
        dropdownCount.textContent = totalItems + (totalItems === 1 ? ' item' : ' items');
    }
    
    // If cart is empty
    if (cart.length === 0) {
        dropdownItems.style.display = 'none';
        dropdownEmpty.style.display = 'flex';
        dropdownFooter.style.display = 'none';
        return;
    }
    
    // Show items section
    dropdownItems.style.display = 'block';
    dropdownEmpty.style.display = 'none';
    dropdownFooter.style.display = 'block';
    
    // Fetch product details
    const cartWithDetails = await fetchProductDetailsForDropdown(cart);
    
    // Render items
    renderDropdownItems(cartWithDetails);
    
    // Update total
    const total = cartWithDetails.reduce((sum, item) => sum + (item.price * item.quantity), 0);
    const totalElement = document.querySelector('.cart-dropdown-total-amount');
    if (totalElement) {
        totalElement.textContent = '$' + total.toFixed(2);
    }
}

/**
 * Fetch product details for dropdown
 */
async function fetchProductDetailsForDropdown(cart) {
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
                    image_url: product.image_url,
                    sku: product.sku
                };
            }
            return cartItem;
        });
    } catch (error) {
        console.error('Error fetching product details:', error);
        return cart;
    }
}

/**
 * Render dropdown items
 */
function renderDropdownItems(items) {
    const dropdownItems = document.getElementById('cart-dropdown-items');
    if (!dropdownItems) return;
    
    let html = '';
    
    // Limit to 5 items in dropdown
    const displayItems = items.slice(0, 5);
    
    displayItems.forEach(item => {
        const imageUrl = item.image_url || 'images/placeholder.jpg';
        const itemTotal = (item.price * item.quantity).toFixed(2);
        
        html += `
            <div class="cart-dropdown-item">
                <div class="cart-dropdown-item-image">
                    <img src="${imageUrl}" alt="${item.product_name}" onerror="this.src='images/placeholder.jpg'">
                </div>
                <div class="cart-dropdown-item-details">
                    <h4>${item.product_name}</h4>
                    <p class="cart-dropdown-item-qty">Qty: ${item.quantity}</p>
                    <p class="cart-dropdown-item-price">$${item.price.toFixed(2)} × ${item.quantity} = $${itemTotal}</p>
                </div>
                <button class="cart-dropdown-item-remove" onclick="removeFromDropdown(${item.product_id})" title="Remove">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        `;
    });
    
    // If more than 5 items, show message
    if (items.length > 5) {
        html += `
            <div class="cart-dropdown-more">
                <p>+ ${items.length - 5} more item${items.length - 5 > 1 ? 's' : ''} in cart</p>
            </div>
        `;
    }
    
    dropdownItems.innerHTML = html;
}

/**
 * Remove item from dropdown
 */
window.removeFromDropdown = function(productId) {
    if (confirm('Remove this item from cart?')) {
        const result = simpleCart.removeFromCart(productId);
        
        if (result.success) {
            // Show notification
            showDropdownNotification('Item removed from cart', 'success');
        }
    }
};

/**
 * Show notification
 */
function showDropdownNotification(message, type = 'info') {
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

// Make function globally available
window.updateCartDropdown = updateCartDropdown;

