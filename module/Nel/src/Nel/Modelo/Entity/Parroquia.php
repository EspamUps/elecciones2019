<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Nel\Modelo\Entity;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Adapter\Adapter;

class Parroquia extends TableGateway
{
    public function __construct(Adapter $adapter = null, $databaseSchema = null, ResultSet $selectResultPrototype = null)
    {
        return parent::__construct('parroquia', $adapter, $databaseSchema, $selectResultPrototype);
    }
    

    
    public function obtenerParroquias()
    {
        return  $this->select()->toArray();
    }
    public function filtrarParroquia($idParroquia)
    {
        return  $this->select(array('idParroquia'=>$idParroquia))->toArray();
    }
    
    

 
   
}