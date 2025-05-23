Feature: Install WordPress plugins

  Background:
    Given an empty cache

  Scenario: Branch names should be removed from Github projects
    Given a WP install

    When I run `wp plugin install https://github.com/wp-cli-test/generic-example-plugin/archive/refs/heads/master.zip --activate`
    Then STDOUT should contain:
      """
      Downloading install
      """
    And STDOUT should contain:
      """
      package from https://github.com/wp-cli-test/generic-example-plugin/archive/refs/heads/master.zip
      """
    And STDOUT should contain:
      """
      Renamed Github-based project from 'generic-example-plugin-master' to 'generic-example-plugin'.
      """
    And STDOUT should contain:
      """
      Plugin installed successfully.
      """
    And the wp-content/plugins/generic-example-plugin directory should exist
    And the wp-content/plugins/generic-example-plugin-master directory should not exist

    When I try `wp plugin install https://github.com/wp-cli-test/generic-example-plugin/archive/refs/heads/master.zip`
    Then STDERR should contain:
      """
      Warning: Destination folder already exists
      """
    And STDERR should contain:
      """
      Error: No plugins installed.
      """
    And the wp-content/plugins/generic-example-plugin directory should exist
    And the wp-content/plugins/generic-example-plugin-master directory should not exist
    And the return code should be 1

    When I run `wp plugin install https://github.com/wp-cli-test/generic-example-plugin/archive/refs/heads/master.zip --force`
    Then STDOUT should contain:
      """
      Plugin updated successfully.
      """
    And the wp-content/plugins/generic-example-plugin directory should exist
    And the wp-content/plugins/generic-example-plugin-master directory should not exist

    # However if the plugin slug ('modern-framework') does not match the project name then it's downloaded to wrong directory.
    When I run `wp plugin install https://github.com/Miller-Media/modern-wordpress/archive/master.zip`
    Then STDOUT should contain:
      """
      Plugin installed successfully.
      """
    And STDOUT should match /Renamed Github-based project from 'modern-(?:wordpress|framework)-master' to 'modern-wordpress'/
    # Wrong directory.
    And the wp-content/plugins/modern-wordpress directory should exist
    And the wp-content/plugins/modern-framework directory should not exist

  Scenario: Don't attempt to rename ZIPs uploaded to GitHub's releases page
    Given a WP install

    When I run `wp plugin install https://github.com/wp-cli-test/generic-example-plugin/releases/download/v0.1.0/generic-example-plugin.0.1.0.zip`
    Then STDOUT should contain:
      """
      Plugin installed successfully.
      """
    And STDOUT should not contain:
      """
      Renamed Github-based project from 'generic-example-plugin-0.1.0' to 'generic-example-plugin'.
      """
    And the wp-content/plugins/generic-example-plugin directory should exist

  Scenario: Don't attempt to rename ZIPs coming from a GitHub raw source
    Given a WP install

    When I run `wp plugin install https://github.com/Miller-Media/modern-wordpress/raw/master/builds/modern-framework-stable.zip`
    Then STDOUT should contain:
      """
      Plugin installed successfully.
      """
    And STDOUT should not contain:
      """
      Renamed Github-based project from 'modern-framework-stable' to 'modern-framework'.
      """
    And the wp-content/plugins/modern-framework directory should exist

  Scenario: Installing respects WP_PROXY_HOST and WP_PROXY_PORT
    Given a WP install
    And a invalid-proxy-details.php file:
      """
      <?php
      define( 'WP_PROXY_HOST', 'https://wp-cli.org' );
      define( 'WP_PROXY_PORT', '443' );
      """

    When I try `wp --require=invalid-proxy-details.php plugin install debug-bar`
    Then STDERR should contain:
      """
      Warning: debug-bar: An unexpected error occurred. Something may be wrong with WordPress.org or this server&#8217;s configuration.
      """
    And STDERR should contain:
      """
      Error: No plugins installed.
      """
    And STDOUT should be empty
    And the return code should be 1

    When I run `wp plugin install debug-bar`
    Then STDOUT should contain:
      """
      Plugin installed successfully.
      """

  Scenario: Return code is 1 when one or more plugin installations fail
    Given a WP install

    When I try `wp plugin install site-secrets site-secrets-not-a-plugin`
    Then STDERR should contain:
      """
      Warning:
      """
    And STDERR should contain:
      """
      site-secrets-not-a-plugin
      """
    And STDERR should contain:
      """
      Error: Only installed 1 of 2 plugins.
      """
    And STDOUT should contain:
      """
      Installing Site Secrets
      """
    And STDOUT should contain:
      """
      Plugin installed successfully.
      """
    And the return code should be 1

    When I try `wp plugin install site-secrets`
    Then STDOUT should be:
      """
      Success: Plugin already installed.
      """
    And STDERR should be:
      """
      Warning: site-secrets: Plugin already installed.
      """
    And the return code should be 0

    When I try `wp plugin install site-secrets-not-a-plugin`
    Then STDERR should contain:
      """
      Warning:
      """
    And STDERR should contain:
      """
      site-secrets-not-a-plugin
      """
    And STDERR should contain:
      """
      Error: No plugins installed.
      """
    And the return code should be 1

  Scenario: Paths aren't backslashed when destination folder already exists
    Given a WP install

    When I run `pwd`
    Then save STDOUT as {WORKING_DIR}

    When I run `rm wp-content/plugins/akismet/akismet.php`
    Then the return code should be 0

    When I try `wp plugin install akismet --ignore-requirements`
    Then STDERR should contain:
      """
      Warning: Destination folder already exists. "{WORKING_DIR}/wp-content/plugins/akismet/"
      """
    And STDERR should contain:
      """
      Error: No plugins installed.
      """
    And the return code should be 1

  Scenario: For Github archive URLs use the Github project name as the plugin directory
    Given a WP install

    When I run `wp plugin install https://github.com/wp-cli-test/generic-example-plugin/archive/v0.1.0.zip`
    Then STDOUT should contain:
      """
      Plugin installed successfully.
      """
    And STDOUT should contain:
      """
      package from https://github.com/wp-cli-test/generic-example-plugin/archive/v0.1.0.zip
      """
    And STDOUT should contain:
      """
      Renamed Github-based project from 'generic-example-plugin-0.1.0' to 'generic-example-plugin'.
      """
    And the wp-content/plugins/generic-example-plugin directory should exist
    And the wp-content/plugins/generic-example-plugi directory should not exist
    And the wp-content/plugins/generic-example-plugin-0.1.0 directory should not exist

    When I run `wp plugin install https://github.com/Automattic/sensei/archive/version/1.9.19.zip`
    Then STDOUT should contain:
      """
      Plugin installed successfully.
      """
    And STDOUT should contain:
      """
      package from https://github.com/Automattic/sensei/archive/version/1.9.19.zip
      """
    And STDOUT should contain:
      """
      Renamed Github-based project from 'sensei-version-1.9.19' to 'sensei'.
      """
    And the wp-content/plugins/sensei directory should exist
    And the wp-content/plugins/archive directory should not exist
    And the wp-content/plugins/sensei-version-1.9.19 directory should not exist

  Scenario: Verify installed plugin activation
    Given a WP install

    When I run `wp plugin install site-secrets`
    Then STDOUT should not be empty

    When I try `wp plugin install site-secrets --activate`
    Then STDERR should contain:
      """
      Warning: site-secrets: Plugin already installed.
      """

    And STDOUT should contain:
      """
      Activating 'site-secrets'...
      Plugin 'site-secrets' activated.
      Success: Plugin already installed.
      """

  @require-php-7
  Scenario: Can't install plugin that requires a newer version of WordPress
    Given a WP install

    When I run `wp core download --version=6.4 --force`
    And I run `rm -r wp-content/themes/*`

    And I try `wp plugin install wp-super-cache`
    Then STDERR should contain:
      """
      Warning: wp-super-cache: This plugin does not work with your version of WordPress
      """

    And STDERR should contain:
      """
      Error: No plugins installed.
      """

  @less-than-php-7.4 @require-wp-6.6
  Scenario: Can't install plugin that requires a newer version of PHP
    Given a WP install

    And I try `wp plugin install contact-form-7`
    Then STDERR should contain:
      """
      Warning: contact-form-7: This plugin does not work with your version of PHP
      """

    And STDERR should contain:
      """
      Error: No plugins installed.
      """
