# Guide d'Installation - PrismOffice

## 🎯 Prérequis

Avant d'installer PrismOffice, assurez-vous d'avoir :

- ✅ PHP 8.1 ou supérieur
- ✅ Symfony 6.0+ ou 7.0+
- ✅ **PrismBundle** déjà installé et configuré
- ✅ Doctrine DBAL configuré

---

## 📦 Méthodes d'Installation

### Méthode 1 : Développement Local (Recommandé)

Parfait pour développer et tester localement.

#### Étape 1 : Copier la Recette

```bash
cp -r PrismOffice/recipes/prism-office config/recipes/
```

Cela active la configuration automatique de Symfony Flex.

#### Étape 2 : Ajouter au composer.json

```json
{
    "repositories": [
        {
            "type": "path",
            "url": "./PrismOffice"
        }
    ],
    "require-dev": {
        "prism/office": "@dev"
    }
}
```

#### Étape 3 : Installer

```bash
composer update prism/office
```

**Ce qui se passe automatiquement :**
- ✅ Bundle enregistré dans `config/bundles.php` (dev uniquement)
- ✅ Configuration créée dans `config/packages/dev/prism_office.yaml`
- ✅ Routes chargées dans `config/routes/dev/prism_office.yaml`
- ✅ Message post-installation affiché

#### Étape 4 : Vider le Cache

```bash
php bin/console cache:clear
```

#### Étape 5 : Vérifier l'Installation

```bash
# Vérifier les routes
php bin/console debug:router | grep prism

# Vous devriez voir :
# prism_office_list       GET    /prism/list
# prism_office_load       POST   /prism/load
# prism_office_purge      POST   /prism/purge
# prism_office_loaded     GET    /prism/loaded
# prism_office_resources  GET    /prism/{name}/{scope}/resources
```

#### Étape 6 : Accéder à l'Interface

Ouvrez votre navigateur :
```
http://localhost:8000/prism/list
```

---

### Méthode 2 : Installation Manuelle

Si vous n'utilisez pas Symfony Flex ou préférez la configuration manuelle.

#### Étape 1 : Activer le Bundle

Éditez `config/bundles.php` :

```php
<?php

return [
    // ... autres bundles
    PrismOffice\PrismOfficeBundle::class => ['dev' => true],
];
```

> ⚠️ **Important** : N'activez qu'en environnement `dev` !

#### Étape 2 : Créer la Configuration

Créez `config/packages/dev/prism_office.yaml` :

```yaml
prism_office:
    enabled: '%kernel.debug%'
    route_prefix: '/prism'
```

#### Étape 3 : Charger les Routes

Créez `config/routes/dev/prism_office.yaml` :

```yaml
_prism_office:
    resource: '@PrismOfficeBundle/config/routes.yaml'
```

#### Étape 4 : Vider le Cache

```bash
php bin/console cache:clear
```

---

### Méthode 3 : Via Packagist (Futur)

Une fois publié sur Packagist :

```bash
composer require --dev prism/office
```

Symfony Flex configurera tout automatiquement.

---

## ✅ Liste de Vérification

Après l'installation, vérifiez que tout fonctionne :

- [ ] Le bundle apparaît dans `config/bundles.php` (dev uniquement)
- [ ] Le fichier de configuration existe dans `config/packages/dev/`
- [ ] Le fichier de routes existe dans `config/routes/dev/`
- [ ] Les routes sont enregistrées : `php bin/console debug:router | grep prism`
- [ ] L'interface est accessible à `/prism/list`
- [ ] Pas d'erreurs dans les logs : `tail -f var/log/dev.log`

---

## 🔧 Options de Configuration

Configuration par défaut (`config/packages/dev/prism_office.yaml`) :

```yaml
prism_office:
    # Activer/désactiver PrismOffice (défaut: %kernel.debug%)
    enabled: '%kernel.debug%'
    
    # Préfixe de route pour toutes les routes PrismOffice (défaut: /prism)
    route_prefix: '/prism'
```

### Préfixe de Route Personnalisé

Si `/prism` entre en conflit avec votre application :

```yaml
prism_office:
    route_prefix: '/admin/prism'  # Préfixe personnalisé
```

Les routes deviendront :
- `/admin/prism/list`
- `/admin/prism/load`
- etc.

---

## 🐛 Dépannage

### Routes non trouvées (404)

```bash
# Vider le cache
php bin/console cache:clear

# Vérifier que les routes sont chargées
php bin/console debug:router | grep prism
```

### Bundle non trouvé

Vérifiez `config/bundles.php` :
```php
PrismOffice\PrismOfficeBundle::class => ['dev' => true],
```

Assurez-vous d'être en **environnement dev** :
```bash
# Vérifier l'environnement actuel
echo $APP_ENV  # Devrait être 'dev'

# Ou forcer le mode dev
APP_ENV=dev php bin/console cache:clear
```

### CSS/JS ne se chargent pas

PrismOffice utilise **CSS/JS inline**, aucun fichier externe nécessaire.
Si les styles sont manquants, vérifiez la console du navigateur pour les erreurs.

### "PrismBundle non trouvé"

PrismOffice **nécessite PrismBundle** installé en premier :

```bash
# Vérifier si PrismBundle est installé
composer show prism/bundle
```

Si non installé, installez d'abord PrismBundle :
```bash
composer require prism/bundle:@dev
```

---

## 🔄 Mise à Jour

### Mettre à Jour vers la Dernière Version

```bash
# Mettre à jour les dépendances composer
composer update prism/office

# Vider le cache
php bin/console cache:clear
```

### Vérifier la Version Installée

```bash
composer show prism/office
```

---

## 🗑️ Désinstallation

### Étape 1 : Retirer le Bundle

Éditez `config/bundles.php` et supprimez :
```php
PrismOffice\PrismOfficeBundle::class => ['dev' => true],
```

### Étape 2 : Supprimer la Configuration

```bash
rm config/packages/dev/prism_office.yaml
rm config/routes/dev/prism_office.yaml
```

### Étape 3 : Supprimer la Recette (si copiée)

```bash
rm -rf config/recipes/prism-office
```

### Étape 4 : Désinstaller via Composer

```bash
composer remove prism/office --dev
```

### Étape 5 : Vider le Cache

```bash
php bin/console cache:clear
```

---

## 📚 Prochaines Étapes

Après une installation réussie :

1. **Accédez à l'interface** : `http://localhost:8000/prism/list`
2. **Lisez la documentation principale** : [README.md](README.md)
3. **Vérifiez les scénarios disponibles** : Cliquez sur l'onglet "Scénarios"
4. **Chargez votre premier scénario** : Entrez un scope et cliquez sur "Charger"

---

## 💡 Conseils

### Flux de Travail de Développement

```bash
# 1. Charger un scénario pour le développement
http://localhost:8000/prism/list
# Entrez le scope : dev_votrenom
# Cliquez sur "Charger" sur votre scénario

# 2. Développez votre fonctionnalité
# ... code ...

# 3. Voir les ressources chargées
http://localhost:8000/prism/loaded

# 4. Vérifier les détails des ressources
# Cliquez sur "Voir" sur votre scénario

# 5. Purger quand terminé
# Cliquez sur "Purger" sur le scénario chargé
```

### Plusieurs Développeurs

Chaque développeur utilise son propre scope :

- Alice : `dev_alice`
- Bob : `dev_bob`
- QA : `qa_team`

Zéro collision, isolation complète ! 🎯

---

## 🆘 Support

- **Documentation** : [README.md](README.md)
- **Issues** : Créez une issue sur GitHub
- **Email** : dlhoumaud@gmail.com

---

Bonne gestion de scénarios ! 🚀
