<?php

/**
 * Copyright (c) 2015 Lepidus Tecnologia
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 */

import('lib.pkp.classes.plugins.GenericPlugin');
import('classes.publication.PublicationDAO');

class DoiNoSumarioPlugin extends GenericPlugin {

    public function register($category, $path, $mainContextId = null){

        if (!parent::register($category, $path, $mainContextId)) {
            return false;
        }

        if($this->getEnabled($mainContextId)){
            HookRegistry::register('TemplateManager::display', array($this, 'templateManagerCallback'));
        
            //adicionando idiomas para o plugin
            $this->addLocaleData();

            $request = Application::getRequest();
            $url = $request->getBaseUrl() . '/' . $this->getPluginPath() . '/doi.css';
            $templateMgr = TemplateManager::getManager($request);
            $templateMgr->addStyleSheet('doiCSS', $url);
        }

        return true;
    }

    public function getDisplayName(){
        return __('plugins.generic.doiNoSumario.displayName');
    }

    public function getDescription(){
        return __('plugins.generic.doiNoSumario.description');
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
        $split = preg_split('#(<h4 class="title">.*?</h4>)#s', $output, -1, PREG_SPLIT_DELIM_CAPTURE);
        if(strpos($output, '<h4 class="title">')){
            $split = preg_split('#(<h4 class="title">.*?</h4>)#s', $output, -1, PREG_SPLIT_DELIM_CAPTURE);
        }
        if(strpos($output, '<h3 class="title">')){
            $split = preg_split('#(<h3 class="title">.*?</h3>)#s', $output, -1, PREG_SPLIT_DELIM_CAPTURE);
        }

        // verificando se as tags "title existem, se não existirem"
        // o $split só retorna no primeiro indice a página completa
        // sem os registros encontrados, ou seja, o vetor ficará com tamanho (1)
        if(sizeof($split) <= 1){
            return $output;
        }

		//instanciando um article para buscar pelo id
        $PublicationDAO = new PublicationDAO();

        for ($i = 0; $i < sizeof($split); $i++) {

            if ($i % 2 !== 0) {

                preg_match('#.+view\/([0-9]*)#', $split[$i], $obj);

				$publication = $PublicationDAO->getById($obj[1]);
				
				// adicionado if para verificar se DOI existe
				if(isset($publication->_data['pub-id::doi'])){

					if(strlen($publication->_data['pub-id::doi']) > 0){
						
						$doiUrl = 'https://doi.org/' . $publication->_data['pub-id::doi'];

						$string = "<div class='doiNoSumario'> <a href='" . $doiUrl . "'>" . $doiUrl . " </a> </div>";

						$split[$i] .= $string;
					}
				}

                $newTpl .= $split[$i];
            } else {
                $newTpl .= $split[$i];   
            }
        }

        $templateMgr->unregisterFilter('output', array($this, 'addDoi'));
        return $newTpl;
    }

}