<?php

/**
 * Copyright (c) 2015 Lepidus Tecnologia
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 */

import('lib.pkp.classes.plugins.GenericPlugin');
import('classes.article.ArticleDAO');

class DoiNoSumarioPlugin extends GenericPlugin {

	function register($category, $path, $mainContextId = NULL) {
		error_log("FUNÇÃO REGISTER CHAMADA");
		
		if (!parent::register($category, $path, $mainContextId)) {
			return false;
		}
	

		// HookRegistry::register ('Installer::postInstall', array($this, 'clearCache'));
		// HookRegistry::register('TemplateManager::display', array($this, 'templateManagerCallback'));
		// HookRegistry::register('TemplateManager::registerFilter', array($this, 'outputFilter'));

		HookRegistry::register('TemplateManager::display', array($this, 'templateManagerCallback'));
		$this->addLocaleData();

		/* ANOTAÇÕES EM LOG */
		error_log("CARREGANDO O CSS"); 
		error_log("-------------------------------");
		/* ---------------- */

		$request = Application::getRequest();
		$url = $request->getBaseUrl() . '/' . $this->getPluginPath() . '/doi.css';
		$templateMgr = TemplateManager::getManager($request);
		$templateMgr->addStyleSheet('doiCSS', $url);

		return true;
	}

	function getDisplayName() {
		return __('plugins.generic.doiInSummary.displayName');
	}

	function getDescription() {
		return __('plugins.generic.doiInSummary.description');
	}

	function clearCache($hookName, $args) {
		/* ANOTAÇÕES EM LOG */
		error_log("CLEAR CACHE");
		error_log("-------------------------------");
		/* ---------------- */

		$templateMgr = TemplateManager::getManager();
		$templateMgr->clearTemplateCache();
		return false;
	}

	function getInstallSitePluginSettingsFile() {
		return $this->getPluginPath() . '/settings.xml';
	}

	function templateManagerCallback($hookName, $args) {

		error_log("FUNÇÃO TEMPLATEMANAGERCALLBACK");
		//$request = Application::getRequest();
		//$templateMgr = TemplateManager::getManager($request);
		
		$templateMgr = $args[0];

		/* ANOTAÇÕES EM LOG */
		error_log("SWITCH CASE DA TEMPLATE_MNG");
		/* ---------------- */

		/* ANOTAÇÕES EM LOG */
		error_log("PAGINA IDENTIFICADA PELO CALLBACK");
		error_log("$args[1]");
		/* ---------------- */

		switch ($args[1]) {
		case "frontend/pages/indexJournal.tpl":
			error_log("break");
			break;
		case "frontend/pages/issue.tpl":

			error_log("Registro de out filtro");
			$templateMgr->registerFilter('output',array($this, 'outputFilter'));
	
			//HookRegistry::call('TemplateManager::registerFilter', array($this, 'pre'));
			break;
		}
		
	}

	// Antiga função 'outputFilter', deprecidada

	function outputFilter($output, $templateMgr) {

		/* ANOTAÇÕES EM LOG */
		error_log("FUNÇÃO outputFilter");
		error_log("-------------------------------");
		/* ---------------- */

		if($templateMgr->source->filepath !== "app:frontendpagesissue.tpl"){
			return $output;
		}

		//error_log("Output variável: " . $output);

// 		if ($templateMgr->_current_file !== "issue/issue.tpl") {
// 			return $output;
// 		}

		 $split = preg_split('#(<div class="title">.*?</div>)#s', $output,-1, PREG_SPLIT_DELIM_CAPTURE);

		$article = new ArticleDAO();
		// $novoArticle = $article->getById('116');
		// print_r($novoArticle);
		
		for ($i=0; $i < sizeof($split); $i++ ) { 
			if($i % 2 !== 0){

				$idArray = array();
				$idDoArtigo = preg_match('#.+view\/([0-9]*)#', $split[$i], $idArray);

				$articleDoi = $article->getById($idArray[1]);
				$doiUrl02 = $articleDoi->_data['pub-id::doi'];
				
				$doiUrl = 'https://doi.org/' . $doiUrl02;

				$string = "<div id='DoiNoProgramador'> <a href='". $doiUrl ."'> DOI FUNCIONANDO -> ". $doiUrl ." </a> </div>";
				
				$split[$i] .= $string;
				
				$retornoTPL .= $split[$i];
			}else{
				$retornoTPL .= $split[$i];
			}
		}
		
// 		if (sizeof($split) == 3) {
// 			$templateMgr->unregister_prefilter('outputFilter');
// 			$snippet = <<<'END'
// 				$this->assign("doiPlugin", PluginRegistry::getPlugin("generic", "DoiNoSumarioPlugin"))
// 				{if $doiPlugin->getEnabled()}
// 				{assign var="doi" value=$article->getStoredPubId('doi')}
// 				{if $doi}
// 				<div>
// 					<div class="tocDoi">
// 					<span><a href="http://dx.doi.org/{$doi|escape}">{$doi|escape}</a></span>
// 					</div>
// 				</div>
// 				{/if}
// 				{/if}
// END;
// 			$output = $split[0] . $split[1] . $snippet . $split[2];
// 		}
		return $retornoTPL;
	}

}

?>
