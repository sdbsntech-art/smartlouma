/**
 * SMART-LOUMA — main.js v2.0
 * Logique principale avec gestion rôles (admin / producteur / consommateur)
 */

/* ============================================================
   UTILITAIRES GLOBAUX
   ============================================================ */
const fmt = n => Number(n || 0).toLocaleString('fr-FR');

function toast(title, msg, type = 'success') {
  const icons = { success:'fa-check-circle', warning:'fa-exclamation-triangle', info:'fa-info-circle', error:'fa-times-circle' };
  const container = document.getElementById('toastContainer');
  if (!container) return;
  const el = document.createElement('div');
  el.className = `toast toast-${type}`;
  el.innerHTML = `<div class="toast-icon"><i class="fas ${icons[type]||icons.info}"></i></div><div><div class="toast-title">${title}</div><div class="toast-msg">${msg}</div></div>`;
  container.appendChild(el);
  requestAnimationFrame(() => el.classList.add('show'));
  setTimeout(() => { el.classList.remove('show'); setTimeout(() => el.remove(), 500); }, 4000);
}

/* ============================================================
   NAVBAR & SCROLL
   ============================================================ */
function initNavbar() {
  const navbar = document.querySelector('.navbar');
  const ham    = document.getElementById('hamburger');
  const links  = document.getElementById('navLinks');
  const overlay = document.getElementById('navOverlay');
  if (!navbar) return;

  window.addEventListener('scroll', () => {
    navbar.classList.toggle('scrolled', window.scrollY > 60);
    const sections = document.querySelectorAll('section[id]');
    let cur = '';
    sections.forEach(s => { if (window.scrollY >= s.offsetTop - 100) cur = s.id; });
    document.querySelectorAll('.nav-links a').forEach(a => {
      a.classList.toggle('active', a.getAttribute('href') === '#' + cur);
    });
  });

  ham?.addEventListener('click', () => {
    const isOpen = !ham.classList.contains('active');
    ham.classList.toggle('active', isOpen);
    links?.classList.toggle('open', isOpen);
    overlay?.classList.toggle('open', isOpen);
  });
  document.querySelectorAll('.nav-links a').forEach(a => {
    a.addEventListener('click', () => {
      ham?.classList.remove('active');
      links?.classList.remove('open');
      overlay?.classList.remove('open');
    });
  });
  overlay?.addEventListener('click', () => {
    ham?.classList.remove('active');
    links?.classList.remove('open');
    overlay?.classList.remove('open');
  });
}

/* ============================================================
   SCROLL REVEAL
   ============================================================ */
function initReveal() {
  const obs = new IntersectionObserver(entries => {
    entries.forEach(e => { if (e.isIntersecting) { e.target.classList.add('revealed'); obs.unobserve(e.target); } });
  }, { threshold: .12 });
  document.querySelectorAll('[data-reveal]').forEach(el => obs.observe(el));
}

/* ============================================================
   COMPTEURS ANIMÉS
   ============================================================ */
function initCounters() {
  const obs = new IntersectionObserver(entries => {
    entries.forEach(e => {
      if (!e.isIntersecting) return;
      const el = e.target; const end = parseFloat(el.dataset.count); const dec = String(end).includes('.') ? 1 : 0;
      let cur = 0; const steps = 80; const inc = end / steps;
      const t = setInterval(() => {
        cur += inc; if (cur >= end) { cur = end; clearInterval(t); }
        el.textContent = dec ? cur.toFixed(dec) : Math.floor(cur);
      }, 18);
      obs.unobserve(el);
    });
  }, { threshold: .5 });
  document.querySelectorAll('.counter').forEach(el => obs.observe(el));
}

/* ============================================================
   SLIDER GALERIE
   ============================================================ */
function initSlider() {
  const wrapper = document.querySelector('.slider-track');
  if (!wrapper) return;
  const slides = wrapper.querySelectorAll('.slide');
  let cur = 0;
  const go = n => { cur = (n + slides.length) % slides.length; wrapper.style.transform = `translateX(-${cur * 100}%)`; };
  document.getElementById('sliderPrev')?.addEventListener('click', () => go(cur - 1));
  document.getElementById('sliderNext')?.addEventListener('click', () => go(cur + 1));
  setInterval(() => go(cur + 1), 5500);
  const dotsWrap = document.getElementById('sliderDots');
  if (dotsWrap) {
    slides.forEach((_, i) => {
      const d = document.createElement('button');
      d.className = 'slider-dot' + (i === 0 ? ' active' : '');
      d.addEventListener('click', () => { go(i); });
      dotsWrap.appendChild(d);
    });
    setInterval(() => {
      dotsWrap.querySelectorAll('.slider-dot').forEach((d, i) => d.classList.toggle('active', i === cur));
    }, 80);
  }
}

/* ============================================================
   INTERFACE UTILISATEUR / AUTHENTIFICATION
   ============================================================ */
function initUIForUser() {
  const user = Auth.currentUser;
  const loginBtn     = document.getElementById('loginBtn');
  const registerBtn  = document.getElementById('registerBtn');
  const userInfo     = document.getElementById('userInfo');
  const producerSec  = document.getElementById('producer');
  const adminLink    = document.getElementById('adminNavLink');

  if (user) {
    if (loginBtn)    loginBtn.style.display    = 'none';
    if (registerBtn) registerBtn.style.display = 'none';
    if (userInfo) {
      userInfo.style.display = 'flex';
      const roleLabel = { admin:'Admin', producer:'Producteur', consumer:'Restaurateur' }[user.role] || 'Utilisateur';
      userInfo.innerHTML = `
        <div style="display:flex;align-items:center;gap:.5rem">
          <div style="width:32px;height:32px;border-radius:50%;background:var(--green);color:#fff;display:flex;align-items:center;justify-content:center;font-size:.82rem;font-weight:700">${user.name[0].toUpperCase()}</div>
          <div style="line-height:1.2">
            <div style="font-size:.85rem;font-weight:700;color:var(--dark)">${user.name}</div>
            <div style="font-size:.72rem;color:var(--gray)">${roleLabel}</div>
          </div>
        </div>
        <button class="btn btn-sm btn-outline" id="logoutBtn"><i class="fas fa-sign-out-alt"></i> <span class="btn-text">Déconnexion</span></button>
        ${user.role === 'admin' ? '<a href="' + (typeof USE_LARAVEL !== 'undefined' && USE_LARAVEL ? (window.location.port === '8000' ? '/admin' : 'http://localhost:8000/admin') : 'admin.html') + '" class="btn btn-sm btn-primary"><i class="fas fa-cogs"></i> <span class="btn-text">Admin</span></a>' : ''}`;
      document.getElementById('logoutBtn')?.addEventListener('click', () => {
        Auth.logout(); location.reload();
      });
    }

    // Afficher/masquer section producteur
    if (producerSec) {
      producerSec.style.display = Auth.canManageProducts() ? 'block' : 'none';
    }

    // Afficher lien admin dans nav
    if (adminLink) adminLink.style.display = Auth.isAdmin() ? 'block' : 'none';

  } else {
    if (userInfo) userInfo.style.display = 'none';
    if (producerSec) producerSec.style.display = 'none';
    if (adminLink) adminLink.style.display = 'none';
  }
}

/* ============================================================
   MODAL AUTHENTIFICATION
   ============================================================ */
function initAuthModals() {
  document.getElementById('loginBtn')?.addEventListener('click', () => openModal('authModal'));
  document.getElementById('registerBtn')?.addEventListener('click', () => {
    openModal('authModal');
    document.querySelector('[data-tab="register"]')?.click();
  });

  document.querySelectorAll('.tab-btn').forEach(btn => {
    btn.addEventListener('click', function() {
      document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
      document.querySelectorAll('.tab-pane').forEach(p => p.classList.remove('active'));
      this.classList.add('active');
      document.getElementById('tab_' + this.dataset.tab)?.classList.add('active');
    });
  });

  document.getElementById('loginForm')?.addEventListener('submit', async function(e) {
    e.preventDefault();
    const email    = this.querySelector('[name=email]').value.trim();
    const password = this.querySelector('[name=password]').value;
    const result = await Auth.login(email, password);
    if (result.error) { toast('Erreur', result.error, 'error'); return; }
    closeModal('authModal');
    toast('Connexion réussie', `Bienvenue, ${result.user.name} !`, 'success');
    if (result.user.role === 'admin') {
      const adminUrl = (typeof USE_LARAVEL !== 'undefined' && USE_LARAVEL)
        ? (window.location.port === '8000' ? '/admin' : 'http://localhost:8000/admin')
        : 'admin.html';
      setTimeout(() => { window.location.href = adminUrl; }, 800);
      return;
    }
    await loadAllData();
    initUIForUser(); renderProducts(); initProducerSection();
  });

  document.getElementById('registerForm')?.addEventListener('submit', async function(e) {
    e.preventDefault();
    const pwd  = this.querySelector('[name=password]').value;
    const pwd2 = this.querySelector('[name=password2]').value;
    if (pwd !== pwd2) { toast('Erreur', 'Les mots de passe ne correspondent pas.', 'error'); return; }
    const data = {
      name:     this.querySelector('[name=name]').value.trim(),
      email:    this.querySelector('[name=email]').value.trim(),
      password: pwd,
      phone:    this.querySelector('[name=phone]').value.trim(),
      role:     this.querySelector('[name=userType]').value,
      company:  this.querySelector('[name=company]')?.value.trim() || '',
      zone:     this.querySelector('[name=zone]')?.value || '',
    };
    const result = (typeof USE_LARAVEL !== 'undefined' && USE_LARAVEL) ? await Auth.register(data) : DB.createUser(data);
    if (result.error) { toast('Erreur', result.error, 'error'); return; }
    if (result.pending || (result.user && result.user.role === 'producer' && result.user.status === 'pending')) {
      toast('Inscription envoyée', 'Votre demande est en attente d\'approbation par l\'admin.', 'info');
    } else if (result.user) {
      // Mode local: connecter automatiquement l'utilisateur actif
      if (!result.token) {
        const loginRes = await Auth.login(result.user.email, data.password);
        if (loginRes?.user) Auth.currentUser = loginRes.user;
      } else {
        Auth.currentUser = result.user;
      }
      toast('Compte créé !', `Bienvenue ${result.user.name} !`, 'success');
      await loadAllData();
      initUIForUser(); renderProducts();
    }
    closeModal('authModal'); this.reset();
  });
}

/* ============================================================
   MODALES GÉNÉRIQUES
   ============================================================ */
function openModal(id)  { const el = document.getElementById(id); if(el){ el.classList.add('active'); document.body.style.overflow='hidden'; } }
function closeModal(id) { const el = document.getElementById(id); if(el){ el.classList.remove('active'); document.body.style.overflow=''; } }

function initModals() {
  document.querySelectorAll('.modal-overlay').forEach(ov => {
    ov.addEventListener('click', e => { if (e.target === ov) closeModal(ov.id); });
  });
  document.querySelectorAll('.modal-close').forEach(btn => {
    btn.addEventListener('click', () => closeModal(btn.dataset.modal));
  });

  // Cart button
  document.getElementById('cartBtn')?.addEventListener('click', () => {
    renderCartModal(); openModal('cartModal');
  });
  document.getElementById('checkoutBtn')?.addEventListener('click', doCheckout);
}

/* ============================================================
   PANIER
   ============================================================ */
function updateCartBadge() {
  const cart  = DB.getCart();
  const total = cart.reduce((s, i) => s + i.qty, 0);
  const badge = document.getElementById('cartBadge');
  if (badge) { badge.textContent = total; badge.style.display = total > 0 ? 'flex' : 'none'; }
}

function addToCart(productId, qty = 1) {
  if (!Auth.isLoggedIn()) { toast('Connexion requise', 'Veuillez vous connecter pour commander.', 'warning'); openModal('authModal'); return; }
  const p = DB.getProduct(productId);
  if (!p) return;
  const cart = DB.getCart();
  const existing = cart.find(x => x.id === productId);
  if (existing) {
    if (existing.qty + qty > Math.min(p.quantity, 50)) { toast('Stock insuffisant', `Maximum ${Math.min(p.quantity,50)} kg disponibles.`, 'warning'); return; }
    existing.qty += qty;
  } else {
    cart.push({ id:p.id, name:p.name, price:p.price, image:p.image, producer:p.producerName, qty });
  }
  DB.saveCart(cart);
  updateCartBadge();
  toast('Ajouté au panier', `${p.name} (${qty} kg)`, 'success');
}

function removeFromCart(id) {
  DB.saveCart(DB.getCart().filter(x => x.id !== id));
  updateCartBadge(); renderCartModal();
}

function updateQty(id, delta) {
  const cart = DB.getCart();
  const item = cart.find(x => x.id === id);
  if (!item) return;
  const nq = item.qty + delta;
  if (nq < 1) { removeFromCart(id); return; }
  const p = DB.getProduct(id);
  if (nq > Math.min(p?.quantity || 50, 50)) { toast('Limite', 'Stock insuffisant.', 'warning'); return; }
  item.qty = nq;
  DB.saveCart(cart);
  updateCartBadge(); renderCartModal();
}

function renderCartModal() {
  const cart    = DB.getCart();
  const cartBody  = document.getElementById('cartBody');
  const cartSumEl = document.getElementById('cartSummary');
  if (!cartBody) return;

  const settings = DB.get('settings') || { freeDelivery:20000, deliveryFee:5000 };

  if (cart.length === 0) {
    cartBody.innerHTML = `<div style="text-align:center;padding:3rem"><i class="fas fa-shopping-cart" style="font-size:2.5rem;color:var(--gray-light);margin-bottom:1rem;display:block"></i><p style="color:var(--gray)">Votre panier est vide</p></div>`;
    if (cartSumEl) cartSumEl.innerHTML = '';
    return;
  }

  let subtotal = 0;
  cartBody.innerHTML = `
    <table class="cart-table">
      <thead><tr><th>Produit</th><th>Prix/kg</th><th>Qté</th><th>Total</th><th></th></tr></thead>
      <tbody>${cart.map(item => {
        const total = item.price * item.qty; subtotal += total;
        return `<tr>
          <td><div style="display:flex;align-items:center;gap:.75rem"><img src="${item.image}" class="cart-item-img" alt="${item.name}"><div><strong>${item.name}</strong><br><small style="color:var(--gray)">${item.producer||''}</small></div></div></td>
          <td>${fmt(item.price)} F</td>
          <td><div class="qty-control"><button class="qty-btn" onclick="updateQty('${item.id}',-1)">−</button><span>${item.qty} kg</span><button class="qty-btn" onclick="updateQty('${item.id}',1)">+</button></div></td>
          <td><strong>${fmt(total)} F</strong></td>
          <td><button style="background:none;border:none;cursor:pointer;color:var(--red);font-size:1rem" onclick="removeFromCart('${item.id}')"><i class="fas fa-trash"></i></button></td>
        </tr>`;
      }).join('')}</tbody>
    </table>`;

  const delivery = subtotal >= settings.freeDelivery ? 0 : settings.deliveryFee;
  const total    = subtotal + delivery;
  if (cartSumEl) cartSumEl.innerHTML = `
    <div class="cart-summary">
      <div class="cart-summary-row"><span>Sous-total</span><span>${fmt(subtotal)} FCFA</span></div>
      <div class="cart-summary-row"><span>Livraison</span><span>${delivery === 0 ? '<span style="color:var(--green)">Gratuite ✓</span>' : fmt(delivery)+' FCFA'}</span></div>
      <div class="cart-summary-row total"><span>Total</span><span>${fmt(total)} FCFA</span></div>
    </div>
    ${subtotal < settings.freeDelivery ? `<p style="font-size:.78rem;color:var(--gray);margin-top:.5rem"><i class="fas fa-info-circle"></i> Livraison gratuite à partir de ${fmt(settings.freeDelivery)} FCFA (encore ${fmt(settings.freeDelivery - subtotal)} FCFA)</p>` : ''}`;
}

async function doCheckout() {
  const cart = DB.getCart();
  if (cart.length === 0) { toast('Panier vide', 'Ajoutez des produits d\'abord.', 'warning'); return; }
  if (!Auth.isLoggedIn()) { toast('Connexion requise', 'Veuillez vous connecter.', 'warning'); closeModal('cartModal'); openModal('authModal'); return; }
  if (!confirm('Confirmer la commande ? Livraison demain matin. Paiement à la livraison. Aucune annulation possible.')) return;

  const settings = DB.getSettings();
  const subtotal = cart.reduce((s,i) => s + i.price*i.qty, 0);
  const delivery = subtotal >= settings.freeDelivery ? 0 : settings.deliveryFee;

  try {
    if (typeof DB.createOrder === 'function' && DB.createOrder.constructor.name === 'AsyncFunction') {
      await DB.createOrder({
        items: cart.map(i => ({ id: parseInt(i.id) || i.id, name: i.name, price: i.price, qty: i.qty })),
        delivery_address: null,
      });
    } else {
      DB.createOrder({
        items: cart.map(i => ({ id: i.id, name: i.name, price: i.price, qty: i.qty })),
        total: subtotal + delivery,
        delivery,
        buyerEmail: Auth.currentUser.email,
        clientName: Auth.currentUser.name,
        buyerId: Auth.currentUser.id,
      });
    }
    DB.saveCart([]);
    updateCartBadge();
    closeModal('cartModal');
    toast('Commande confirmée !', 'Livraison prévue demain matin. Paiement à la livraison.', 'success');
    await loadAllData();
    renderProducts();
  } catch (err) {
    toast('Erreur', err.error || err.message || 'Impossible de valider la commande.', 'error');
  }
}

/* ============================================================
   CHARGEMENT DONNÉES (API)
   ============================================================ */
async function loadAllData() {
  if (typeof DB.loadProducts === 'function') {
    await DB.loadProducts(activeFilters);
  }
  if (Auth.isLoggedIn() && typeof DB.loadMyProducts === 'function') {
    await DB.loadMyProducts();
  }
  if (typeof DB.loadStats === 'function') {
    await DB.loadStats();
  }
}

/* ============================================================
   FILTRES PRODUITS
   ============================================================ */
let currentPage   = 1;
const PER_PAGE    = 8;
let activeFilters = { search:'', letter:'', zone:'', category:'' };
let currentProduct = null;

function initLetterFilters() {
  const wrap = document.getElementById('letterFilters');
  if (!wrap) return;
  const allBtn = document.createElement('button');
  allBtn.className = 'letter-btn active'; allBtn.textContent = 'Tous'; allBtn.dataset.letter = '';
  allBtn.addEventListener('click', async function() {
    activeFilters.letter = ''; currentPage = 1;
    wrap.querySelectorAll('.letter-btn').forEach(b => b.classList.remove('active'));
    this.classList.add('active');
    if (typeof DB.loadProducts === 'function') await DB.loadProducts(activeFilters);
    renderProducts();
  });
  wrap.appendChild(allBtn);
  'ABCDEFGHIJKLMNOPQRSTUVWXYZ'.split('').forEach(l => {
    const btn = document.createElement('button');
    btn.className = 'letter-btn'; btn.textContent = l; btn.dataset.letter = l;
    btn.addEventListener('click', async function() {
      activeFilters.letter = l; currentPage = 1;
      wrap.querySelectorAll('.letter-btn').forEach(b => b.classList.remove('active'));
      this.classList.add('active');
      if (typeof DB.loadProducts === 'function') await DB.loadProducts(activeFilters);
      renderProducts();
    });
    wrap.appendChild(btn);
  });
}

function initFilters() {
  document.getElementById('searchInput')?.addEventListener('input', async function() {
    activeFilters.search = this.value; currentPage = 1;
    if (typeof DB.loadProducts === 'function') await DB.loadProducts(activeFilters);
    renderProducts();
  });
  document.getElementById('zoneFilter')?.addEventListener('change', async function() {
    activeFilters.zone = this.value; currentPage = 1;
    if (typeof DB.loadProducts === 'function') await DB.loadProducts(activeFilters);
    renderProducts();
  });
  document.getElementById('categoryFilter')?.addEventListener('change', async function() {
    activeFilters.category = this.value; currentPage = 1;
    if (typeof DB.loadProducts === 'function') await DB.loadProducts(activeFilters);
    renderProducts();
  });
}

function renderProducts() {
  const container    = document.getElementById('productsGrid');
  const paginationEl = document.getElementById('pagination');
  if (!container) return;

  // Seulement les produits disponibles (définis par admin/producteur)
  const all = DB.getAvailableProducts().filter(p => {
    const s = activeFilters.search.toLowerCase();
    return (!s || p.name.toLowerCase().includes(s) || p.producerName?.toLowerCase().includes(s) || p.description?.toLowerCase().includes(s))
        && (!activeFilters.letter   || p.name[0]?.toUpperCase() === activeFilters.letter)
        && (!activeFilters.zone     || p.zone === activeFilters.zone)
        && (!activeFilters.category || p.category === activeFilters.category);
  });

  const pages    = Math.ceil(all.length / PER_PAGE);
  if (currentPage > pages && pages > 0) currentPage = pages;
  const paginated = all.slice((currentPage - 1) * PER_PAGE, currentPage * PER_PAGE);

  container.innerHTML = '';
  if (paginated.length === 0) {
    container.innerHTML = `<div class="products-empty"><i class="fas fa-seedling"></i><h4>Aucun produit disponible</h4><p class="text-gray">Revenez bientôt, notre catalogue est en cours de constitution.</p></div>`;
    if (paginationEl) paginationEl.innerHTML = '';
    return;
  }

  paginated.forEach(p => {
    const low  = p.quantity <= 10;
    const card = document.createElement('div');
    card.className = 'product-card';
    card.innerHTML = `
      <div class="product-img-wrap">
        <img src="${p.image}" alt="${p.name}" loading="lazy" onerror="this.src='https://images.unsplash.com/photo-1512621776951-a57141f2eefd?auto=format&fit=crop&w=800&q=80'">
        <span class="product-badge ${low ? 'badge-low' : 'badge-high'}">${low ? 'Stock limité' : 'En stock'}</span>
      </div>
      <div class="product-body">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:.3rem">
          <h5>${p.name}</h5>
          ${p.rating > 0 ? `<span class="rating"><i class="fas fa-star"></i> ${p.rating}</span>` : ''}
        </div>
        <div class="product-meta">
          <span><i class="fas fa-tag"></i> ${p.category}</span>
          <span><i class="fas fa-map-marker-alt"></i> ${p.zone}</span>
        </div>
        <p class="product-desc">${(p.description||'').substring(0,75)}…</p>
        <p class="product-price">${fmt(p.price)} FCFA<small style="font-size:.65rem;font-weight:400;color:var(--gray)">/kg</small></p>
        <p style="font-size:.75rem;color:var(--gray);margin-bottom:.8rem"><i class="fas fa-warehouse"></i> ${p.quantity} kg disponibles · <i class="fas fa-user-tie"></i> ${p.producerName||'Producteur'}</p>
        <div class="product-actions">
          <button class="btn btn-outline btn-sm" onclick="openProductDetail('${p.id}')">
            <i class="fas fa-eye"></i> Détails
          </button>
          <button class="btn btn-primary btn-sm" onclick="addToCart('${p.id}')">
            <i class="fas fa-cart-plus"></i> Ajouter
          </button>
        </div>
      </div>`;
    container.appendChild(card);
  });

  // Pagination
  if (paginationEl) {
    paginationEl.innerHTML = '';
    if (pages > 1) {
      for (let i = 1; i <= pages; i++) {
        const btn = document.createElement('button');
        btn.className = 'page-btn' + (i === currentPage ? ' active' : '');
        btn.textContent = i;
        btn.addEventListener('click', () => { currentPage = i; renderProducts(); container.scrollIntoView({behavior:'smooth',block:'start'}); });
        paginationEl.appendChild(btn);
      }
    }
  }
}

/* ============================================================
   DÉTAIL PRODUIT
   ============================================================ */
function openProductDetail(id) {
  const p = DB.getProduct(id);
  if (!p) return;
  currentProduct = p;
  document.getElementById('detailTitle').textContent    = p.name;
  document.getElementById('detailImg').src              = p.image;
  document.getElementById('detailImg').alt              = p.name;
  document.getElementById('detailCategory').textContent = p.category;
  document.getElementById('detailZone').textContent     = '📍 ' + p.zone;
  document.getElementById('detailDesc').textContent     = p.description;
  document.getElementById('detailPrice').textContent    = fmt(p.price) + ' FCFA/kg';
  document.getElementById('detailQty').textContent      = p.quantity + ' kg disponibles';
  document.getElementById('detailHarvest').textContent  = new Date(p.harvestDate).toLocaleDateString('fr-FR');
  document.getElementById('detailProducer').textContent = p.producerName || 'Producteur SMART-LOUMA';
  document.getElementById('detailRating').innerHTML     = p.rating > 0 ? '⭐ '+p.rating+'/5' : 'Nouveau produit';
  document.getElementById('detailQtyInput').value       = 1;
  document.getElementById('detailQtyInput').max        = Math.min(p.quantity, 50);
  openModal('productDetailModal');
}

document.addEventListener('click', e => {
  if (e.target.id === 'addToCartFromDetail') {
    if (!currentProduct) return;
    const qty = parseInt(document.getElementById('detailQtyInput').value) || 1;
    addToCart(currentProduct.id, qty);
    closeModal('productDetailModal');
  }
});

/* ============================================================
   ESPACE PRODUCTEUR (réservé producteurs + admin)
   ============================================================ */
function initProducerSection() {
  const sec = document.getElementById('producer');
  if (!sec) return;
  if (!Auth.canManageProducts()) { sec.style.display = 'none'; return; }
  sec.style.display = 'block';
  renderProducerKPIs();
  renderProducerTable();
  initProducerChart();

  document.getElementById('addProductForm')?.addEventListener('submit', handleAddProduct);
}

function handleAddProduct(e) {
  e.preventDefault();
  const form = e.target;
  const name     = form.querySelector('#pName').value.trim();
  const category = form.querySelector('#pCategory').value;
  const qty      = parseInt(form.querySelector('#pQty').value);
  const price    = parseInt(form.querySelector('#pPrice').value);
  const zone     = form.querySelector('#pZone').value;
  const harvest  = form.querySelector('#pHarvest').value;
  const desc     = form.querySelector('#pDesc').value.trim();

  if (!name || !category || !qty || !price || !zone || !harvest) {
    toast('Formulaire incomplet', 'Veuillez remplir tous les champs obligatoires.', 'warning'); return;
  }

  // Upload image
  const fileInput = form.querySelector('#pImageFile');
  const file = fileInput?.files[0];
  if (file) {
    const reader = new FileReader();
    reader.onload = function(ev) { createProductWithImage(name, category, qty, price, zone, harvest, desc, ev.target.result, form); };
    reader.readAsDataURL(file);
  } else {
    createProductWithImage(name, category, qty, price, zone, harvest, desc, null, form);
  }
}

async function createProductWithImage(name, category, qty, price, zone, harvest, desc, imageData, form) {
  try {
    if (typeof DB.createProduct === 'function' && DB.createProduct.constructor.name === 'AsyncFunction') {
      await DB.createProduct({ name, category, quantity: qty, price, zone, harvestDate: harvest, description: desc, image: imageData || null, available: true }, Auth.currentUser);
    } else {
      const producer = Auth.isAdmin() ? { id: 'admin_0', name: Auth.currentUser.name, company: 'SMART-LOUMA Admin' } : Auth.currentUser;
      DB.createProduct({ name, category, quantity: qty, price, zone, harvestDate: harvest, description: desc, image: imageData || null, available: true }, producer);
    }
    toast('Produit ajouté', `${name} est maintenant visible dans la marketplace.`, 'success');
    form.reset();
    if (form.querySelector('#pImagePreview')) { form.querySelector('#pImagePreview').src = ''; form.querySelector('#pImagePreview').style.display = 'none'; }
    await loadAllData();
    renderProducerTable(); renderProducerKPIs(); renderProducts();
  } catch (err) {
    toast('Erreur', err.error || err.message || 'Impossible d\'ajouter le produit.', 'error');
  }
}

function renderProducerKPIs() {
  const mine = getMyProducts();
  const totalQty = mine.reduce((s,p) => s + p.quantity, 0);
  const totalVal = mine.reduce((s,p) => s + p.quantity*p.price, 0);
  document.getElementById('kpiCount') && (document.getElementById('kpiCount').textContent = mine.length);
  document.getElementById('kpiQty')   && (document.getElementById('kpiQty').textContent   = totalQty + ' kg');
  document.getElementById('kpiVal')   && (document.getElementById('kpiVal').textContent   = fmt(totalVal) + ' F');
}

function getMyProducts() {
  const all = DB.getProducts();
  if (Auth.isAdmin()) return all;
  return all.filter(p => p.producerId == Auth.currentUser?.id || p.producer_id == Auth.currentUser?.id);
}

function renderProducerTable() {
  const tbody = document.getElementById('producerTableBody');
  const count = document.getElementById('producerCount');
  if (!tbody) return;
  const mine = getMyProducts();
  if (count) count.textContent = mine.length + ' produit' + (mine.length > 1 ? 's' : '');
  tbody.innerHTML = mine.length === 0
    ? `<tr><td colspan="8" style="text-align:center;padding:2rem;color:var(--gray)"><i class="fas fa-seedling" style="margin-right:.5rem"></i>Aucun produit ajouté</td></tr>`
    : mine.map(p => `
      <tr>
        <td><img src="${p.image}" style="width:40px;height:40px;border-radius:8px;object-fit:cover" alt="${p.name}"></td>
        <td><strong>${p.name}</strong></td>
        <td>${p.category}</td>
        <td><strong>${p.quantity} kg</strong></td>
        <td>${fmt(p.price)} F</td>
        <td>${p.zone}</td>
        <td>
          <div style="display:flex;align-items:center;gap:.4rem;cursor:pointer" onclick="toggleMyProductAvail('${p.id}')">
            <div style="width:34px;height:18px;border-radius:9px;background:${p.available?'var(--green)':'#D1D5DB'};position:relative;transition:all .3s">
              <div style="width:14px;height:14px;background:#fff;border-radius:50%;position:absolute;top:2px;${p.available?'left:18px':'left:2px'};transition:all .3s"></div>
            </div>
            <span style="font-size:.78rem;font-weight:600">${p.available?'Visible':'Masqué'}</span>
          </div>
        </td>
        <td>
          <button class="btn btn-sm btn-outline" onclick="promptEditQty('${p.id}')"><i class="fas fa-edit"></i></button>
          <button class="btn btn-sm" style="background:var(--red);color:#fff;border:none;padding:.35rem .8rem;border-radius:50px;cursor:pointer;font-size:.78rem;margin-left:.3rem" onclick="deleteMyProduct('${p.id}')"><i class="fas fa-trash"></i></button>
        </td>
      </tr>`).join('');
}

async function toggleMyProductAvail(id) {
  const p = DB.getProduct(id);
  if (!p) return;
  try {
    if (typeof DB.toggleProduct === 'function') {
      await DB.toggleProduct(id);
    } else {
      DB.updateProduct(id, { available: !p.available });
    }
    await loadAllData();
    renderProducerTable(); renderProducts();
    toast('Disponibilité', `${p.name} : ${!p.available ? 'visible' : 'masqué'}`, 'success');
  } catch (err) {
    toast('Erreur', err.error || err.message, 'error');
  }
}

async function promptEditQty(id) {
  const p = DB.getProduct(id);
  if (!p) return;
  const newQty = prompt(`Modifier la quantité de "${p.name}" (actuel: ${p.quantity} kg, max 50):`, p.quantity);
  if (newQty === null) return;
  const q = parseInt(newQty);
  if (isNaN(q) || q < 0 || q > 50) { toast('Invalide', 'Entre 0 et 50 kg.', 'warning'); return; }
  try {
    if (typeof DB.updateProduct === 'function' && DB.updateProduct.constructor.name === 'AsyncFunction') {
      await DB.updateProduct(id, { quantity: q });
    } else {
      DB.updateProduct(id, { quantity: q });
    }
    await loadAllData();
    renderProducerTable(); renderProducerKPIs(); renderProducts();
    toast('Stock mis à jour', `${p.name} : ${q} kg`, 'success');
  } catch (err) {
    toast('Erreur', err.error || err.message, 'error');
  }
}

async function deleteMyProduct(id) {
  if (!confirm('Supprimer ce produit ?')) return;
  try {
    if (typeof DB.deleteProduct === 'function' && DB.deleteProduct.constructor.name === 'AsyncFunction') {
      await DB.deleteProduct(id);
    } else {
      DB.deleteProduct(id);
    }
    await loadAllData();
    renderProducerTable(); renderProducerKPIs(); renderProducts();
    toast('Supprimé', 'Produit retiré du catalogue.', 'info');
  } catch (err) {
    toast('Erreur', err.error || err.message, 'error');
  }
}

// Graphique producteur
let prodChart;
function initProducerChart() {
  const ctx = document.getElementById('salesChart');
  if (!ctx) return;
  const stats = DB.getStats();
  const monthly = stats.monthlySales || [];
  const labels   = monthly.map(m => m.label);
  const revenues = monthly.map(m => m.revenue || 0);
  const ordersArr = monthly.map(m => m.orders || 0);

  if (prodChart) prodChart.destroy();
  prodChart = new Chart(ctx, {
    type: 'bar',
    data: {
      labels,
      datasets: [
        { label:'Revenus (FCFA)', data:revenues, backgroundColor:'rgba(45,106,79,.7)', borderColor:'#2D6A4F', borderWidth:2, borderRadius:6 },
        { label:'Commandes',      data:ordersArr, backgroundColor:'rgba(233,130,12,.6)', borderColor:'#E9820C', borderWidth:2, borderRadius:6 }
      ]
    },
    options: {
      responsive:true,
      plugins:{ legend:{ position:'bottom', labels:{ font:{ family:"'DM Sans',sans-serif", size:11 } } } },
      scales:{
        y:{ grid:{color:'#f0ece6'}, ticks:{font:{family:"'DM Sans',sans-serif"}} },
        x:{ grid:{display:false}, ticks:{font:{family:"'DM Sans',sans-serif"}} }
      }
    }
  });

  // Message contextuel pour nouveau projet
  const chartNote = document.getElementById('chartNote');
  if (chartNote) {
    const hasData = revenues.some(v => v > 0);
    chartNote.innerHTML = hasData
      ? '<i class="fas fa-chart-line"></i> Statistiques basées sur vos commandes réelles'
      : '<i class="fas fa-seedling" style="color:var(--green)"></i> <strong>Nouveau projet !</strong> Les statistiques évolueront automatiquement avec vos premières ventes. Aucune donnée fictive — tout part de zéro.';
  }
}

/* ============================================================
   CHATBOT
   ============================================================ */
const CHATBOT_KB = {
  livraison: { kw:['livraison','délai','frais','expédition','port','demain'], ans:"🚚 <strong>Livraison SMART-LOUMA :</strong><br>• Gratuite à partir de 20 000 FCFA<br>• 5 000 FCFA sinon<br>• Délai : lendemain matin<br>• Zone : Dakar et périphérie" },
  commande:  { kw:['commander','commande','acheter','panier','achat'],       ans:"🛒 <strong>Comment commander :</strong><br>1. Créez un compte (restaurateur)<br>2. Parcourez la marketplace<br>3. Ajoutez au panier (50 kg max/produit)<br>4. Validez → paiement à la livraison" },
  conservation:{ kw:['conserver','conservation','stocker','durée','frais'], ans:"🌿 <strong>Conservation :</strong><br>• Carottes : sac perforé au frigo<br>• Tomates : température ambiante<br>• Oignons : endroit sec et aéré<br>• Légumes-feuilles : torchon humide au frigo" },
  producteur:{ kw:['producteur','vendre','inscrire','ferme','agriculteur','approbation'], ans:"👨‍🌾 <strong>Devenir producteur :</strong><br>1. Inscrivez-vous → rôle \"Producteur\"<br>2. L'admin approuve votre compte<br>3. Ajoutez vos produits (max 50 kg/produit)<br>4. Fixez vos prix — nous gérons la logistique<br>📞 <strong>+221 77 777 77 77</strong>" },
  paiement:  { kw:['paiement','payer','wave','orange money','espèces'],    ans:"💳 <strong>Modes de paiement :</strong><br>• Paiement à la livraison (espèces)<br>• Wave Money<br>• Orange Money<br>• Virement bancaire (grandes commandes)" },
  disponible:{ kw:['disponible','stock','catalogue','produit','légume','fruit'], ans:"📦 <strong>Produits disponibles :</strong><br>Consultez notre marketplace pour voir les produits actuellement en stock. Les disponibilités sont mises à jour en temps réel par nos producteurs." },
  default:   "Bonjour ! Je suis l'assistant SMART-LOUMA. Je peux vous aider sur la <strong>livraison</strong>, les <strong>commandes</strong>, la <strong>conservation</strong>, les <strong>producteurs</strong>, le <strong>paiement</strong> ou les <strong>produits disponibles</strong>. Comment puis-je vous aider ?"
};

function getBotResponse(msg) {
  const lower = msg.toLowerCase();
  for (const key in CHATBOT_KB) {
    if (key === 'default') continue;
    if (CHATBOT_KB[key].kw.some(w => lower.includes(w))) return CHATBOT_KB[key].ans;
  }
  return CHATBOT_KB.default;
}

function addMessage(html, type) {
  const wrap = document.getElementById('chatMessages');
  if (!wrap) return;
  const div = document.createElement('div');
  div.className = `msg msg-${type}`;
  div.innerHTML = `<div class="msg-bubble">${html}</div>`;
  wrap.appendChild(div); wrap.scrollTop = wrap.scrollHeight;
}

let chatOpen = false;
function initChatbot() {
  const toggle = document.getElementById('chatbotToggle');
  const win    = document.getElementById('chatbotWindow');
  const close  = document.getElementById('chatbotClose');
  const input  = document.getElementById('chatInput');
  const send   = document.getElementById('chatSend');

  toggle?.addEventListener('click', () => {
    chatOpen = !chatOpen;
    win?.classList.toggle('active', chatOpen);
    if (chatOpen && document.getElementById('chatMessages')?.children.length === 0) addMessage(getBotResponse(''), 'bot');
  });
  close?.addEventListener('click', () => { chatOpen = false; win?.classList.remove('active'); });

  const sendMsg = () => {
    const txt = input?.value.trim(); if (!txt) return;
    addMessage(txt, 'user'); input.value = '';
    setTimeout(() => addMessage(getBotResponse(txt), 'bot'), 500);
  };
  send?.addEventListener('click', sendMsg);
  input?.addEventListener('keypress', e => { if (e.key === 'Enter') sendMsg(); });
}

/* ============================================================
   CONTACT
   ============================================================ */
function initContact() {
  document.getElementById('contactForm')?.addEventListener('submit', async function(e) {
    e.preventDefault();
    const data = {
      name: this.querySelector('[name=name]').value.trim(),
      email: this.querySelector('[name=email]').value.trim(),
      subject: this.querySelector('[name=subject]').value,
      message: this.querySelector('[name=message]').value.trim(),
    };
    try {
      // Envoi direct à Formspree (sans backend, simple et fiable)
      const response = await fetch('https://formspree.io/f/xqapevvv', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
        },
        body: JSON.stringify(data),
      });

      if (!response.ok) {
        throw new Error('Le service de formulaire ne répond pas correctement.');
      }

      toast('Message envoyé !', `Merci ${data.name}, nous vous répondrons sous 24h.`, 'success');
      this.reset();
    } catch (err) {
      toast('Erreur', err.message || 'Impossible d\'envoyer le message.', 'error');
    }
  });
}

/* ============================================================
   SECTION HERO STATS (dynamiques)
   ============================================================ */
function updateHeroStats() {
  const stats = DB.getStats();
  // Les stats hero restent symboliques pour un nouveau projet
  // Elles reflèteront la réalité quand les données existent
}

/* ============================================================
   INIT PRINCIPAL
   ============================================================ */
document.addEventListener('DOMContentLoaded', async () => {
  Auth.init();
  await loadAllData();
  if (typeof updateLiveStats === 'function') updateLiveStats();
  initNavbar();
  initReveal();
  initCounters();
  initSlider();
  initLetterFilters();
  initFilters();
  renderProducts();
  initModals();
  initAuthModals();
  initUIForUser();
  initProducerSection();
  initChatbot();
  initContact();
  updateCartBadge();
});
