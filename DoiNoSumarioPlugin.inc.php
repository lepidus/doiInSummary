<?php

/**
 * Copyright (c) 2015 Lepidus Tecnologia
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 */

import('lib.pkp.classes.plugins.GenericPlugin');
import('classes.publication.PublicationDAO');
require_once('TitulosDaPagina.php');

class DoiNoSumarioPlugin extends GenericPlugin {

    public function register($category, $path, $mainContextId = null){

        if (!parent::register($category, $path, $mainContextId)) {
            return false;
        }

        if($this->getEnabled($mainContextId)){
            HookRegistry::register('TemplateManager::display', array($this, 'templateManagerCallback'));
    
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

		if ($templateMgr->source->filepath !== "app:frontendpagesissue.tpl" && $templateMgr->source->filepath !== "app:frontendpagesindexJournal") {
            return $output;
        }
        
        $SubmissionDAO = new SubmissionDAO();
        $TitulosDaPagina = new TitulosDaPagina(); 
        $blocosHTML = $TitulosDaPagina->obterTitulos($output);

        if(sizeof($blocosHTML) <= 1){
            return $output;
        }

        for ($i = 0; $i < sizeof($blocosHTML); $i++) {
            
            if ($i % 2 !== 0) {
                $idDaSubmissao = $this->recuperaIdDaSubmissao($blocosHTML[$i]);
            
                $submissao = $SubmissionDAO->getById($idDaSubmissao);
                $publicacao = $submissao->getCurrentPublication();

				if(isset($publicacao->_data['pub-id::doi'])){
					if(strlen($publicacao->_data['pub-id::doi']) > 0){
						
                        $doiUrl = 'https://doi.org/' . $publicacao->_data['pub-id::doi'];
                                    
						$doiDiv = "<div class='doiNoSumario'> DOI: <a href='" . $doiUrl . "'>" . $doiUrl . " </a> </div>";

						$blocosHTML[$i] .= $doiDiv;
					}
				}
                $novoTpl .= $blocosHTML[$i];
            } else {
                $novoTpl .= $blocosHTML[$i];   
            }
        }

        $templateMgr->unregisterFilter('output', array($this, 'adicionaDoi'));
        return $novoTpl;
    }

    public function recuperaIdDaSubmissao($blocoHTML){

        preg_match('#.+view\/([0-9]*)#', $blocoHTML, $resultado); 

        $idDaSubmissao = $resultado[1];

        if (empty($idDaSubmissao)){
            preg_match('#.+view\/e([0-9]*)#', $blocoHTML, $resultado); 
            $idDaSubmissao = $resultado[1];
        }

        return $idDaSubmissao;
    }

}