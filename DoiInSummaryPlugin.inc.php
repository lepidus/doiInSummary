<?php

/**
 * @file plugins/generic/doiInSummary/DoiInSummaryPlugin.inc.php
 *
 * Copyright (c) 2015-2023 Lepidus Tecnologia
 * Distributed under the GNU GPL v3. For full terms see LICENSE or https://www.gnu.org/licenses/gpl-3.0.txt.
 */

import('lib.pkp.classes.plugins.GenericPlugin');

class DoiInSummaryPlugin extends GenericPlugin
{
    public function register($category, $path, $mainContextId = null)
    {
        $success = parent::register($category, $path);

        if ($success && $this->getEnabled()) {
            HookRegistry::register('Templates::Issue::Issue::Article', [$this, 'addDoiToArticleSummary']);

            $this->addLocaleData();
            $this->addDoiStyleSheet();
        }

        return $success;
    }

    public function getDisplayName(): string
    {
        return __('plugins.generic.doiInSummary.displayName');
    }

    public function getDescription(): string
    {
        return __('plugins.generic.doiInSummary.description');
    }

    public function addDoiToArticleSummary(string $hookName, array $args): bool
    {
        $templateMgr = &$args[1];
        $output = &$args[2];

        $submission = $templateMgr->getTemplateVars('article');
        $doiUrl = $this->getArticleDoiUrl($submission);

        if (!is_null($doiUrl)) {
            $templateMgr->assign('doiUrl', $doiUrl);
            $output .= $templateMgr->fetch($this->getTemplateResource('doi_summary.tpl'));
        }

        return false;
    }

    private function getArticleDoiUrl(Submission $article): ?string
    {
        $publication = $article->getCurrentPublication();
        $doiObject = $publication->getData('doiObject');

        if (is_null($doiObject)) {
            return null;
        }

        $doiUrl = $doiObject->getData('resolvingUrl');

        return $doiUrl;
    }

    private function addDoiStyleSheet(): void
    {
        $request = Application::get()->getRequest();
        $templateMgr = TemplateManager::getManager($request);

        $url = $request->getBaseUrl() . '/' . $this->getPluginPath() . '/styles/doi.css';
        $templateMgr->addStyleSheet('doiCSS', $url);
    }
}
