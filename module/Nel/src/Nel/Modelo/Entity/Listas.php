<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Nel\Modelo\Entity;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Adapter\Adapter;

class Listas extends TableGateway
{
    public function __construct(Adapter $adapter = null, $databaseSchema = null, ResultSet $selectResultPrototype = null)
    {
        return parent::__construct('listas', $adapter, $databaseSchema, $selectResultPrototype);
    }
    

    
  public function obtenerListas()
    {
        return  $this->select()->toArray();
    }
    
     public function filtrarLista($idLista)
    {
        return  $this->select(array('idLista'=>$idLista))->toArray();
    }
 
   
}