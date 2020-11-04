<?php declare(string_types=1);

use PHPUnit\Framework\TestCase;

require_once('tests/TestDados.php');
require_once('Mapeador.php');


class TestDoiNoSumarioPlugin extends TestCase{
    
    public function testTemaPadraoH3(): void{
        //teste para um  html com caracteristicas do tema padrão com h3/sumario
        $blocosHtml = Mapeador::mapearRegex(TestDados::htmlPadraoH3());
        $this->assertEquals(
            true,
            Mapeador::verificaTitulos($blocosHtml)
        );
    }

    public function testTemaPadraoH4():void {
        //teste para um html com caracteristicas do tema padrão com h4/pagina inicial
        $blocosHtml = Mapeador::mapearRegex(TestDados::htmlPadraoH4());
        $this->assertEquals(
            true,
            Mapeador::verificaTitulos($blocosHtml)
        );

    }

    public function testTemaImmersion(){
        //teste para um html generico com características do tema immersion h4
        $blocosHtml = Mapeador::mapearRegex(TestDados::htmlImmersion());
        $this->assertEquals(
            true,
            Mapeador::verificaTitulos($blocosHtml)
        );
        
    }
}

?>