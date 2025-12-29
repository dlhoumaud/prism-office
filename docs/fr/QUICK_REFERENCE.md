# PrismOffice - Référence Rapide

## 🎯 Vue d'ensemble

PrismOffice est une **interface web autonome** pour gérer les scénarios PrismBundle. Pensez-y comme le "Symfony Profiler" pour les scénarios Prism.

**NOUVEAU :** Constructeur Visuel de Scénarios - Créez des scénarios YAML sans écrire de code !

---

## 📦 Installation Rapide

```bash
# 1. Copier la recette
cp -r PrismOffice/recipes/prism-office config/recipes/

# 2. Ajouter dans composer.json
{
    "repositories": [{"type": "path", "url": "./PrismOffice"}],
    "require-dev": {"prism/office": "@dev"}
}

# 3. Installer
composer update prism/office

# 4. Accéder
http://localhost:8000/prism/list
```

---

## 🚀 Routes

| Route | Méthode | Description |
|-------|---------|-------------|
| `/prism/list` | GET | Lister les scénarios |
| `/prism/create` | GET/POST | **NOUVEAU** Créer un scénario avec le constructeur visuel |
| `/prism/load` | POST | Charger un scénario |
| `/prism/purge` | POST | Purger un scénario |
| `/prism/loaded` | GET | Voir les scénarios chargés |
| `/prism/{name}/{scope}/resources` | GET | Voir les ressources |

---

## 🎨 Fonctionnalités

- ✅ Interface à thème sombre
- ✅ **Constructeur Visuel de Scénarios** - Créer du YAML avec l'UI
- ✅ Isolation basée sur les scopes  
- ✅ Chargement/purge en un clic
- ✅ Suivi des ressources en temps réel
- ✅ Tableau de bord des statistiques
- ✅ Mode dev uniquement (sécurisé)

---

## 📖 Documentation

- [README.md](README.md) - Documentation complète
- [SCENARIO_BUILDER.md](SCENARIO_BUILDER.md) - **NOUVEAU** Guide du constructeur visuel
- [INSTALLATION.md](INSTALLATION.md) - Guide d'installation détaillé
- [BIDIRECTIONAL_EDITING.md](BIDIRECTIONAL_EDITING.md) - Édition bidirectionnelle YAML

---

## 🔧 Configuration

```yaml
# config/packages/dev/prism_office.yaml
prism_office:
    enabled: '%kernel.debug%'
    route_prefix: '/prism'
```

---

## 🗑️ Désinstallation

```bash
composer remove prism/office --dev
rm config/packages/dev/prism_office.yaml
rm config/routes/dev/prism_office.yaml
php bin/console cache:clear
```

---

## 💡 Exemple d'utilisation

**Workflow classique :**
1. Aller sur `http://localhost:8000/prism/list`
2. Entrer un scope : `dev_john`
3. Cliquer "Charger" sur `test_users`
4. Voir dans l'onglet "Chargés"
5. Cliquer "Voir" pour afficher les ressources
6. Cliquer "Purger" quand terminé

**NOUVEAU - Workflow de création de scénario :**
1. Cliquer "✨ Créer un nouveau scénario"
2. Remplir le nom du scénario : `my_scenario`
3. Ajouter des variables, instructions de chargement, etc.
4. Cliquer "🔄 Actualiser l'aperçu" pour voir le YAML
5. Cliquer "💾 Sauvegarder le scénario"
6. Scénario créé dans `prism/my_scenario.yaml`

---

## 🎯 Workflow

```
Liste → Charger (avec scope) → Voir Chargés → Voir Ressources → Purger
```

---

## 📊 Captures d'écran

### Page Liste
- Tous les scénarios disponibles
- Champ de saisie du scope
- Boutons Charger/Purger

### Page Chargés
- Scénarios actifs
- Compteurs de ressources
- Statistiques

### Page Ressources
- Liste détaillée des ressources
- Table, colonne, ID de ligne
- Horodatages de création

---

## 🔒 Sécurité

- ⚠️ **Mode dev uniquement** - Désactivé en production
- ⚠️ Protégé par `kernel.debug`
- ⚠️ Aucune exposition de données sensibles

---

## 🆘 Dépannage

### 404 Non Trouvé
```bash
php bin/console cache:clear
php bin/console debug:router | grep prism
```

### Bundle Non Chargé
Vérifier `config/bundles.php` :
```php
PrismOffice\PrismOfficeBundle::class => ['dev' => true],
```

### Styles Manquants
Le CSS inline devrait fonctionner automatiquement. Vérifier la console du navigateur.

---

## 📞 Support

- **Issues** : GitHub Issues
- **Email** : dlhoumaud@gmail.com
- **Docs** : PrismOffice/README.md

---

**Licence** : MIT  
**Auteur** : David Lhoumaud  
**Version** : 1.0.0

🚀 Bonne gestion de scénarios !
