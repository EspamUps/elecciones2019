<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Nel\Modelo\Entity;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Adapter\Adapter;

class TipoCandidato extends TableGateway
{
    public function __construct(Adapter $adapter = null, $databaseSchema = null, ResultSet $selectResultPrototype = null)
    {
        return parent::__construct('tipocandidato', $adapter, $databaseSchema, $selectResultPrototype);
    }
    

    
    public function obtenerTipoCandidato()
    {
        return  $this->select()->toArray();
    }  
    
    public function filtrarTipoCandidato($idTipoCandidato)
    {
        return  $this->select(array('idTipoCandidato'=>$idTipoCandidato))->toArray();
    }

 
   
}