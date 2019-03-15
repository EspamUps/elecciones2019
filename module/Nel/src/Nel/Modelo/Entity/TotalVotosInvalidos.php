<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Nel\Modelo\Entity;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Adapter\Adapter;

class TotalVotosInvalidos extends TableGateway
{
    public function __construct(Adapter $adapter = null, $databaseSchema = null, ResultSet $selectResultPrototype = null)
    {
        return parent::__construct('totalvotosinvalidos', $adapter, $databaseSchema, $selectResultPrototype);
    }
    
 
     public function filtrarTotalVotosInvalidosPorTipoCandidatoPorTipoVotoPorJuntaElectoral($TipoVotoInvalido,$idTipoCandidato,$idConfigurarJuntaElectoral)
    {
        return  $this->select(array('idConfigurarJuntaElectoral'=>$idConfigurarJuntaElectoral, 'idTipoCandidato'=>$idTipoCandidato,'idTipoVotoInvalido'=>$TipoVotoInvalido))->toArray();
    }
    
     public function filtrarTotalVotosInvalidosPorTipoCandidatoPorJuntaElectoral($idTipoCandidato,$idConfigurarJuntaElectoral)
    {
        return  $this->select(array('idConfigurarJuntaElectoral'=>$idConfigurarJuntaElectoral, 'idTipoCandidato'=>$idTipoCandidato))->toArray();
    }
    
    
   public function ingresarTotalVotosInvalidos($array)
        {
            $inserted = $this->insert($array);
            if($inserted)
            {
                return  TRUE;
            }  else {
                return FALSE;
            }
        }
     
    public function editarTotalVotosInvalidos($idTotalVotosInvalidos,$arrayDatos)
    {
        return (bool)$this->update($arrayDatos,array('idTotalVotosInvalidos=?'=>$idTotalVotosInvalidos));
    }
   
}