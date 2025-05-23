# xwander.fi WordPress Site

[![WordPress](https://img.shields.io/badge/WordPress-6.8.1-blue.svg)](https://wordpress.org/)
[![WP-CLI](https://img.shields.io/badge/managed%20with-WP--CLI-green.svg)](https://wp-cli.org/)
[![WP-BLUEPRINT](https://img.shields.io/badge/standard-WP--BLUEPRINT-orange.svg)](https://github.com/xwander-dev/wp-blueprint)

This repository contains the xwander.fi WordPress website, a standard WordPress installation managed with WP-CLI and following the WP-BLUEPRINT data separation standard.

## Overview

- **Standard WordPress**: Native WordPress installation for maximum compatibility
- **WP-CLI Management**: Command-line management of WordPress
- **Code/Data Separation**: WordPress code in `/srv/`, data in `/data/`
- **Git Workflow**: Feature branches, pull requests, and semantic versioning

## Directory Structure

```
/srv/xwander-platform/xwander.fi/    # Git repository
├── dev/                             # Development environment
│   ├── wp-content/                  # WordPress content directory
│   │   ├── mu-plugins/              # Must-use plugins
│   │   ├── plugins/                 # Custom plugins
│   │   ├── themes/                  # Custom themes
│   │   └── uploads/ -> /data/...    # Symlink to uploads directory
├── qa/                              # QA environment
├── prod/                            # Production environment
├── scripts/                         # Utility scripts
└── docs/                            # Documentation
```

Data directory (not in Git):
```
/data/xwander-platform/xwander.fi/
├── dev/uploads/                     # Development uploads
├── qa/uploads/                      # QA uploads
├── prod/uploads/                    # Production uploads
└── backups/                         # Database backups
```

## Getting Started

### Prerequisites

- PHP 8.1 or later
- MySQL/MariaDB 10.6 or later
- WP-CLI 2.8 or later
- Nginx or Apache
- Git

### Installation

1. Clone the repository:
   ```bash
   git clone https://github.com/xwander-dev/xwander.fi.git
   cd xwander.fi
   ```

2. Set up WordPress in development environment:
   ```bash
   cd dev
   wp core download
   wp config create --dbname=xwander_fi_dev --dbuser=xwander_fi_dev --dbpass=password --dbhost=localhost
   wp core install --url=dev.xwander.fi --title="XWander Finland" --admin_user=admin --admin_password=secure_password --admin_email=admin@xwander.fi
   ```

3. Configure uploads directory:
   ```bash
   mkdir -p /data/xwander-platform/xwander.fi/dev/uploads
   ln -sf /data/xwander-platform/xwander.fi/dev/uploads wp-content/uploads
   ```

4. Set proper permissions:
   ```bash
   find . -type d -exec chmod 755 {} \;
   find . -type f -exec chmod 644 {} \;
   chmod 600 wp-config.php
   ```

## Development Workflow

See [docs/development.md](docs/development.md) for detailed information about the development workflow.

```bash
# Create feature branch
git checkout -b feat/feature-name main

# Make changes, then commit
git add .
git commit -m "feat: Implement feature"

# Push branch
git push -u origin feat/feature-name
```

## Deployment

See [docs/deployment.md](docs/deployment.md) for detailed information about the deployment process.

```bash
# Deploy to QA
./scripts/deploy.sh qa

# Deploy to production
./scripts/deploy.sh prod
```

## WP-CLI Usage

See [docs/wp-cli.md](docs/wp-cli.md) for detailed information about using WP-CLI with this project.

```bash
# Common operations
wp core update
wp plugin update --all
wp theme update --all
wp db export backup.sql
```

## Documentation

- [docs/index.md](docs/index.md): Documentation overview
- [docs/setup.md](docs/setup.md): Installation and configuration
- [docs/structure.md](docs/structure.md): Repository structure
- [docs/wp-cli.md](docs/wp-cli.md): WP-CLI usage
- [docs/development.md](docs/development.md): Development workflow
- [docs/deployment.md](docs/deployment.md): Deployment process

## License

This project is licensed under the terms of the MIT license.