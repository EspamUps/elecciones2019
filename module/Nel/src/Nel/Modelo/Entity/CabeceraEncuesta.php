<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Nel\Modelo\Entity;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Adapter\Adapter;

class CabeceraEncuesta extends TableGateway
{
    public function __construct(Adapter $adapter = null, $databaseSchema = null, ResultSet $selectResultPrototype = null)
    {
        return parent::__construct('cabeceraencuesta', $adapter, $databaseSchema, $selectResultPrototype);
    }

    public function obtenerCabeceraEncuesta()
    {
        return  $this->select()->toArray();
    }
    public function filtrarCabeceraEncuestaPorRangoEdad($idRangoEdad)
    {
        return  $this->select(array('idRangoEdad'=>$idRangoEdad))->toArray();
    }
    public function filtrarCabeceraEncuestaPorSector($idSector)
    {
        return  $this->select(array('idSector'=>$idSector))->toArray();
    }
    public function filtrarCabeceraEncuestaPorSexo($idSexo)
    {
        return  $this->select(array('idSexo'=>$idSexo))->toArray();
    }
    
    public function filtrarCabeceraEncuestaPorParroquia($idParroquia)
    {
        return  $this->select(array('idParroquia'=>$idParroquia))->toArray();
    }
    
    public function filtrarCabeceraEncuestaPorParroquiaSector($idParroquia,$idSector)
    {
        return  $this->select(array('idParroquia'=>$idParroquia,'idSector'=>$idSector))->toArray();
    }
    
    public function ingresarCabeceraEncuesta($array)
    {
        $inserted = $this->insert($array);
        if($inserted)
        {
            return  $this->getLastInsertValue();
        }  else {
            return 0;
        }
    }
 
    
    public function eliminarCabeceraEncuesta($idCabeceraEncuesta)
    {
        return $this->delete(array('idCabeceraEncuesta=?'=>$idCabeceraEncuesta));
    }
   
}