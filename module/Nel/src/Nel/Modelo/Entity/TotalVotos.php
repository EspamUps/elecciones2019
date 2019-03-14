<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Nel\Modelo\Entity;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Adapter\Adapter;

class TotalVotos extends TableGateway
{
    public function __construct(Adapter $adapter = null, $databaseSchema = null, ResultSet $selectResultPrototype = null)
    {
        return parent::__construct('totalVotos', $adapter, $databaseSchema, $selectResultPrototype);
    }
    

    
        public function ingresarTotalVotos($array)
        {
            $inserted = $this->insert($array);
            if($inserted)
            {
                return  TRUE;
            }  else {
                return FALSE;
            }
        }
     
    public function editarTotalVotos($idTotalVotos,$arrayDatos)
    {
        return (bool)$this->update($arrayDatos,array('idTotalVotos=?'=>$idTotalVotos));
    }
    public function filtrarTotalVotosPorCandidatoPorJuntaElectoral($idCandidato,$idConfigurarJuntaElectoral)
    {
        return  $this->select(array('idConfigurarJunta'=>$idConfigurarJuntaElectoral, 'idCandidato'=>$idCandidato))->toArray();
    }

 
   
}