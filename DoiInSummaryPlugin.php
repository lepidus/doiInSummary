<?php

/**
 * @file plugins/generic/doiInSummary/DoiInSummaryPlugin.php
 *
 * Copyright (c) 2015-2026 Lepidus Tecnologia
 * Distributed under the GNU GPL v3. For full terms see LICENSE or https://www.gnu.org/licenses/gpl-3.0.txt.
 */

namespace APP\plugins\generic\doiInSummary;

use APP\core\Application;
use APP\submission\Submission;
use APP\template\TemplateManager;
use PKP\plugins\GenericPlugin;
use PKP\plugins\Hook;

class DoiInSummaryPlugin extends GenericPlugin
{
    public function register($category, $path, $mainContextId = null): bool
    {
        $success = parent::register($category, $path, $mainContextId);

        if ($success && $this->getEnabled($mainContextId)) {
            Hook::add('Templates::Issue::Issue::Article', $this->addDoiToArticleSummary(...));

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
        $templateMgr = $args[1];
        $output = &$args[2];

        $submission = $templateMgr->getTemplateVars('article');
        $doiUrl = $this->getArticleDoiUrl($submission);

        if ($doiUrl !== null) {
            $templateMgr->assign('doiUrl', $doiUrl);
            $output .= $templateMgr->fetch($this->getTemplateResource('doi_summary.tpl'));
        }

        return false;
    }

    private function getArticleDoiUrl(Submission $article): ?string
    {
        $doiUrl = $article->getCurrentPublication()
            ?->getData('doiObject')
            ?->getData('resolvingUrl');

        return $doiUrl ?: null;
    }

    private function addDoiStyleSheet(): void
    {
        $request = Application::get()->getRequest();
        $templateMgr = TemplateManager::getManager($request);
        $url = $request->getBaseUrl() . '/' . $this->getPluginPath() . '/styles/doi.css';

        $templateMgr->addStyleSheet(
            'doiCSS',
            $url,
            [
                'contexts' => ['frontend'],
            ]
        );
    }
}
