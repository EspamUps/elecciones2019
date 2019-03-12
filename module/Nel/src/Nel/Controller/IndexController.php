<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Nel\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Nel\Metodos\Metodos;
use Nel\Metodos\Correo;
use Nel\Modelo\Entity\Sexo;
use Nel\Modelo\Entity\TipoCandidato;
use Nel\Modelo\Entity\Parroquia;
use Nel\Modelo\Entity\Listas;
use Zend\Session\Container;
use Zend\Db\Adapter\Adapter;

class IndexController extends AbstractActionController
{
    public $dbAdapter;

    public function indexAction()
    {
        set_time_limit(600);
        $this->layout('layout/encuesta');
        $this->dbAdapter=$this->getServiceLocator()->get('Zend\Db\Adapter');
        
        $objCandidato = new TipoCandidato($this->dbAdapter); 
        $objParroquia = new Parroquia($this->dbAdapter);
        $objLista = new Listas($this->dbAdapter);
        $objSexo = new Sexo($this->dbAdapter);
        
        $listaTiposCandidatos = $objCandidato->obtenerTipoCandidato();
        $optionTipoCandidato = '<option style="font-weight: bold;" value="0">SELECCIONE UN TIPO DE CANDIDATO</option>';
         foreach ($listaTiposCandidatos as $valueTiposCandidatos) {
            $optionTipoCandidato = $optionTipoCandidato.'<option style="font-weight: bold;" value="'.$valueTiposCandidatos['idTipoCandidato'].'">'.$valueTiposCandidatos['descripcionTipoCandidato'].'</option>';
        }
        
        $listaListasC = $objLista->obtenerListas();
        $optionListas = '<option style="font-weight: bold;" value="0">SELECCIONE UNA LISTA</option>';
         foreach ($listaListasC as $valueListas) {
            $optionListas = $optionListas.'<option style="font-weight: bold;" value="'.$valueListas['idLista'].'">'.$valueListas['nombreLista'].'</option>';
        }
        
         $listaParroquias = $objParroquia->obtenerParroquias();
         $optionParroquia = '<option style="font-weight: bold;" value="0">SELECCIONE UNA ZONA ELECTORAL</option>';
         foreach ($listaParroquias as $valueParroquias) {
            $optionParroquia = $optionParroquia.'<option style="font-weight: bold;" value="'.$valueParroquias['idParroquia'].'">'.$valueParroquias['nombreParroquia'].'</option>';
        }
        
          $listaSexo = $objSexo->obtenerSexo();
         $optionSexo = '<option style="font-weight: bold;" value="0">SELECCIONE UN SEXO</option>';
         foreach ($listaSexo as $valueSexo) {
            $optionSexo = $optionSexo.'<option style="font-weight: bold;" value="'.$valueSexo['idSexo'].'">'.$valueSexo['descripcionSexo'].'</option>';
        }
        
        $optionJunta= '<option style="font-weight: bold;" value="0">SELECCIONE UNA JUNTA</option>';

        $array = array(
            'optionTipoCandidato'=>$optionTipoCandidato,
            'optionParroquia'=>$optionParroquia,
            'optionSexo'=>$optionSexo,
            'optionListas'=>$optionListas,
            'optionJunta'=>$optionJunta
        );
        return new ViewModel($array);
    }
    
    public function resultadosAction()
    {
        set_time_limit(600);
        $this->layout('layout/encuesta');
       
        
        $array = array(
            
        );
        return new ViewModel($array);
    }
    
//    public function resultadosAction()
//    {
//        set_time_limit(600);
//        $this->layout('layout/encuesta');
//        $this->dbAdapter=$this->getServiceLocator()->get('Zend\Db\Adapter');
//        $objSectores = new Sector($this->dbAdapter);
//        $objParroquia = new Parroquia($this->dbAdapter);
//        $objRangoEdad = new RangoEdad($this->dbAdapter);
//        $objSexo = new Sexo($this->dbAdapter);
//        $objMetodos = new Metodos();
//        $listaSector = $objSectores->obtenerSector();
//        $optionSectores = '<option style="font-weight: bold;" value="0">SELECCIONE UN SECTOR</option>';
//        foreach ($listaSector as $valueSector) {
//            $idSectorEncriptado = $objMetodos->encriptar($valueSector['idSector']);
//            $optionSectores = $optionSectores.'<option style="font-weight: bold;" value="'.$idSectorEncriptado.'">'.$valueSector['descripcionSector'].'</option>';
//        }
//        
//        $listaParroquia = $objParroquia->obtenerParroquias();
//        $optionParroquias = '<option style="font-weight: bold;" value="0">SELECCIONE UNA PARRÓQUIA</option>';
//        foreach ($listaParroquia as $valueParroquia){
//            $idParroquiaEncriptado = $objMetodos->encriptar($valueParroquia['idParroquia']);
//            $optionParroquias = $optionParroquias.'<option style="font-weight: bold;" value="'.$idParroquiaEncriptado.'">'.$valueParroquia['descripcionParroquia'].'</option>';
//            
//        }
//         
//        $listaRangoEdad = $objRangoEdad->obtenerRangoEdad();
//        $optionRangoEdad = '<option style="font-weight: bold;" value="0">SELECCIONE UN RANGO DE EDAD</option>';
//        foreach ($listaRangoEdad as $valueRangoEdad) {
//            $idRangoEdadEncriptado = $objMetodos->encriptar($valueRangoEdad['idRangoEdad']);
//            $optionRangoEdad = $optionRangoEdad.'<option style="font-weight: bold;" value="'.$idRangoEdadEncriptado.'">'.$valueRangoEdad['descripcionEdad'].'</option>';
//        }
//        $listaSexo = $objSexo->obtenerSexo();
//        $optionSexo = '<option style="font-weight: bold;" value="0">SELECCIONE UN SEXO</option>';
//        foreach ($listaSexo as $valueSexo) {
//            $idSexoEncriptado = $objMetodos->encriptar($valueSexo['idSexo']);
//            $optionSexo = $optionSexo.'<option style="font-weight: bold;" value="'.$idSexoEncriptado.'">'.$valueSexo['descripcionSexo'].'</option>';
//        }
//        
//        
//        
//        $array = array(
//            'optionSectores'=>$optionSectores,
//            'optionSexo'=>$optionSexo,
//            'optionParroquias'=>$optionParroquias,
//            'optionRangoEdad'=>$optionRangoEdad
//        );
//        return new ViewModel($array);
//    }
//     
}