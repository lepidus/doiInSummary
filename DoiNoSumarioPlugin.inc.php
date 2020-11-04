<?php

/**
 * Copyright (c) 2015 Lepidus Tecnologia
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 */

import('lib.pkp.classes.plugins.GenericPlugin');
import('classes.publication.PublicationDAO');
require_once('Mapeador.php');

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
                $templateMgr->registerFilter('output', array($this, 'adicionaDoi'));
            	break;
        }
    }

    public function adicionaDoi($output, $templateMgr){

		//verificando se o tpl final corresponde a página totalmente compilada
        if ($templateMgr->source->filepath !== "app:frontendpagesissue.tpl" && $templateMgr->source->filepath !== "app:frontendpagesindexJournal") {
            return $output;
        }
        
        
        // coleta blocos h3 ou h4, no codigo html, de titulos dos artigos 
        $blocosHTML = Mapeador::mapearRegex($output);

        // verificando se as tags "title existem, se não existirem"
        // o $blocosHTML só retorna no primeiro indice a página completa
        // sem os registros encontrados, ou seja, o vetor ficará com tamanho (1)
        if(sizeof($blocosHTML) <= 1){
            return $output;
        }

		//instanciando um article para buscar pelo id
        $PublicationDAO = new PublicationDAO();

        for ($i = 0; $i < sizeof($blocosHTML); $i++) {

            if ($i % 2 !== 0) {

                preg_match('#.+view\/([0-9]*)#', $blocosHTML[$i], $objeto);

				$publicacao = $PublicationDAO->getById($objeto[1]);
				
				// adicionado if para verificar se DOI existe
				if(isset($publicacao->_data['pub-id::doi'])){

					if(strlen($publicacao->_data['pub-id::doi']) > 0){
						
                        $doiUrl = 'https://doi.org/' . $publicacao->_data['pub-id::doi'];
                        
						$doiDiv = "<div class='doiNoSumario'> DOI: <a href='" . $doiUrl . "'>" . $doiUrl . " </a> </div>";

						$blocosHTML[$i] .= $doiDiv;
					}
				}
                //variavel $newTpl para $novoTpl
                $novoTpl .= $blocosHTML[$i];
            } else {
                $novoTpl .= $blocosHTML[$i];   
            }
        }

        $templateMgr->unregisterFilter('output', array($this, 'adicionaDoi'));
        return $novoTpl;
    }

}