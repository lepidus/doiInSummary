<?php declare(string_types=1);

use PHPUnit\Framework\TestCase;

import('plugins.generic.doiNoSumario.tests.TestDados');
import('plugins.generic.doiNoSumario.classes.TitulosDaPagina');


class TitulosDaPaginaTest extends TestCase{
    
    public function testExisteHtmlComTagH3TemaPadrao(): void{
        
        $TitulosDaPagina = new TitulosDaPagina();
        $TestDados = new TestDados();
        
        $blocosHtml = $TitulosDaPagina->obterTitulos($TestDados->htmlPadraoH3());
        $this->assertEquals(
            true,
            $TitulosDaPagina->existeTitulos($blocosHtml)
        );
    }

    public function testExisteHtmlComTagH4TemaPadrao():void {
        
        $TitulosDaPagina = new TitulosDaPagina();
        $TestDados = new TestDados();
        
        $blocosHtml = $TitulosDaPagina->obterTitulos($TestDados->htmlPadraoH4());
        $this->assertEquals(
            true,
            $TitulosDaPagina->existeTitulos($blocosHtml)
        );

    }

    public function testExisteHtmlComTagH4TemaImmersion(){
        
        $TitulosDaPagina = new TitulosDaPagina();
        $TestDados = new TestDados();
        
        $blocosHtml = $TitulosDaPagina->obterTitulos($TestDados->htmlImmersion());
        $this->assertEquals(
            true,
            $TitulosDaPagina->existeTitulos($blocosHtml)
        );
        
    }
}

?>