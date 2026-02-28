# GUIDE DE TEST - Jour 3 : Modules Pèlerins et Forfaits

## 🚀 Accès aux Modules

### URLs de Test
- **Pèlerins** : http://localhost:8000/pilgrims
- **Forfaits** : http://localhost:8000/packages
- **Dashboard** : http://localhost:8000/dashboard

### Compte de Test
- **Email** : `admin@omra.test`
- **Mot de passe** : `password`
- **Rôle** : Super Admin Agence (toutes les permissions)

---

## 📋 Tests à Effectuer

### 1. Module Forfaits

#### Test 1.1 : Créer un Forfait
1. Aller sur : http://localhost:8000/packages
2. Cliquer sur **"+ Nouveau Forfait"**
3. Remplir le formulaire :
   - Nom : "Omra Premium Mars 2024"
   - Type : Premium
   - Places : 30
   - Prix : 15000
   - Coût : 12000
   - Date départ : (date future)
   - Date retour : (date après départ)
   - Nuits La Mecque : 5
   - Nuits Médine : 3
4. Cliquer sur **"Créer le forfait"**
5. ✅ Vérifier que le forfait apparaît dans la liste

#### Test 1.2 : Voir les Détails d'un Forfait
1. Cliquer sur **"Voir"** sur un forfait
2. ✅ Vérifier que les informations s'affichent correctement
3. ✅ Vérifier le calcul du profit (Prix - Coût)

#### Test 1.3 : Modifier un Forfait
1. Cliquer sur **"Modifier"** sur un forfait
2. Changer le prix ou le nombre de places
3. Enregistrer
4. ✅ Vérifier que les modifications sont sauvegardées

#### Test 1.4 : Cloner un Forfait
1. Sur la page de détails d'un forfait
2. Cliquer sur **"Cloner"**
3. ✅ Vérifier qu'un nouveau forfait est créé avec les mêmes informations
4. Modifier les dates et enregistrer

#### Test 1.5 : Filtrer les Forfaits
1. Utiliser les filtres (Type, Disponibilité)
2. ✅ Vérifier que la liste se filtre correctement

---

### 2. Module Pèlerins

#### Test 2.1 : Créer un Pèlerin
1. Aller sur : http://localhost:8000/pilgrims
2. Cliquer sur **"+ Nouveau Pèlerin"**
3. Remplir le formulaire :
   - Prénom : "Ahmed"
   - Nom : "Benali"
   - Email : "ahmed.benali@test.com"
   - Téléphone : "+212612345678"
   - Passeport : "AB123456"
   - Nationalité : "Maroc"
   - Forfait : Sélectionner un forfait disponible
   - Statut : Inscrit
4. (Optionnel) Uploader des documents :
   - Passeport (PDF ou image)
   - Photo (image)
   - Certificat médical (PDF)
5. Cliquer sur **"Créer le pèlerin"**
6. ✅ Vérifier que le pèlerin apparaît dans la liste

#### Test 2.2 : Voir les Détails d'un Pèlerin
1. Cliquer sur **"Voir"** sur un pèlerin
2. ✅ Vérifier que les informations s'affichent
3. ✅ Vérifier la **Timeline d'Activité** (doit montrer "Pèlerin créé")
4. ✅ Si des documents ont été uploadés, vérifier qu'ils sont visibles

#### Test 2.3 : Modifier un Pèlerin
1. Cliquer sur **"Modifier"** sur un pèlerin
2. Changer le statut (ex: "Dossier complet")
3. Enregistrer
4. ✅ Vérifier que les modifications sont sauvegardées
5. ✅ Vérifier que la timeline montre "Statut changé"

#### Test 2.4 : Filtrer les Pèlerins
1. Utiliser les filtres :
   - Recherche (nom, email, passeport)
   - Statut
   - Nationalité
2. ✅ Vérifier que la liste se filtre correctement

#### Test 2.5 : Assigner un Forfait
1. Modifier un pèlerin
2. Sélectionner un forfait dans le menu déroulant
3. Enregistrer
4. ✅ Vérifier que le forfait est assigné
5. ✅ Vérifier que la timeline montre "Forfait assigné"

---

## ✅ Checklist de Validation

### Fonctionnalités Backend
- [ ] Création de forfait fonctionne
- [ ] Création de pèlerin fonctionne
- [ ] Modification fonctionne
- [ ] Suppression fonctionne (soft delete)
- [ ] Filtres fonctionnent
- [ ] Upload de documents fonctionne
- [ ] Timeline d'activité s'enregistre automatiquement

### Fonctionnalités Frontend
- [ ] Liste s'affiche correctement
- [ ] Formulaires s'affichent correctement
- [ ] Messages de succès/erreur s'affichent
- [ ] Pagination fonctionne
- [ ] Design responsive

### Sécurité
- [ ] Les permissions sont respectées
- [ ] L'isolation par branche fonctionne
- [ ] Les validations fonctionnent

---

## 🐛 Problèmes Potentiels

### Si erreur 404
- Vérifier que les routes sont bien enregistrées : `php artisan route:list`

### Si erreur de base de données
- Vérifier que les migrations sont exécutées : `php artisan migrate:status`
- Réexécuter si nécessaire : `php artisan migrate`

### Si les documents ne s'uploadent pas
- Vérifier que le lien symbolique existe : `php artisan storage:link`
- Vérifier les permissions du dossier `storage/app/public`

### Si la timeline ne s'affiche pas
- Vérifier que l'Observer est enregistré dans `AppServiceProvider`
- Vérifier que les logs sont créés dans la table `activity_logs`

---

## 📝 Notes

- Les documents sont stockés dans `storage/app/public/pilgrims/{id}/documents`
- La timeline enregistre automatiquement : création, modification de statut, assignation de forfait
- Les forfaits calculent automatiquement le profit (Prix - Coût)
- Les places restantes sont calculées automatiquement (slots - pèlerins inscrits)

---

**Bon test ! 🎉**


