<?php

/**
 * Copyright (c) 2015-2025 Lepidus Tecnologia
 * Distributed under the GNU GPL v3. For full terms see LICENSE or https://www.gnu.org/licenses/gpl-3.0.txt.
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Capsule\Manager as Capsule;

class ObsoleteVersionMigration extends Migration
{
    public function up()
    {
        $this->deleteObsoletePlugin();

        Capsule::table('plugin_settings')
            ->where('plugin_name', 'doinosumarioplugin')
            ->where('setting_name', 'enabled')
            ->get(['context_id', 'setting_value'])
            ->each(function ($row) {
                # Enable or disable the new plugin based on the old plugin's settings
                Capsule::table('plugin_settings')
                    ->where('plugin_name', 'doiinsummaryplugin')
                    ->where('context_id', $row->context_id)
                    ->where('setting_name', 'enabled')
                    ->update(['setting_value' => $row->setting_value]);

                # Remove old plugin settings
                $pluginSettingsDao = DAORegistry::getDAO('PluginSettingsDAO');
                $pluginSettingsDao->updateSetting(
                    $row->context_id,
                    'doinosumarioplugin',
                    'enabled',
                    $row->setting_value,
                    'bool'
                );
            });

        # Delete old plugin version record
        Capsule::table('versions')
            ->where('product_type', 'plugins.generic')
            ->where('product', 'doiNoSumario')
            ->delete();
    }

    private function deleteObsoletePlugin()
    {
        $plugin = PluginRegistry::getPlugin('generic', 'doinosumarioplugin');
        $category = $plugin->getCategory();
        $productName = basename($plugin->getPluginPath());

        $versionDao = DAORegistry::getDAO('VersionDAO');
        $installedPlugin = $versionDao->getCurrentVersion('plugins.' . $category, $productName, true);

        if ($installedPlugin) {
            $pluginDest = Core::getBaseDir() . DIRECTORY_SEPARATOR . 'plugins' . DIRECTORY_SEPARATOR . $category .
                DIRECTORY_SEPARATOR . $productName;
            $pluginLibDest = Core::getBaseDir() . DIRECTORY_SEPARATOR . PKP_LIB_PATH . DIRECTORY_SEPARATOR . 'plugins' .
                DIRECTORY_SEPARATOR . $category . DIRECTORY_SEPARATOR . $productName;

            if (in_array($category, PluginRegistry::getCategories())) {
                $fileManager = new FileManager();
                $fileManager->rmtree($pluginDest);
                $fileManager->rmtree($pluginLibDest);
            }

            if (is_dir($pluginDest) || is_dir($pluginLibDest)) {
                error_log('Error deleting obsolete version of plugin ' . $productName);
            } else {
                $versionDao->disableVersion('plugins.' . $category, $productName);
                error_log('Successfully deleted obsolete version of plugin ' . $productName);
            }
        } else {
            error_log('Obsolete plugin does not exist: ' . $productName);
        }
    }
}
