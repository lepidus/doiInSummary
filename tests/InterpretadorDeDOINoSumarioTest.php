<?php

use PHPUnit\Framework\TestCase;

import('plugins.generic.doiNoSumario.tests.TestDados');
import('plugins.generic.doiNoSumario.classes.InterpretadorDeDOINoSumario');


class InterpretadorDeDOINoSumarioTest extends TestCase{

    public function testRecuperacaoDoIdDaSubmissaoEmHtmlPadraoH4(): void{
        
        $InterpretadorDeDOINoSumario = new InterpretadorDeDOINoSumario();
        $TestDados = new TestDados();
        
        $idDaSubmissao = $InterpretadorDeDOINoSumario->obterIdDaSubmissao($TestDados->htmlPadraoH4ComIdDaSumissao());

        $idDaSubmissaoEsperado = [12704];

        $this->assertEquals(
            $idDaSubmissaoEsperado,
            $idDaSubmissao
        );
    }

    public function testRecuperacaoDoIdDaSubmissaoEmHtmlPadraoH3(): void{
        
        $InterpretadorDeDOINoSumario = new InterpretadorDeDOINoSumario();
        $TestDados = new TestDados();
        
        $idDaSubmissao = $InterpretadorDeDOINoSumario->obterIdDaSubmissao($TestDados->htmlPadraoH3ComIdDaSumissao());

        $idDaSubmissaoEsperado = [10818];

        $this->assertEquals(
            $idDaSubmissaoEsperado,
            $idDaSubmissao
        );
    }

    public function testRecuperacaoDoIdDaSubmissaoEmHtmlImmersion(): void{
        
        $InterpretadorDeDOINoSumario = new InterpretadorDeDOINoSumario();
        $TestDados = new TestDados();
        
        $idDaSubmissao = $InterpretadorDeDOINoSumario->obterIdDaSubmissao($TestDados->htmlImmersionComIdDaSumissao());

        $idDaSubmissaoEsperado = [11161];

        $this->assertEquals(
            $idDaSubmissaoEsperado,
            $idDaSubmissao
        );
    }

    public function testCriacaoDeDIVDaPublicacaoComDOI(): void{

        $publication = (object) Array(
            '_data' => Array
            (
                'id' => '10023',
                'submissionId' => '10818',
                'pub-id::doi' => '10.5020/2318-0722.2021.27.1.11439'
            )
        );

        $TestDados = new TestDados();
        $blocoHTMLComIdDaSubmissao = Array(
            '10818' => $TestDados->tituloHtmlPadraoH3ComIdDaSumissao()
        );
        
        $DIVDaPublicacaoComDOI = $this->criacaoDoInterpretadorERenderizacao($blocoHTMLComIdDaSubmissao,$publication);

        $DIVDaPublicacaoComDOIEsperada = $TestDados->tituloHtmlPadraoH3ComIdDaSumissaoComDOI();

        $this->assertEquals(
            $DIVDaPublicacaoComDOIEsperada,
            $DIVDaPublicacaoComDOI
        );
    }

    public function testCriacaoDeDIVDaPublicacaoSemDOI(): void{

        $publication = (object) Array(
            '_data' => Array
            (
                'id' => '10023',
                'submissionId' => '10818',
            )
        );

        $TestDados = new TestDados();
        $blocoHTMLComIdDaSubmissao = Array(
            '10818' => $TestDados->tituloHtmlPadraoH3ComIdDaSumissao()
        );
        
        $DIVDaPublicacaoSemDOI = $this->criacaoDoInterpretadorERenderizacao($blocoHTMLComIdDaSubmissao,$publication);

        $DIVDaPublicacaoSemDOIEsperada = $TestDados->tituloHtmlPadraoH3ComIdDaSumissao();

        $this->assertEquals(
            $DIVDaPublicacaoSemDOIEsperada,
            $DIVDaPublicacaoSemDOI
        );
    }

    public function criacaoDoInterpretadorERenderizacao($blocoHTMLComIdDaSubmissao,$publication){
        $InterpretadorDeDOINoSumario = new InterpretadorDeDOINoSumario();
        $InterpretadorDeDOINoSumario->defineBlocosHTMLComTituloEIdsDasSubmissoes($blocoHTMLComIdDaSubmissao);
        
        return $InterpretadorDeDOINoSumario->renderizarDoiNoSumario($publication);
    }

}

?>