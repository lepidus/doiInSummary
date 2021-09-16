<?php

class TestDados{
    
    public function htmlFalho(){
        return '<!DOCTYPE html> <html> <head> <title></title> </head> <body> <h4 class="titulo"> </h4> </body> </html>';
    }

    public function htmlPadraoH3(){
        return '<!DOCTYPE html> <html> <head> <title></title> </head> <body> <h3 class="title"> </h3> </body> </html>';
    }

    public function htmlPadraoH4(){
        return '<!DOCTYPE html> <html> <head> <title></title> </head> <body> <h4 class="title"> </h4> </body> </html>';
    }

    public function htmlImmersion(){
        return '<!DOCTYPE html> <html> <head> <title></title> </head> <body> <h4 class="article__title"> </h4> </body> </html>';
    }

    public function htmlPadraoH4ComIdDaSumissao(){
        return '<!DOCTYPE html> <html> <head> <title></title> </head> 
                <body> 
                    <div class="sections"> 
                        <h4 class="title"> 
                            <a id="article-12704" href="http://localhost:8080/index.php/rpen/article/view/12704">
                                Artificial Intelligence, Human Rights and Disability
                            </a> 
                        </h4> 
                    </div>
                </body> </html>';
    }
    
    public function htmlPadraoH3ComIdDaSumissao(){
        return '<!DOCTYPE html> <html> <head> <title></title> </head> 
                <body> 
                    <div class="obj_article_summary">
                        <h3 class="title">
                            <a id="article-10818" href="http://localhost:8080/index.php/rca/article/view/e10818">
                                Diferentes Tipos de Reprodução Musical e sua Influência sobre o Tempo de Permanência dos Clientes em Restaurantes
                            </a>
                        </h3>
	                </div>
                </body> </html>';
    }
    public function htmlImmersionComIdDaSumissao(){
        return '<!DOCTYPE html> <html> <head> <title></title> </head> 
                <body> 
                    <div class="obj_article_summary">
                        <h4 class="article__title">
                            <a id="article-11161" href="http://localhost:8080/index.php/rca/article/view/e11161">
                                Contribuições do uso de Redes Sociais Virtuais para o Empreendedorismo Feminino
                            </a>
                        </h4>
                    </div>
                </body> </html>';
    }
}