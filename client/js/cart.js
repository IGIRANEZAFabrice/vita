/**
 * Shopping Cart Manager
 * Handles cart operations with localStorage and database sync
 */

class CartManager {
    constructor() {
        this.storageKey = 'redy_med_cart';
        this.sessionKey = 'redy_med_session';
        this.isLoggedIn = this.checkLoginStatus();
        this.sessionId = this.getOrCreateSessionId();
        
        // Initialize cart
        this.init();
    }
    
    /**
     * Initialize cart
     */
    async init() {
        if (this.isLoggedIn) {
            // Sync localStorage cart to database on page load
            await this.syncToDatabase();
        }
        
        // Update cart count in UI
        this.updateCartCount();
    }
    
    /**
     * Check if user is logged in
     */
    checkLoginStatus() {
        // Check if user session exists (you may need to adjust this)
        return document.body.dataset.loggedIn === 'true' || 
               document.querySelector('[data-user-id]') !== null;
    }
    
    /**
     * Get or create session ID for guest users
     */
    getOrCreateSessionId() {
        let sessionId = localStorage.getItem(this.sessionKey);
        if (!sessionId) {
            sessionId = 'guest_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9);
            localStorage.setItem(this.sessionKey, sessionId);
        }
        return sessionId;
    }
    
    /**
     * Get cart from localStorage
     */
    getLocalCart() {
        const cart = localStorage.getItem(this.storageKey);
        return cart ? JSON.parse(cart) : [];
    }
    
    /**
     * Save cart to localStorage
     */
    saveLocalCart(cart) {
        localStorage.setItem(this.storageKey, JSON.stringify(cart));
        this.updateCartCount();
    }
    
    /**
     * Add item to cart
     */
    async addToCart(productId, quantity = 1, productName = '') {
        if (this.isLoggedIn) {
            // Add to database
            return await this.addToDatabase(productId, quantity, productName);
        } else {
            // Add to localStorage
            return this.addToLocalStorage(productId, quantity, productName);
        }
    }
    
    /**
     * Add to localStorage
     */
    addToLocalStorage(productId, quantity, productName) {
        const cart = this.getLocalCart();
        const existingItem = cart.find(item => item.product_id == productId);
        
        if (existingItem) {
            existingItem.quantity += quantity;
        } else {
            cart.push({
                product_id: productId,
                quantity: quantity,
                product_name: productName,
                added_at: new Date().toISOString()
            });
        }
        
        this.saveLocalCart(cart);
        
        return {
            success: true,
            message: 'Added to cart successfully!',
            cart_count: this.getCartCount()
        };
    }
    
    /**
     * Add to database
     */
    async addToDatabase(productId, quantity, productName) {
        try {
            const formData = new FormData();
            formData.append('action', 'add');
            formData.append('product_id', productId);
            formData.append('quantity', quantity);
            
            const response = await fetch('client/pages/cart-api.php', {
                method: 'POST',
                body: formData
            });
            
            const data = await response.json();
            
            if (data.success) {
                this.updateCartCount();
            }
            
            return data;
        } catch (error) {
            console.error('Error adding to cart:', error);
            return {
                success: false,
                message: 'Error adding to cart'
            };
        }
    }
    
    /**
     * Update item quantity
     */
    async updateQuantity(cartItemId, quantity) {
        if (this.isLoggedIn) {
            return await this.updateInDatabase(cartItemId, quantity);
        } else {
            return this.updateInLocalStorage(cartItemId, quantity);
        }
    }
    
    /**
     * Update in localStorage
     */
    updateInLocalStorage(productId, quantity) {
        const cart = this.getLocalCart();
        const item = cart.find(item => item.product_id == productId);
        
        if (item) {
            if (quantity <= 0) {
                // Remove item
                const index = cart.indexOf(item);
                cart.splice(index, 1);
            } else {
                item.quantity = quantity;
            }
            
            this.saveLocalCart(cart);
            
            return {
                success: true,
                message: 'Cart updated'
            };
        }
        
        return {
            success: false,
            message: 'Item not found'
        };
    }
    
    /**
     * Update in database
     */
    async updateInDatabase(cartItemId, quantity) {
        try {
            const formData = new FormData();
            formData.append('action', 'update');
            formData.append('cart_item_id', cartItemId);
            formData.append('quantity', quantity);
            
            const response = await fetch('client/pages/cart-api.php', {
                method: 'POST',
                body: formData
            });
            
            const data = await response.json();
            
            if (data.success) {
                this.updateCartCount();
            }
            
            return data;
        } catch (error) {
            console.error('Error updating cart:', error);
            return {
                success: false,
                message: 'Error updating cart'
            };
        }
    }
    
    /**
     * Remove item from cart
     */
    async removeFromCart(itemId) {
        if (this.isLoggedIn) {
            return await this.removeFromDatabase(itemId);
        } else {
            return this.updateInLocalStorage(itemId, 0);
        }
    }
    
    /**
     * Remove from database
     */
    async removeFromDatabase(cartItemId) {
        try {
            const formData = new FormData();
            formData.append('action', 'remove');
            formData.append('cart_item_id', cartItemId);
            
            const response = await fetch('client/pages/cart-api.php', {
                method: 'POST',
                body: formData
            });
            
            const data = await response.json();
            
            if (data.success) {
                this.updateCartCount();
            }
            
            return data;
        } catch (error) {
            console.error('Error removing from cart:', error);
            return {
                success: false,
                message: 'Error removing from cart'
            };
        }
    }
    
    /**
     * Get cart count
     */
    getCartCount() {
        if (this.isLoggedIn) {
            // Will be updated via AJAX
            const countElement = document.querySelector('[data-cart-count]');
            return countElement ? parseInt(countElement.textContent) : 0;
        } else {
            const cart = this.getLocalCart();
            return cart.reduce((total, item) => total + item.quantity, 0);
        }
    }
    
    /**
     * Update cart count in UI
     */
    updateCartCount() {
        const count = this.getCartCount();
        const countElements = document.querySelectorAll('[data-cart-count]');
        
        countElements.forEach(element => {
            element.textContent = count;
            
            // Add animation
            element.classList.add('cart-count-updated');
            setTimeout(() => {
                element.classList.remove('cart-count-updated');
            }, 300);
        });
    }
    
    /**
     * Sync localStorage cart to database (on login)
     */
    async syncToDatabase() {
        const localCart = this.getLocalCart();
        
        if (localCart.length === 0) {
            return;
        }
        
        try {
            const formData = new FormData();
            formData.append('action', 'sync');
            formData.append('cart_items', JSON.stringify(localCart));
            
            const response = await fetch('client/pages/cart-api.php', {
                method: 'POST',
                body: formData
            });
            
            const data = await response.json();
            
            if (data.success) {
                // Clear localStorage cart after successful sync
                localStorage.removeItem(this.storageKey);
                this.updateCartCount();
                
                console.log('Cart synced:', data.message);
            }
        } catch (error) {
            console.error('Error syncing cart:', error);
        }
    }
    
    /**
     * Clear cart
     */
    async clearCart() {
        if (this.isLoggedIn) {
            try {
                const formData = new FormData();
                formData.append('action', 'clear');
                
                const response = await fetch('client/pages/cart-api.php', {
                    method: 'POST',
                    body: formData
                });
                
                const data = await response.json();
                this.updateCartCount();
                return data;
            } catch (error) {
                console.error('Error clearing cart:', error);
                return { success: false, message: 'Error clearing cart' };
            }
        } else {
            localStorage.removeItem(this.storageKey);
            this.updateCartCount();
            return { success: true, message: 'Cart cleared' };
        }
    }
}

// Initialize cart manager
const cartManager = new CartManager();

// Make it globally available
window.cartManager = cartManager;

// Helper function for quick add to cart
window.addToCart = async function(productId, quantity = 1, productName = '') {
    const result = await cartManager.addToCart(productId, quantity, productName);
    
    if (result.success) {
        showNotification(result.message, 'success');
    } else {
        showNotification(result.message, 'error');
    }
    
    return result;
};

// Show notification helper
function showNotification(message, type = 'info') {
    // Create notification element
    const notification = document.createElement('div');
    notification.className = `cart-notification cart-notification-${type}`;
    notification.innerHTML = `
        <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'}"></i>
        <span>${message}</span>
    `;
    
    document.body.appendChild(notification);
    
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

