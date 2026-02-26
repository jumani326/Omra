# GUIDE DE TEST - Interface Umrah Management System

## 🚀 Démarrage du Serveur

### 1. Démarrer le serveur Laravel
```bash
php artisan serve
```
Le serveur sera accessible sur : **http://localhost:8000**

### 2. Démarrer Vite (pour les assets CSS/JS)
Dans un **nouveau terminal**, exécutez :
```bash
npm run dev
```

## 🔐 Comptes de Test

### Super Admin
- **Email** : `admin@omra.test`
- **Mot de passe** : `password`
- **Rôle** : Super Admin Agence (toutes les permissions)

### Admin Branche
- **Email** : `admin.branche@omra.test`
- **Mot de passe** : `password`
- **Rôle** : Admin Branche

## 📋 Tests à Effectuer

### 1. Test de Connexion
1. Accédez à : http://localhost:8000
2. Vous serez redirigé vers `/login`
3. Connectez-vous avec `admin@omra.test` / `password`
4. ✅ Vous devriez être redirigé vers `/dashboard`

### 2. Test du Dashboard
1. Vérifiez que la sidebar s'affiche à gauche (vert foncé #0F3F2E)
2. Vérifiez que le header s'affiche en haut
3. Vérifiez que les 4 KPI Cards s'affichent :
   - Total Pèlerins
   - Revenu Mensuel
   - Taux Visa
   - Groupes Actifs
4. ✅ Testez le bouton de collapse de la sidebar (icône hamburger)

### 3. Test de la Navigation
1. Cliquez sur les liens de la sidebar :
   - Dashboard
   - Pèlerins (si permission)
   - Forfaits (si permission)
   - Visas (si permission)
   - Finance (si permission)
   - Utilisateurs (si permission)
2. ✅ Vérifiez que les liens changent de couleur quand actifs

### 4. Test du Menu Utilisateur
1. Cliquez sur votre avatar en haut à droite
2. ✅ Vérifiez que le menu déroulant s'affiche
3. Testez "Déconnexion"
4. ✅ Vous devriez être redirigé vers `/login`

### 5. Test des Permissions
1. Connectez-vous avec `admin.branche@omra.test`
2. ✅ Vérifiez que seuls les liens autorisés s'affichent dans la sidebar

## 🎨 Vérification du Design

### Couleurs
- ✅ Sidebar : Vert foncé (#0F3F2E)
- ✅ Header : Blanc avec ombre
- ✅ Cards : Arrondies (border-radius 16px)
- ✅ Hover effects : Ombres plus prononcées

### Typographie
- ✅ Titres : Police Poppins
- ✅ Corps : Police Inter

## ⚠️ Problèmes Potentiels

### Si les styles ne s'affichent pas
1. Vérifiez que `npm run dev` est en cours d'exécution
2. Videz le cache : `php artisan view:clear`
3. Rechargez la page avec Ctrl+F5

### Si erreur 404
1. Vérifiez que le serveur Laravel est démarré
2. Vérifiez les routes : `php artisan route:list`

### Si erreur de connexion
1. Vérifiez la base de données : `php artisan migrate:status`
2. Réexécutez les seeders : `php artisan db:seed`

## 📝 Notes

- Les graphiques Chart.js seront implémentés au Jour 6
- Les modules CRUD seront créés aux Jours 3-4
- Le chatbot IA sera créé au Jour 5

---

**Bon test ! 🎉**

