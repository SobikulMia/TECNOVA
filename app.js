/**
 * TechNova storefront — lightweight vanilla JS.
 * Handles "Add to Cart" AJAX calls and the cart count badge.
 * No SPA framework by design — keeps the site fast and simple.
 */

function getCsrfToken() {
    return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ?? '';
}

function updateCartBadge(count) {
    const badge = document.getElementById('cart-count-badge');
    if (!badge) return;

    badge.textContent = count;
    badge.classList.toggle('hidden', count <= 0);
}

function showToast(message, isSuccess = true) {
    let toast = document.getElementById('technova-toast');

    if (!toast) {
        toast = document.createElement('div');
        toast.id = 'technova-toast';
        toast.className = 'fixed bottom-6 right-6 z-50 px-5 py-3.5 rounded-xl shadow-lg text-sm font-medium text-white transition-all duration-300 translate-y-4 opacity-0';
        document.body.appendChild(toast);
    }

    toast.textContent = message;
    toast.style.backgroundColor = isSuccess ? '#14B8A6' : '#EF4444';

    requestAnimationFrame(() => {
        toast.classList.remove('translate-y-4', 'opacity-0');
    });

    clearTimeout(window.__technovaToastTimer);
    window.__technovaToastTimer = setTimeout(() => {
        toast.classList.add('translate-y-4', 'opacity-0');
    }, 2200);
}

async function addToCart(productId, quantity = 1) {
    try {
        const response = await fetch('/cart/add', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': getCsrfToken(),
                Accept: 'application/json',
            },
            body: JSON.stringify({ product_id: productId, quantity }),
        });

        const data = await response.json();

        if (data.success) {
            updateCartBadge(data.cart_count);
            showToast('Added to cart!');
        } else {
            showToast(data.message || 'Could not add item.', false);
        }
    } catch (error) {
        showToast('Network error. Please try again.', false);
    }
}

document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('[data-add-to-cart]').forEach((button) => {
        button.addEventListener('click', (event) => {
            event.preventDefault();
            const productId = button.getAttribute('data-product-id');
            const quantityInput = document.querySelector(button.getAttribute('data-quantity-target') || '');
            const quantity = quantityInput ? parseInt(quantityInput.value, 10) || 1 : 1;
            addToCart(productId, quantity);
        });
    });
});
