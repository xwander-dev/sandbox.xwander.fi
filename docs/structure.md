# Repository Structure for xwander.fi

## Git Repository Organization

### Branch Strategy
- `main`: Production-ready code
- `dev`: Active development branch
- Feature branches: `feat/feature-name`
- Bug fixes: `fix/bug-description`
- Hotfixes: `hotfix/issue-description`

### Included in Git Repository
- WordPress configuration files
- Custom theme files
- Custom plugin files
- Documentation
- Scripts
- Configuration templates

### Excluded from Git Repository
- WordPress core files
- Standard plugins
- User uploads (in `/data/`)
- Database files
- Logs and temporary files
- Sensitive configuration

## Directory Structure

### Code Structure (Git-tracked)
```
/srv/xwander-platform/xwander.fi/
├── .git/                           # Git repository
├── .github/                        # GitHub workflows and templates
├── dev/                            # Development environment
│   ├── wp-content/                 # WordPress content directory
│   │   ├── mu-plugins/             # Must-use plugins
│   │   ├── plugins/                # Only custom plugins
│   │   └── themes/                 # Custom themes
│   ├── wp-config.php               # WordPress configuration (templated)
│   └── wp-cli.yml                  # WP-CLI configuration
├── scripts/                        # Utility scripts
│   ├── backup.sh                   # Backup scripts
│   ├── deploy.sh                   # Deployment scripts
│   └── maintenance.sh              # Maintenance scripts
├── docs/                           # Documentation
├── .gitignore                      # Git exclusion rules
└── README.md                       # Repository documentation
```

### Runtime Data (Not Git-tracked)
```
/data/xwander-platform/xwander.fi/
├── databases/                      # MariaDB data files
├── backups/                        # Database backups
└── dev/uploads/                    # Development environment uploads
```

## .gitignore Configuration

Essential .gitignore rules for WordPress:

```
# WordPress core files
wp-admin/
wp-includes/
index.php
license.txt
readme.html
wp-*.php
xmlrpc.php

# Content to exclude
wp-content/uploads/
wp-content/upgrade/
wp-content/cache/
wp-content/backup*/
wp-content/debug.log

# Standard plugins (install via WP-CLI)
wp-content/plugins/akismet/
wp-content/plugins/hello.php

# Environment-specific configs
.env
*.env
wp-config.php

# Only include custom themes
wp-content/themes/twenty*/

# Exclude database exports
*.sql
*.sql.gz

# Exclude system files
.DS_Store
Thumbs.db
*.log
*.swp
*.bak

# Exclude composer dependencies
vendor/
```

## Git Workflow

### New Feature Development
```bash
# Create feature branch
git checkout -b feat/feature-name main

# Make changes, then commit
git add .
git commit -m "feat: Implement feature X"

# Push to remote
git push -u origin feat/feature-name

# Create pull request to main
```

### Deploying to Production
```bash
# Merge to main after review
git checkout main
git merge feat/feature-name

# Tag release
git tag -a v1.0.0 -m "Version 1.0.0"
git push origin main --tags
```

## Data Management

### Uploads Handling
- Uploads directory is symlinked to `/data/xwander-platform/xwander.fi/{env}/uploads`
- This keeps binary files outside the Git repository
- Each environment has its own uploads directory

### Database Management
- Database files are stored outside Git
- Schema changes are version controlled through migration scripts
- Database credentials are stored in environment-specific configuration files