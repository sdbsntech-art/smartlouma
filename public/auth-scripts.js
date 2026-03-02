/**
 * SMART-LOUMA — Scripts d'authentification
 * Gestion des modales et interactions
 */

// Ouvrir la modal d'authentification
function openAuthModal(tab = 'login') {
  const modal = document.getElementById('authModal');
  if (modal) {
    modal.style.display = 'flex';
    document.body.style.overflow = 'hidden';
    
    // Activer l'onglet demandé
    switchAuthTab(tab);
    
    // Focus sur le premier champ
    setTimeout(() => {
      const firstInput = modal.querySelector('input');
      if (firstInput) firstInput.focus();
    }, 100);
  }
}

// Fermer la modal d'authentification
function closeAuthModal() {
  const modal = document.getElementById('authModal');
  if (modal) {
    modal.style.display = 'none';
    document.body.style.overflow = '';
    clearAuthMessages();
    resetForms();
  }
}

// Changer d'onglet dans la modal
function switchAuthTab(tab) {
  // Mettre à jour les boutons d'onglets
  document.querySelectorAll('.auth-tab').forEach(btn => {
    btn.classList.remove('active');
    if (btn.dataset.tab === tab) {
      btn.classList.add('active');
    }
  });
  
  // Mettre à jour le contenu
  document.querySelectorAll('.auth-tab-content').forEach(content => {
    content.classList.remove('active');
  });
  
  const activeContent = document.getElementById(tab + 'Tab');
  if (activeContent) {
    activeContent.classList.add('active');
  }
}

// Afficher/masquer le champ entreprise selon le rôle
function toggleCompanyField() {
  const roleSelect = document.getElementById('registerRole');
  const companyField = document.getElementById('companyField');
  
  if (roleSelect && companyField) {
    if (roleSelect.value === 'producer') {
      companyField.style.display = 'block';
      document.getElementById('registerCompany').required = true;
    } else {
      companyField.style.display = 'none';
      document.getElementById('registerCompany').required = false;
      document.getElementById('registerCompany').value = '';
    }
  }
}

// Gérer la connexion
async function handleLogin(event) {
  event.preventDefault();
  
  const email = document.getElementById('loginEmail').value;
  const password = document.getElementById('loginPassword').value;
  
  try {
    showAuthMessage('Connexion en cours...', 'info');
    
    const result = await authManager.login(email, password);
    
    if (result.success) {
      showAuthMessage('Connexion réussie ! Redirection...', 'success');
      setTimeout(() => {
        closeAuthModal();
        
        // Redirection selon le rôle
        if (authManager.isAdmin()) {
          window.location.href = 'admin.html';
        } else if (authManager.isProducer()) {
          document.getElementById('producerNavLink')?.click();
        } else {
          document.getElementById('marketplace')?.scrollIntoView();
        }
      }, 1500);
    }
  } catch (error) {
    showAuthMessage(error.message, 'error');
  }
}

// Gérer l'inscription
async function handleRegister(event) {
  event.preventDefault();
  
  const formData = {
    name: document.getElementById('registerName').value,
    email: document.getElementById('registerEmail').value,
    phone: document.getElementById('registerPhone').value,
    role: document.getElementById('registerRole').value,
    company: document.getElementById('registerCompany').value,
    password: document.getElementById('registerPassword').value,
    passwordConfirm: document.getElementById('registerPasswordConfirm').value
  };
  
  // Validation
  if (formData.password !== formData.passwordConfirm) {
    showAuthMessage('Les mots de passe ne correspondent pas', 'error');
    return;
  }
  
  if (formData.password.length < 6) {
    showAuthMessage('Le mot de passe doit contenir au moins 6 caractères', 'error');
    return;
  }
  
  if (formData.role === 'producer' && !formData.company) {
    showAuthMessage('Le nom de l\'entreprise est requis pour les producteurs', 'error');
    return;
  }
  
  try {
    showAuthMessage('Inscription en cours...', 'info');
    
    const result = await authManager.register(formData);
    
    if (result.success) {
      showAuthMessage('Inscription réussie ! Bienvenue sur SMART-LOUMA', 'success');
      setTimeout(() => {
        closeAuthModal();
        
        // Redirection selon le rôle
        if (authManager.isAdmin()) {
          window.location.href = 'admin.html';
        } else if (authManager.isProducer()) {
          document.getElementById('producerNavLink')?.click();
        } else {
          document.getElementById('marketplace')?.scrollIntoView();
        }
      }, 2000);
    }
  } catch (error) {
    showAuthMessage(error.message, 'error');
  }
}

// Afficher un message dans la modal
function showAuthMessage(message, type = 'info') {
  const messageEl = document.getElementById('authMessage');
  if (messageEl) {
    messageEl.textContent = message;
    messageEl.className = `auth-message ${type}`;
    messageEl.style.display = 'block';
    
    // Auto-cacher pour les messages de succès
    if (type === 'success') {
      setTimeout(() => {
        messageEl.style.display = 'none';
      }, 5000);
    }
  }
}

// Effacer les messages
function clearAuthMessages() {
  const messageEl = document.getElementById('authMessage');
  if (messageEl) {
    messageEl.style.display = 'none';
  }
}

// Réinitialiser les formulaires
function resetForms() {
  document.getElementById('loginForm')?.reset();
  document.getElementById('registerForm')?.reset();
  toggleCompanyField();
}

// Fermeture avec la touche Échap
document.addEventListener('keydown', (event) => {
  if (event.key === 'Escape') {
    closeAuthModal();
  }
});

// Fermeture en cliquant sur l'overlay
document.addEventListener('click', (event) => {
  if (event.target.classList.contains('modal-overlay')) {
    closeAuthModal();
  }
});

// Initialisation des boutons
document.addEventListener('DOMContentLoaded', () => {
  // Bouton de connexion
  const loginBtn = document.getElementById('loginBtn');
  if (loginBtn) {
    loginBtn.addEventListener('click', () => openAuthModal('login'));
  }
  
  // Bouton d'inscription
  const registerBtn = document.getElementById('registerBtn');
  if (registerBtn) {
    registerBtn.addEventListener('click', () => openAuthModal('register'));
  }
  
  // Validation en temps réel du mot de passe
  const passwordConfirm = document.getElementById('registerPasswordConfirm');
  if (passwordConfirm) {
    passwordConfirm.addEventListener('input', () => {
      const password = document.getElementById('registerPassword').value;
      if (passwordConfirm.value && password !== passwordConfirm.value) {
        passwordConfirm.setCustomValidity('Les mots de passe ne correspondent pas');
      } else {
        passwordConfirm.setCustomValidity('');
      }
    });
  }
});
