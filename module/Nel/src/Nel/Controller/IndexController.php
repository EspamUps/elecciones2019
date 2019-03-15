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
        
        $optionListas = '<option style="font-weight: bold;" value="0">SELECCIONE UNA LISTA</option>';
        
         $optionParroquia = '<option style="font-weight: bold;" value="0">SELECCIONE UNA ZONA ELECTORAL</option>';
         
        
          $listaSexo = $objSexo->obtenerSexo();
         $optionSexo = '<option style="font-weight: bold;" value="0">SELECCIONE UN SEXO</option>';
         foreach ($listaSexo as $valueSexo) {
            $optionSexo = $optionSexo.'<option style="font-weight: bold;" value="'.$valueSexo['idSexo'].'">'.$valueSexo['descripcionSexo'].'</option>';
        }
        
        $optionJunta= '<option style="font-weight: bold;" value="0">SELECCIONE UNA JUNTA</option>';
        
        $optionTipoVoto = '<option value="0">VOTOS NORMALES</option><option value="1">VOTOS ESPECIALES</option>';

        $array = array(
            'optionTipoCandidato'=>$optionTipoCandidato,
            'optionParroquia'=>$optionParroquia,
            'optionSexo'=>$optionSexo,
            'optionListas'=>$optionListas,
            'optionJunta'=>$optionJunta,
            'optionTipoVoto'=>$optionTipoVoto
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
}