<?php

/**
 * Standalone contract for the DOI link printed in the issue table of contents.
 * Run with: php tests/doi-url-contract.php
 */

function import($path)
{
}

class GenericPlugin
{
}

class DoiInSummaryContractPublication
{
    private $doi;

    public function __construct($doi)
    {
        $this->doi = $doi;
    }

    public function getData($key)
    {
        return $key === 'pub-id::doi' ? $this->doi : null;
    }
}

class DoiInSummaryContractArticle
{
    private $publication;

    public function __construct($doi)
    {
        $this->publication = new DoiInSummaryContractPublication($doi);
    }

    public function getCurrentPublication()
    {
        return $this->publication;
    }
}

require_once dirname(__DIR__) . '/DoiInSummaryPlugin.inc.php';

$failures = [];
$plugin = new DoiInSummaryPlugin();
$getArticleDoiUrl = new ReflectionMethod(DoiInSummaryPlugin::class, 'getArticleDoiUrl');
$getArticleDoiUrl->setAccessible(true);

$cases = [
    '10.1234/abc.2026' => 'https://doi.org/10.1234/abc.2026',
    '10.1234/x"><img src=x onerror=alert(1)>' => 'https://doi.org/10.1234/x%22%3e%3cimg%20src=x%20onerror=alert(1)%3e',
    '10.1234/a%b#c{d' => 'https://doi.org/10.1234/a%25b%23c%7bd',
];
foreach ($cases as $doi => $expectedUrl) {
    $url = $getArticleDoiUrl->invoke($plugin, new DoiInSummaryContractArticle($doi));
    if ($url !== $expectedUrl) {
        $failures[] = "DOI {$doi} must resolve to {$expectedUrl}, got {$url}";
    }
}
if ($getArticleDoiUrl->invoke($plugin, new DoiInSummaryContractArticle('')) !== null) {
    $failures[] = 'An article without DOI must not produce a link';
}

$template = file_get_contents(dirname(__DIR__) . '/templates/doi_summary.tpl');
$outputCount = substr_count($template, '{$doiUrl');
if ($outputCount === 0 || substr_count($template, '{$doiUrl|escape}') !== $outputCount) {
    $failures[] = 'Every {$doiUrl} output in templates/doi_summary.tpl must use |escape';
}

if ($failures) {
    fwrite(STDERR, implode("\n", $failures) . "\n");
    exit(1);
}

fwrite(STDOUT, "DOI summary URL contract satisfied.\n");
