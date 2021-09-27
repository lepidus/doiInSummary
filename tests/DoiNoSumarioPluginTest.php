<?php

use PHPUnit\Framework\TestCase;

import('plugins.generic.doiNoSumario.DoiNoSumarioPlugin');


class DoiNoSumarioPluginTest extends TestCase{

    public function testSubstituicaoDoHTMLSemCaracteresEspeciaisDoRegex(): void{
        
        $InterpretadorDeDOINoSumario = new DoiNoSumarioPlugin();
        $TestDados = new TestDados();
        
        $HTMLComTituloEIdDaSumissao = $TestDados->tituloHtmlPadraoH3ComIdDaSumissao();
        $HTMLComTituloEIdDaSumissaoEDOI = $TestDados->tituloHtmlPadraoH3ComIdDaSumissaoComDOI();
        $novoHTML = $InterpretadorDeDOINoSumario->substituiHtml($HTMLComTituloEIdDaSumissao,$HTMLComTituloEIdDaSumissaoEDOI, $HTMLComTituloEIdDaSumissao);

        $novoHTMLEsperado = $TestDados->tituloHtmlPadraoH3ComIdDaSumissaoComDOI();

        $this->assertEquals(
            $novoHTMLEsperado,
            $novoHTML
        );
    }

    public function testSubstituicaoDoHTMLComCaracteresEspeciaisDoRegex(): void{
        
        $InterpretadorDeDOINoSumario = new DoiNoSumarioPlugin();
        $TestDados = new TestDados();
        
        $HTMLComTituloEIdDaSumissao = $TestDados->tituloHtmlPadraoH4ComIdDaSumissao();
        $HTMLComTituloEIdDaSumissaoEDOI = $TestDados->tituloHtmlPadraoH4ComIdDaSumissaoComDOI();
        $novoHTML = $InterpretadorDeDOINoSumario->substituiHtml($HTMLComTituloEIdDaSumissao, $HTMLComTituloEIdDaSumissaoEDOI, $HTMLComTituloEIdDaSumissao);

        $novoHTMLEsperado = $TestDados->tituloHtmlPadraoH4ComIdDaSumissaoComDOI();

        $this->assertEquals(
            $novoHTMLEsperado,
            $novoHTML
        );
    }
}

?>