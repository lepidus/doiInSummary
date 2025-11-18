<div id="doi_article-{$article->getId()}" class='doiInTOC'>
    <strong>
        {capture assign=translatedDOI}{translate key="doi.readerDisplayName"}{/capture}
        {translate key="semicolon" label=$translatedDOI}
    </strong>
    <a href="{$doiUrl|escape}">
        {$doiUrl}
    </a>
</div>

<script>
    function insertAfter(newNode, referenceNode) {ldelim}
        referenceNode.parentNode.insertBefore(newNode, referenceNode.nextSibling);
    {rdelim}

    function updateDoiPosition(){ldelim}
        const doiDiv = document.getElementById('doi_article-{$article->getId()}');
        const articleSummary = doiDiv.parentNode;
        const titlesDefault = articleSummary.getElementsByClassName('title');
        const titlesImmersion = articleSummary.getElementsByClassName('article__title');

        if(titlesDefault.length > 0) {ldelim}
            let title = titlesDefault[0];
            insertAfter(doiDiv, title);
        {rdelim}
        else if(titlesImmersion.length > 0) {ldelim}
            let title = titlesImmersion[0];
            insertAfter(doiDiv, title);
        {rdelim}
    {rdelim}

    updateDoiPosition();
</script>
