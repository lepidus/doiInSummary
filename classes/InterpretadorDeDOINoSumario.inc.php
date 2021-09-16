<?php

import('plugins.generic.doiNoSumario.classes.TitulosDaPagina');

class InterpretadorDeDOINoSumario{
    
    public function obterIdDaSubmissao($htlm) : array {       

        $TitulosDaPagina = new TitulosDaPagina(); 
        $blocosHTMLComTitulo = $TitulosDaPagina->obterTitulos($htlm);
        
        if(sizeof($blocosHTMLComTitulo) <= 1){
            return $htlm;
        }
        
        $idDaSubmissao = [];
        
        for ($indice = 0; $indice < sizeof($blocosHTMLComTitulo); $indice++) {
            if ($indice % 2 == 1) {
                preg_match('#.+view\/e*([0-9]*)#', $blocosHTMLComTitulo[$indice], $resultado);
                $idDaSubmissao[] = $resultado[1];
            }
        }
        return $idDaSubmissao;
    }

}