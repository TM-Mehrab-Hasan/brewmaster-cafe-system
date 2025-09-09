// Cart functionality for customer interface
let cart = [];
let cartTotal = 0;

function addToCart(productId, productName, productPrice) {
    const quantity = parseInt(document.getElementById(`qty-${productId}`).textContent) || 1;
    
    const existingItem = cart.find(item => item.id === productId);
    if (existingItem) {
        existingItem.quantity += quantity;
    } else {
        cart.push({
            id: productId,
            name: productName,
            price: productPrice,
            quantity: quantity
        });
    }
    
    updateCartDisplay();
    resetQuantity(productId);
}

function increaseQuantity(productId) {
    const qtyElement = document.getElementById(`qty-${productId}`);
    let qty = parseInt(qtyElement.textContent);
    qtyElement.textContent = qty + 1;
}

function decreaseQuantity(productId) {
    const qtyElement = document.getElementById(`qty-${productId}`);
    let qty = parseInt(qtyElement.textContent);
    if (qty > 0) {
        qtyElement.textContent = qty - 1;
    }
}

function resetQuantity(productId) {
    document.getElementById(`qty-${productId}`).textContent = '0';
}

function updateCartDisplay() {
    const cartCount = document.getElementById('cart-count');
    const cartTotal = document.getElementById('cart-total');
    const sidebarTotal = document.getElementById('sidebar-total');
    const cartItems = document.getElementById('cart-items');
    
    let totalItems = 0;
    let totalPrice = 0;
    
    cart.forEach(item => {
        totalItems += item.quantity;
        totalPrice += item.price * item.quantity;
    });
    
    cartCount.textContent = totalItems;
    cartTotal.textContent = totalPrice.toFixed(2);
    sidebarTotal.textContent = totalPrice.toFixed(2);
    
    // Update cart items display
    cartItems.innerHTML = '';
    cart.forEach(item => {
        const itemElement = document.createElement('div');
        itemElement.className = 'cart-item';
        itemElement.innerHTML = `
            <div class="item-details">
                <h5>${item.name}</h5>
                <p>৳${item.price} x ${item.quantity}</p>
            </div>
            <div class="item-controls">
                <button onclick="removeFromCart(${item.id})">Remove</button>
            </div>
        `;
        cartItems.appendChild(itemElement);
    });
}

function removeFromCart(productId) {
    cart = cart.filter(item => item.id !== productId);
    updateCartDisplay();
}

function toggleCart() {
    const cartSidebar = document.getElementById('cart-sidebar');
    cartSidebar.classList.toggle('open');
}

function filterCategory(category) {
    const products = document.querySelectorAll('.product-card');
    const sections = document.querySelectorAll('.category-section');
    const buttons = document.querySelectorAll('.filter-btn');
    
    // Update active button
    buttons.forEach(btn => btn.classList.remove('active'));
    event.target.classList.add('active');
    
    if (category === 'all') {
        sections.forEach(section => section.style.display = 'block');
    } else {
        sections.forEach(section => {
            if (section.dataset.category === category) {
                section.style.display = 'block';
            } else {
                section.style.display = 'none';
            }
        });
    }
}

function checkout() {
    if (cart.length === 0) {
        alert('Your cart is empty!');
        return;
    }
    
    // Send cart data to checkout process
    fetch('checkout_process.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            cart: cart,
            total: cart.reduce((sum, item) => sum + (item.price * item.quantity), 0)
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Order placed successfully!');
            cart = [];
            updateCartDisplay();
            toggleCart();
            window.location.href = 'my_orders.php';
        } else {
            alert('Error placing order: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error placing order');
    });
}
