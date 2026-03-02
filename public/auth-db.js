/**
 * SMART-LOUMA — Base de données Auth JavaScript
 * Remplacement de SQLite pour connexion/inscription avec Formspree
 */

class AuthDatabase {
  constructor() {
    this.init();
  }

  init() {
    if (!localStorage.getItem('users')) {
      const defaultUsers = [
        {
          id: 1,
          name: 'Seydou Bakhay Okho',
          email: 'seydoubakhayokho1@gmail.com',
          password: 'louma',
          role: 'admin',
          status: 'active',
          created_at: new Date().toISOString()
        }
      ];
      localStorage.setItem('users', JSON.stringify(defaultUsers));
    }
  }

  getUsers() {
    return JSON.parse(localStorage.getItem('users') || '[]');
  }

  saveUsers(users) {
    localStorage.setItem('users', JSON.stringify(users));
  }

  findUserByEmail(email) {
    const users = this.getUsers();
    return users.find(u => u.email === email);
  }

  addUser(userData) {
    const users = this.getUsers();
    const newUser = {
      id: Date.now(),
      ...userData,
      created_at: new Date().toISOString()
    };
    users.push(newUser);
    this.saveUsers(users);
    return newUser;
  }

  validateLogin(email, password) {
    const user = this.findUserByEmail(email);
    if (user && user.password === password) {
      const { password, ...userWithoutPassword } = user;
      return userWithoutPassword;
    }
    return null;
  }
}

const authDB = new AuthDatabase();
