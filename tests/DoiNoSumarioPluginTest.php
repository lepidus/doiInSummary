<?php


import('lib.pkp.tests.PKPTestCase');
import('plugins.generic.doiNoSumario.DoiNoSumarioPlugin');


class DoiNoSumarioPluginTest extends PKPTestCase {

    public function testHTMLDentroDoPadraoRegexParaRecuperarIdDaSubmissao(){
		$blocoHTML = '<h4 class="title">
                            <a id="article-10910" href="http://localhost:8080/index.php/tec/article/view/10910">
                                Utilização de melaço de soja (resíduo) da produção de proteína de soja para a geração de biometano em reator UASB (Upflow Anaerobic Sludge Blanket)
                            </a>
                     </h4>';

        $DoiNoSumarioPlugin = new DoiNoSumarioPlugin();
        $idObtido = $DoiNoSumarioPlugin->recuperaIdDaSubmissao($blocoHTML);

        $idEsperado = "10910";

		$this->assertEquals($idEsperado,$idObtido);
	}

    public function testHTMLAdicionadoAoPadraoRegexParaRecuperarIdDaSubmissao(){
		$blocoHTML = '<h4 class="title">
                            <a id="article-9303" href="http://localhost:8080/index.php/rmes/article/view/e9303">
                                Reescrevendo o Percurso da Psicologia Existencial: Um Retorno a Kierkegaard
                            </a>
                    </h4>';

        $DoiNoSumarioPlugin = new DoiNoSumarioPlugin();
        $idObtido = $DoiNoSumarioPlugin->recuperaIdDaSubmissao($blocoHTML);

        $idEsperado = "9303";

		$this->assertEquals($idEsperado,$idObtido);
	}

}

?>