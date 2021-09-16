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

}

?>