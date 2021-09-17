<?php

import('plugins.generic.doiNoSumario.classes.TitulosDaPagina');

class InterpretadorDeDOINoSumario{

    private $blocosHTMLComTituloEIdsDasSubmissoes;

    public function __construct(){
        $this->blocosHTMLComTituloEIdsDasSubmissoes = [];
    }

    public function defineBlocosHTMLComTituloEIdsDasSubmissoes($HTMLComTituloEIdsDasSubmissoes){
        $this->blocosHTMLComTituloEIdsDasSubmissoes = $HTMLComTituloEIdsDasSubmissoes;
    }

    public function recuperaBlocoHTMLComTituloAPatirDoIdDaSubmissao($idDaSubmissao){
        return $this->blocosHTMLComTituloEIdsDasSubmissoes[$idDaSubmissao];
    }
    
    public function obterIdDaSubmissao($htlm) : array {       

        $TitulosDaPagina = new TitulosDaPagina(); 
        $blocosHTMLComTitulo = $TitulosDaPagina->obterTitulos($htlm);
        
        if(sizeof($blocosHTMLComTitulo) <= 1){
            return $htlm;
        }
        
        $idsDasSubmissoes = [];
        
        for ($indice = 0; $indice < sizeof($blocosHTMLComTitulo); $indice++) {
            if ($indice % 2 == 1) {
                preg_match('#.+view\/e*([0-9]*)#', $blocosHTMLComTitulo[$indice], $resultado);
                $idDaSubmissao = $resultado[1];
                $idsDasSubmissoes[] = $idDaSubmissao;
                $this->blocosHTMLComTituloEIdsDasSubmissoes[$idDaSubmissao] = $blocosHTMLComTitulo[$indice];
            }
        }
        return $idsDasSubmissoes;
    }

    public function renderizarDoiNoSumario($publicacao) : string{
        
        $idDaSubmissao = $publicacao->_data['submissionId'];
        $blocoHTMLComTitulo = $this->blocosHTMLComTituloEIdsDasSubmissoes[$idDaSubmissao];

        if(isset($publicacao->_data['pub-id::doi'])){
            if(strlen($publicacao->_data['pub-id::doi']) > 0){
                
                $doiUrl = 'https://doi.org/' . $publicacao->_data['pub-id::doi'];
                            
                $doiDiv = "<div class='doiNoSumario'> DOI: <a href='" . $doiUrl . "'>" . $doiUrl . " </a> </div>";

                return $blocoHTMLComTitulo . $doiDiv;
            }
        }

        return $blocoHTMLComTitulo;
    }

}
