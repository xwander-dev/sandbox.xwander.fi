# Development Workflow for xwander.fi

## Development Environment

### Prerequisites
- PHP 8.1+
- MySQL/MariaDB 10.6+
- Git
- WP-CLI
- Nginx or Apache

### Local Setup
```bash
# Clone repository
git clone https://github.com/xwander-dev/xwander.fi.git
cd xwander.fi

# Set up development environment
cd dev
wp core download

# Create configuration
cp wp-config-sample.php wp-config.php
# Edit wp-config.php with your local database credentials

# Create uploads directory
mkdir -p /data/xwander-platform/xwander.fi/dev/uploads
ln -sf /data/xwander-platform/xwander.fi/dev/uploads wp-content/uploads

# Import database (if available)
wp db import /path/to/database.sql

# Update URLs
wp search-replace 'production-domain.com' 'localhost:8000' --all-tables
```

## Development Standards

### PHP Coding Standards
- Follow WordPress Coding Standards
- PSR-12 for custom plugins
- Use WordPress core functions when available
- Prefix custom functions with `xwfi_`

### CSS/SCSS Standards
- Use BEM naming convention
- Organize by components
- Mobile-first approach
- Minimize specificity conflicts

### JavaScript Standards
- Use ES6+ features
- Modular organization
- Namespaced global objects
- Avoid jQuery when possible

### Documentation
- PHPDoc for functions and classes
- Inline comments for complex logic
- README for plugins and themes
- Changelog for version tracking

## Git Workflow

### Feature Development
```bash
# Create feature branch
git checkout -b feat/feature-name main

# Make changes and commit regularly
git add .
git commit -m "feat: Description of changes"

# Push feature branch
git push -u origin feat/feature-name
```

### Pull Request Process
1. Push feature branch to GitHub
2. Create PR against main branch
3. Wait for code review
4. Address feedback
5. Merge only when approved

### Commit Message Format
```
<type>(<scope>): <description>

[optional body]

[optional footer]
```

Types:
- `feat`: New feature
- `fix`: Bug fix
- `docs`: Documentation changes
- `style`: Code style changes (formatting, etc)
- `refactor`: Code changes that neither fix bugs nor add features
- `perf`: Performance improvements
- `test`: Adding or updating tests
- `chore`: Changes to build process or tooling

Example:
```
feat(theme): Add responsive navigation menu

- Implement mobile-first hamburger menu
- Add keyboard navigation support
- Ensure WCAG 2.1 compliance

Closes #123
```

## Testing

### Manual Testing Checklist
- Cross-browser testing (Chrome, Firefox, Safari, Edge)
- Mobile device testing
- Accessibility testing
- Performance testing
- Content entry testing

### Automated Testing
- PHP unit tests for custom plugins
- JavaScript tests for interactive components
- Lighthouse for performance and accessibility
- Visual regression testing

## Database Management

### Local Development
- Use WP-CLI for database operations
- Export: `wp db export backup.sql`
- Import: `wp db import backup.sql`
- Search-replace: `wp search-replace 'prod' 'dev' --all-tables`

### Migration Path
1. Export production database
2. Import to development environment
3. Replace URLs and paths
4. Make development changes
5. Export development database for staging

## Plugin Management

### Core Plugins
- Yoast SEO
- Advanced Custom Fields
- WP Fastest Cache

### Custom Plugins
Custom functionality should be implemented as plugins rather than in the theme.

```bash
# Create custom plugin scaffold
wp scaffold plugin xwfi-custom-functionality
```

### Plugin Updates
Test plugin updates in development before applying to production:
```bash
wp plugin update --all
```

## Theme Development

### Child Theme Structure
```
themes/xwfi-theme/
├── assets/
│   ├── css/
│   ├── js/
│   └── images/
├── inc/
│   ├── customizer.php
│   └── template-functions.php
├── template-parts/
├── functions.php
├── style.css
└── README.md
```

### Theme Customization
- Use WordPress Customizer API
- Implement block editor support
- Modular template parts
- Responsive design patterns

## Performance Optimization

### Frontend Optimization
- Minimize HTTP requests
- Optimize images
- Use responsive images
- Critical CSS loading
- Defer non-critical JS

### Backend Optimization
- Object caching
- Database query optimization
- Transient caching
- Reduce external API calls