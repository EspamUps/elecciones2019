<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Nel\Modelo\Entity;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Adapter\Adapter;

class Sector extends TableGateway
{
    public function __construct(Adapter $adapter = null, $databaseSchema = null, ResultSet $selectResultPrototype = null)
    {
        return parent::__construct('sector', $adapter, $databaseSchema, $selectResultPrototype);
    }
    

    
    public function obtenerSector()
    {
        return  $this->select()->toArray();
    }
    
    public function filtrarSector($idSector)
    {
        return  $this->select(array('idSector'=>$idSector))->toArray();
    }
 
   
}