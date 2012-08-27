<?php

/**
 * @file DoiPlugin.inc.php
 *
 * Copyright (c) 2003-2012 John Willinsky
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * @class DoiPlugin
 * @ingroup plugins_generic_doi
 *
 * @brief Doi plugin class
 */


import('lib.pkp.classes.plugins.GenericPlugin');

class DoiPlugin extends GenericPlugin {
	/**
	 * Called as a plugin is registered to the registry
	 * @param $category String Name of category plugin was registered to
	 * @return boolean True iff plugin initialized successfully; if false,
	 * 	the plugin will not be registered.
	 */
	function register($category, $path) {
		$success = parent::register($category, $path);
		if (!Config::getVar('general', 'installed') || defined('RUNNING_UPGRADE')) return true;
		if ($success && $this->getEnabled()) {
			HookRegistry::register('TemplateManager::display', array(&$this, 'templateManagerCallback'));
		}
		return $success;
	}

	function getDisplayName() {
		return __('plugins.generic.doi.displayName');
	}

	function getDescription() {
		return __('plugins.generic.doi.description');
	}

	/**
	 * Get the name of the settings file to be installed site-wide when
	 * OJS is installed.
	 * @return string
	 */
	function getInstallSitePluginSettingsFile() {
		return $this->getPluginPath() . '/settings.xml';
	}

	function templateManagerCallback($hookName, $args) {
		$smarty =& $args[0];
		$baseUrl = $smarty->get_template_vars('baseUrl');
		$smarty->addStyleSheet($baseUrl . '/plugins/generic/doi/doi.css');
		if ($args[1] == "issue/viewPage.tpl") {
			$smarty->register_prefilter(array(&$this, 'outputFilter'));
		}
	}

	function outputFilter($output, &$smarty) {
		$split = preg_split('#(<td class="tocPages">.*</tr>)#s', $output, 2);

		if (sizeof($split) == 2) {
			$smarty->unregister_prefilter('outputFilter');
			$snippet = <<<'END'
				{if $article->getDOI()}
				<tr>
					<td class="tocDoi">
					<span><a href="http://dx.doi.org/{$article->getDOI()|escape}">{$article->getDOI()|escape}</a></span>
					</td>
				</tr>
				{/if}
END;
			$output = $split[0] . $snippet . $split[1];
		}
		return $output;
	}
}
?>
