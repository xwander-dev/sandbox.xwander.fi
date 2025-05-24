# Sandbox.xwander.fi Documentation

Welcome to the comprehensive documentation for the sandbox.xwander.fi experimental WordPress environment.

## Documentation Structure

### Setup & Architecture
- [Sandbox Setup Guide](./sandbox-setup-guide.md) - Complete setup process and architecture details
- [Reset Script Documentation](./reset-script-documentation.md) - How the reset script works

### Quick Links
- [Main Sandbox README](../SANDBOX-README.md) - User-facing quick start guide
- [Reset Script](../reset-sandbox-clone-from-dev.sh) - The actual reset script
- [Setup Plan](../../docs/sandbox-setup-plan.md) - Original setup checklist

## Purpose

This sandbox environment serves as:
- 🧪 Experimental testing ground
- 🔨 Breaking things safely
- 📚 Learning WordPress internals
- 🚀 Testing new plugins/themes
- 🐛 Debugging complex issues

## Key Features

1. **Complete WordPress Installation**
   - Cloned from dev.xwander.fi
   - WordPress 6.8.1 with all plugins
   - 1.6GB of media files

2. **One-Command Reset**
   - `./reset-sandbox-clone-from-dev.sh`
   - Restores to dev state in ~3 minutes
   - Preserves nothing (by design)

3. **Relaxed Security**
   - Debug mode available
   - File modifications allowed
   - No automated backups

## Environment Details

| Component | Value |
|-----------|-------|
| URL | https://sandbox.xwander.fi |
| Database | xwander_fi_sandbox |
| PHP Version | 8.1 |
| WordPress | 6.8.1 |
| Server | Nginx |
| SSL | Let's Encrypt (auto-renew) |

## Common Tasks

### Reset Everything
```bash
cd /srv/xwander-platform/sandbox.xwander.fi
./reset-sandbox-clone-from-dev.sh
```

### Check Logs
```bash
# PHP errors
tail -f /var/log/php8.1-fpm.log

# Nginx access
tail -f /var/log/nginx/access.log

# WordPress debug (if enabled)
tail -f web/app/debug.log
```

### Database Access
```bash
mysql -u xwander_fi_sandbox -psandbox_secure_password_2024 xwander_fi_sandbox
```

### WP-CLI Commands
```bash
cd web
wp plugin list
wp user list
wp option get siteurl
```

## Architecture Diagram

```
┌─────────────────────────────────────────────┐
│         sandbox.xwander.fi                  │
├─────────────────────────────────────────────┤
│  Nginx (443/SSL) → PHP-FPM → WordPress     │
├─────────────────────────────────────────────┤
│ /srv/xwander-platform/sandbox.xwander.fi/   │
│   ├── web/                                  │
│   │   ├── wp/        (WordPress core)      │
│   │   ├── app/       (content)             │
│   │   └── vendor/    (Composer)            │
│   └── reset-sandbox-clone-from-dev.sh      │
├─────────────────────────────────────────────┤
│ /data/xwander-platform/xwander.fi/          │
│   ├── config/sandbox/                       │
│   │   ├── wp-config.php                    │
│   │   └── wp-cli.yml                       │
│   └── uploads/sandbox/                      │
└─────────────────────────────────────────────┘
```

## Support

This is an experimental environment. When in doubt:
1. Run the reset script
2. Check the documentation
3. Break it again!

Remember: This sandbox is meant to be broken. That's what the reset script is for!