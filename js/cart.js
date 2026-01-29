document.addEventListener('DOMContentLoaded', function () {
    console.log('Cart System Initializing...');
    handleGlobalSearch();

    // Check if jQuery is loaded
    if (typeof $ === 'undefined') {
        console.error('jQuery is not loaded! Cart system will not work.');
        return;
    }

    refreshCartUI();

    // Event delegation for "Agregar al presupuesto" button
    document.addEventListener('click', function (e) {
        let btn = e.target.closest('#liveToastBtn') || e.target.closest('.add-to-budget');
        if (btn) {
            console.log('Add to cart button clicked', btn);
            if (btn.id === 'liveToastBtn' && !btn.hasAttribute('data-id')) {
                const id = new URLSearchParams(window.location.search).get('id');
                const qtyInput = document.querySelector('input[type="number"]');
                const qty = qtyInput ? qtyInput.value : 1;
                const nameEl = document.getElementById('prod-title');
                const name = nameEl ? nameEl.textContent.trim() : 'Producto';
                const subnameEl = document.getElementById('prod-category');
                const subname = subnameEl ? subnameEl.textContent.trim() : '';
                const imgEl = document.getElementById('zoom-img');
                const img = imgEl ? imgEl.src : '';

                if (id) addToCart(id, qty, name, subname, img);
            } else {
                const id = btn.dataset.id;
                const qty = 1;
                const name = btn.dataset.name;
                const subname = btn.dataset.subname;
                const img = btn.dataset.img;

                if (id) addToCart(id, qty, name, subname, img);
            }
        }
    });

    // Delegation for sidebar events (remove, qty +/-)
    const sidebar = document.getElementById('offcanvasWithBothOptions');
    if (sidebar) {
        sidebar.addEventListener('click', function (e) {
            if (e.target.matches('.qty-btn-plus')) {
                const id = e.target.dataset.id;
                updateQty(id, 1);
            } else if (e.target.matches('.qty-btn-minus')) {
                const id = e.target.dataset.id;
                updateQty(id, -1);
            } else if (e.target.matches('.remove-item')) {
                const id = e.target.dataset.id;
                removeFromCart(id);
            }
        });
    }
});

function addToCart(id, qty, name, subname, img) {
    $.post('ajax/cart-actions.php', { action: 'add', id: id, qty: qty, name: name, subname: subname, img: img }, function (resp) {
        if (resp.success) {
            refreshCartUI();
            showStackingToast(name, subname, img);
        }
    }, 'json').fail(function () {
        console.error('Failed to add to cart');
    });
}

function showStackingToast(name, subname, img) {
    const container = document.querySelector('.toast-container');
    if (!container) return;

    const toastId = 'toast-' + Date.now();
    const html = `
        <div id="${toastId}" class="toast bg-white" role="alert" aria-live="assertive" aria-atomic="true" data-bs-delay="3000" style="background-color: #fff !important; opacity: 1 !important;">
            <div class="toast-header bg-white border-bottom-0">
                <p class="me-auto blue mb-0 fw-600 font13">Producto agregado al presupuesto</p>
                <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
            <div class="toast-body">
                <div class="d-flex align-items-center mb-1">
                    <div class="offcanvas-product wh70 rounded-3 border" style="background-image: url(${img})"></div>
                    <div class="ms-3">
                        <p class="blue fw-700 mb-1 font14">${name}</p>
                        <p class="blue font12 mb-0">${subname}</p>
                    </div>
                </div>
            </div>
        </div>
    `;

    container.insertAdjacentHTML('beforeend', html);
    const toastEl = document.getElementById(toastId);
    const toast = new bootstrap.Toast(toastEl);
    toast.show();

    // Clean up DOM after toast is hidden
    toastEl.addEventListener('hidden.bs.toast', () => {
        toastEl.remove();
    });
}

function updateQty(id, delta) {
    $.getJSON('ajax/cart-actions.php', { action: 'get' }, function (resp) {
        if (resp.success && resp.cart && resp.cart[id]) {
            const currentQty = parseInt(resp.cart[id].qty);
            const newQty = currentQty + delta;

            if (newQty < 1) {
                removeFromCart(id);
            } else {
                $.post('ajax/cart-actions.php', { action: 'update', id: id, qty: newQty }, function (resp2) {
                    if (resp2.success) refreshCartUI();
                }, 'json');
            }
        }
    });
}

function removeFromCart(id) {
    $.post('ajax/cart-actions.php', { action: 'remove', id: id }, function (resp) {
        if (resp.success) refreshCartUI();
    }, 'json');
}

function refreshCartUI() {
    console.log('Refreshing Cart UI...');
    $.getJSON('ajax/cart-actions.php', { action: 'get' }, function (resp) {
        console.log('Cart Data Received:', resp);
        if (resp.success) {
            updateSidebar(resp.cart || {});
            updateBadges(resp.cart || {});
        } else {
            updateSidebar({});
        }
    }).fail(function () {
        console.error('Failed to fetch cart data');
        updateSidebar({});
    });
}

function updateBadges(cart) {
    const list = cart ? Object.values(cart) : [];
    const count = list.reduce((acc, item) => acc + parseInt(item.qty || 0), 0);
    document.querySelectorAll('.cart-badge').forEach(el => {
        el.textContent = count;
        el.style.display = count > 0 ? 'flex' : 'none';
        console.log('Updated badge to:', count);
    });
}

function updateSidebar(cart) {
    const containers = document.querySelectorAll('.offcanvas-body');
    if (!containers.length) {
        console.warn('No .offcanvas-body found for sidebar update');
        return;
    }

    let html = '';
    const items = cart ? Object.values(cart) : [];
    const totalQty = items.reduce((acc, item) => acc + parseInt(item.qty || 0), 0);

    if (items.length === 0) {
        html = '<p class="text-center py-5 blue">Todavía no tienes productos en tu presupuesto.</p>';
    } else {
        items.forEach(item => {
            html += `
            <div class="d-flex align-items-center mb-3">
                <div class="offcanvas-product rounded-3" style="background-image: url(${item.img})"></div>
                <div class="ms-3 flex-grow-1">
                    <p class="blue fw-700 mb-0 font14">${item.name}</p>
                    <p class="blue font12 mb-0">${item.subname}</p>
                    <a href="javascript:void(0)" class="remove-item font14 fw-700 text-danger" data-id="${item.id}">Eliminar</a>
                </div>
                <div class="d-flex flex-column align-items-center justify-content-between qty-container ms-3">
                    <button class="qty-btn qty-btn-plus" type="button" data-id="${item.id}">+</button>
                    <div class="qty-display">${item.qty}</div>
                    <button class="qty-btn qty-btn-minus" type="button" data-id="${item.id}">-</button>
                </div>
            </div>`;
        });

        html += `
        <div class="border-top mt-4 pt-4">
            <div class="d-flex align-items-center justify-content-between">
                <p class="mb-0 fw-700 font14 blue">Productos en lista</p>
                <h5 class="mb-0 blue fw-300">${totalQty}</h5>
            </div>
        </div>
        <div class="text-center mt-4">
            <button class="btn btn-primary font14 blue border-blue rounded-5 px-4 mt-3" data-bs-toggle="modal" data-bs-target="#budgetModal">Enviar presupuesto</button>
        </div>`;
    }

    containers.forEach(container => {
        container.innerHTML = html;
        console.log('Updated container innerHTML');
    });
}

// Form submission handler for budget
document.addEventListener('submit', function (e) {
    if (e.target.id === 'budgetForm') {
        e.preventDefault();
        const responseDiv = document.getElementById('budgetResponse');

        responseDiv.style.display = 'block';
        responseDiv.innerHTML = '<div class="spinner-border spinner-border-sm" role="status"></div> Enviando...';

        $.ajax({
            url: 'send_form.php',
            method: 'POST',
            data: $(e.target).serialize(),
            success: function (resp) {
                responseDiv.textContent = resp;
                if (resp.includes('enviado')) {
                    refreshCartUI();
                    setTimeout(() => {
                        const modal = bootstrap.Modal.getInstance(document.getElementById('budgetModal'));
                        if (modal) modal.hide();
                        e.target.reset();
                        responseDiv.style.display = 'none';
                        // Close offcanvas too
                        const offcanvas = bootstrap.Offcanvas.getInstance(document.getElementById('offcanvasWithBothOptions'));
                        if (offcanvas) offcanvas.hide();
                    }, 2000);
                }
            },
            error: function () {
                responseDiv.textContent = 'Error al enviar el formulario.';
            }
        });
    }
});

function handleGlobalSearch() {
    const input = document.getElementById('globalSearchInput');
    const btn = document.getElementById('globalSearchBtn');

    if (!input) return;

    // Pre-fill input if there's a search param
    const params = new URLSearchParams(window.location.search);
    if (params.has('search')) {
        input.value = params.get('search');
    }

    const doSearch = () => {
        const query = input.value.trim();
        if (query) {
            window.location.href = 'productos.php?search=' + encodeURIComponent(query);
        }
    };

    input.addEventListener('keypress', (e) => {
        if (e.key === 'Enter') {
            e.preventDefault();
            doSearch();
        }
    });

    if (btn) {
        btn.addEventListener('click', (e) => {
            e.preventDefault();
            doSearch();
        });
    }
}
