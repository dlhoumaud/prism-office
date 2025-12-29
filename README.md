# PrismOffice

[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](https://opensource.org/licenses/MIT)
[![PHP Version](https://img.shields.io/badge/php-%3E%3D8.1-blue.svg)](https://www.php.net/)
[![Symfony Version](https://img.shields.io/badge/symfony-%5E6.0%7C%5E7.0-green.svg)](https://symfony.com/)
[![Tests](https://img.shields.io/badge/tests-109%20passed-brightgreen.svg)](https://github.com/YOUR-USERNAME/prism-office/actions)
[![Coverage](https://img.shields.io/badge/coverage-100%25-brightgreen.svg)](https://github.com/YOUR-USERNAME/prism-office)
[![PHPStan](https://img.shields.io/badge/PHPStan-level%209-brightgreen.svg)](https://phpstan.org/)
[![PSR-12](https://img.shields.io/badge/PSR12-0%20error-brightgreen.svg)](https://phpstan.org/)

> 🇫🇷 **Version française** : [README.md](docs/fr/README.md)

**Web interface for managing Prism scenarios** - A development tool like Symfony Profiler.

## 🎯 What is it?

PrismOffice is a **standalone web interface** for managing [prism-bundle](https://github.com/dlhoumaud/prism-bundle) scenarios. It provides a clean, dark-themed UI to:

- 📋 **List** all available scenarios
- ✨ **Create** new YAML scenarios with visual builder
- ✏️ **Edit** YAML directly and sync to visual builder (bidirectional)
- 🚀 **Load** scenarios with custom scopes
- 🗑️ **Purge** scenario data
- 👁️ **View** loaded scenarios and their resources
- 📊 **Monitor** active scopes and resource counts

## ✨ Features

✅ **Standalone Interface** - Works like Symfony Profiler (no base template needed)  
✅ **Visual Scenario Builder** - Create YAML scenarios with interactive UI  
✅ **🔄 Bidirectional Editing** - Edit YAML directly and sync back to visual builder  
✅ **Dark Theme** - Modern, professional interface  
✅ **Zero Dependencies** - Pure CSS/JS inline (no compilation)  
✅ **Debug Mode Only** - Enabled only in development  
✅ **6 Routes** - Simple, intuitive navigation  

## 📦 Installation

### Option 1: Installation via Path Repository (recommended for local development)

**Step 1: Copy the recipe** (for auto-configuration)

```bash
cp -r PrismOffice/recipes/prism-office config/recipes/
```

**Step 2: Add to your `composer.json`**

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

**Step 3: Install**

```bash
composer update prism/office
```

> ℹ️ Symfony Flex will automatically:
> - Add `PrismOffice\PrismOfficeBundle::class` in `config/bundles.php` (dev only)
> - Create `config/packages/dev/prism_office.yaml`
> - Create `config/routes/dev/prism_office.yaml`

**Step 4: Clear cache and test**

```bash
php bin/console cache:clear
php bin/console debug:router | grep prism
```

---

### Option 2: Manual Installation (without Flex)

**Step 1: Enable the Bundle**

Add to `config/bundles.php`:

```php
return [
    // ...
    PrismOffice\PrismOfficeBundle::class => ['dev' => true], // Only in dev!
];
```

**Step 2: Configure**

Create `config/packages/dev/prism_office.yaml`:

```yaml
prism_office:
    enabled: '%kernel.debug%'
    route_prefix: '/prism'
```

**Step 3: Load Routes**

Create `config/routes/dev/prism_office.yaml`:

```yaml
_prism_office:
    resource: '@PrismOfficeBundle/config/routes.yaml'
```

---

### Option 3: Installation via Packagist (once published)

```bash
composer require --dev prism/office
```

Symfony Flex will auto-configure everything.

## 🚀 Usage

### Access the Interface

Once installed, access PrismOffice at:

```
http://localhost:8000/prism
```

### Routes Available

| Route | Method | Description |
|-------|--------|-------------|
| `/prism` | GET | List all available scenarios |
| `/prism/create` | GET/POST | Create a new YAML scenario with visual builder |
| `/prism/load` | POST | Load a scenario |
| `/prism/purge` | POST | Purge a scenario |
| `/prism/loaded` | GET | View loaded scenarios |
| `/prism/{name}/{scope}/resources` | GET | View scenario resources |

---

## 🎨 Visual Scenario Builder

PrismOffice includes a **visual builder** to create YAML scenarios without writing code!

### Features

✅ **Interactive UI** - Build scenarios step-by-step  
✅ **No YAML knowledge required** - Guided form interface  
✅ **Real-time preview** - See the generated YAML  
✅ **All YAML features supported**:
- Imports
- Variables
- Load instructions (with lookup support)
- Types configuration
- Pivot custom
- Purge instructions

### Example Workflow

1. Go to `/prism` and click **"✨ Create New Scenario"**
2. Fill in the scenario name
3. Add imports, variables, load instructions, and purge rules
4. Click **"🔄 Refresh Preview"** to see the generated YAML
5. Click **"💾 Save Scenario"** to create the file

For detailed documentation, see:
- [Bidirectional Editing](docs/fr/BIDIRECTIONAL_EDITING.md) (French)

## 🔒 Security

⚠️ **Important**: PrismOffice should **NEVER** be enabled in production.

- Only loaded in `dev` environment
- Protected by `kernel.debug` check
- No sensitive data exposure (reads only metadata)

## ✅ Tests and Quality

The bundle comes with a **complete quality configuration**:

- **109 unit tests** with **212 assertions**
- **100% coverage** (Classes, Methods, Lines)
- **PHPStan Level 9**: Maximum static analysis
- **PHPCS PSR-12**: Zero coding standard violations
- **Hexagonal Architecture**: Testable and maintainable

### 🚀 Complete Quality Check (Recommended)

**Single command to verify everything** (source code + tests):

```bash
# From the bundle directory
vendor/bin/phpcs src tests --standard=phpcs.xml.dist && \
vendor/bin/phpstan analyse src -c phpstan.neon --level=9 --memory-limit=256M && \
vendor/bin/phpstan analyse tests -c phpstan.neon --level=9 --memory-limit=256M && \
vendor/bin/phpunit -c phpunit.xml.dist --no-coverage

# From the root project with Docker (⭐ RECOMMENDED)
docker compose exec php vendor/bin/phpcs PrismOffice/src PrismOffice/tests --standard=PrismOffice/phpcs.xml.dist && \
docker compose exec php vendor/bin/phpstan analyse PrismOffice/src -c PrismOffice/phpstan.neon --level=9 --memory-limit=256M && \
docker compose exec php vendor/bin/phpstan analyse PrismOffice/tests -c PrismOffice/phpstan.neon --level=9 --memory-limit=256M && \
docker compose exec php vendor/bin/phpunit -c PrismOffice/phpunit.xml.dist --no-coverage

# Generate HTML report with PCOV
docker compose exec php php -d pcov.directory=/var/www/html/PrismOffice vendor/bin/phpunit -c PrismOffice/phpunit.xml.dist --coverage-html PrismOffice/var/report

# Generate text report in terminal
docker compose exec php php -d pcov.directory=/var/www/html/PrismOffice vendor/bin/phpunit -c PrismOffice/phpunit.xml.dist --coverage-text
```

**Expected result:**
```
✅ PHPCS: 0 violation on 57 files
✅ PHPStan src: 0 error on 33 files
✅ PHPStan tests: 0 error on 24 files
✅ PHPUnit: 78/78 tests passing, 162 assertions, 100% coverage
```

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

**Step 4: Uninstall via Composer**

```bash
composer remove prism/office --dev
php bin/console cache:clear
```

## 📚 Documentation

- [French Documentation](docs/fr/README.md)
- [Bidirectional Editing](docs/fr/BIDIRECTIONAL_EDITING.md)

## 📄 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## 🤝 Contributing

Contributions are welcome! Please see [CONTRIBUTING.md](CONTRIBUTING.md) for details.

## 🔗 Related Projects

- [PrismBundle](../PrismBundle) - The core scenario management bundle
