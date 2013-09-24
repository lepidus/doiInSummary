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
		if (!parent::register($category, $path)) {
			return false;
		}
		HookRegistry::register ('Installer::postInstall', array(&$this, 'clearCache'));
		HookRegistry::register('TemplateManager::display', array(&$this, 'templateManagerCallback'));
		$this->addLocaleData();
		return true;
	}

	function getDisplayName() {
		return __('plugins.generic.doi.displayName');
	}

	function getDescription() {
		return __('plugins.generic.doi.description');
	}

	function clearCache($hookName, $args) {
		$templateMgr =& TemplateManager::getManager();
		$templateMgr->clearTemplateCache();
		return false;
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

		switch ($args[1]) {
		case "issue/viewPage.tpl":
		case "index/journal.tpl":
			$smarty->register_prefilter(array(&$this, 'outputFilter'));
			break;
		}
	}

	function outputFilter($output, &$smarty) {
		$split = preg_split('#(<td class="tocPages">.*</tr>)#s', $output, 2);

		if (sizeof($split) == 2) {
			$smarty->unregister_prefilter('outputFilter');
			$snippet = <<<'END'
				{php}$this->assign("doiPlugin", PluginRegistry::getPlugin("generic", "doiplugin")){/php}
				{if $doiPlugin->getEnabled()}
				{if method_exists($article, "getPubId")}
					{assign var="doi" value=$article->getPubId('doi')}
				{else}
					{assign var="doi" value=$article->getDoi()}
				{/if}
				{if $doi}
				<tr>
					<td class="tocDoi">
					<span><a href="http://dx.doi.org/{$doi|escape}">{$doi|escape}</a></span>
					</td>
				</tr>
				{/if}
				{/if}
END;
			$output = $split[0] . $snippet . $split[1];
		}
		return $output;
	}
}
?>
