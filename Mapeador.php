<?php

/*
Classe que mapeia o regex de acordo com a existência da chave no html de entrada
*/

class Mapeador{

    public function mapearRegex($output){        
        $expressoesRegulares = [
            '<h4 class="title">' => '#(<h4 class="title">.*?</h4>)#s',
            '<h3 class="title">' => '#(<h3 class="title">.*?</h3>)#s',
            '<h4 class="article__title">' =>  '#(<h4 class="article__title">.*?</h4>)#s'
        ];
    
        foreach ($expressoesRegulares as $key => $value) {
            if (strpos($output, $key) ){
                return preg_split($value, $output,-1, PREG_SPLIT_DELIM_CAPTURE);
            }		
        }
        // saída padronizada para um vetor teste
        $htmlSemTitulos[0] = $output;
        return $htmlSemTitulos;
    }

    // verifica se a função mapearRegex identificou algum titulo no codigo html
    public function verificaTitulos($blocosHtml){
        if(sizeof($blocosHtml) > 1 ){
            return true;
        }
        return false;
    }

        
}

