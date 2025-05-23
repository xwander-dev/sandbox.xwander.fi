# CLAUDE.md - xwander.fi WordPress Site

## Identity & Context

**Site**: dev.xwander.fi - Xwander Nordic WordPress site
**Repository**: /srv/xwander-platform/xwander.fi/ (Git submodule)
**Architecture**: Native WordPress with WP-CLI (matches wp.xwander.fi reference)
**Data Separation**: Code in `/srv/`, runtime data in `/data/`

## Directory Structure

```
/srv/xwander-platform/xwander.fi/             # Git repository (code)
├── dev/                                      # Development environment
│   ├── wp-core files (wp-admin/, wp-includes/, *.php)
│   ├── wp-config.php                         # WordPress configuration
│   ├── wp-cli.yml                            # WP-CLI configuration
│   ├── wp-content/                           # Custom content
│   │   ├── plugins/                          # Plugins (from old Bedrock)
│   │   ├── themes/                           # Themes (from old Bedrock)
│   │   └── uploads -> /data/...              # Symlink to data directory
├── qa/                                       # QA environment (future)
├── prod/                                     # Production environment (future)
├── scripts/                                  # Management scripts
└── docs/                                     # Documentation
```

```
/data/xwander-platform/xwander.fi/            # Data directory (NOT in Git)
├── dev/uploads/                              # Development uploads
├── qa/uploads/                               # QA uploads (future)
├── prod/uploads/                             # Production uploads (future)
└── backups/                                  # Database backups
```

## Migration Completed

### What Was Changed
- **Removed Bedrock structure**: No more web/app/ directories or composer.json
- **Migrated to native WordPress**: Standard wp-content structure
- **Preserved content**: Moved plugins and themes from Bedrock to native structure
- **Updated nginx**: Fixed document root and enabled Let's Encrypt SSL
- **Database setup**: Created xwander_fi_dev database and user
- **Fresh WordPress install**: Clean installation with migrated content

### Current Status
- ✅ **Working site**: https://dev.xwander.fi/ (HTTP/2 200)
- ✅ **SSL enabled**: Let's Encrypt certificates
- ✅ **Native WordPress**: Follows wp.xwander.fi reference pattern
- ✅ **Database configured**: MySQL user and database created
- ✅ **Plugins/themes preserved**: Migrated from old Bedrock structure

## Configuration Details

### Database
- **Name**: xwander_fi_dev
- **User**: xwander_fi_dev
- **Password**: xwander_dev_password
- **Host**: localhost

### WordPress Admin
- **URL**: https://dev.xwander.fi/wp-admin/
- **Username**: admin
- **Password**: admin123
- **Email**: admin@xwander.fi

### WP-CLI
- **Config**: dev/wp-cli.yml
- **Path**: /srv/xwander-platform/xwander.fi/dev
- **URL**: https://dev.xwander.fi

## Architecture Compliance

This site now follows the **wp.xwander.fi reference implementation**:
- Native WordPress (no Bedrock complexity)
- WP-CLI for all management
- Code/data separation without containers
- Let's Encrypt SSL certificates
- Three-environment structure (dev/qa/prod)

## Next Steps

1. **Migrate existing content**: If needed, import old database content
2. **Set up QA environment**: Copy dev structure to qa/
3. **Configure production**: Set up prod/ environment
4. **Theme customization**: Continue development with xwander theme
5. **Plugin management**: Use WP-CLI for plugin management

## Common Commands

```bash
# WordPress management
cd /srv/xwander-platform/xwander.fi/dev
wp plugin list
wp theme list
wp core update

# Database operations
wp db export backup.sql
wp search-replace 'old-url' 'new-url'

# Development workflow
git status
git add .
git commit -m "feat: Add new feature"
```

## References

- **Reference Implementation**: /srv/xwander-platform/wp.xwander.fi/
- **Documentation**: /srv/xwander-platform/docs/wp-blueprint-native.md
- **Parent Repository**: /srv/xwander-platform/CLAUDE.md

---

*Migration completed: Bedrock → Native WordPress | 2025-05-22*