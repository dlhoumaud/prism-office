# PrismOffice

[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](https://opensource.org/licenses/MIT)
[![PHP Version](https://img.shields.io/badge/php-%3E%3D8.1-blue.svg)](https://www.php.net/)
[![Symfony Version](https://img.shields.io/badge/symfony-%5E5.4%5E6.0%7C%5E7.0-green.svg)](https://symfony.com/)
[![Tests](https://img.shields.io/badge/tests-109%20passed-brightgreen.svg)](https://github.com/dlhoumaud/prism-office)
[![Coverage](https://img.shields.io/badge/coverage-100%25-brightgreen.svg)](https://github.com/dlhoumaud/prism-office)
[![PHPStan](https://img.shields.io/badge/PHPStan-level%209-brightgreen.svg)](https://phpstan.org/)
[![PSR-12](https://img.shields.io/badge/PSR12-0%20error-brightgreen.svg)](https://phpstan.org/)

🇬🇧 English version: [../en/README.md](../en/README.md)

**Interface web pour gérer les scénarios Prism** - Un outil de développement comme le Symfony Profiler.

## 🎯 Qu'est-ce que c'est ?

PrismOffice est une **interface web autonome** pour gérer les scénarios [prism-bundle](https://github.com/dlhoumaud/prism-bundle). Elle fournit une interface moderne avec thème sombre pour :

- 📋 **Lister** tous les scénarios disponibles
- ✨ **Créer** de nouveaux scénarios YAML avec le constructeur visuel
- 🔄 **Éditer** le YAML directement et synchroniser avec le constructeur visuel (bidirectionnel)
- 🚀 **Charger** des scénarios avec des scopes personnalisés
- 🗑️ **Purger** les données de scénarios
- 👁️ **Voir** les scénarios chargés et leurs ressources
- 📊 **Surveiller** les scopes actifs et le nombre de ressources

## ✨ Fonctionnalités

✅ **Interface Autonome** - Fonctionne comme le Symfony Profiler (pas besoin de template de base)  
✅ **Constructeur de Scénarios Visuel** - Créez des scénarios YAML avec une interface interactive  
✅ **🔄 Édition Bidirectionnelle** - Éditez le YAML directement et synchronisez avec le constructeur visuel  
✅ **Thème Sombre** - Interface moderne et professionnelle  
✅ **Zéro Dépendances** - CSS/JS pur en ligne (pas de compilation)  
✅ **Mode Debug Uniquement** - Activé seulement en développement  
✅ **6 Routes** - Navigation simple et intuitive  

## 📦 Installation

### Option 1 : Installation via Path Repository (recommandé pour le développement local)

**Étape 1 : Copier la recette** (pour la configuration automatique)

```bash
cp -r PrismOffice/recipes/prism-office config/recipes/
```

**Étape 2 : Ajouter à votre `composer.json`**

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

**Étape 3 : Installer**

```bash
composer update prism/office:@dev
```

> ℹ️ Symfony Flex va automatiquement :
> - Ajouter `PrismOffice\PrismOfficeBundle::class` dans `config/bundles.php` (dev uniquement)
> - Créer `config/packages/dev/prism_office.yaml`
> - Créer `config/routes/dev/prism_office.yaml`

**Étape 4 : Vider le cache et tester**

```bash
php bin/console cache:clear
php bin/console debug:router | grep prism
```

---

### Option 2 : Installation Manuelle (sans Flex)

**Étape 1 : Activer le Bundle**

Ajouter à `config/bundles.php` :

```php
return [
    // ...
    PrismOffice\PrismOfficeBundle::class => ['dev' => true], // Uniquement en dev !
];
```

**Étape 2 : Configurer**

Créer `config/packages/dev/prism_office.yaml` :

```yaml
prism_office:
    enabled: '%kernel.debug%'
    route_prefix: '/prism'
```

**Étape 3 : Charger les Routes**

Créer `config/routes/dev/prism_office.yaml` :

```yaml
_prism_office:
    resource: '@PrismOfficeBundle/config/routes.yaml'
```

---

### Option 3 : Installation via Packagist (une fois publié)

```bash
composer require --dev prism/office
```

Symfony Flex configurera tout automatiquement.

## 🚀 Utilisation

### Accéder à l'Interface

Une fois installé, accédez à PrismOffice à :

```
http://localhost:8000/prism
```

### Routes Disponibles

| Route | Méthode | Description |
|-------|---------|-------------|
| `/prism` | GET | Liste tous les scénarios disponibles |
| `/prism/create` | GET/POST | Créer un nouveau scénario YAML avec le constructeur visuel |
| `/prism/load` | POST | Charger un scénario |
| `/prism/purge` | POST | Purger un scénario |
| `/prism/loaded` | GET | Voir les scénarios chargés |
| `/prism/{name}/{scope}/resources` | GET | Voir les ressources d'un scénario |

---

## 🎨 Constructeur de Scénarios Visuel

PrismOffice inclut un **constructeur visuel** pour créer des scénarios YAML sans écrire de code !

### Fonctionnalités

✅ **Interface Interactive** - Construisez des scénarios étape par étape  
✅ **Pas besoin de connaissances YAML** - Interface de formulaire guidée  
✅ **Prévisualisation en temps réel** - Voyez le YAML généré  
✅ **Toutes les fonctionnalités YAML supportées** :
- Imports
- Variables
- Instructions de chargement (avec support lookup)
- Configuration des types
- Pivot custom
- Instructions de purge

### Comment Utiliser

1. Allez sur `/prism` et cliquez sur **"✨ Créer un Nouveau Scénario"**
2. Remplissez le nom du scénario
3. **Ajouter des Imports** (optionnel) - Réutiliser des scénarios existants
4. **Ajouter des Variables** (optionnel) - Définir des valeurs réutilisables comme `admin: "admin_{{ scope }}"`
5. **Ajouter des Instructions de Chargement** (requis) :
   - Entrer le nom de la table
   - Ajouter des champs avec des valeurs simples ou des lookups (résolution FK)
   - Configurer les types (datetime_immutable, int, etc.)
   - Définir pivot custom si nécessaire
6. **Ajouter des Instructions de Purge** (optionnel) - Logique de nettoyage personnalisée
7. Cliquez sur **"🔄 Actualiser Prévisualisation"** pour voir le YAML généré
8. Cliquez sur **"💾 Sauvegarder Scénario"** pour créer le fichier dans `prism/`

### Exemple : Créer un Scénario d'Utilisateurs

```
Nom du Scénario : my_users

Variables :
  - admin = "admin_{{ scope }}"

Instructions de Chargement :
  Table : users
  Champs :
    - username : Valeur Simple = "{{ $admin }}"
    - email : Valeur Simple = "{{ $admin }}@test.com"
    - password : Valeur Simple = "{{ hash('secret') }}"
  Types :
    - created_at : datetime_immutable

[Sauvegarder] → Crée prism/my_users.yaml
```

### Placeholders Supportés

Le constructeur supporte tous les placeholders de PrismBundle :
- `{{ scope }}` - Scope actuel
- `{{ uuid }}` - Générer un UUID
- `{{ hash('pwd') }}` - Hasher un mot de passe
- `{{ now }}` - Timestamp actuel
- `{{ date('+7 days') }}` - Dates relatives
- `{{ env('VAR') }}` - Variables d'environnement
- `{{ math(10*2) }}` - Expressions mathématiques
- `{{ $variable }}` - Variables personnalisées
- `{{ fake(type) }}` - Génération de données factices

### Constructeur de Lookup

Créez des relations de clés étrangères visuellement :

```
Type de Champ : Lookup (FK)
Nom de Colonne : user_id
Table de Lookup : users
Base de Données de Lookup (Optionnel) : hexagonal_secondary
Colonne Where : username
Valeur Where : admin_{{ scope }}
Colonne de Retour : id
```

Génère :
```yaml
user_id:
  table: users
  db: hexagonal_secondary
  where:
    username: "admin_{{ scope }}"
  return: id
```

### Support Multi-Base de Données

PrismOffice supporte le travail avec plusieurs bases de données :

```yaml
load:
  - table: users
    data:
      username: "admin_{{ scope }}"
      
  - table: audit_logs
    db: hexagonal_secondary  # Cibler une base de données différente
    data:
      user_id: 1
      action: "user_created"
```

**Le champ database est optionnel** :
- Laisser vide pour la connexion de base de données par défaut
- Spécifier le nom de la base de données pour les bases secondaires
- Disponible dans les Instructions de chargement, Instructions de purge et champs Lookup

---

## 🗑️ Uninstallation

**Step 1: Remove from `config/bundles.php`**

Remove the line:
```php
PrismOffice\PrismOfficeBundle::class => ['dev' => true],
```

**Step 2: Remove configuration files**

```bash
rm config/packages/dev/prism_office.yaml
rm config/routes/dev/prism_office.yaml
```

**Step 3: Remove from `composer.json`**

Remove from `require-dev`:
```json
"prism/office": "@dev"
```

And from `repositories`:
```json
{
    "type": "path",
    "url": "./PrismOffice"
}
```

**Step 4: Remove the recipe** (if copied locally)

```bash
rm -rf config/recipes/prism-office
```

**Step 5: Uninstall via Composer**

```bash
composer remove prism/office --dev
php bin/console cache:clear
```

## 🎨 Interface Preview

```
┌─────────────────────────────────────────────┐
│  🔮 Prism Office                    [v1.0]  │
├─────────────────────────────────────────────┤
│  📋 Scenarios  |  🚀 Loaded                 │
├─────────────────────────────────────────────┤
│                                             │
│  Available Scenarios                        │
│  ┌─────────────────────────────────────┐  │
│  │ test_users      [Scope] [Load][Del] │  │
│  │ chat            [Scope] [Load][Del] │  │
│  └─────────────────────────────────────┘  │
│                                             │
└─────────────────────────────────────────────┘
```

## 🏗️ Architecture

PrismOffice follows **hexagonal architecture**:

```
PrismOffice/
├── src/
│   ├── Domain/           # Pure business entities
│   ├── Application/      # Use cases
│   └── Infrastructure/   # Adapters (Bridge, Doctrine, Symfony)
├── templates/            # Twig templates (standalone)
└── config/              # Services and routes
```

## 🔧 How It Works

1. **Bridge to PrismBundle** - Connects to PrismBundle's registry
2. **Read from Database** - Queries `prism_resource` table
3. **Standalone UI** - Self-contained templates with inline CSS/JS
4. **Debug Mode Only** - Disabled automatically in production

## 📊 Example Workflow

```bash
# 1. Open PrismOffice
http://localhost:8000/prism/list

# 2. Load a scenario with scope
Scenario: test_users
Scope: dev_john
[Click Load]

# 3. View loaded scenarios
http://localhost:8000/prism/loaded

# 4. View resources details
http://localhost:8000/prism/test_users/dev_john/resources

# 5. Purge when done
[Click Purge]
```

## 🎯 Development Tips

### Add to .gitignore

```gitignore
# PrismOffice is dev-only
/var/prism/
```

### Customize Theme

Edit `/templates/layout.html.twig` to change colors:

```css
/* Main colors */
--primary: #4a9eff;
--background: #1a1a1a;
--card-bg: #2d2d2d;
```

## 🔒 Sécurité

⚠️ **Important** : PrismOffice ne doit **JAMAIS** être activé en production.

- Chargé uniquement en environnement `dev`
- Protégé par la vérification `kernel.debug`
- Pas d'exposition de données sensibles (lit seulement les métadonnées)

## ✅ Tests et Qualité

Le bundle est fourni avec une **configuration de qualité complète** :

- **109 tests unitaires** avec **212 assertions**
- **100% de couverture** (Classes, Méthodes, Lignes)
- **PHPStan Niveau 9** : Analyse statique maximale
- **PHPCS PSR-12** : Zéro violation des normes de codage
- **Architecture Hexagonale** : Testable et maintenable

### 🚀 Vérification Complète de Qualité (Recommandé)

**Commande unique pour tout vérifier** (code source + tests) :

```bash
# Depuis le répertoire du bundle
vendor/bin/phpcs src tests --standard=phpcs.xml.dist && \
vendor/bin/phpstan analyse src -c phpstan.neon --level=9 --memory-limit=256M && \
vendor/bin/phpstan analyse tests -c phpstan.neon --level=9 --memory-limit=256M && \
vendor/bin/phpunit -c phpunit.xml.dist --no-coverage

# Depuis le projet racine avec Docker (⭐ RECOMMANDÉ)
docker compose exec php vendor/bin/phpcs PrismOffice/src PrismOffice/tests --standard=PrismOffice/phpcs.xml.dist && \
docker compose exec php vendor/bin/phpstan analyse PrismOffice/src -c PrismOffice/phpstan.neon --level=9 --memory-limit=256M && \
docker compose exec php vendor/bin/phpstan analyse PrismOffice/tests -c PrismOffice/phpstan.neon --level=9 --memory-limit=256M && \
docker compose exec php vendor/bin/phpunit -c PrismOffice/phpunit.xml.dist --no-coverage

# Générer le rapport HTML avec PCOV
docker compose exec php php -d pcov.directory=/var/www/html/PrismOffice vendor/bin/phpunit -c PrismOffice/phpunit.xml.dist --coverage-html PrismOffice/var/report

# Générer le rapport texte dans le terminal
docker compose exec php php -d pcov.directory=/var/www/html/PrismOffice vendor/bin/phpunit -c PrismOffice/phpunit.xml.dist --coverage-text
```

**Cette vérification valide :**
- ✅ **PHPCS** : Normes PSR-12 sur tous les fichiers (src + tests)
- ✅ **PHPStan src** : Analyse statique niveau 9 sur le code source
- ✅ **PHPStan tests** : Analyse statique niveau 9 sur les tests
- ✅ **PHPUnit** : Exécution de tous les tests avec couverture

**Résultat attendu :**
```
✅ PHPCS : 0 violation sur 57 fichiers
✅ PHPStan src : 0 erreur sur 33 fichiers
✅ PHPStan tests : 0 erreur sur 24 fichiers
✅ PHPUnit : 78/78 tests passent, 162 assertions, 100% de couverture
```

---

### 🧪 Tests Unitaires

Depuis le répertoire du bundle :

```bash
# Installation des dépendances de développement
cd PrismOffice
composer install

# Lancer tous les tests
vendor/bin/phpunit -c phpunit.xml.dist

# Lancer les tests sans coverage (plus rapide)
vendor/bin/phpunit -c phpunit.xml.dist --no-coverage

# Lancer un fichier de test spécifique
vendor/bin/phpunit -c phpunit.xml.dist tests/Application/ListScenariosServiceTest.php

# Lancer un test spécifique
vendor/bin/phpunit -c phpunit.xml.dist --filter testListScenariosReturnsAllScenarios
```

Depuis le répertoire racine du projet (avec Docker) :

```bash
# Lancer tous les tests
docker compose exec php vendor/bin/phpunit -c PrismOffice/phpunit.xml.dist --no-coverage

# Lancer un fichier de test spécifique
docker compose exec php vendor/bin/phpunit -c PrismOffice/phpunit.xml.dist PrismOffice/tests/Application/ListScenariosServiceTest.php
```

---

### 📋 Vérification du Style (PHPCS)

```bash
# Vérifier les violations PSR-12 sur src et tests
vendor/bin/phpcs src tests --standard=phpcs.xml.dist

# Depuis le projet racine
docker compose exec php vendor/bin/phpcs PrismOffice/src PrismOffice/tests --standard=PrismOffice/phpcs.xml.dist

# Corriger automatiquement les violations
vendor/bin/phpcbf src tests --standard=phpcs.xml.dist

# Depuis le projet racine
docker compose exec php vendor/bin/phpcbf PrismOffice/src PrismOffice/tests --standard=PrismOffice/phpcs.xml.dist

# Rapport détaillé avec résumé
vendor/bin/phpcs src tests --standard=phpcs.xml.dist --report=summary
```

---

### 🔍 Analyse Statique (PHPStan)

```bash
# Analyser le code source (niveau max)
vendor/bin/phpstan analyse src -c phpstan.neon --level=9 --memory-limit=256M

# Analyser les tests (niveau max)
vendor/bin/phpstan analyse tests -c phpstan.neon --level=9 --memory-limit=256M

# Depuis le projet racine - analyser src
docker compose exec php vendor/bin/phpstan analyse PrismOffice/src -c PrismOffice/phpstan.neon --level=9 --memory-limit=256M

# Depuis le projet racine - analyser tests
docker compose exec php vendor/bin/phpstan analyse PrismOffice/tests -c PrismOffice/phpstan.neon --level=9 --memory-limit=256M
```

## 📄 Licence

Licence MIT - voir le fichier [LICENSE](LICENSE)

## 👤 Auteur

**David Lhoumaud**
- Email : dlhoumaud@gmail.com

## 🔗 Voir Aussi

- [prism-bundle](https://github.com/dlhoumaud/prism-bundle) - Bundle principal de gestion de scénarios
