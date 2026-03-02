/**
 * SMART-LOUMA — db.js
 * Gestion centralisée des données (localStorage comme base de données)
 * Version 2.0
 */

/* ============================================================
   CONFIGURATION ADMIN (Hard-coded sécurisé côté client)
   ============================================================ */
const ADMIN_CREDENTIALS = {
  email: 'seydoubakhayokho1@gmail.com',
  password: 'louma',
  name: 'Seydou Bakhay Okho',
  role: 'admin'
};

/* ============================================================
   PHOTOS PAR DÉFAUT (vraies photos Unsplash par produit)
   ============================================================ */
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
  'Piment':          'https://images.unsplash.com/photo-1583119022894-919a68a3d0e3?auto=format&fit=crop&w=800&q=80',
  'Laitue':          'https://images.unsplash.com/photo-1621259182978-fbf93132d53d?auto=format&fit=crop&w=800&q=80',
  'Concombre':       'https://images.unsplash.com/photo-1604977042946-1eecc30f269e?auto=format&fit=crop&w=800&q=80',
  'Manioc':          'https://images.unsplash.com/photo-1594282486555-2d6e0e4c44b9?auto=format&fit=crop&w=800&q=80',
  'Patate douce':    'https://images.unsplash.com/photo-1596097635121-14b63b7a0c19?auto=format&fit=crop&w=800&q=80',
  'Gombo':           'https://images.unsplash.com/photo-1601515167852-c1ef03df0e60?auto=format&fit=crop&w=800&q=80',
  'Bissap':          'https://images.unsplash.com/photo-1597345037741-70caa94a9c7f?auto=format&fit=crop&w=800&q=80',
  'Papaye':          'https://images.unsplash.com/photo-1517282009859-f000ec3b26fe?auto=format&fit=crop&w=800&q=80',
  'Pastèque':        'https://images.unsplash.com/photo-1587049352846-4a222e784d38?auto=format&fit=crop&w=800&q=80',
  'Haricots verts':  'https://images.unsplash.com/photo-1567306226416-28f0efdc88ce?auto=format&fit=crop&w=800&q=80',
  'Chou-fleur':      'https://images.unsplash.com/photo-1568584711075-3d021a7c3ca3?auto=format&fit=crop&w=800&q=80',
  'default':         'https://images.unsplash.com/photo-1512621776951-a57141f2eefd?auto=format&fit=crop&w=800&q=80',
};

function getProductPhoto(name) {
  for (const key of Object.keys(PRODUCT_PHOTOS)) {
    if (name.toLowerCase().includes(key.toLowerCase()) || key.toLowerCase().includes(name.toLowerCase())) {
      return PRODUCT_PHOTOS[key];
    }
  }
  return PRODUCT_PHOTOS['default'];
}

/* ============================================================
   BASE DE DONNÉES LOCALE
   ============================================================ */
const DB = {
  // --- Lecture ---
  get(key) {
    try { return JSON.parse(localStorage.getItem('sl_' + key)) || null; } catch { return null; }
  },
  set(key, val) {
    try { localStorage.setItem('sl_' + key, JSON.stringify(val)); } catch(e) { console.error('DB.set error', e); }
  },
  remove(key) { localStorage.removeItem('sl_' + key); },

  // --- Utilisateurs ---
  getUsers()       { return this.get('users') || []; },
  saveUsers(u)     { this.set('users', u); },

  getUser(email) {
    return this.getUsers().find(u => u.email.toLowerCase() === email.toLowerCase()) || null;
  },

  createUser(data) {
    const users = this.getUsers();
    if (users.find(u => u.email.toLowerCase() === data.email.toLowerCase())) return { error: 'Email déjà utilisé.' };
    const user = {
      id: 'u_' + Date.now(),
      name: data.name,
      email: data.email,
      password: data.password, // en prod: hacher le mdp
      phone: data.phone || '',
      role: data.role || 'consumer', // 'admin' | 'producer' | 'consumer'
      status: data.role === 'producer' ? 'pending' : 'active', // producteurs attendent approbation admin
      company: data.company || '',
      zone: data.zone || '',
      createdAt: new Date().toISOString(),
      approvedAt: null,
    };
    users.push(user);
    this.saveUsers(users);
    return { user };
  },

  updateUser(id, patch) {
    const users = this.getUsers();
    const idx = users.findIndex(u => u.id === id);
    if (idx === -1) return false;
    users[idx] = { ...users[idx], ...patch };
    this.saveUsers(users);
    return users[idx];
  },

  deleteUser(id) {
    const users = this.getUsers().filter(u => u.id !== id);
    this.saveUsers(users);
  },

  // --- Produits ---
  getProducts()       { return this.get('products') || []; },
  saveProducts(p)     { this.set('products', p); },

  getProduct(id)     { return this.getProducts().find(p => p.id === id) || null; },

  createProduct(data, producer) {
    const products = this.getProducts();
    const product = {
      id: 'p_' + Date.now(),
      name: data.name,
      category: data.category,
      quantity: parseInt(data.quantity),
      price: parseInt(data.price),
      zone: data.zone,
      harvestDate: data.harvestDate,
      description: data.description || `${data.name} frais de ${data.zone}.`,
      image: data.image || getProductPhoto(data.name),
      rating: 0,
      available: data.available !== false, // visible dans marketplace
      producerId: producer.id,
      producerName: producer.name,
      producerCompany: producer.company || producer.name,
      createdAt: new Date().toISOString(),
      updatedAt: new Date().toISOString(),
      soldQty: 0,
    };
    products.push(product);
    this.saveProducts(products);
    return product;
  },

  updateProduct(id, patch) {
    const products = this.getProducts();
    const idx = products.findIndex(p => p.id === id);
    if (idx === -1) return false;
    products[idx] = { ...products[idx], ...patch, updatedAt: new Date().toISOString() };
    this.saveProducts(products);
    return products[idx];
  },

  deleteProduct(id) {
    this.saveProducts(this.getProducts().filter(p => p.id !== id));
  },

  getAvailableProducts() {
    return this.getProducts().filter(p => p.available && p.quantity > 0);
  },

  // --- Commandes ---
  getOrders()       { return this.get('orders') || []; },
  saveOrders(o)     { this.set('orders', o); },

  createOrder(data) {
    const orders = this.getOrders();
    const order = {
      id: 'cmd_' + Date.now(),
      ...data,
      status: 'pending', // pending | confirmed | delivered | cancelled
      createdAt: new Date().toISOString(),
    };
    orders.push(order);
    this.saveOrders(orders);

    // Déduire du stock
    order.items.forEach(item => {
      const p = this.getProduct(item.id);
      if (p) {
        this.updateProduct(item.id, {
          quantity: Math.max(0, p.quantity - item.qty),
          soldQty: (p.soldQty || 0) + item.qty,
        });
      }
    });
    return order;
  },

  updateOrder(id, patch) {
    const orders = this.getOrders();
    const idx = orders.findIndex(o => o.id === id);
    if (idx === -1) return false;
    orders[idx] = { ...orders[idx], ...patch };
    this.saveOrders(orders);
    return orders[idx];
  },

  // --- Panier ---
  getCart()   { return this.get('cart') || []; },
  saveCart(c) { this.set('cart', c); },

  // --- Session ---
  getSession() { return this.get('session') || null; },
  saveSession(u) { this.set('session', u); },
  clearSession() { this.remove('session'); },

  // --- Stats calculées dynamiquement ---
  getStats() {
    const products = this.getProducts();
    const orders   = this.getOrders();
    const users    = this.getUsers();

    const now = new Date();
    const monthNames = ['Jan','Fév','Mar','Avr','Mai','Jun','Jul','Aoû','Sep','Oct','Nov','Déc'];

    // Ventes par mois (12 derniers mois, commence à 0)
    const monthlySales = Array(6).fill(null).map((_, i) => {
      const d = new Date(now);
      d.setMonth(now.getMonth() - (5 - i));
      const monthOrders = orders.filter(o => {
        const od = new Date(o.createdAt);
        return od.getMonth() === d.getMonth() && od.getFullYear() === d.getFullYear() && o.status !== 'cancelled';
      });
      return {
        label: monthNames[d.getMonth()],
        orders: monthOrders.length,
        revenue: monthOrders.reduce((s, o) => s + (o.total || 0), 0),
        qty: monthOrders.reduce((s, o) => s + (o.items || []).reduce((ss, it) => ss + it.qty, 0), 0),
      };
    });

    return {
      totalProducts:    products.length,
      availableProducts: products.filter(p => p.available && p.quantity > 0).length,
      totalProducers:   users.filter(u => u.role === 'producer' && u.status === 'active').length,
      pendingProducers: users.filter(u => u.role === 'producer' && u.status === 'pending').length,
      totalConsumers:   users.filter(u => u.role === 'consumer').length,
      totalOrders:      orders.length,
      totalRevenue:     orders.filter(o => o.status !== 'cancelled').reduce((s, o) => s + (o.total || 0), 0),
      totalQtySold:     products.reduce((s, p) => s + (p.soldQty || 0), 0),
      monthlySales,
    };
  },
};

/* ============================================================
   SESSION COURANTE
   ============================================================ */
const Auth = {
  currentUser: null,

  init() {
    this.currentUser = DB.getSession();
  },

  login(email, password) {
    // Vérifier admin d'abord
    if (email.toLowerCase() === ADMIN_CREDENTIALS.email.toLowerCase() && password === ADMIN_CREDENTIALS.password) {
      const admin = { id: 'admin_0', name: ADMIN_CREDENTIALS.name, email: ADMIN_CREDENTIALS.email, role: 'admin', status: 'active' };
      DB.saveSession(admin);
      this.currentUser = admin;
      return { user: admin };
    }
    const user = DB.getUser(email);
    if (!user) return { error: 'Email ou mot de passe incorrect.' };
    if (user.password !== password) return { error: 'Email ou mot de passe incorrect.' };
    if (user.status === 'pending') return { error: 'Votre compte est en attente d\'approbation par l\'administrateur.' };
    if (user.status === 'suspended') return { error: 'Votre compte a été suspendu. Contactez l\'administration.' };
    DB.saveSession(user);
    this.currentUser = user;
    return { user };
  },

  logout() {
    DB.clearSession();
    this.currentUser = null;
  },

  isAdmin()    { return this.currentUser?.role === 'admin'; },
  isProducer() { return this.currentUser?.role === 'producer'; },
  isConsumer() { return this.currentUser?.role === 'consumer'; },
  isLoggedIn() { return !!this.currentUser; },
  canManageProducts() { return this.isAdmin() || (this.isProducer() && this.currentUser.status === 'active'); },
};
