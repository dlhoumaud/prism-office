# Changelog

All notable changes to PrismOffice will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

### Added
- ✅ **Multi-Database Support in Scenario Builder**
  - Database field in Load instructions (optional)
  - Database field in Lookup fields (optional)
  - Database field in Purge instructions (optional)
  - Visual builder generates YAML with `db:` parameter
  - Supports both direct database names and Doctrine `%connection%` syntax
  - Full synchronization in bidirectional YAML editing
  - Documentation updated in SCENARIO_BUILDER.md and README.md
- ✅ **Scope Management Enhancement**
  - `data-keep-scope` attribute to preserve scenario-specific scopes
  - Global scope (header) for new load/purge operations
  - Specific scope preservation in `/prism/loaded` page
  - Specific scope preservation in `/prism/resources` page
  - JavaScript smart detection to prevent scope override
  - Each loaded scenario maintains its original scope for purge/reload
- 🔄 **Bidirectional YAML Editing** - Edit YAML directly and sync back to visual builder
  - YAML editor textarea (replaces read-only preview)
  - "Builder → YAML" button to generate YAML from form
  - "YAML → Builder" button to parse YAML and update form
  - Custom YAML parser (no external dependencies)
  - Support for all YAML features: imports, variables, load, purge, lookups, types, pivot
  - Confirmation dialog to prevent accidental overwrites
  - Updated floating button icon: 📝 YAML (was 👁️ Preview)
- 🎨 **Visual Scenario Builder** - Create YAML scenarios with interactive UI
- ✨ New route `/prism/create` for scenario creation
- 📝 ScenarioDefinition, LoadInstruction, PurgeInstruction domain entities
- 💾 SaveScenarioService to write scenarios to prism/ directory
- 🏗️ Support for all YAML features in visual builder:
  - Imports selection
  - Variables definition
  - Load instructions with field builder
  - Simple values and lookup (FK) resolution
  - Types configuration (datetime_immutable, int, float, etc.)
  - Pivot custom support
  - Purge instructions
- 👁️ Real-time YAML preview in builder
- 🎯 Guided form interface - no YAML knowledge required
- 📦 "Create New Scenario" button in main interface

## [1.0.0] - 2025-12-21

### Added
- 🎉 Initial release of PrismOffice
- ✅ Standalone web interface for managing Prism scenarios
- ✅ Dark theme UI with inline CSS/JS (no compilation)
- ✅ 5 core routes: list, load, purge, loaded, resources
- ✅ Bridge to PrismBundle registry
- ✅ Hexagonal architecture (Domain/Application/Infrastructure)
- ✅ Debug mode only activation
- ✅ Flash messages for user feedback
- ✅ Statistics dashboard for loaded scenarios
- ✅ Resource details view with full tracking info
- ✅ Responsive design for mobile/tablet
- ✅ Auto-focus on scope input fields
- ✅ Confirmation dialogs for destructive actions
- ✅ **Symfony Flex recipe for auto-configuration**
- ✅ **Post-install instructions**

### Features
- **List Scenarios**: View all available scenarios with load/purge actions
- **Load Scenario**: Create scenario data with custom scope
- **Purge Scenario**: Remove all scenario data for a specific scope
- **View Loaded**: Dashboard of currently loaded scenarios with statistics
- **View Resources**: Detailed list of tracked database resources

### Architecture
- Domain Layer: ScenarioInfo, LoadedScenario entities
- Application Layer: 5 use case services
- Infrastructure Layer: PrismBundleBridge, DoctrineLoadedScenarioRepository
- UI Layer: 3 Twig templates with standalone layout

### Documentation
- README.md with installation and usage guide
- LICENSE (MIT)
- CHANGELOG.md

[1.0.0]: https://github.com/dlhoumaud/prism-office/releases/tag/v1.0.0
