/**
 * Simple Shopping Cart - localStorage Only
 * No database, completely temporary storage
 */

class SimpleCart {
    constructor() {
        this.storageKey = 'redy_med_cart';
        this.init();
    }
    
    /**
     * Initialize cart
     */
    init() {
        this.updateCartCount();
    }
    
    /**
     * Get cart from localStorage
     */
    getCart() {
        const cart = localStorage.getItem(this.storageKey);
        return cart ? JSON.parse(cart) : [];
    }
    
    /**
     * Save cart to localStorage
     */
    saveCart(cart) {
        localStorage.setItem(this.storageKey, JSON.stringify(cart));
        this.updateCartCount();
        
        // Trigger custom event for cart update
        window.dispatchEvent(new CustomEvent('cartUpdated', { detail: { cart } }));
    }
    
    /**
     * Add item to cart
     */
    addToCart(productId, quantity = 1, productData = {}) {
        const cart = this.getCart();
        const existingItem = cart.find(item => item.product_id == productId);
        
        if (existingItem) {
            existingItem.quantity += quantity;
            existingItem.updated_at = new Date().toISOString();
        } else {
            cart.push({
                product_id: productId,
                quantity: quantity,
                product_name: productData.product_name || '',
                price: productData.price || 0,
                image_url: productData.image_url || '',
                sku: productData.sku || '',
                added_at: new Date().toISOString(),
                updated_at: new Date().toISOString()
            });
        }
        
        this.saveCart(cart);
        
        return {
            success: true,
            message: 'Added to cart successfully!',
            cart_count: this.getCartCount()
        };
    }
    
    /**
     * Update item quantity
     */
    updateQuantity(productId, quantity) {
        const cart = this.getCart();
        const item = cart.find(item => item.product_id == productId);
        
        if (item) {
            if (quantity <= 0) {
                // Remove item
                return this.removeFromCart(productId);
            } else {
                item.quantity = quantity;
                item.updated_at = new Date().toISOString();
                this.saveCart(cart);
                
                return {
                    success: true,
                    message: 'Cart updated'
                };
            }
        }
        
        return {
            success: false,
            message: 'Item not found'
        };
    }
    
    /**
     * Remove item from cart
     */
    removeFromCart(productId) {
        let cart = this.getCart();
        const initialLength = cart.length;
        
        cart = cart.filter(item => item.product_id != productId);
        
        if (cart.length < initialLength) {
            this.saveCart(cart);
            return {
                success: true,
                message: 'Item removed from cart'
            };
        }
        
        return {
            success: false,
            message: 'Item not found'
        };
    }
    
    /**
     * Clear entire cart
     */
    clearCart() {
        localStorage.removeItem(this.storageKey);
        this.updateCartCount();
        
        window.dispatchEvent(new CustomEvent('cartUpdated', { detail: { cart: [] } }));
        
        return {
            success: true,
            message: 'Cart cleared'
        };
    }
    
    /**
     * Get cart count (total items)
     */
    getCartCount() {
        const cart = this.getCart();
        return cart.reduce((total, item) => total + item.quantity, 0);
    }
    
    /**
     * Get cart total (sum of prices)
     */
    getCartTotal() {
        const cart = this.getCart();
        return cart.reduce((total, item) => total + (item.price * item.quantity), 0);
    }
    
    /**
     * Update cart count in UI
     */
    updateCartCount() {
        const count = this.getCartCount();
        const countElements = document.querySelectorAll('[data-cart-count], #header-cart-count, .cart-count, .icon-link .cart-count');

        countElements.forEach(element => {
            element.textContent = count;

            // Show/hide badge based on count
            if (count > 0) {
                element.style.display = 'flex';
            } else {
                element.style.display = 'none';
            }

            // Add animation
            element.classList.add('cart-count-updated');
            setTimeout(() => {
                element.classList.remove('cart-count-updated');
            }, 300);
        });
    }
    
    /**
     * Get item from cart
     */
    getItem(productId) {
        const cart = this.getCart();
        return cart.find(item => item.product_id == productId);
    }
    
    /**
     * Check if item is in cart
     */
    isInCart(productId) {
        return this.getItem(productId) !== undefined;
    }
}

// Initialize cart manager
const simpleCart = new SimpleCart();

// Make it globally available
window.simpleCart = simpleCart;

// Helper function for quick add to cart
window.addToCart = function(productId, quantity = 1, productData = {}) {
    const result = simpleCart.addToCart(productId, quantity, productData);
    
    if (result.success) {
        showNotification(result.message, 'success');
    } else {
        showNotification(result.message, 'error');
    }
    
    return result;
};

// Show notification helper
function showNotification(message, type = 'info') {
    const container = document.getElementById('notification-container') || document.body;
    
    // Create notification element
    const notification = document.createElement('div');
    notification.className = `cart-notification cart-notification-${type}`;
    notification.innerHTML = `
        <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'}"></i>
        <span>${message}</span>
    `;
    
    container.appendChild(notification);
    
    // Show notification
    setTimeout(() => {
        notification.classList.add('show');
    }, 10);
    
    // Hide and remove notification
    setTimeout(() => {
        notification.classList.remove('show');
        setTimeout(() => {
            notification.remove();
        }, 300);
    }, 3000);
}

// Export for use in other scripts
if (typeof module !== 'undefined' && module.exports) {
    module.exports = SimpleCart;
}

