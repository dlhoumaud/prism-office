# Contributing to PrismOffice

Thank you for your interest in contributing to PrismOffice! 🎉

## 🎯 Project Philosophy

PrismOffice aims to be:
- **Simple**: Easy to install, easy to use
- **Standalone**: Zero external dependencies (inline CSS/JS)
- **Safe**: Dev mode only, no production risk
- **Fast**: No compilation, no build steps

## 📋 How to Contribute

### Reporting Bugs

1. Check existing issues first
2. Create a new issue with:
   - PrismOffice version
   - Symfony version
   - PHP version
   - Steps to reproduce
   - Expected vs actual behavior

### Suggesting Features

1. Open an issue with `[Feature Request]` prefix
2. Describe the use case
3. Explain why it fits PrismOffice's philosophy

### Code Contributions

#### Before You Start

1. Fork the repository
2. Create a feature branch: `git checkout -b feature/amazing-feature`
3. Follow our coding standards

#### Coding Standards

- **PHP**: PSR-12, strict types
- **Architecture**: Hexagonal (Domain/Application/Infrastructure)
- **Tests**: Unit tests required for new features
- **PHPStan**: Level 9 (maximum)
- **Documentation**: Update README.md if needed

#### Development Setup

```bash
# 1. Clone your fork
git clone https://github.com/YOUR-USERNAME/prism-office.git

# 2. Install dependencies
cd PrismOffice
composer install

# 3. Run tests
vendor/bin/phpunit

# 4. Run PHPStan
vendor/bin/phpstan analyse src -c phpstan.neon --level=9

# 5. Check code style
vendor/bin/phpcs src --standard=phpcs.xml.dist
```

#### Pull Request Process

1. Update documentation if needed
2. Add tests for new features
3. Ensure all tests pass
4. Update CHANGELOG.md
5. Submit PR with clear description

## 🏗️ Architecture Guidelines

### Hexagonal Architecture

```
Domain/          # Pure business logic, no dependencies
Application/     # Use cases, orchestration
Infrastructure/  # Adapters (Symfony, Doctrine, etc.)
```

**Rules**:
- Domain NEVER imports from Application or Infrastructure
- Application NEVER imports from Infrastructure
- Infrastructure can import from Domain and Application

### Adding a New Route

1. Add route in Controller with attribute
2. Create corresponding Use Case in Application/
3. Add repository method if needed in Domain/Repository/
4. Implement in Infrastructure/
5. Add template in templates/
6. Update documentation

### Adding a New Feature

1. Start with Domain (entities, value objects)
2. Create Use Case in Application
3. Implement adapter in Infrastructure
4. Add controller action if web UI needed
5. Write tests
6. Update docs

## 🧪 Testing

### Unit Tests

```bash
vendor/bin/phpunit
```

### Manual Testing

```bash
# 1. Install in test project
composer update prism/office

# 2. Access interface
http://localhost:8000/prism/list

# 3. Test all routes
- List scenarios
- Load scenario
- View loaded
- View resources
- Purge scenario
```

## 📝 Documentation

### Update Documentation

When adding features:
1. Update README.md
2. Update INSTALLATION.md if installation changes
3. Add to CHANGELOG.md
4. Update QUICK_REFERENCE.md

### Documentation Standards

- Clear, concise language
- Code examples for complex features
- Screenshots for UI changes
- Update table of contents if needed

## 🎨 UI/UX Guidelines

### Design Principles

- **Dark theme**: Match Symfony Profiler aesthetic
- **Inline CSS/JS**: No external dependencies
- **Responsive**: Mobile-friendly
- **Accessible**: Keyboard navigation, ARIA labels
- **Fast**: No heavy libraries

### Adding UI Changes

1. Update templates/ with inline styles
2. Keep CSS in `<style>` tags
3. Keep JS in `<script>` tags
4. Test on mobile/tablet
5. Ensure accessibility

## 🔖 Versioning

We use [Semantic Versioning](https://semver.org/):

- **MAJOR**: Breaking changes
- **MINOR**: New features, backward compatible
- **PATCH**: Bug fixes

## 📜 License

By contributing, you agree that your contributions will be licensed under the MIT License.

## 🙏 Thank You!

Every contribution makes PrismOffice better. Thank you for your time and effort! 

---

**Questions?** Open an issue or email dlhoumaud@gmail.com
