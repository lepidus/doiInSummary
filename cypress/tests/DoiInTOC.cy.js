describe('DOI In TOC plugin tests', function () {
    it('Creates and exercises a static page', function () {
        const title = 'The Signalling Theory Dividends';

        cy.login('admin', 'admin', 'publicknowledge');

        cy.get('.app__nav a').contains('Website').click();
        cy.get('button[id="plugins-button"]').click();

        cy.get('input[id^="select-cell-doiinsummaryplugin-enabled"]').click();
        cy.get('div:contains(\'The plugin "DOI in TOC" has been enabled.\')');

        cy.visit('/index.php/publicknowledge/dois');
        cy.get('.doiListPanel .listPanel__item:contains(' + title + ') .listPanel__itemActions .expander').click();
        cy.get('.doiListPanel .listPanel__item:contains(' + title + ') button:contains(Edit)').click();
        cy.get('.doiListPanel .listPanel__item:contains(' + title + ') table input[type="text"]').type('10.1234/a6f4l3');
        cy.get('.doiListPanel .listPanel__item:contains(' + title + ') button:contains(Save)').click();

        cy.visit('/index.php/publicknowledge');
        cy.get('.obj_article_summary:contains(' + title + ') .doiInSummary a').contains('10.1234/a6f4l3').and('have.attr', 'href', 'https://doi.org/10.1234/a6f4l3');
    });
})