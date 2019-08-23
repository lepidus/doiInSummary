<?php

/**
 * Copyright (c) 2015 Lepidus Tecnologia
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 */

import('lib.pkp.classes.plugins.GenericPlugin');

class DoiInSummaryPlugin extends GenericPlugin {

	function register($category, $path, $mainContextId = NULL) {
		print_r(error_log("FUNÇÃO REGISTER CHAMADA", true));
		print_r(error_log($category, true));
		if (!parent::register($category, $path)) {
			return false;
		}
		HookRegistry::register ('Installer::postInstall', array(&$this, 'clearCache'));
		HookRegistry::register('TemplateManager::display', array(&$this, 'templateManagerCallback'));
		$this->addLocaleData();
		return true;
	}

	function getDisplayName() {
		return __('plugins.generic.doiInSummary.displayName');
	}

	function getDescription() {
		return __('plugins.generic.doiInSummary.description');
	}

	function clearCache($hookName, $args) {
		$templateMgr =& TemplateManager::getManager();
		$templateMgr->clearTemplateCache();
		return false;
	}

	function getInstallSitePluginSettingsFile() {
		return $this->getPluginPath() . '/settings.xml';
	}

	function templateManagerCallback($hookName, $args) {
		$smarty = $args[0];
		$baseUrl = $smarty->get_template_vars('baseUrl');
		$smarty->addStyleSheet($baseUrl . '/plugins/generic/doiInSummary/doi.css');

		switch ($args[1]) {
		case "issue/viewPage.tpl":
		case "index/journal.tpl":
			$smarty->register_prefilter(array(&$this, 'outputFilter'));
			break;
		}
	}

	function outputFilter($output, &$smarty) {
		if ($smarty->_current_file !== "issue/issue.tpl") {
			return $output;
		}

		$split = preg_split('#(<div class="tocAuthors">.*?</div>)#s', $output, 2, PREG_SPLIT_DELIM_CAPTURE);

		if (sizeof($split) == 3) {
			$smarty->unregister_prefilter('outputFilter');
			$snippet = <<<'END'
				{php}$this->assign("doiPlugin", PluginRegistry::getPlugin("generic", "doiinsummaryplugin")){/php}
				{if $doiPlugin->getEnabled()}
				{assign var="doi" value=$article->getStoredPubId('doi')}
				{if $doi}
				<div>
					<div class="tocDoi">
					<span><a href="http://dx.doi.org/{$doi|escape}">{$doi|escape}</a></span>
					<span> rita </span>
					</div>
				</div>
				{/if}
				{/if}
END;
			$output = $split[0] . $split[1] . $snippet . $split[2];
		}
		return $output;
	}
}
?>
