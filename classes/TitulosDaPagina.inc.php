<?php

class TitulosDaPagina{

    public function obterTitulos($html){        
        $expressoesRegulares = [
            '<h4 class="title">' => '#(<h4 class="title">.*?</h4>)#s',
            '<h3 class="title">' => '#(<h3 class="title">.*?</h3>)#s',
            '<h4 class="article__title">' =>  '#(<h4 class="article__title">.*?</h4>)#s'
        ];
    
        foreach ($expressoesRegulares as $key => $value) {
            if (strpos($html, $key) ){
                return preg_split($value, $html,-1, PREG_SPLIT_DELIM_CAPTURE);
            }		
        }
        $htmlSemTitulos[0] = $html;
        return $htmlSemTitulos;
    }

    public function existeTitulos($blocosHtml){
        if(sizeof($blocosHtml) > 1 ){
            return true;
        }
        return false;
    }
     
}

