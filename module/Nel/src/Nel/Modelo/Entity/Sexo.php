<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Nel\Modelo\Entity;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Adapter\Adapter;

class Sexo extends TableGateway
{
    public function __construct(Adapter $adapter = null, $databaseSchema = null, ResultSet $selectResultPrototype = null)
    {
        return parent::__construct('sexo', $adapter, $databaseSchema, $selectResultPrototype);
    }
    

    
    public function obtenerSexo()
    {
        return  $this->select()->toArray();
    }
    
    public function filtrarSexo($idSexo)
    {
        return  $this->select(array('idSexo'=>$idSexo))->toArray();
    }
 
   
}