# Sandbox Environment for xwander.fi

⚠️ **EXPERIMENTAL ENVIRONMENT** - This is a sandbox for testing and experimentation. Expect things to break!

## Overview

This is a complete clone of dev.xwander.fi designed for destructive testing and experimentation. The environment can be completely reset at any time using the included reset script.

## Quick Start

### Access
- **URL**: https://sandbox.xwander.fi (requires DNS setup)
- **Admin**: Use dev.xwander.fi credentials
- **Database**: xwander_fi_sandbox

### Reset Everything

When things break (and they will), just run:

```bash
./reset-sandbox-clone-from-dev.sh
```

This will:
1. Reset all code to match dev
2. Drop and recreate the database
3. Clone fresh data from dev
4. Update all URLs
5. Sync uploads from dev

## Key Features

- **Full Debug Mode**: All WordPress debug features enabled
- **No Restrictions**: File modifications allowed
- **Larger Limits**: 100MB uploads, 300s execution time
- **Disposable**: Break anything without consequences

## Important Notes

1. **No Backups**: This environment has no backup strategy
2. **No CI/CD**: Direct Git operations only
3. **Shared Uploads**: Currently shares uploads with dev (will be fixed)
4. **DNS Required**: Need A record for sandbox.xwander.fi

## Common Tasks

### Manual Database Reset
```bash
mysql -u xwander_fi_sandbox -psandbox_secure_password_2024 xwander_fi_sandbox
DROP DATABASE xwander_fi_sandbox;
CREATE DATABASE xwander_fi_sandbox;
```

### Check Error Logs
```bash
tail -f web/app/debug.log
```

### Install a Plugin for Testing
```bash
cd web
wp plugin install plugin-name --activate
```

### Complete Environment Wipe
```bash
git reset --hard
git clean -fdx
./reset-sandbox-clone-from-dev.sh
```

## Configuration Files

- **wp-config.php**: `/data/xwander-platform/xwander.fi/config/sandbox/wp-config.php`
- **Nginx**: `/etc/nginx/sites-available/sandbox.xwander.fi`
- **Database**: xwander_fi_sandbox / sandbox_secure_password_2024

## Warnings

- This is NOT for production use
- Data can be lost at any time
- No security hardening
- Full debug output visible
- All errors displayed

## Support

This is an experimental environment. If it's broken, reset it!