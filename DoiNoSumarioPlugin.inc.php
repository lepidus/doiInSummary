<?php

/**
 * Copyright (c) 2015 Lepidus Tecnologia
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 */

import('lib.pkp.classes.plugins.GenericPlugin');
import('classes.article.ArticleDAO');

class DoiNoSumarioPlugin extends GenericPlugin {

    public function register($category, $path, $mainContextId = null){
		
		error_log("FUNÇÃO REGISTER CHAMADA");

        if (!parent::register($category, $path, $mainContextId)) {
            return false;
        }

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

    public function getDisplayName(){
        return __('plugins.generic.doiInSummary.displayName');
    }

    public function getDescription(){
        return __('plugins.generic.doiInSummary.description');
    }

    public function clearCache($hookName, $args){
        $templateMgr = TemplateManager::getManager();
        $templateMgr->clearTemplateCache();
        return false;
    }

    public function getInstallSitePluginSettingsFile(){
        return $this->getPluginPath() . '/settings.xml';
    }

    public function templateManagerCallback($hookName, $args){

        switch ($args[1]) {
            case "frontend/pages/indexJournal.tpl":
            case "frontend/pages/issue.tpl":
				$templateMgr = $args[0];
                $templateMgr->registerFilter('output', array($this, 'addDoi'));
            	break;
        }

    }

    public function addDoi($output, $templateMgr){

		//verificando se o tpl final corresponde a página totalmente compilada
        if ($templateMgr->source->filepath !== "app:frontendpagesissue.tpl" && $templateMgr->source->filepath !== "app:frontendpagesindexJournal") {
            return $output;
        }

		// usando expressão regular para pegar todas as divs "title"
        $split = preg_split('#(<div class="title">.*?</div>)#s', $output, -1, PREG_SPLIT_DELIM_CAPTURE);

		//instanciando um article para buscar pelo id
		$ArticleDAO = new ArticleDAO();

        for ($i = 0; $i < sizeof($split); $i++) {
            if ($i % 2 !== 0) {

                preg_match('#.+view\/([0-9]*)#', $split[$i], $obj);

				$article = $ArticleDAO->getById($obj[1]);
				
				//TODO adicionar if para verificar se DOI existe
				if(isset($article->_data['pub-id::doi'])){

					if(strlen($article->_data['pub-id::doi']) > 0){
						
						$doiUrl = 'https://doi.org/' . $article->_data['pub-id::doi'];

						$string = "<div class='doiNoSumario'> <span> DOI: </span> <a href='" . $doiUrl . "'>" . $doiUrl . " </a> </div>";

						$split[$i] .= $string;
					}
				}

                $newTpl .= $split[$i];
            } else {
                $newTpl .= $split[$i];
            }
        }

        return $newTpl;
    }

}