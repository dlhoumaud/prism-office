# PrismOffice - Quick Reference

## 🎯 Overview

PrismOffice is a **standalone web interface** for managing PrismBundle scenarios. Think of it as the "Symfony Profiler" for Prism scenarios.

**NEW:** Visual Scenario Builder - Create YAML scenarios without writing code!

---

## 📦 Quick Installation

```bash
# 1. Copy recipe
cp -r PrismOffice/recipes/prism-office config/recipes/

# 2. Add to composer.json
{
    "repositories": [{"type": "path", "url": "./PrismOffice"}],
    "require-dev": {"prism/office": "@dev"}
}

# 3. Install
composer update prism/office

# 4. Access
http://localhost:8000/prism/list
```

---

## 🚀 Routes

| Route | Method | Description |
|-------|--------|-------------|
| `/prism/list` | GET | List scenarios |
| `/prism/create` | GET/POST | **NEW** Create scenario with visual builder |
| `/prism/load` | POST | Load scenario |
| `/prism/purge` | POST | Purge scenario |
| `/prism/loaded` | GET | View loaded |
| `/prism/{name}/{scope}/resources` | GET | View resources |

---

## 🎨 Features

- ✅ Dark theme interface
- ✅ **Visual Scenario Builder** - Create YAML with UI
- ✅ Scope-based isolation  
- ✅ One-click load/purge
- ✅ Real-time resource tracking
- ✅ Statistics dashboard
- ✅ Dev mode only (safe)

---

## 📖 Documentation

- [README.md](README.md) - Complete documentation
- [SCENARIO_BUILDER.md](SCENARIO_BUILDER.md) - **NEW** Visual builder guide
- [INSTALLATION.md](INSTALLATION.md) - Detailed installation guide
- [CHANGELOG.md](CHANGELOG.md) - Version history

---

## 🔧 Configuration

```yaml
# config/packages/dev/prism_office.yaml
prism_office:
    enabled: '%kernel.debug%'
    route_prefix: '/prism'
```

---

## 🗑️ Uninstall

```bash
composer remove prism/office --dev
rm config/packages/dev/prism_office.yaml
rm config/routes/dev/prism_office.yaml
php bin/console cache:clear
```

---

## 💡 Usage Example
**Classic workflow:**
1. Go to `http://localhost:8000/prism/list`
2. Enter scope: `dev_john`
3. Click "Load" on `test_users`
4. View in "Loaded" tab
5. Click "View" to see resources
6. Click "Purge" when done

**NEW - Create scenario workflow:**
1. Click "✨ Create New Scenario"
2. Fill scenario name: `my_scenario`
3. Add variables, load instructions, etc.
4. Click "🔄 Refresh Preview" to see YAML
5. ClicCreate (NEW!) → k "💾 Save Scenario"
6. Scenario created in `prism/my_scenario.yaml`ources
6. Click "Purge" when done

---

## 🎯 Workflow

```
List → Load (with scope) → View Loaded → View Resources → Purge
```

---

## 📊 Screenshots

### List Page
- All available scenarios
- Scope input field
- Load/Purge buttons

### Loaded Page
- Active scenarios
- Resource counts
- Statistics

### Resources Page
- Detailed resource list
- Table, column, row ID
- Creation timestamps

---

## 🔒 Security

- ⚠️ **Dev mode only** - Disabled in production
- ⚠️ Protected by `kernel.debug`
- ⚠️ No sensitive data exposure

---

## 🆘 Troubleshooting

### 404 Not Found
```bash
php bin/console cache:clear
php bin/console debug:router | grep prism
```

### Bundle Not Loaded
Check `config/bundles.php`:
```php
PrismOffice\PrismOfficeBundle::class => ['dev' => true],
```

### Styles Missing
Inline CSS should work automatically. Check browser console.

---

## 📞 Support

- **Issues**: GitHub Issues
- **Email**: dlhoumaud@gmail.com
- **Docs**: PrismOffice/README.md

---

**License**: MIT  
**Author**: David Lhoumaud  
**Version**: 1.0.0

🚀 Happy scenario management!
