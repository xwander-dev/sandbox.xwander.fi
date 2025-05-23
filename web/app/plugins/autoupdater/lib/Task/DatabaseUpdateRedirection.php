<?php
defined('AUTOUPDATER_LIB') or die;

class AutoUpdater_Task_DatabaseUpdateRedirection extends AutoUpdater_Task_Base
{
    /**
     * @return array
     */

    public function doTask()
    {
        $success = false;
        $message = '';

        if (!is_plugin_active('redirection/redirection.php')) {
            return array(
                'success' => true,
                'message' => 'Redirections plugin is not active, skipping database update.',
            );
        }

        $plugin_file = WP_PLUGIN_DIR . '/redirection/redirection.php';
        if (
            file_exists($plugin_file)
            && file_exists(WP_PLUGIN_DIR . '/redirection/database/database.php')
        ) {
            include_once $plugin_file; // phpcs:ignore
            include_once WP_PLUGIN_DIR . '/redirection/database/database.php';
        }

        $data = get_file_data($plugin_file, array('Version' => 'Version'));
        $version = $data['Version'];

        if (!defined('REDIRECTION_VERSION') || !defined('REDIRECTION_DB_VERSION')) {
            return array(
                'success' => true,
                'needs_refactor' => true,
                'message' => 'Redirections ' . $version . ' plugin not loaded.',
            );
        }

        if (!method_exists('Red_Database','apply_to_sites')) {
            return array(
                'success' => true,
                'needs_refactor' => true,
                'message' => 'Red_Database::apply_to_sites method not found. Version ' . $version,
            );
        }

        Red_Database::apply_to_sites(function() use (&$success, &$message, $version) {
            $database = new Red_Database();
            $status = new Red_Database_Status();

            if (!$status->needs_updating()) {
                $success = true;
                $message = 'Redirections ' . $version . ' plugin database is up to date.';
               return;
            }

            $start = microtime(true);
            $limit = intval(ini_get('max_execution_time')) - 1; // seconds
            $limit = $limit > 0 ? $limit : 59;

           do {
                $result = $database->apply_upgrade($status);
                $info = $status->get_json();

                if ($info['status'] === 'error') {
                    $message = 'Redirections ' . $version . ' plugin database failed to upgrade: ' . $info['reason'];
                    return;
                }

                if ($info['status'] === 'ok') {
                    $success = true;
                    $message = 'Redirections ' . $version . ' plugin database upgraded successfully.';
                    return;
                }

                $message = 'Redirections ' . $version . ' plugin database upgrade timed out after '. $limit . 's.';
            } while (microtime(true) - $start < $limit);

        });

        return array(
            'success' => $success,
            'message' => $message,
        );
    }
}
