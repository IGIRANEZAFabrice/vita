# 🛒 Shopping Cart Feature Documentation

## Overview
The shopping cart is a **completely temporary, localStorage-based system** with NO database tables. The cart persists in the browser's localStorage until the user closes their browser or clears their data. This allows users to add products to cart before logging in.

---

## 📁 Files Created

### 1. Frontend Files
- **`pages/cart.php`** - Main cart page (public, accessible without login)
- **`js/simple-cart.js`** - Cart manager using localStorage only
- **`js/cart-display.js`** - Cart page display and interactions
- **`css/cart.css`** - Complete styling for cart page (responsive)
- **`api/get-products.php`** - API to fetch product details for cart items

### 2. Updated Files
- **`client/pages/index.php`** - Updated to use localStorage cart count

---

## 🎯 Key Features

### ✅ No Database Required
- **100% localStorage** - All cart data stored in browser
- **No tables** - No cart or cart_items tables needed
- **Temporary storage** - Cart clears when browser closes or localStorage is cleared
- **Works without login** - Users can shop as guests

### ✅ Full Functionality
1. **Add to Cart** - Add products with quantity
2. **Update Quantity** - Increase/decrease item quantities
3. **Remove Items** - Remove individual items
4. **Clear Cart** - Clear entire cart at once
5. **View Cart** - See all items with details
6. **Calculate Total** - Automatic price calculations
7. **Stock Validation** - Check stock availability

### ✅ User Experience
- **Persistent** - Cart survives page refreshes
- **Real-time Updates** - Instant UI updates
- **Product Details** - Fetches latest product info from database
- **Stock Status** - Shows in-stock/out-of-stock
- **Responsive Design** - Works on all devices
- **Notifications** - Success/error messages

---

## 🔧 How It Works

### localStorage Structure
```javascript
// Key: 'redy_med_cart'
// Value: JSON array of cart items
[
    {
        "product_id": 9,
        "quantity": 2,
        "product_name": "SpO2 Sensor",
        "price": 45.99,
        "image_url": "images/products/spo2.jpg",
        "sku": "SPO2-001",
        "added_at": "2025-10-29T10:30:00.000Z",
        "updated_at": "2025-10-29T10:35:00.000Z"
    }
]
```

### Data Flow
1. **User adds product** → Saved to localStorage
2. **Page loads cart** → Reads from localStorage
3. **Fetches product details** → API call to get latest prices/stock
4. **Displays cart** → Renders with current data
5. **User updates** → localStorage updated instantly
6. **Browser closes** → Cart data cleared (temporary)

---

## 📝 Usage Examples

### For Users

#### 1. Add Product to Cart
```html
<!-- On product page -->
<button onclick="addToCart(9, 1, {
    product_name: 'SpO2 Sensor',
    price: 45.99,
    image_url: 'images/products/spo2.jpg',
    sku: 'SPO2-001'
})">
    Add to Cart
</button>
```

#### 2. View Cart
Navigate to: `http://localhost/vita/pages/cart.php`

#### 3. Update Quantity
- Click + or - buttons
- Or type quantity directly

#### 4. Remove Item
- Click trash icon
- Confirm removal

#### 5. Clear Cart
- Click "Clear Cart" button
- Confirm action

### For Developers

#### Add to Cart (JavaScript)
```javascript
// Simple add
addToCart(productId, quantity);

// With product data
addToCart(9, 2, {
    product_name: 'SpO2 Sensor',
    price: 45.99,
    image_url: 'images/products/spo2.jpg',
    sku: 'SPO2-001'
});
```

#### Get Cart Data
```javascript
// Get all cart items
const cart = simpleCart.getCart();

// Get cart count
const count = simpleCart.getCartCount();

// Get cart total
const total = simpleCart.getCartTotal();

// Check if product in cart
const inCart = simpleCart.isInCart(productId);

// Get specific item
const item = simpleCart.getItem(productId);
```

#### Update Cart
```javascript
// Update quantity
simpleCart.updateQuantity(productId, newQuantity);

// Remove item
simpleCart.removeFromCart(productId);

// Clear cart
simpleCart.clearCart();
```

#### Listen for Cart Updates
```javascript
window.addEventListener('cartUpdated', function(event) {
    const cart = event.detail.cart;
    console.log('Cart updated:', cart);
    // Update UI, etc.
});
```

---

## 🎨 Design Features

### Color Scheme
- **Header**: Purple gradient (#667eea → #764ba2)
- **Checkout Button**: Green gradient (#00e600 → #00b300)
- **Remove Button**: Red gradient (#dc3545 → #c82333)
- **Quantity Buttons**: Purple gradient (#667eea → #764ba2)

### Animations
- **Float**: Empty cart icon floats up and down
- **Cart Count**: Pulsing animation on update
- **Notifications**: Slide in from right
- **Hover Effects**: Card lift and shadow

### Responsive Breakpoints
- **Desktop** (>1200px): 2-column layout
- **Tablet** (768px-1200px): 2-column layout
- **Mobile** (<768px): 1-column layout, stacked items

---

## 🔗 Integration

### Cart Page URL
```
http://localhost/vita/pages/cart.php
```

### Add to Navigation
```html
<a href="pages/cart.php">
    <i class="fas fa-shopping-cart"></i>
    Cart (<span data-cart-count>0</span>)
</a>
```

### Display Cart Count
```html
<!-- Anywhere in your HTML -->
<span data-cart-count>0</span>

<!-- Or with ID -->
<span id="header-cart-count">0</span>
```

The JavaScript will automatically update these elements.

---

## 🚀 API Endpoints

### Get Products by IDs
**Endpoint**: `api/get-products.php?ids=1,2,3`

**Method**: GET

**Response**:
```json
[
    {
        "product_id": 1,
        "product_name": "SpO2 Sensor",
        "price": "45.99",
        "sku": "SPO2-001",
        "stock_quantity": 50,
        "image_url": "images/products/spo2.jpg",
        "category_name": "Sensors",
        "short_description": "High-quality SpO2 sensor..."
    }
]
```

---

## 💡 Important Notes

### ⚠️ Limitations
1. **Temporary Storage** - Cart clears when browser closes
2. **No Cross-Device Sync** - Cart is device-specific
3. **No Login Required** - Anyone can add to cart
4. **No Checkout** - Users must login to place orders

### ✅ Advantages
1. **No Database** - Simpler architecture
2. **Fast Performance** - No server calls for cart operations
3. **Works Offline** - Cart works without internet (after page load)
4. **Privacy** - No tracking of guest carts
5. **Easy to Implement** - Just JavaScript, no backend

### 🔄 Future Enhancements (Optional)
If you want to make cart persistent:
1. **On Login** - Transfer localStorage cart to database
2. **On Logout** - Transfer database cart to localStorage
3. **Sync** - Keep both in sync while logged in

---

## 🐛 Troubleshooting

### Issue: Cart count not updating
**Solution**: Make sure `simple-cart.js` is loaded before other scripts

### Issue: Cart items not showing
**Solution**: Check browser console for errors, verify `api/get-products.php` is working

### Issue: Cart clears unexpectedly
**Solution**: Check if localStorage is enabled, not in incognito mode

### Issue: Images not loading
**Solution**: Verify image paths in database are correct

---

## 📊 Testing Checklist

- [ ] Add product to cart
- [ ] View cart page
- [ ] Update quantity (increase)
- [ ] Update quantity (decrease)
- [ ] Remove single item
- [ ] Clear entire cart
- [ ] Refresh page (cart persists)
- [ ] Close browser (cart clears)
- [ ] Test on mobile device
- [ ] Test with multiple products
- [ ] Test stock validation
- [ ] Test price calculations

---

## 🎉 Summary

The shopping cart is now **fully functional** with:
- ✅ localStorage-based temporary storage
- ✅ No database tables required
- ✅ Works without login
- ✅ Full CRUD operations (Create, Read, Update, Delete)
- ✅ Real-time UI updates
- ✅ Product details from database
- ✅ Stock validation
- ✅ Responsive design
- ✅ Beautiful UI with animations

**Access the cart at**: `http://localhost/vita/pages/cart.php`

**Enjoy your temporary shopping cart! 🛒**

