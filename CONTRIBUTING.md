# Contributing to xwander.fi

Thank you for your interest in contributing to xwander.fi! This document provides guidelines and instructions for contributing to this project.

## Development Workflow

We follow a GitFlow-inspired workflow:

1. **Branch from develop**: All feature development should branch from `develop`
2. **Feature branches**: Use the naming convention `feature/your-feature-name`
3. **Pull requests**: Submit PRs against the `develop` branch
4. **Code review**: All PRs require at least one code review
5. **CI/CD**: Automated tests must pass before merging

## Local Development Setup

### Prerequisites

- PHP 7.4 or later
- Composer
- MariaDB 10.11 or later
- Node.js and npm
- Git

### Setup Steps

1. Clone the repository:
   ```bash
   git clone https://github.com/xwander-dev/xwander.fi.git
   cd xwander.fi
   ```

2. Set up the development environment:
   ```bash
   cd dev
   composer install
   cp .env.example .env
   # Edit .env with your local database credentials
   ```

3. Set up the database:
   ```bash
   # Create database using WP-BLUEPRINT script
   cd /srv/xwander-platform
   ./scripts/create-db.sh xwander_fi
   ```

4. Configure your web server to point to `/path/to/xwander.fi/dev/web`

## Coding Standards

### PHP

- Follow PSR-12 coding standards
- Use WordPress coding standards for theme and plugin development

### JavaScript

- Follow Airbnb JavaScript Style Guide
- Use ES6+ features when appropriate

### CSS/SCSS

- Use BEM naming convention
- Follow the existing project structure

## Commit Guidelines

- Use conventional commit messages: `type(scope): subject`
- Types: `feat`, `fix`, `docs`, `style`, `refactor`, `test`, `chore`
- Keep commits focused and atomic
- Reference issues in commit messages when applicable

Example:
```
feat(theme): add mobile navigation menu
fix(acf): resolve issue with custom fields not saving
docs(readme): update installation instructions
```

## Pull Request Process

1. Update documentation if necessary
2. Add or update tests as needed
3. Ensure your code passes all tests
4. Get at least one code review approval
5. Rebase onto the latest develop branch before merging

## Releasing

Releases are handled by the project maintainers following the versioning schedule in the roadmap.

## Questions?

If you have any questions or need clarification, please open an issue in the repository.

---

Thank you for contributing to xwander.fi! Together, we're building an amazing experience for our users.