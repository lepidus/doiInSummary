<?php

/**
 * Copyright (c) 2015-2023 Lepidus Tecnologia
 * Distributed under the GNU GPL v3. For full terms see LICENSE or https://www.gnu.org/licenses/gpl-3.0.txt.
 */

import('lib.pkp.classes.plugins.GenericPlugin');

class DoiInSummaryPlugin extends GenericPlugin
{
    public function register($category, $path, $mainContextId = null)
    {

        if (!parent::register($category, $path, $mainContextId)) {
            return false;
        }

        if ($this->getEnabled($mainContextId)) {
            HookRegistry::register('Templates::Issue::Issue::Article', array($this, 'addDoiToArticleSummary'));

            $this->addLocaleData();
            $this->addDoiStyleSheet();
        }

        return true;
    }

    private function addDoiStyleSheet()
    {
        $request = Application::get()->getRequest();
        $url = $request->getBaseUrl() . '/' . $this->getPluginPath() . '/styles/doi.css';
        $templateMgr = TemplateManager::getManager($request);
        $templateMgr->addStyleSheet('doiCSS', $url);
    }

    public function addDoiToArticleSummary($hookName, $args)
    {
        $templateMgr = & $args[1];
        $output = & $args[2];

        $submission = $templateMgr->getTemplateVars('article');
        $doiUrl = $this->getArticleDoiUrl($submission);

        if (!is_null($doiUrl)) {
            $templateMgr->assign('doiUrl', $doiUrl);
            $output .= $templateMgr->fetch($this->getTemplateResource('doi_summary.tpl'));
        }
    }

    private function getArticleDoiUrl($article): ?string
    {
        $publication = $article->getCurrentPublication();
        $doi = $publication->getData('pub-id::doi');

        if (empty($doi)) {
            return null;
        }

        return "https://doi.org/$doi";
    }

    public function getDisplayName()
    {
        return __('plugins.generic.doiInSummary.displayName');
    }

    public function getDescription()
    {
        return __('plugins.generic.doiInSummary.description');
    }

    public function clearCache($hookName, $args)
    {
        $templateMgr = TemplateManager::getManager();
        $templateMgr->clearTemplateCache();
        return false;
    }

    public function getInstallSitePluginSettingsFile()
    {
        return $this->getPluginPath() . '/settings.xml';
    }
}
