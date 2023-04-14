<?php

/**
 * Copyright (c) 2015 Lepidus Tecnologia
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 */

import('lib.pkp.classes.plugins.GenericPlugin');

class DoiNoSumarioPlugin extends GenericPlugin {

    public function register($category, $path, $mainContextId = null){

        if (!parent::register($category, $path, $mainContextId)) {
            return false;
        }

        if($this->getEnabled($mainContextId)){
            HookRegistry::register('Templates::Issue::Issue::Article', array($this, 'addDoiToArticleSummary'));
    
            $this->addLocaleData();

            $request = Application::getRequest();
            $url = $request->getBaseUrl() . '/' . $this->getPluginPath() . '/doi.css';
            $templateMgr = TemplateManager::getManager($request);
            $templateMgr->addStyleSheet('doiCSS', $url);
        }

        return true;
    }

    public function addDoiToArticleSummary($hookName, $args)
    {
        $templateMgr =& $args[1];
        $output =& $args[2];

        $submission = $templateMgr->getTemplateVars('article');
        $doiUrl = $this->getArticleDoiUrl($submission);
        
        if(!is_null($doiUrl)) {
            $templateMgr->assign('doiUrl', $doiUrl);
            $output .= $templateMgr->fetch($this->getTemplateResource('doi_summary.tpl'));
        }
    }

    private function getArticleDoiUrl($article): ?string
    {
        $publication = $article->getCurrentPublication();
        $doi = $publication->getData('pub-id::doi');
        
        if(empty($doi)) return null;

        return "https://doi.org/$doi";
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
}