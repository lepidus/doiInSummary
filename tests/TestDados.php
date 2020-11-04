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
    
}