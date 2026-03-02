/**
 * SMART-LOUMA — Gestionnaire d'authentification
 * Intégration Formspree pour les inscriptions
 */

class AuthManager {
  constructor() {
    this.currentUser = null;
    this.formspreeEndpoint = 'https://formspree.io/f/xqapevvv';
    this.init();
  }

  init() {
    const savedUser = localStorage.getItem('currentUser');
    if (savedUser) {
      this.currentUser = JSON.parse(savedUser);
      this.updateUI();
    }
  }

  async register(userData) {
    try {
      // Vérifier si l'email existe déjà
      const existingUser = authDB.findUserByEmail(userData.email);
      if (existingUser) {
        throw new Error('Cet email est déjà utilisé');
      }

      // Ajouter l'utilisateur à la base locale
      const newUser = authDB.addUser(userData);

      // Envoyer les données à Formspree pour sauvegarde
      await this.sendToFormspree({
        action: 'register',
        name: userData.name,
        email: userData.email,
        phone: userData.phone || '',
        company: userData.company || '',
        role: userData.role || 'consumer',
        message: `Nouvelle inscription : ${userData.name} (${userData.role})`
      });

      // Connecter automatiquement l'utilisateur
      this.login(userData.email, userData.password);
      
      return { success: true, user: newUser };
    } catch (error) {
      console.error('Erreur inscription:', error);
      throw error;
    }
  }

  login(email, password) {
    const user = authDB.validateLogin(email, password);
    if (user) {
      this.currentUser = user;
      localStorage.setItem('currentUser', JSON.stringify(user));
      this.updateUI();
      
      // Envoyer la connexion à Formspree pour suivi
      this.sendToFormspree({
        action: 'login',
        email: email,
        message: `Connexion réussie : ${user.name}`
      }).catch(err => console.log('Erreur suivi connexion:', err));
      
      return { success: true, user };
    }
    throw new Error('Email ou mot de passe incorrect');
  }

  logout() {
    if (this.currentUser) {
      this.sendToFormspree({
        action: 'logout',
        email: this.currentUser.email,
        message: `Déconnexion : ${this.currentUser.name}`
      }).catch(err => console.log('Erreur suivi déconnexion:', err));
    }
    
    this.currentUser = null;
    localStorage.removeItem('currentUser');
    this.updateUI();
  }

  async sendToFormspree(data) {
    try {
      const response = await fetch(this.formspreeEndpoint, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json'
        },
        body: JSON.stringify(data)
      });
      
      // Ne pas lever d'erreur si Formspree ne répond pas correctement
      // pour ne pas bloquer l'expérience utilisateur
      if (!response.ok) {
        console.warn('Formspree response not OK:', response.status);
        return { ok: false, status: response.status };
      }
      
      return await response.json();
    } catch (error) {
      console.warn('Erreur Formspree (non bloquante):', error.message);
      // Retourner un objet pour éviter les erreurs
      return { ok: false, error: error.message };
    }
  }

  updateUI() {
    const loginBtn = document.getElementById('loginBtn');
    const registerBtn = document.getElementById('registerBtn');
    const userInfo = document.getElementById('userInfo');
    const adminNavLink = document.getElementById('adminNavLink');
    const producerNavLink = document.getElementById('producerNavLink');

    if (this.currentUser) {
      // Utilisateur connecté
      if (loginBtn) loginBtn.style.display = 'none';
      if (registerBtn) registerBtn.style.display = 'none';
      
      if (userInfo) {
        userInfo.style.display = 'flex';
        userInfo.innerHTML = `
          <span style="color: white; font-weight: 500;">${this.currentUser.name}</span>
          <button class="btn btn-outline" onclick="authManager.logout()" style="padding: 0.3rem 0.8rem; font-size: 0.85rem;">
            <i class="fas fa-sign-out-alt"></i> Déconnexion
          </button>
        `;
      }

      // Afficher lien admin si c'est un admin
      if (adminNavLink && this.currentUser.role === 'admin') {
        adminNavLink.style.display = 'block';
      }

      // Afficher lien producteur si c'est un producteur
      if (producerNavLink && this.currentUser.role === 'producer') {
        producerNavLink.style.display = 'block';
      }
    } else {
      // Utilisateur non connecté
      if (loginBtn) loginBtn.style.display = 'block';
      if (registerBtn) registerBtn.style.display = 'block';
      if (userInfo) userInfo.style.display = 'none';
      if (adminNavLink) adminNavLink.style.display = 'none';
      if (producerNavLink) producerNavLink.style.display = 'none';
    }
  }

  isAuthenticated() {
    return this.currentUser !== null;
  }

  isAdmin() {
    return this.currentUser && this.currentUser.role === 'admin';
  }

  isProducer() {
    return this.currentUser && this.currentUser.role === 'producer';
  }
}

const authManager = new AuthManager();
