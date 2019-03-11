<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Nel\Modelo\Entity;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Adapter\Adapter;

class OpcionesPregunta extends TableGateway
{
    public function __construct(Adapter $adapter = null, $databaseSchema = null, ResultSet $selectResultPrototype = null)
    {
        return parent::__construct('opcionespregunta', $adapter, $databaseSchema, $selectResultPrototype);
    }
    

    

    public function filtrarOpcionesPregunta($idOpcionPregunta)
    {
        return  $this->select(array('idOpcionPregunta'=>$idOpcionPregunta))->toArray();
    }
    public function filtrarOpcionesPreguntaPorPregunta($idPregunta)
    {
        return  $this->select(array('idPregunta'=>$idPregunta))->toArray();
    }
    
//    public function filtrarOpcionesPreguntaPorCandidato($idCandidato)
//    {
//        return  $this->select(array('idCandidato'=>$idCandidato))->toArray();
//    }
    
    

 
   
}