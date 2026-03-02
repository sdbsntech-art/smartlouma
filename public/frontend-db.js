/**
 * SMART-LOUMA — db.js v3.0
 * Connecté au backend Laravel via API REST
 * Fallback localStorage si le backend est indisponible
 */

// ══════════════════════════════════════════════════════════════
//  CONFIGURATION
// ══════════════════════════════════════════════════════════════
// URL relative si servi depuis Laravel (port 8000), absolue si front séparé (ex: 8080)
const API_BASE_URL = (typeof window !== 'undefined' && window.location.port !== '8000' && window.location.hostname === 'localhost')
  ? 'http://localhost:8000/api' : '/api';
const USE_LARAVEL  = true; // Mettre false pour revenir au mode localStorage uniquement

// ══════════════════════════════════════════════════════════════
//  ADMIN (fallback local si pas de backend)
// ══════════════════════════════════════════════════════════════
const ADMIN_CREDENTIALS = {
  email: 'seydoubakhayokho1@gmail.com',
  password: 'louma',
  name: 'Seydou Bakhay Okho',
  role: 'admin'
};

// ══════════════════════════════════════════════════════════════
//  PHOTOS PAR DÉFAUT
// ══════════════════════════════════════════════════════════════
const PRODUCT_PHOTOS = {
  'Carottes':        'https://images.unsplash.com/photo-1598170845058-78131a90f4bf?auto=format&fit=crop&w=800&q=80',
  'Tomates':         'https://images.unsplash.com/photo-1592924357228-91a4daadcfea?auto=format&fit=crop&w=800&q=80',
  'Oignons':         'https://images.unsplash.com/photo-1587049633312-d628ae50a8ae?auto=format&fit=crop&w=800&q=80',
  'Courgettes':      'https://images.unsplash.com/photo-1540420828642-fca2c5c18abb?auto=format&fit=crop&w=800&q=80',
  'Pommes de terre': 'https://images.unsplash.com/photo-1518977676601-b53f82aba655?auto=format&fit=crop&w=800&q=80',
  'Aubergines':      'https://images.unsplash.com/photo-1621956838481-f8bc3bc2e9a5?auto=format&fit=crop&w=800&q=80',
  'Choux':           'https://images.unsplash.com/photo-1655403454657-25e4f5ea7c3e?auto=format&fit=crop&w=800&q=80',
  'Poivrons':        'https://images.unsplash.com/photo-1563565375-f3fdfdbefa83?auto=format&fit=crop&w=800&q=80',
  'Mangues':         'https://images.unsplash.com/photo-1553279768-865429fa0078?auto=format&fit=crop&w=800&q=80',
  'Bananes':         'https://images.unsplash.com/photo-1571771894821-ce9b6c11b08e?auto=format&fit=crop&w=800&q=80',
  'Épinards':        'https://images.unsplash.com/photo-1576045057995-568f588f82fb?auto=format&fit=crop&w=800&q=80',
  'Ail':             'https://images.unsplash.com/photo-1615478503562-ec2d8aa0e24e?auto=format&fit=crop&w=800&q=80',
  'default':         'https://images.unsplash.com/photo-1512621776951-a57141f2eefd?auto=format&fit=crop&w=800&q=80',
};

function getProductPhoto(name) {
  for (const key of Object.keys(PRODUCT_PHOTOS)) {
    if (key === 'default') continue;
    if (name.toLowerCase().includes(key.toLowerCase()) || key.toLowerCase().includes(name.toLowerCase())) {
      return PRODUCT_PHOTOS[key];
    }
  }
  return PRODUCT_PHOTOS['default'];
}

// ══════════════════════════════════════════════════════════════
//  REQUÊTES API LARAVEL
// ══════════════════════════════════════════════════════════════
const API = {
  token: null,

  getToken() {
    return this.token || localStorage.getItem('sl_api_token');
  },

  setToken(t) {
    this.token = t;
    if (t) localStorage.setItem('sl_api_token', t);
    else localStorage.removeItem('sl_api_token');
  },

  headers() {
    const h = { 'Content-Type': 'application/json', 'Accept': 'application/json' };
    const tok = this.getToken();
    if (tok) h['Authorization'] = 'Bearer ' + tok;
    return h;
  },

  async get(path) {
    const r = await fetch(API_BASE_URL + path, { headers: this.headers(), credentials: 'include' });
    const json = await r.json().catch(() => ({}));
    if (!r.ok) throw json;
    return json;
  },

  async post(path, data) {
    const r = await fetch(API_BASE_URL + path, {
      method: 'POST', headers: this.headers(), body: JSON.stringify(data), credentials: 'include'
    });
    const json = await r.json().catch(() => ({}));
    if (!r.ok) throw json;
    return json;
  },

  async put(path, data) {
    const r = await fetch(API_BASE_URL + path, {
      method: 'PUT', headers: this.headers(), body: JSON.stringify(data), credentials: 'include'
    });
    const json = await r.json().catch(() => ({}));
    if (!r.ok) throw json;
    return json;
  },

  async delete(path) {
    const r = await fetch(API_BASE_URL + path, { method: 'DELETE', headers: this.headers(), credentials: 'include' });
    const json = await r.json().catch(() => ({}));
    if (!r.ok) throw json;
    return json;
  },

  async patch(path, data) {
    const r = await fetch(API_BASE_URL + path, {
      method: 'PATCH', headers: this.headers(), body: JSON.stringify(data || {}), credentials: 'include'
    });
    const json = await r.json().catch(() => ({}));
    if (!r.ok) throw json;
    return json;
  },
};

// ══════════════════════════════════════════════════════════════
//  CACHE PRODUITS (pour compatibilité main.js)
// ══════════════════════════════════════════════════════════════
let productsCache = [];
let myProductsCache = [];
let statsCache = null;

// ══════════════════════════════════════════════════════════════
//  BASE DE DONNÉES (local + compatibilité API pour main.js)
// ══════════════════════════════════════════════════════════════
const DB = {
  get(key)       { try { return JSON.parse(localStorage.getItem('sl_' + key)) || null; } catch { return null; } },
  set(key, val)  { try { localStorage.setItem('sl_' + key, JSON.stringify(val)); } catch(e) {} },
  remove(key)    { localStorage.removeItem('sl_' + key); },

  // Panier (toujours local)
  getCart()      { return this.get('cart') || []; },
  saveCart(c)    { this.set('cart', c); },

  // Settings (livraison etc.)
  getSettings()  { return this.get('settings') || { freeDelivery: 20000, deliveryFee: 5000 }; },

  // ── Produits (cache API) ───────────────────────────────────
  getAvailableProducts() {
    return USE_LARAVEL ? productsCache : (this.get('products') || []).filter(p => p.available && p.quantity > 0);
  },
  getProduct(id) {
    const list = USE_LARAVEL ? productsCache : (this.get('products') || []);
    return list.find(p => p.id == id) || null;
  },
  getProducts() {
    return USE_LARAVEL ? myProductsCache : (this.get('products') || []);
  },

  // ── Stats (cache) ───────────────────────────────────────────
  getStats() {
    if (USE_LARAVEL && statsCache && Object.keys(statsCache).length) {
      const m = statsCache.monthly_sales || [];
      return {
        totalProducts: statsCache.total_products || 0,
        availableProducts: statsCache.available_products || 0,
        totalProducers: statsCache.total_producers || 0,
        pendingProducers: statsCache.pending_producers || 0,
        totalConsumers: statsCache.total_consumers || 0,
        totalOrders: statsCache.total_orders || 0,
        totalRevenue: statsCache.total_revenue || 0,
        totalQtySold: statsCache.total_kg_sold || 0,
        monthlySales: m.map(x => ({ label: x.label, orders: x.orders, revenue: x.revenue, qty: x.qty })),
      };
    }
    const prods = this.get('products') || [];
    const orders = this.get('orders') || [];
    const users = this.get('users') || [];
    const now = new Date();
    const monthNames = ['Jan','Fév','Mar','Avr','Mai','Jun','Jul','Aoû','Sep','Oct','Nov','Déc'];
    const monthlySales = Array(6).fill(null).map((_, i) => {
      const d = new Date(now); d.setMonth(now.getMonth() - (5 - i));
      const monthOrders = orders.filter(o => {
        const od = new Date(o.createdAt);
        return od.getMonth() === d.getMonth() && od.getFullYear() === d.getFullYear() && o.status !== 'cancelled';
      });
      return { label: monthNames[d.getMonth()], orders: monthOrders.length, revenue: monthOrders.reduce((s,o) => s + (o.total||0), 0), qty: monthOrders.reduce((s,o) => s + (o.items||[]).reduce((ss,it) => ss + it.qty, 0), 0) };
    });
    return {
      totalProducts: prods.length,
      availableProducts: prods.filter(p => p.available && p.quantity > 0).length,
      totalProducers: users.filter(u => u.role === 'producer' && u.status === 'active').length,
      pendingProducers: users.filter(u => u.role === 'producer' && u.status === 'pending').length,
      totalConsumers: users.filter(u => u.role === 'consumer').length,
      totalOrders: orders.length,
      totalRevenue: orders.filter(o => o.status !== 'cancelled').reduce((s,o) => s + (o.total||0), 0),
      totalQtySold: prods.reduce((s,p) => s + (p.soldQty||0), 0),
      monthlySales,
    };
  },

  // ── Chargement async (à appeler au démarrage) ────────────────
  async loadProducts(filters = {}) {
    if (USE_LARAVEL) {
      try {
        const res = await Products.getAvailable(filters);
        productsCache = (res.data || []).map(normalizeProduct);
        return productsCache;
      } catch (e) {
        console.warn('API produits indisponible:', e);
        productsCache = [];
        return [];
      }
    }
    productsCache = this.getAvailableProducts();
    return productsCache;
  },
  async loadMyProducts() {
    if (USE_LARAVEL && Auth.isLoggedIn()) {
      try {
        const res = await Products.getMyProducts();
        myProductsCache = Array.isArray(res) ? res.map(normalizeProduct) : [];
        return myProductsCache;
      } catch (e) {
        myProductsCache = [];
        return [];
      }
    }
    myProductsCache = this.getProducts();
    return myProductsCache;
  },
  async loadStats() {
    if (USE_LARAVEL) {
      try {
        const pub = await Stats.getPublic();
        if (pub) statsCache = {
          total_products: pub.products, available_products: pub.products,
          total_producers: pub.producers, total_consumers: pub.consumers,
          total_kg_sold: pub.total_kg || 0, total_orders: 0, total_revenue: 0,
          pending_producers: 0, monthly_sales: []
        };
        if (Auth.isAdmin() && API.getToken()) {
          const adm = await API.get('/stats/admin').catch(() => null);
          if (adm) statsCache = adm;
        }
        return statsCache;
      } catch (e) { return null; }
    }
    return this.getStats();
  },

  // ── Création / mise à jour (async, pour producteur) ─────────
  async createProduct(data, producer) {
    if (USE_LARAVEL) {
      const payload = {
        name: data.name,
        category: data.category,
        quantity: data.quantity,
        price: data.price,
        zone: data.zone,
        harvest_date: data.harvestDate || data.harvest_date || null,
        description: data.description || '',
        image: data.image || null,
        available: data.available !== false,
      };
      const p = await Products.create(payload);
      const norm = normalizeProduct(p);
      productsCache = productsCache.filter(x => x.id !== norm.id);
      productsCache.push(norm);
      myProductsCache.push(norm);
      return norm;
    }
    throw new Error('Backend Laravel requis.');
  },
  async updateProduct(id, patch) {
    if (USE_LARAVEL) {
      const payload = {};
      if (patch.quantity !== undefined) payload.quantity = patch.quantity;
      if (patch.available !== undefined) payload.available = patch.available;
      if (patch.price !== undefined) payload.price = patch.price;
      if (patch.name !== undefined) payload.name = patch.name;
      if (patch.zone !== undefined) payload.zone = patch.zone;
      if (Object.keys(payload).length === 0) return DB.getProduct(id);
      const p = await Products.update(id, payload);
      const norm = normalizeProduct(p);
      const idx = productsCache.findIndex(x => x.id == id);
      if (idx >= 0) productsCache[idx] = norm;
      const midx = myProductsCache.findIndex(x => x.id == id);
      if (midx >= 0) myProductsCache[midx] = norm;
      return norm;
    }
    throw new Error('Backend Laravel requis.');
  },
  async deleteProduct(id) {
    if (USE_LARAVEL) {
      await Products.delete(id);
      productsCache = productsCache.filter(p => p.id != id);
      myProductsCache = myProductsCache.filter(p => p.id != id);
      return;
    }
    throw new Error('Backend Laravel requis.');
  },
  async toggleProduct(id) {
    if (USE_LARAVEL) {
      const res = await Products.toggle(id);
      const p = DB.getProduct(id);
      if (p) p.available = res.available;
      return res;
    }
    throw new Error('Backend Laravel requis.');
  },

  // ── Commande ─────────────────────────────────────────────────
  async createOrder(data) {
    if (USE_LARAVEL) {
      const items = (data.items || []).map(i => ({ id: parseInt(i.id), qty: i.qty }));
      return Orders.create(items, data.delivery_address || null);
    }
    throw new Error('Backend Laravel requis.');
  },

  // ── Utilisateurs (pour inscription, fallback local) ──────────
  createUser(data) {
    if (USE_LARAVEL) return { error: 'Utilisez Auth.register()' };
    const users = this.get('users') || [];
    if (users.find(u => u.email.toLowerCase() === data.email.toLowerCase())) return { error: 'Email déjà utilisé.' };
    const user = { id: 'u_' + Date.now(), name: data.name, email: data.email, password: data.password, phone: data.phone || '', role: data.role || 'consumer', status: data.role === 'producer' ? 'pending' : 'active', company: data.company || '', zone: data.zone || '', createdAt: new Date().toISOString(), approvedAt: null };
    users.push(user);
    this.set('users', users);
    return { user };
  },
};

// ══════════════════════════════════════════════════════════════
//  AUTHENTIFICATION
// ══════════════════════════════════════════════════════════════
const Auth = {
  currentUser: null,

  init() {
    this.currentUser = DB.get('session');
    API.token = localStorage.getItem('sl_api_token');
  },

  async login(email, password) {
    if (USE_LARAVEL) {
      try {
        const res = await API.post('/auth/login', { email, password });
        API.setToken(res.token);
        DB.set('session', res.user);
        this.currentUser = res.user;
        return { user: res.user };
      } catch(err) {
        return { error: err.message || err.error || 'Email ou mot de passe incorrect.' };
      }
    }
    // Fallback local
    if (email.toLowerCase() === ADMIN_CREDENTIALS.email.toLowerCase() && password === ADMIN_CREDENTIALS.password) {
      const admin = { id:'admin_0', name:ADMIN_CREDENTIALS.name, email:ADMIN_CREDENTIALS.email, role:'admin', status:'active' };
      DB.set('session', admin); this.currentUser = admin; return { user: admin };
    }
    return { error: 'Email ou mot de passe incorrect.' };
  },

  async register(data) {
    if (USE_LARAVEL) {
      try {
        const res = await API.post('/auth/register', data);
        if (res.token) {
          API.setToken(res.token);
          DB.set('session', res.user);
          this.currentUser = res.user;
        }
        return res;
      } catch(err) {
        const firstError = err.errors ? Object.values(err.errors)[0][0] : (err.error || err.message || 'Erreur lors de l\'inscription.');
        return { error: firstError };
      }
    }
    return { error: 'Backend Laravel requis pour l\'inscription.' };
  },

  logout() {
    if (USE_LARAVEL && API.getToken()) {
      API.post('/auth/logout', {}).catch(() => {});
    }
    API.setToken(null);
    DB.remove('session');
    this.currentUser = null;
  },

  isAdmin()            { return this.currentUser?.role === 'admin'; },
  isProducer()         { return this.currentUser?.role === 'producer'; },
  isConsumer()         { return this.currentUser?.role === 'consumer'; },
  isLoggedIn()         { return !!this.currentUser; },
  canManageProducts()  { return this.isAdmin() || (this.isProducer() && this.currentUser?.status === 'active'); },
};

// ══════════════════════════════════════════════════════════════
//  NORMALISATION PRODUIT (API → format frontend)
// ══════════════════════════════════════════════════════════════
function normalizeProduct(p) {
  if (!p) return null;
  const prod = p.producer || {};
  return {
    id: p.id,
    name: p.name,
    category: p.category,
    quantity: parseInt(p.quantity) || 0,
    price: parseInt(p.price) || 0,
    zone: p.zone || '',
    harvestDate: p.harvest_date || p.harvestDate,
    description: p.description || '',
    image: p.image || p.image_url || getProductPhoto(p.name),
    rating: p.rating || 0,
    available: p.available !== false,
    producerId: p.producer_id || p.producerId,
    producerName: prod.name || prod.company || p.producer_name || 'Producteur',
    producerCompany: prod.company || prod.name || '',
  };
}

// ══════════════════════════════════════════════════════════════
//  PRODUITS (via API ou localStorage)
// ══════════════════════════════════════════════════════════════
const Products = {
  async getAvailable(filters = {}) {
    if (USE_LARAVEL) {
      const clean = Object.fromEntries(Object.entries(filters).filter(([, v]) => v != null && v !== ''));
      clean.per_page = 200; // Récupérer tout le catalogue pour filtrage côté client
      const params = new URLSearchParams(clean);
      const res = await API.get('/products?' + params);
      return res;
    }
    // Fallback localStorage
    return { data: (DB.get('products') || []).filter(p => p.available && p.quantity > 0) };
  },

  async getMyProducts() {
    if (USE_LARAVEL) return API.get('/my-products');
    return DB.get('products') || [];
  },

  async create(data) {
    if (USE_LARAVEL) return API.post('/products', data);
    throw new Error('Backend Laravel requis.');
  },

  async update(id, data) {
    if (USE_LARAVEL) return API.put('/products/' + id, data);
    throw new Error('Backend Laravel requis.');
  },

  async delete(id) {
    if (USE_LARAVEL) return API.delete('/products/' + id);
    throw new Error('Backend Laravel requis.');
  },

  async toggle(id) {
    if (USE_LARAVEL) return API.patch('/products/' + id + '/toggle');
    throw new Error('Backend Laravel requis.');
  },
};

// ══════════════════════════════════════════════════════════════
//  COMMANDES (via API)
// ══════════════════════════════════════════════════════════════
const Orders = {
  async create(items, deliveryAddress) {
    if (USE_LARAVEL) return API.post('/orders', { items, delivery_address: deliveryAddress });
    throw new Error('Backend Laravel requis pour les commandes.');
  },
  async getMine() {
    if (USE_LARAVEL) return API.get('/my-orders');
    return DB.get('orders') || [];
  },
};

// ══════════════════════════════════════════════════════════════
//  CONTACT (via API)
// ══════════════════════════════════════════════════════════════
const Contact = {
  async send(data) {
    if (USE_LARAVEL) return API.post('/contact', data);
    return { message: 'Message enregistré localement.' };
  },
};

// ══════════════════════════════════════════════════════════════
//  STATS (via API)
// ══════════════════════════════════════════════════════════════
const Stats = {
  async getPublic() {
    if (USE_LARAVEL) {
      try { return API.get('/stats'); } catch { return null; }
    }
    return null;
  },
};
