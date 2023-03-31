<?php

/**
 * Copyright (c) 2015 Lepidus Tecnologia
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 */

import('lib.pkp.classes.plugins.GenericPlugin');
import('classes.publication.PublicationDAO');
import('plugins.generic.doiNoSumario.classes.InterpretadorDeDOINoSumario');

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

        $InterpretadorDeDOINoSumario = new InterpretadorDeDOINoSumario();
        $idsDasSubmissoes = $InterpretadorDeDOINoSumario->obterIdDaSubmissao($output);

        if (empty($idsDasSubmissoes)){
            return $output;
        }
        else{
            foreach ($idsDasSubmissoes as $idDaSubmissao) {
                $SubmissionDAO = new SubmissionDAO();
				$submissao = $SubmissionDAO->getById($idDaSubmissao);
                if(empty($submissao)) {
                    error_log("Submissão inexistente: $idDaSubmissao");
                    continue;
                }
                
                $publicacao = $submissao->getCurrentPublication();
                $DIVdaPublicacaoComDOI = $InterpretadorDeDOINoSumario->renderizarDoiNoSumario($publicacao);

                $BlocoHTMLComTitulo = $InterpretadorDeDOINoSumario->recuperaBlocoHTMLComTituloAPatirDoIdDaSubmissao($idDaSubmissao);

                $output = $this->substituiHtml($BlocoHTMLComTitulo,$DIVdaPublicacaoComDOI,$output);
			}
        }

        return $output;
    }

    public function substituiHtml($BlocoHTMLComTitulo,$DIVdaPublicacaoComDOI,$output){

        $regex = $BlocoHTMLComTitulo;

        $regex = addslashes($regex);
        $regex = str_replace("\'","'",$regex);
        $regex = str_replace('\"','"',$regex); 

        $regex = addcslashes($regex, '(,),?,[,],{,},|,^,$,*,+,-,.,—,/,\'');

        $regex = "'".$regex."'";

        return preg_replace($regex,$DIVdaPublicacaoComDOI,$output);

    }
}