<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Nel\Modelo\Entity;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Adapter\Adapter;

class CuerpoEncuesta extends TableGateway
{
    public function __construct(Adapter $adapter = null, $databaseSchema = null, ResultSet $selectResultPrototype = null)
    {
        return parent::__construct('cuerpoencuesta', $adapter, $databaseSchema, $selectResultPrototype);
    }

    public function filtrarCuerpoEncuestaPorOpcionPregunta($idOpcionPregunta)
    {
        return  $this->select(array('idOpcionPregunta'=>$idOpcionPregunta))->toArray();
    }
    
    public function filtrarCuerpoEncuestaPorPregunta($idPregunta)
    {
        return  $this->select(array('idPregunta'=>$idPregunta))->toArray();
    }
    
    public function ingresarCuerpoEncuesta($array)
    {
        $inserted = $this->insert($array);
        if($inserted)
        {
            return  $this->getLastInsertValue();
        }  else {
            return 0;
        }
    }
 
   
}