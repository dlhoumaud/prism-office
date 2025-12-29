# Installation Guide - PrismOffice

## 🎯 Prerequisites

Before installing PrismOffice, make sure you have:

- ✅ PHP 8.1 or higher
- ✅ Symfony 6.0+ or 7.0+
- ✅ **PrismBundle** already installed and configured
- ✅ Doctrine DBAL configured

---

## 📦 Installation Methods

### Method 1: Local Development (Recommended)

Perfect for developing and testing locally.

#### Step 1: Copy the Recipe

```bash
cp -r PrismOffice/recipes/prism-office config/recipes/
```

This enables Symfony Flex auto-configuration.

#### Step 2: Add to composer.json

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

#### Step 3: Install

```bash
composer update prism/office
```

**What happens automatically:**
- ✅ Bundle registered in `config/bundles.php` (dev only)
- ✅ Configuration created in `config/packages/dev/prism_office.yaml`
- ✅ Routes loaded in `config/routes/dev/prism_office.yaml`
- ✅ Post-install message displayed

#### Step 4: Clear Cache

```bash
php bin/console cache:clear
```

#### Step 5: Verify Installation

```bash
# Check routes
php bin/console debug:router | grep prism

# You should see:
# prism_office_list       GET    /prism/list
# prism_office_load       POST   /prism/load
# prism_office_purge      POST   /prism/purge
# prism_office_loaded     GET    /prism/loaded
# prism_office_resources  GET    /prism/{name}/{scope}/resources
```

#### Step 6: Access the Interface

Open your browser:
```
http://localhost:8000/prism/list
```

---

### Method 2: Manual Installation

If you don't use Symfony Flex or prefer manual setup.

#### Step 1: Enable the Bundle

Edit `config/bundles.php`:

```php
<?php

return [
    // ... other bundles
    PrismOffice\PrismOfficeBundle::class => ['dev' => true],
];
```

> ⚠️ **Important**: Only enable in `dev` environment!

#### Step 2: Create Configuration

Create `config/packages/dev/prism_office.yaml`:

```yaml
prism_office:
    enabled: '%kernel.debug%'
    route_prefix: '/prism'
```

#### Step 3: Load Routes

Create `config/routes/dev/prism_office.yaml`:

```yaml
_prism_office:
    resource: '@PrismOfficeBundle/config/routes.yaml'
```

#### Step 4: Clear Cache

```bash
php bin/console cache:clear
```

---

### Method 3: Via Packagist (Future)

Once published on Packagist:

```bash
composer require --dev prism/office
```

Symfony Flex will auto-configure everything.

---

## ✅ Verification Checklist

After installation, verify everything is working:

- [ ] Bundle appears in `config/bundles.php` (dev only)
- [ ] Configuration file exists in `config/packages/dev/`
- [ ] Routes file exists in `config/routes/dev/`
- [ ] Routes are registered: `php bin/console debug:router | grep prism`
- [ ] Interface is accessible at `/prism/list`
- [ ] No errors in logs: `tail -f var/log/dev.log`

---

## 🔧 Configuration Options

Default configuration (`config/packages/dev/prism_office.yaml`):

```yaml
prism_office:
    # Enable/disable PrismOffice (default: %kernel.debug%)
    enabled: '%kernel.debug%'
    
    # Route prefix for all PrismOffice routes (default: /prism)
    route_prefix: '/prism'
```

### Custom Route Prefix

If `/prism` conflicts with your application:

```yaml
prism_office:
    route_prefix: '/admin/prism'  # Custom prefix
```

Routes will become:
- `/admin/prism/list`
- `/admin/prism/load`
- etc.

---

## 🐛 Troubleshooting

### Routes not found (404)

```bash
# Clear cache
php bin/console cache:clear

# Verify routes are loaded
php bin/console debug:router | grep prism
```

### Bundle not found

Check `config/bundles.php`:
```php
PrismOffice\PrismOfficeBundle::class => ['dev' => true],
```

Make sure you're in **dev environment**:
```bash
# Check current environment
echo $APP_ENV  # Should be 'dev'

# Or force dev mode
APP_ENV=dev php bin/console cache:clear
```

### CSS/JS not loading

PrismOffice uses **inline CSS/JS**, no external files needed.
If styles are missing, check browser console for errors.

### "PrismBundle not found"

PrismOffice **requires PrismBundle** to be installed first:

```bash
# Check if PrismBundle is installed
composer show prism/bundle
```

If not installed, install PrismBundle first:
```bash
composer require prism/bundle:@dev
```

---

## 🔄 Updating

### Update to Latest Version

```bash
# Update composer dependencies
composer update prism/office

# Clear cache
php bin/console cache:clear
```

### Check Installed Version

```bash
composer show prism/office
```

---

## 🗑️ Uninstallation

### Step 1: Remove Bundle

Edit `config/bundles.php` and remove:
```php
PrismOffice\PrismOfficeBundle::class => ['dev' => true],
```

### Step 2: Remove Configuration

```bash
rm config/packages/dev/prism_office.yaml
rm config/routes/dev/prism_office.yaml
```

### Step 3: Remove Recipe (if copied)

```bash
rm -rf config/recipes/prism-office
```

### Step 4: Uninstall via Composer

```bash
composer remove prism/office --dev
```

### Step 5: Clear Cache

```bash
php bin/console cache:clear
```

---

## 📚 Next Steps

After successful installation:

1. **Access the interface**: `http://localhost:8000/prism/list`
2. **Read the main documentation**: [README.md](README.md)
3. **Check available scenarios**: Click on "Scenarios" tab
4. **Load your first scenario**: Enter a scope and click "Load"

---

## 💡 Tips

### Development Workflow

```bash
# 1. Load scenario for development
http://localhost:8000/prism/list
# Enter scope: dev_yourname
# Click "Load" on your scenario

# 2. Develop your feature
# ... code ...

# 3. View loaded resources
http://localhost:8000/prism/loaded

# 4. Check resource details
# Click "View" on your scenario

# 5. Purge when done
# Click "Purge" on the loaded scenario
```

### Multiple Developers

Each developer uses their own scope:

- Alice: `dev_alice`
- Bob: `dev_bob`
- QA: `qa_team`

Zero collision, complete isolation! 🎯

---

## 🆘 Support

- **Documentation**: [README.md](README.md)
- **Issues**: Create an issue on GitHub
- **Email**: dlhoumaud@gmail.com

---

Happy scenario management! 🚀
