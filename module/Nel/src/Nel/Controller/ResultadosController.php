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
use Zend\View\Model\JsonModel;
use Nel\Metodos\Metodos;
use Nel\Modelo\Entity\Sector;
use Nel\Modelo\Entity\Preguntas;
use Nel\Modelo\Entity\Parroquia;
use Nel\Modelo\Entity\RangoEdad;
use Nel\Modelo\Entity\Sexo;
use Nel\Modelo\Entity\CabeceraEncuesta;
use Nel\Modelo\Entity\CuerpoEncuesta;
use Zend\Db\Adapter\Adapter;

class ResultadosController extends AbstractActionController
{
//    public $dbAdapter;
//        public function filtrarresultadoporsexoAction()
//    {
//        $mensaje = '<div class="alert alert-danger text-center" role="alert">OCURRIÓ UN ERROR INESPERADO</div>';
//        $validar = false;
//        $request=$this->getRequest();
//        if(!$request->isPost()){
//            $this->redirect()->toUrl($this->getRequest()->getBaseUrl().'/index/index');
//        }else{
//            $this->dbAdapter=$this->getServiceLocator()->get('Zend\Db\Adapter');
//            $objCabeceraEncuesta = new CabeceraEncuesta($this->dbAdapter);
//            $objPreguntas = new Preguntas($this->dbAdapter);
//            $objSexo = new Sexo($this->dbAdapter);
//            $objMetodos = new Metodos();
//            $post = array_merge_recursive(
//                $request->getPost()->toArray(),
//                $request->getFiles()->toArray()
//            );
//            
//            $idSexoEncriptado = $post['idSexoEncriptado'];
//            
//            if($idSexoEncriptado == "" || $idSexoEncriptado == "0" || $idSexoEncriptado == NULL){
//                $mensaje = '<div class="alert alert-danger text-center" role="alert">SELECCIONE UN SECTOR</div>';
//            }else{
//                $idSexo = $objMetodos->desencriptar($idSexoEncriptado);
//                $listaSexo = $objSexo->filtrarSexo($idSexo);
//                if(count($listaSexo) == 0){
//                    $mensaje = '<div class="alert alert-danger text-center" role="alert">EL RANGO DE EDAD SELECCIONADO NO EXISTE EN LA BASE DE DATOS</div>';
//                }else {
//                    $listaCabeceraEncuesta= $objCabeceraEncuesta->filtrarCabeceraEncuestaPorSexo($idSexo);
//                    if(count($listaCabeceraEncuesta) == 0){
//                        $mensaje = '<div class="alert alert-danger text-center" role="alert">AÚN NO SE HAN REALIZADO ENCUESTAS</div>';
//                    }else{
//                        $listaPreguntas = $objPreguntas->obtenerPreguntas();
//                        $arrayAlcalde = array();
//                        $arrayConcejal = array();
//                        $arrayPrefecto = array();
//                        $arrayJunta = array();
//                           
//                        $iAlcalde = 0;
//                        $iConcejal = 0;
//                        $iPrefecto = 0;
//                        $iJunta = 0;
//                        $preguntaAlcalde = "";
//                        $preguntaConcejal = "";
//                        $preguntaPrefecto ="";
//                        $preguntaJunta ="";
//                        $contadorAlcalde = 0;
//                        $contadorConcejal = 0;
//                        $contadorPrefecto = 0;
//                        $contadorJunta = 0;
//                        $filaAlcalde = "";
//                        $filaConcejal ="";
//                        $filaPrefecto = "";
//                        $filaJunta = "";
//                        $totalVotosSumadosAlcalde = 0;
//                        $totalVotosSumadosConcejal = 0;
//                        $totalVotosSumadosPrefecto = 0;
//                        $totalVotosSumadosJunta = 0;
//                        foreach ($listaPreguntas as $valuePreguntas) {
//                            $idPregunta = $valuePreguntas['idPregunta'];
//
//
//                            $listaOpcionesPregunta = $this->dbAdapter->query("select count(cuerpoencuesta.idOpcionPregunta) as total, opcionespregunta.idOpcionPregunta, tipocandidato.identificadorTipoCandidato,candidatos.nombreCandidato
//                            from cuerpoencuesta right join opcionespregunta on cuerpoencuesta.idOpcionPregunta = opcionespregunta.idOpcionPregunta
//                            inner join candidatos on opcionespregunta.idCandidato = candidatos.idCandidato
//                            inner join tipocandidato on candidatos.idTipoCandidato = tipocandidato.idTipoCandidato
//                            inner join cabeceraencuesta on cuerpoencuesta.idCabeceraEncuesta = cabeceraencuesta.idCabeceraEncuesta
//                            where opcionespregunta.idPregunta = $idPregunta and cabeceraencuesta.idSexo = $idSexo
//                            group by opcionespregunta.idCandidato
//                            order by total desc
//                            ",Adapter::QUERY_MODE_EXECUTE)->toArray();
//                            $listaCuerpoEncuestaPorPregunta = $this->dbAdapter->query("select *
//                                from cabeceraencuesta inner join cuerpoencuesta on cabeceraencuesta.idCabeceraEncuesta = cuerpoencuesta.idCabeceraEncuesta
//                                where cabeceraencuesta.idSexo = $idSexo and  cuerpoencuesta.idPregunta = $idPregunta
//                                ",Adapter::QUERY_MODE_EXECUTE)->toArray();
//                            
//                            foreach ($listaOpcionesPregunta as $valueOpcionesPregunta) {
//                                $idOpcionPregunta = $valueOpcionesPregunta['idOpcionPregunta'];
//                                $listaCuerpoEncuesta = $this->dbAdapter->query("select *
//                                from cabeceraencuesta inner join cuerpoencuesta on cabeceraencuesta.idCabeceraEncuesta = cuerpoencuesta.idCabeceraEncuesta
//                                where cabeceraencuesta.idSexo = $idSexo and  cuerpoencuesta.idOpcionPregunta = $idOpcionPregunta
//                                ",Adapter::QUERY_MODE_EXECUTE)->toArray();
//                                $totalVotos = count($listaCuerpoEncuesta);
//                                    if($valueOpcionesPregunta['identificadorTipoCandidato'] == 1){
//
//                                        $contadorAlcalde = (count($listaCuerpoEncuesta)/  count($listaCuerpoEncuestaPorPregunta))*100;
//                                         $arrayAlcalde[$iAlcalde] = array(
//                                            'value'=>$contadorAlcalde,
//                                            'label'=>$valueOpcionesPregunta['nombreCandidato'],
//                                        );
//                                        $iAlcalde++;
//                                        $filaAlcalde = $filaAlcalde.'<tr><td>'.$iAlcalde.'</td><td>'.$valueOpcionesPregunta['nombreCandidato'].'</td><td>'.$totalVotos.'</td><td>'.number_format($contadorAlcalde,2).'%</td></tr>';
//                                        $totalVotosSumadosAlcalde = $totalVotosSumadosAlcalde + $totalVotos;
//                                        
//                                    }else if($valueOpcionesPregunta['identificadorTipoCandidato'] == 2){
//                                        $contadorConcejal = (count($listaCuerpoEncuesta)/  count($listaCuerpoEncuestaPorPregunta))*100;
//                                         $arrayConcejal[$iConcejal] = array(
//                                            'value'=>$contadorConcejal,
//                                            'label'=>$valueOpcionesPregunta['nombreCandidato'],
//                                        );
//                                        $iConcejal++;
//                                         $filaConcejal = $filaConcejal.'<tr><td>'.$iConcejal.'</td><td>'.$valueOpcionesPregunta['nombreCandidato'].'</td><td>'.$totalVotos.'</td><td>'.number_format($contadorConcejal,2).'%</td></tr>';
//                                         $totalVotosSumadosConcejal = $totalVotosSumadosConcejal + $totalVotos;
//                                         
//                                    }else if($valueOpcionesPregunta['identificadorTipoCandidato'] == 3){
//                                        $contadorPrefecto = (count($listaCuerpoEncuesta)/  count($listaCuerpoEncuestaPorPregunta))*100;
//                                         $arrayPrefecto[$iPrefecto] = array(
//                                            'value'=>$contadorPrefecto,
//                                            'label'=>$valueOpcionesPregunta['nombreCandidato'],
//                                        );
//                                        $iPrefecto++;
//                                        $filaPrefecto = $filaPrefecto.'<tr><td>'.$iPrefecto.'</td><td>'.$valueOpcionesPregunta['nombreCandidato'].'</td><td>'.$totalVotos.'</td><td>'.number_format($contadorPrefecto,2).'%</td></tr>';
//                                    
//                                        $totalVotosSumadosPrefecto = $totalVotosSumadosPrefecto + $totalVotos;
//                                    }else if($valueOpcionesPregunta['identificadorTipoCandidato'] == 4){
//                                        $contadorJunta = ($totalVotos/  count($listaCuerpoEncuestaPorPregunta))*100;
//                                         $arrayJunta[$iJunta] = array(
//                                            'value'=>$contadorJunta,
//                                            'label'=>$valueOpcionesPregunta['nombreCandidato'],
//                                        );
//                                        $iJunta++;
//                                        $filaJunta = $filaJunta.'<tr><td>'.$iJunta.'</td><td>'.$valueOpcionesPregunta['nombreCandidato'].'</td><td>'.$totalVotos.'</td><td>'.number_format($contadorJunta,2).'%</td></tr>';
//
//                                         $totalVotosSumadosJunta = $totalVotosSumadosJunta + $totalVotos;
//                                    }
//                                
//
//                            }
//                            if($valuePreguntas['identificadorPregunta'] == 1){
//                                $preguntaAlcalde = $valuePreguntas['descripcionPregunta'];
//                            }else if($valuePreguntas['identificadorPregunta'] == 2){
//                                $preguntaConcejal = $valuePreguntas['descripcionPregunta'];
//                            }else if($valuePreguntas['identificadorPregunta'] == 3){
//                                $preguntaPrefecto = $valuePreguntas['descripcionPregunta'];
//                            }else if($valuePreguntas['identificadorPregunta'] == 4){
//                                $preguntaJunta = $valuePreguntas['descripcionPregunta'];
//                            }
//                        }
//                        
//                            $tablaAlcalde = '<div class="table-responsive"><table class="table">
//                                        <thead>
//                                            <tr style="background-color: #B3D5FC;">
//                                                <th>#</th>
//                                                <th>CANDIDATO(A)</th>
//                                                <th>TOTAL</th>
//                                                <th>%</th>
//                                            </tr>
//                                        </tehad>
//                                        <tbody>
//                                            '.$filaAlcalde.'
//                                                <tr style="background-color: #B3D5FC;">
//                                            <td colspan="2">TOTAL</td>
//                                            <td>'.$totalVotosSumadosAlcalde.'</td>
//                                            <td></td>
//                                        </tr>
//                                        </tbody>
//                                    </table></div>';
//                        
//                        
//                        $tablaConcejal = '<div class="table-responsive"><table class="table">
//                                    <thead>
//                                        <tr style="background-color: #B3D5FC;">
//                                            <th>#</th>
//                                            <th>CANDIDATO(A)</th>
//                                            <th>TOTAL</th>
//                                            <th>%</th>
//                                        </tr>
//                                    </tehad>
//                                    <tbody>
//                                        '.$filaConcejal.'
//                                        <tr style="background-color: #B3D5FC;">
//                                            <td colspan="2">TOTAL</td>
//                                            <td>'.$totalVotosSumadosConcejal.'</td>
//                                                <td></td>
//                                        </tr>
//                                    </tbody>
//                                </table></div>';
//                        $tablaPrefecto = '<div class="table-responsive"><table class="table">
//                                    <thead>
//                                        <tr style="background-color: #B3D5FC;">
//                                            <th>#</th>
//                                            <th>CANDIDATO(A)</th>
//                                            <th>TOTAL</th>
//                                            <th>%</th>
//                                        </tr>
//                                    </tehad>
//                                    <tbody>
//                                        '.$filaPrefecto.'
//                                            <tr style="background-color: #B3D5FC;">
//                                                <td colspan="2">TOTAL</td>
//                                                <td>'.$totalVotosSumadosPrefecto.'</td>
//                                                    <td></td>
//                                            </tr>
//                                    </tbody>
//                                </table></div>';
//                        $tablaJunta = '<div class="table-responsive"><table class="table">
//                                    <thead>
//                                        <tr style="background-color: #B3D5FC;">
//                                            <th>#</th>
//                                            <th>CANDIDATO(A)</th>
//                                            <th>TOTAL</th>
//                                            <th>%</th>
//                                        </tr>
//                                    </tehad>
//                                    <tbody>
//                                        '.$filaJunta.'
//                                            <tr style="background-color: #B3D5FC;">
//                                                <td colspan="2">TOTAL</td>
//                                                <td>'.$totalVotosSumadosJunta.'</td>
//                                                    <td></td>
//                                            </tr>
//                                    </tbody>
//                                </table></div>';
//                        
//                        
//                        $mensaje = '';
//                        $validar = TRUE;
//                         return new JsonModel(array(
//                            'mensaje'=>$mensaje,
//                            'validar'=>$validar,
//                            'preguntaAlcalde'=>$preguntaAlcalde,
//                            'charAlcalde'=>$arrayAlcalde,
//                            'tablaAlcalde'=>$tablaAlcalde,
//                            'preguntaConcejal'=>$preguntaConcejal,
//                            'tablaConcejal'=>$tablaConcejal,
//                            'charConcejal'=>$arrayConcejal,
//                            'preguntaPrefecto'=>$preguntaPrefecto,
//                            'tablaPrefecto'=>$tablaPrefecto,
//                            'charPrefecto'=>$arrayPrefecto,
//                             'preguntaJunta'=>$preguntaJunta,
//                            'tablaJunta'=>$tablaJunta,
//                            'charJunta'=>$arrayJunta,
//                            ));
//                    }
//                }
//            
//            }
//            
//        }
//        return new JsonModel(array('mensaje'=>$mensaje,'validar'=>$validar));
//    }
//    
//    
//    
//    public function filtrarresultadoporrangoedadAction()
//    {
//        $mensaje = '<div class="alert alert-danger text-center" role="alert">OCURRIÓ UN ERROR INESPERADO</div>';
//        $validar = false;
//        $request=$this->getRequest();
//        if(!$request->isPost()){
//            $this->redirect()->toUrl($this->getRequest()->getBaseUrl().'/index/index');
//        }else{
//            $this->dbAdapter=$this->getServiceLocator()->get('Zend\Db\Adapter');
//            $objCabeceraEncuesta = new CabeceraEncuesta($this->dbAdapter);
//            $objPreguntas = new Preguntas($this->dbAdapter);
//            $objRangoEdad = new RangoEdad($this->dbAdapter);
//            $objMetodos = new Metodos();
//            $post = array_merge_recursive(
//                $request->getPost()->toArray(),
//                $request->getFiles()->toArray()
//            );
//            
//            $idRangoEdadEncriptado = $post['idRangoEdadEncriptado'];
//            
//            if($idRangoEdadEncriptado == "" || $idRangoEdadEncriptado == "0" || $idRangoEdadEncriptado == NULL){
//                $mensaje = '<div class="alert alert-danger text-center" role="alert">SELECCIONE UN SECTOR</div>';
//            }else{
//                $idRangoEdad = $objMetodos->desencriptar($idRangoEdadEncriptado);
//                $listaRangoEdad = $objRangoEdad->filtrarRangoEdad($idRangoEdad);
//                if(count($listaRangoEdad) == 0){
//                    $mensaje = '<div class="alert alert-danger text-center" role="alert">EL RANGO DE EDAD SELECCIONADO NO EXISTE EN LA BASE DE DATOS</div>';
//                }else {
//                    $listaCabeceraEncuesta= $objCabeceraEncuesta->filtrarCabeceraEncuestaPorRangoEdad($idRangoEdad);
//                    if(count($listaCabeceraEncuesta) == 0){
//                        $mensaje = '<div class="alert alert-danger text-center" role="alert">AÚN NO SE HAN REALIZADO ENCUESTAS</div>';
//                    }else{
//                        $listaPreguntas = $objPreguntas->obtenerPreguntas();
//                        $arrayAlcalde = array();
//                        $arrayConcejal = array();
//                        $arrayPrefecto = array();
//                        $arrayJunta = array();
//                        $iAlcalde = 0;
//                        $iConcejal = 0;
//                        $iPrefecto = 0;
//                        $iJunta = 0;
//                        $preguntaAlcalde = "";
//                        $preguntaConcejal = "";
//                        $preguntaPrefecto ="";
//                        $preguntaJunta ="";
//                        $contadorAlcalde = 0;
//                        $contadorConcejal = 0;
//                        $contadorPrefecto = 0;
//                        $contadorJunta = 0;
//                        $filaAlcalde = "";
//                        $filaConcejal ="";
//                        $filaPrefecto = "";
//                        $filaJunta = "";
//                        $totalVotosSumadosAlcalde = 0;
//                        $totalVotosSumadosConcejal = 0;
//                        $totalVotosSumadosPrefecto = 0;
//                        $totalVotosSumadosJunta = 0;
//                        
//                        foreach ($listaPreguntas as $valuePreguntas) {
//                            $idPregunta = $valuePreguntas['idPregunta'];
//
//
//                            $listaOpcionesPregunta = $this->dbAdapter->query("select count(cuerpoencuesta.idOpcionPregunta) as total, opcionespregunta.idOpcionPregunta, tipocandidato.identificadorTipoCandidato,candidatos.nombreCandidato
//                            from cuerpoencuesta right join opcionespregunta on cuerpoencuesta.idOpcionPregunta = opcionespregunta.idOpcionPregunta
//                            inner join candidatos on opcionespregunta.idCandidato = candidatos.idCandidato
//                            inner join tipocandidato on candidatos.idTipoCandidato = tipocandidato.idTipoCandidato
//                            inner join cabeceraencuesta on cuerpoencuesta.idCabeceraEncuesta = cabeceraencuesta.idCabeceraEncuesta
//                            where opcionespregunta.idPregunta = $idPregunta and cabeceraencuesta.idRangoEdad = $idRangoEdad
//                            group by opcionespregunta.idCandidato
//                            order by total desc
//                            ",Adapter::QUERY_MODE_EXECUTE)->toArray();
//                            $listaCuerpoEncuestaPorPregunta = $this->dbAdapter->query("select *
//                                from cabeceraencuesta inner join cuerpoencuesta on cabeceraencuesta.idCabeceraEncuesta = cuerpoencuesta.idCabeceraEncuesta
//                                where cabeceraencuesta.idRangoEdad = $idRangoEdad and  cuerpoencuesta.idPregunta = $idPregunta
//                                ",Adapter::QUERY_MODE_EXECUTE)->toArray();
//                            
//                            foreach ($listaOpcionesPregunta as $valueOpcionesPregunta) {
//                                $idOpcionPregunta = $valueOpcionesPregunta['idOpcionPregunta'];
//                                $listaCuerpoEncuesta = $this->dbAdapter->query("select *
//                                from cabeceraencuesta inner join cuerpoencuesta on cabeceraencuesta.idCabeceraEncuesta = cuerpoencuesta.idCabeceraEncuesta
//                                where cabeceraencuesta.idRangoEdad = $idRangoEdad and  cuerpoencuesta.idOpcionPregunta = $idOpcionPregunta
//                                ",Adapter::QUERY_MODE_EXECUTE)->toArray();
//                                $totalVotos = count($listaCuerpoEncuesta);
//                                    if($valueOpcionesPregunta['identificadorTipoCandidato'] == 1){
//
//                                        $contadorAlcalde = (count($listaCuerpoEncuesta)/  count($listaCuerpoEncuestaPorPregunta))*100;
//                                         $arrayAlcalde[$iAlcalde] = array(
//                                            'value'=>$contadorAlcalde,
//                                            'label'=>$valueOpcionesPregunta['nombreCandidato'],
//                                        );
//                                        $iAlcalde++;
//                                        $filaAlcalde = $filaAlcalde.'<tr><td>'.$iAlcalde.'</td><td>'.$valueOpcionesPregunta['nombreCandidato'].'</td><td>'.$totalVotos.'</td><td>'.number_format($contadorAlcalde,2).'%</td></tr>';
//                                        $totalVotosSumadosAlcalde = $totalVotosSumadosAlcalde + $totalVotos;
//                                        
//                                    }else if($valueOpcionesPregunta['identificadorTipoCandidato'] == 2){
//                                        $contadorConcejal = (count($listaCuerpoEncuesta)/  count($listaCuerpoEncuestaPorPregunta))*100;
//                                         $arrayConcejal[$iConcejal] = array(
//                                            'value'=>$contadorConcejal,
//                                            'label'=>$valueOpcionesPregunta['nombreCandidato'],
//                                        );
//                                        $iConcejal++;
//                                         $filaConcejal = $filaConcejal.'<tr><td>'.$iConcejal.'</td><td>'.$valueOpcionesPregunta['nombreCandidato'].'</td><td>'.$totalVotos.'</td><td>'.number_format($contadorConcejal,2).'%</td></tr>';
//                                         $totalVotosSumadosConcejal = $totalVotosSumadosConcejal + $totalVotos;
//                                         
//                                    }else if($valueOpcionesPregunta['identificadorTipoCandidato'] == 3){
//                                        $contadorPrefecto = (count($listaCuerpoEncuesta)/  count($listaCuerpoEncuestaPorPregunta))*100;
//                                         $arrayPrefecto[$iPrefecto] = array(
//                                            'value'=>$contadorPrefecto,
//                                            'label'=>$valueOpcionesPregunta['nombreCandidato'],
//                                        );
//                                        $iPrefecto++;
//                                        $filaPrefecto = $filaPrefecto.'<tr><td>'.$iPrefecto.'</td><td>'.$valueOpcionesPregunta['nombreCandidato'].'</td><td>'.$totalVotos.'</td><td>'.number_format($contadorPrefecto,2).'%</td></tr>';
//                                    
//                                        $totalVotosSumadosPrefecto = $totalVotosSumadosPrefecto + $totalVotos;
//                                    }else if($valueOpcionesPregunta['identificadorTipoCandidato'] == 4){
//                                        $contadorJunta = ($totalVotos/  count($listaCuerpoEncuestaPorPregunta))*100;
//                                         $arrayJunta[$iJunta] = array(
//                                            'value'=>$contadorJunta,
//                                            'label'=>$valueOpcionesPregunta['nombreCandidato'],
//                                        );
//                                        $iJunta++;
//                                        $filaJunta = $filaJunta.'<tr><td>'.$iJunta.'</td><td>'.$valueOpcionesPregunta['nombreCandidato'].'</td><td>'.$totalVotos.'</td><td>'.number_format($contadorJunta,2).'%</td></tr>';
//
//                                         $totalVotosSumadosJunta = $totalVotosSumadosJunta + $totalVotos;
//                                    }
//                                
//
//                            }
//                            if($valuePreguntas['identificadorPregunta'] == 1){
//                                $preguntaAlcalde = $valuePreguntas['descripcionPregunta'];
//                            }else if($valuePreguntas['identificadorPregunta'] == 2){
//                                $preguntaConcejal = $valuePreguntas['descripcionPregunta'];
//                            }else if($valuePreguntas['identificadorPregunta'] == 3){
//                                $preguntaPrefecto = $valuePreguntas['descripcionPregunta'];
//                            }else if($valuePreguntas['identificadorPregunta'] == 4){
//                                $preguntaJunta = $valuePreguntas['descripcionPregunta'];
//                            }
//                        }
//                        
//                        $tablaAlcalde = '<div class="table-responsive"><table class="table">
//                                    <thead>
//                                        <tr style="background-color: #B3D5FC;">
//                                            <th>#</th>
//                                            <th>CANDIDATO(A)</th>
//                                            <th>TOTAL</th>
//                                            <th>%</th>
//                                        </tr>
//                                    </tehad>
//                                    <tbody>
//                                        '.$filaAlcalde.'
//                                            <tr style="background-color: #B3D5FC;">
//                                        <td colspan="2">TOTAL</td>
//                                        <td>'.$totalVotosSumadosAlcalde.'</td>
//                                            <td></td>
//                                    </tr>
//                                    </tbody>
//                                </table></div>';
//                        
//                        
//                        $tablaConcejal = '<div class="table-responsive"><table class="table">
//                                    <thead>
//                                        <tr style="background-color: #B3D5FC;">
//                                            <th>#</th>
//                                            <th>CANDIDATO(A)</th>
//                                            <th>TOTAL</th>
//                                            <th>%</th>
//                                        </tr>
//                                    </tehad>
//                                    <tbody>
//                                        '.$filaConcejal.'
//                                        <tr style="background-color: #B3D5FC;">
//                                            <td colspan="2">TOTAL</td>
//                                            <td>'.$totalVotosSumadosConcejal.'</td>
//                                                <td></td>
//                                        </tr>
//                                    </tbody>
//                                </table></div>';
//                        $tablaPrefecto = '<div class="table-responsive"><table class="table">
//                                    <thead>
//                                        <tr style="background-color: #B3D5FC;">
//                                            <th>#</th>
//                                            <th>CANDIDATO(A)</th>
//                                            <th>TOTAL</th>
//                                            <th>%</th>
//                                        </tr>
//                                    </tehad>
//                                    <tbody>
//                                        '.$filaPrefecto.'
//                                            <tr style="background-color: #B3D5FC;">
//                                                <td colspan="2">TOTAL</td>
//                                                <td>'.$totalVotosSumadosPrefecto.'</td>
//                                                    <td></td>
//                                            </tr>
//                                    </tbody>
//                                </table></div>';
//                               $tablaJunta = '<div class="table-responsive"><table class="table">
//                                    <thead>
//                                        <tr style="background-color: #B3D5FC;">
//                                            <th>#</th>
//                                            <th>CANDIDATO(A)</th>
//                                            <th>TOTAL</th>
//                                            <th>%</th>
//                                        </tr>
//                                    </tehad>
//                                    <tbody>
//                                        '.$filaJunta.'
//                                            <tr style="background-color: #B3D5FC;">
//                                                <td colspan="2">TOTAL</td>
//                                                <td>'.$totalVotosSumadosJunta.'</td>
//                                                    <td></td>
//                                            </tr>
//                                    </tbody>
//                                </table></div>';
//                        
//                        
//                        $mensaje = '';
//                        $validar = TRUE;
//                         return new JsonModel(array(
//                            'mensaje'=>$mensaje,
//                            'validar'=>$validar,
//                            'preguntaAlcalde'=>$preguntaAlcalde,
//                            'charAlcalde'=>$arrayAlcalde,
//                            'tablaAlcalde'=>$tablaAlcalde,
//                            'preguntaConcejal'=>$preguntaConcejal,
//                            'tablaConcejal'=>$tablaConcejal,
//                            'charConcejal'=>$arrayConcejal,
//                            'preguntaPrefecto'=>$preguntaPrefecto,
//                            'tablaPrefecto'=>$tablaPrefecto,
//                            'charPrefecto'=>$arrayPrefecto,
//                             'preguntaJunta'=>$preguntaJunta,
//                            'tablaJunta'=>$tablaJunta,
//                            'charJunta'=>$arrayJunta,
//                            ));
//                        
//  
//                    }
//                }
//            
//            }
//            
//        }
//        return new JsonModel(array('mensaje'=>$mensaje,'validar'=>$validar));
//    }
//    
//    
//    
//    
//    
//    
//    
//    
//    
//    
//    
//    
//    
//    
//    
//    
//    
//    
//    
//    
//    
//    
//    
//    
//    
//    
//    
//    public function filtrarresultadoporparroquiaAction()
//    {
//        $mensaje = '<div class="alert alert-danger text-center" role="alert">OCURRIÓ UN ERROR INESPERADO</div>';
//        $validar = false;
//        $request=$this->getRequest();
//        if(!$request->isPost()){
//            $this->redirect()->toUrl($this->getRequest()->getBaseUrl().'/index/index');
//        }else{
//            $this->dbAdapter=$this->getServiceLocator()->get('Zend\Db\Adapter');
//            $objCabeceraEncuesta = new CabeceraEncuesta($this->dbAdapter);
//            $objPreguntas = new Preguntas($this->dbAdapter);
//            $objParroquia = new Parroquia($this->dbAdapter);
//            $objSector = new Sector($this->dbAdapter);
//            $objMetodos = new Metodos();
//            $post = array_merge_recursive(
//                $request->getPost()->toArray(),
//                $request->getFiles()->toArray()
//            );
//            
//            $idParroquiaEncriptado = $post['idParroquiaEncriptado'];
//            
//            if($idParroquiaEncriptado == "" || $idParroquiaEncriptado == "0" || $idParroquiaEncriptado == NULL){
//                $mensaje = '<div class="alert alert-danger text-center" role="alert">SELECCIONE UN SECTOR</div>';
//            }else{
//                $idParroquia = $objMetodos->desencriptar($idParroquiaEncriptado);
//                $listaParroquia = $objParroquia->filtrarParroquia($idParroquia);
//                if(count($listaParroquia) == 0){
//                    $mensaje = '<div class="alert alert-danger text-center" role="alert">LA PARRÓQUIA SELECCIONADA NO EXISTE EN LA BASE DE DATOS</div>';
//                }else {
//                    $listaCabeceraEncuesta= $objCabeceraEncuesta->filtrarCabeceraEncuestaPorParroquia($idParroquia);
//                    if(count($listaCabeceraEncuesta) == 0){
//                        $mensaje = '<div class="alert alert-danger text-center" role="alert">AÚN NO SE HAN REALIZADO ENCUESTAS</div>';
//                    }else{
//                        $listaCabeceraEncuestaUrbano = $this->dbAdapter->query("select *
//                        from cabeceraencuesta 
//                        inner join sector on cabeceraencuesta.idSector = sector.idSector
//                        where cabeceraencuesta.idParroquia = $idParroquia and sector.identificadorSector = 1
//                        ",Adapter::QUERY_MODE_EXECUTE)->toArray();
//                        $listaCabeceraEncuestaRural = $this->dbAdapter->query("select *
//                        from cabeceraencuesta 
//                        inner join sector on cabeceraencuesta.idSector = sector.idSector
//                        where cabeceraencuesta.idParroquia = $idParroquia and sector.identificadorSector = 2
//                        ",Adapter::QUERY_MODE_EXECUTE)->toArray();
//                        
//                        
//                        
//                        $listaPreguntas = $objPreguntas->obtenerPreguntas();
//                        $arrayAlcalde = array();
//                        $arrayConcejal = array();
//                        $arrayPrefecto = array();
//                        $arrayJunta = array();
//                        $arrayAlcaldeRural = array();
//                        $arrayConcejalRural = array();
//                        $arrayPrefectoRural = array();
//                        $arrayJuntaRural = array();
//                        $iAlcalde = 0;
//                        $iConcejal = 0;
//                        $iPrefecto = 0;
//                        $iJunta = 0;
//                        $preguntaAlcalde = "";
//                        $preguntaConcejal = "";
//                        $preguntaPrefecto ="";
//                        $preguntaJunta ="";
//                        $contadorVotosAlcaldeUrbano = 0;
//                        $contadorVotosAlcaldeRural = 0;
//                        $contadorVotosConcejalUrbano = 0;
//                        $contadorVotosConcejalRural = 0;
//                        $contadorVotosPrefectoUrbano = 0;
//                        $contadorVotosPrefectoRural = 0;
//                        $contadorVotosJuntaUrbano = 0;
//                        $contadorVotosJuntaRural = 0;
//                        $filaAlcalde = "";
//                        $filaConcejal ="";
//                        $filaPrefecto = "";
//                        $filaJunta = "";
//                        $totalVotosSumadosAlcalde = 0;
//                        $totalVotosSumadosConcejal = 0;
//                        $totalVotosSumadosPrefecto = 0;
//                        $totalVotosSumadosJunta = 0;
//                        if($listaParroquia[0]['identificadorParroquia'] == 1)
//                        {
//                            foreach ($listaPreguntas as $valuePreguntas) {
//                            
//                                $idPregunta = $valuePreguntas['idPregunta'];
//                                $listaOpcionesPregunta = $this->dbAdapter->query("select count(cuerpoencuesta.idOpcionPregunta) as total, opcionespregunta.idOpcionPregunta, tipocandidato.identificadorTipoCandidato,candidatos.nombreCandidato
//                                from cuerpoencuesta right join opcionespregunta on cuerpoencuesta.idOpcionPregunta = opcionespregunta.idOpcionPregunta
//                                inner join candidatos on opcionespregunta.idCandidato = candidatos.idCandidato
//                                inner join tipocandidato on candidatos.idTipoCandidato = tipocandidato.idTipoCandidato
//                                inner join cabeceraencuesta on cuerpoencuesta.idCabeceraEncuesta = cabeceraencuesta.idCabeceraEncuesta
//                                where opcionespregunta.idPregunta = $idPregunta and cabeceraencuesta.idParroquia = $idParroquia
//                                group by opcionespregunta.idCandidato
//                                order by total desc
//                                ",Adapter::QUERY_MODE_EXECUTE)->toArray();
//                                $listaCuerpoEncuestaPorPreguntaUrbano = $this->dbAdapter->query("select *
//                                    from cabeceraencuesta inner join cuerpoencuesta on cabeceraencuesta.idCabeceraEncuesta = cuerpoencuesta.idCabeceraEncuesta
//                                    inner join sector on cabeceraencuesta.idSector = sector.idSector
//                                    where cabeceraencuesta.idParroquia = $idParroquia and sector.identificadorSector = 1 and  cuerpoencuesta.idPregunta = $idPregunta
//                                    ",Adapter::QUERY_MODE_EXECUTE)->toArray();
//
//                                    $listaCuerpoEncuestaPorPreguntaRural = $this->dbAdapter->query("select *
//                                    from cabeceraencuesta inner join cuerpoencuesta on cabeceraencuesta.idCabeceraEncuesta = cuerpoencuesta.idCabeceraEncuesta
//                                    inner join sector on cabeceraencuesta.idSector = sector.idSector
//                                    where cabeceraencuesta.idParroquia = $idParroquia and sector.identificadorSector = 2 and  cuerpoencuesta.idPregunta = $idPregunta
//                                ",Adapter::QUERY_MODE_EXECUTE)->toArray();
//                                    
//                                foreach ($listaOpcionesPregunta as $valueOpcionesPregunta) {
//                                    $idOpcionPregunta = $valueOpcionesPregunta['idOpcionPregunta'];
//                                    $listaCuerpoEncuesta = $this->dbAdapter->query("select *
//                                    from cabeceraencuesta inner join cuerpoencuesta on cabeceraencuesta.idCabeceraEncuesta = cuerpoencuesta.idCabeceraEncuesta
//                                    inner join sector on cabeceraencuesta.idSector = sector.idSector
//                                    where cabeceraencuesta.idParroquia = $idParroquia and sector.identificadorSector = 1 and cuerpoencuesta.idOpcionPregunta = $idOpcionPregunta
//                                    ",Adapter::QUERY_MODE_EXECUTE)->toArray();
//                                    $listaCuerpoEncuestaRural = $this->dbAdapter->query("select *
//                                    from cabeceraencuesta inner join cuerpoencuesta on cabeceraencuesta.idCabeceraEncuesta = cuerpoencuesta.idCabeceraEncuesta
//                                    inner join sector on cabeceraencuesta.idSector = sector.idSector
//                                    where cabeceraencuesta.idParroquia = $idParroquia and sector.identificadorSector = 2 and cuerpoencuesta.idOpcionPregunta = $idOpcionPregunta
//                                    ",Adapter::QUERY_MODE_EXECUTE)->toArray();
//                                    $totalVotos =  count($listaCuerpoEncuesta) + count($listaCuerpoEncuestaRural);
//                                    $totalVotosUrbano = count($listaCuerpoEncuesta);
//                                    $totalVotosRural = count($listaCuerpoEncuestaRural);
//                                    $contador=0;
//                                    $contadorRural = 0;
//                                    $contadorTotal = 0;
//                                    if(count($listaCuerpoEncuestaPorPreguntaUrbano) > 0){
//                                        $contador = ($totalVotosUrbano/ count($listaCuerpoEncuestaPorPreguntaUrbano)) * 100;
//                                    }
//                                    if(count($listaCuerpoEncuestaPorPreguntaRural) > 0){
//                                        $contadorRural = (count($listaCuerpoEncuestaRural)/  count($listaCuerpoEncuestaPorPreguntaRural))*100;
//                                    }
//                                    $contadorTotal = $totalVotos/(count($listaCuerpoEncuestaPorPreguntaUrbano)+count($listaCuerpoEncuestaPorPreguntaRural))*100;
//                                    if($valueOpcionesPregunta['identificadorTipoCandidato'] == 1){
//
//                                         $arrayAlcalde[$iAlcalde] = array(
//                                            'value'=>$contador,
//                                            'label'=>$valueOpcionesPregunta['nombreCandidato'],
//                                        );
//                                         $arrayAlcaldeRural[$iAlcalde] = array(
//                                            'value'=>$contadorRural,
//                                            'label'=>$valueOpcionesPregunta['nombreCandidato'],
//                                        );
//                                        $iAlcalde++;
//                                        $filaAlcalde = $filaAlcalde.'<tr><td>'.$iAlcalde.'</td><td>'.$valueOpcionesPregunta['nombreCandidato'].'</td><td>'.$totalVotosUrbano.'</td><td>'.number_format($contador,2).'%</td><td>'.$totalVotosRural.'</td><td>'.number_format($contadorRural,2).'%</td><td>'.$totalVotos.'</td><td>'.number_format($contadorTotal,2).'%</td></tr>';
//                                        $totalVotosSumadosAlcalde = $totalVotosSumadosAlcalde + $totalVotos;
//                                        $contadorVotosAlcaldeUrbano = $contadorVotosAlcaldeUrbano +$totalVotosUrbano;
//                                        $contadorVotosAlcaldeRural = $contadorVotosAlcaldeRural + $totalVotosRural;
//                                    }else if($valueOpcionesPregunta['identificadorTipoCandidato'] == 2){
//                                         $arrayConcejal[$iConcejal] = array(
//                                            'value'=>$contador,
//                                            'label'=>$valueOpcionesPregunta['nombreCandidato'],
//                                        );
//                                         $arrayConcejalRural[$iConcejal] = array(
//                                            'value'=>$contadorRural,
//                                            'label'=>$valueOpcionesPregunta['nombreCandidato'],
//                                        );
//                                        $iConcejal++;
//                                        $filaConcejal = $filaConcejal.'<tr><td>'.$iConcejal.'</td><td>'.$valueOpcionesPregunta['nombreCandidato'].'</td><td>'.$totalVotosUrbano.'</td><td>'.number_format($contador,2).'%</td><td>'.$totalVotosRural.'</td><td>'.number_format($contadorRural,2).'%</td><td>'.$totalVotos.'</td><td>'.number_format($contadorTotal,2).'%</td></tr>';
//                                        $totalVotosSumadosConcejal = $totalVotosSumadosConcejal + $totalVotos;
//                                        $contadorVotosConcejalUrbano = $contadorVotosConcejalUrbano +$totalVotosUrbano;
//                                        $contadorVotosConcejalRural = $contadorVotosConcejalRural + $totalVotosRural;
//                                    }else if($valueOpcionesPregunta['identificadorTipoCandidato'] == 3){
//                                         $arrayPrefecto[$iPrefecto] = array(
//                                            'value'=>$contador,
//                                            'label'=>$valueOpcionesPregunta['nombreCandidato'],
//                                        );
//                                         $arrayPrefectoRural[$iPrefecto] = array(
//                                            'value'=>$contadorRural,
//                                            'label'=>$valueOpcionesPregunta['nombreCandidato'],
//                                        );
//                                        $iPrefecto++;
//                                        $filaPrefecto = $filaPrefecto.'<tr><td>'.$iPrefecto.'</td><td>'.$valueOpcionesPregunta['nombreCandidato'].'</td><td>'.$totalVotosUrbano.'</td><td>'.number_format($contador,2).'%</td><td>'.$totalVotosRural.'</td><td>'.number_format($contadorRural,2).'%</td><td>'.$totalVotos.'</td><td>'.number_format($contadorTotal,2).'%</td></tr>';
//                                        $totalVotosSumadosPrefecto = $totalVotosSumadosPrefecto + $totalVotos;
//                                        $contadorVotosPrefectoUrbano = $contadorVotosPrefectoUrbano +$totalVotosUrbano;
//                                        $contadorVotosPrefectoRural = $contadorVotosPrefectoRural + $totalVotosRural;
//                                    }else if($valueOpcionesPregunta['identificadorTipoCandidato'] == 4){
//                                         $arrayJunta[$iJunta] = array(
//                                            'value'=>$contador,
//                                            'label'=>$valueOpcionesPregunta['nombreCandidato'],
//                                        );
//                                         $arrayJuntaRural[$iJunta] = array(
//                                            'value'=>$contadorRural,
//                                            'label'=>$valueOpcionesPregunta['nombreCandidato'],
//                                        );
//                                        $iJunta++;
//                                        $filaJunta = $filaJunta.'<tr><td>'.$iJunta.'</td><td>'.$valueOpcionesPregunta['nombreCandidato'].'</td><td>'.$totalVotosUrbano.'</td><td>'.number_format($contador,2).'%</td><td>'.$totalVotosRural.'</td><td>'.number_format($contadorRural,2).'%</td><td>'.$totalVotos.'</td><td>'.number_format($contadorTotal,2).'%</td></tr>';
//                                        $totalVotosSumadosJunta = $totalVotosSumadosJunta + $totalVotos;
//                                        $contadorVotosJuntaUrbano = $contadorVotosJuntaUrbano +$totalVotosUrbano;
//                                        $contadorVotosJuntaRural = $contadorVotosJuntaRural + $totalVotosRural;
//                                    }
//                                }
//                                if($valuePreguntas['identificadorPregunta'] == 1){
//                                    $preguntaAlcalde = $valuePreguntas['descripcionPregunta'];
//                                }else if($valuePreguntas['identificadorPregunta'] == 2){
//                                    $preguntaConcejal = $valuePreguntas['descripcionPregunta'];
//                                }else if($valuePreguntas['identificadorPregunta'] == 3){
//                                    $preguntaPrefecto = $valuePreguntas['descripcionPregunta'];
//                                }else if($valuePreguntas['identificadorPregunta'] == 4){
//                                    $preguntaJunta = $valuePreguntas['descripcionPregunta'];
//                                }
//                            }
//                        }else{
//                            foreach ($listaPreguntas as $valuePreguntas) {
//                                if($valuePreguntas['identificadorPregunta'] != 2){
//                                    $idPregunta = $valuePreguntas['idPregunta'];
//                                    $listaOpcionesPregunta = $this->dbAdapter->query("select count(cuerpoencuesta.idOpcionPregunta) as total, opcionespregunta.idOpcionPregunta, tipocandidato.identificadorTipoCandidato,candidatos.nombreCandidato
//                                    from cuerpoencuesta right join opcionespregunta on cuerpoencuesta.idOpcionPregunta = opcionespregunta.idOpcionPregunta
//                                    inner join candidatos on opcionespregunta.idCandidato = candidatos.idCandidato
//                                    inner join tipocandidato on candidatos.idTipoCandidato = tipocandidato.idTipoCandidato
//                                    inner join cabeceraencuesta on cuerpoencuesta.idCabeceraEncuesta = cabeceraencuesta.idCabeceraEncuesta
//                                    where opcionespregunta.idPregunta = $idPregunta and cabeceraencuesta.idParroquia = $idParroquia
//                                    group by opcionespregunta.idCandidato
//                                    order by total desc
//                                    ",Adapter::QUERY_MODE_EXECUTE)->toArray();
//                                    $listaCuerpoEncuestaPorPreguntaUrbano = $this->dbAdapter->query("select *
//                                    from cabeceraencuesta inner join cuerpoencuesta on cabeceraencuesta.idCabeceraEncuesta = cuerpoencuesta.idCabeceraEncuesta
//                                    inner join sector on cabeceraencuesta.idSector = sector.idSector
//                                    where cabeceraencuesta.idParroquia = $idParroquia and sector.identificadorSector = 1 and  cuerpoencuesta.idPregunta = $idPregunta
//                                    ",Adapter::QUERY_MODE_EXECUTE)->toArray();
//                                    $listaCuerpoEncuestaPorPreguntaRural = $this->dbAdapter->query("select *
//                                    from cabeceraencuesta inner join cuerpoencuesta on cabeceraencuesta.idCabeceraEncuesta = cuerpoencuesta.idCabeceraEncuesta
//                                    inner join sector on cabeceraencuesta.idSector = sector.idSector
//                                    where cabeceraencuesta.idParroquia = $idParroquia and sector.identificadorSector = 2 and  cuerpoencuesta.idPregunta = $idPregunta
//                                    ",Adapter::QUERY_MODE_EXECUTE)->toArray();
//                                   foreach ($listaOpcionesPregunta as $valueOpcionesPregunta) {
//                                        $idOpcionPregunta = $valueOpcionesPregunta['idOpcionPregunta'];
//                                        $listaCuerpoEncuesta = $this->dbAdapter->query("select *
//                                        from cabeceraencuesta inner join cuerpoencuesta on cabeceraencuesta.idCabeceraEncuesta = cuerpoencuesta.idCabeceraEncuesta
//                                        inner join sector on cabeceraencuesta.idSector = sector.idSector
//                                        where cabeceraencuesta.idParroquia = $idParroquia and sector.identificadorSector = 1 and cuerpoencuesta.idOpcionPregunta = $idOpcionPregunta
//                                        ",Adapter::QUERY_MODE_EXECUTE)->toArray();
//                                        $listaCuerpoEncuestaRural = $this->dbAdapter->query("select *
//                                        from cabeceraencuesta inner join cuerpoencuesta on cabeceraencuesta.idCabeceraEncuesta = cuerpoencuesta.idCabeceraEncuesta
//                                        inner join sector on cabeceraencuesta.idSector = sector.idSector
//                                        where cabeceraencuesta.idParroquia = $idParroquia and sector.identificadorSector = 2 and cuerpoencuesta.idOpcionPregunta = $idOpcionPregunta
//                                        ",Adapter::QUERY_MODE_EXECUTE)->toArray();
//                                        $totalVotos =  count($listaCuerpoEncuesta) + count($listaCuerpoEncuestaRural);
//                                        $totalVotosUrbano = count($listaCuerpoEncuesta);
//                                        $totalVotosRural = count($listaCuerpoEncuestaRural);
//                                        $contador=0;
//                                        $contadorRural = 0;
//                                        $contadorTotal = 0;
//                                        if(count($listaCuerpoEncuestaPorPreguntaUrbano) > 0){
//                                            $contador = ($totalVotosUrbano/ count($listaCuerpoEncuestaPorPreguntaUrbano)) * 100;
//                                        }
//                                        if(count($listaCuerpoEncuestaPorPreguntaRural) > 0){
//                                            $contadorRural = (count($listaCuerpoEncuestaRural)/  count($listaCuerpoEncuestaPorPreguntaRural))*100;
//                                        }
//                                        $contadorTotal = $totalVotos/(count($listaCuerpoEncuestaPorPreguntaUrbano)+count($listaCuerpoEncuestaPorPreguntaRural))*100;
//                                        if($valueOpcionesPregunta['identificadorTipoCandidato'] == 1){
//                                            $arrayAlcalde[$iAlcalde] = array(
//                                                'value'=>$contador,
//                                                'label'=>$valueOpcionesPregunta['nombreCandidato'],
//                                            );
//                                             $arrayAlcaldeRural[$iAlcalde] = array(
//                                                'value'=>$contadorRural,
//                                                'label'=>$valueOpcionesPregunta['nombreCandidato'],
//                                            );
//                                            $iAlcalde++;
//                                            $filaAlcalde = $filaAlcalde.'<tr><td>'.$iAlcalde.'</td><td>'.$valueOpcionesPregunta['nombreCandidato'].'</td><td>'.$totalVotosUrbano.'</td><td>'.number_format($contador,2).'%</td><td>'.$totalVotosRural.'</td><td>'.number_format($contadorRural,2).'%</td><td>'.$totalVotos.'</td><td>'.number_format($contadorTotal,2).'%</td></tr>';
//                                            $totalVotosSumadosAlcalde = $totalVotosSumadosAlcalde + $totalVotos;
//                                            $contadorVotosAlcaldeUrbano = $contadorVotosAlcaldeUrbano +$totalVotosUrbano;
//                                            $contadorVotosAlcaldeRural = $contadorVotosAlcaldeRural + $totalVotosRural;
//                                        }else if($valueOpcionesPregunta['identificadorTipoCandidato'] == 3){
//                                             $arrayPrefecto[$iPrefecto] = array(
//                                                'value'=>$contador,
//                                                'label'=>$valueOpcionesPregunta['nombreCandidato'],
//                                            );
//                                             $arrayPrefectoRural[$iPrefecto] = array(
//                                                'value'=>$contadorRural,
//                                                'label'=>$valueOpcionesPregunta['nombreCandidato'],
//                                            );
//                                            $iPrefecto++;
//                                            $filaPrefecto = $filaPrefecto.'<tr><td>'.$iPrefecto.'</td><td>'.$valueOpcionesPregunta['nombreCandidato'].'</td><td>'.$totalVotosUrbano.'</td><td>'.number_format($contador,2).'%</td><td>'.$totalVotosRural.'</td><td>'.number_format($contadorRural,2).'%</td><td>'.$totalVotos.'</td><td>'.number_format($contadorTotal,2).'%</td></tr>';
//                                            $totalVotosSumadosPrefecto = $totalVotosSumadosPrefecto + $totalVotos;
//                                            $contadorVotosPrefectoUrbano = $contadorVotosPrefectoUrbano +$totalVotosUrbano;
//                                            $contadorVotosPrefectoRural = $contadorVotosPrefectoRural + $totalVotosRural;
//                                        }
//                                    }
//                                    if($valuePreguntas['identificadorPregunta'] == 1){
//                                        $preguntaAlcalde = $valuePreguntas['descripcionPregunta'];
//                                    }else  if($valuePreguntas['identificadorPregunta'] == 3){
//                                        $preguntaPrefecto = $valuePreguntas['descripcionPregunta'];
//                                    }
//                                }
//                            }
//                        }
//                        
//                            $tablaAlcalde = '<div class="table-responsive"><table class="table">
//                                        <thead>
//                                            <tr style="background-color: #B3D5FC;">
//                                                <th>#</th>
//                                                <th>CANDIDATO(A)</th>
//                                                <th>URBANO</th>
//                                                <th>%</th>
//                                                <th>RURAL</th>
//                                                <th>%</th>
//                                                <th>TOTAL</th>
//                                                <th>%</th>
//                                            </tr>
//                                        </tehad>
//                                        <tbody>
//                                            '.$filaAlcalde.'
//                                             <tr style="background-color: #B3D5FC;">
//                                                <td colspan="2">TOTAL</td>
//                                                <td>'.$contadorVotosAlcaldeUrbano.'</td>
//                                                <td></td>
//                                                <td>'.$contadorVotosAlcaldeRural.'</td>
//                                                    <td></td>
//                                                <td>'.$totalVotosSumadosAlcalde.'</td>
//                                                    <td></td>
//                                            </tr>
//                                        </tbody>
//                                    </table></div>';
//                        
//                        $tablaConcejal = '';
//                        if($filaConcejal != ""){
//                            
//                            $tablaConcejal = '<div class="table-responsive"><table class="table">
//                                    <thead>
//                                        <tr style="background-color: #B3D5FC;">
//                                            <th>#</th>
//                                            <th>CANDIDATO(A)</th>
//                                            <th>URBANO</th>
//                                            <th>%</th>
//                                            <th>RURAL</th>
//                                            <th>%</th>
//                                            <th>TOTAL</th>
//                                            <th>%</th>
//                                        </tr>
//                                    </tehad>
//                                    <tbody>
//                                        '.$filaConcejal.'
//                                            <tr style="background-color: #B3D5FC;">
//                                                <td colspan="2">TOTAL</td>
//                                                <td>'.$contadorVotosConcejalUrbano.'</td>
//                                                    <td></td>
//                                                <td>'.$contadorVotosConcejalRural.'</td>
//                                                    <td></td>
//                                                <td>'.$totalVotosSumadosConcejal.'</td>
//                                                    <td></td>
//                                            </tr>
//                                    </tbody>
//                                </table></div>';
//                        }
//                        $tablaPrefecto = '<div class="table-responsive"><table class="table">
//                                    <thead>
//                                        <tr style="background-color: #B3D5FC;">
//                                            <th>#</th>
//                                            <th>CANDIDATO(A)</th>
//                                            <th>URBANO</th>
//                                            <th>%</th>
//                                            <th>RURAL</th>
//                                            <th>%</th>
//                                            <th>TOTAL</th>
//                                            <th>%</th>
//                                        </tr>
//                                    </tehad>
//                                    <tbody>
//                                        '.$filaPrefecto.'
//                                            <tr style="background-color: #B3D5FC;">
//                                                <td colspan="2">TOTAL</td>
//                                                <td>'.$contadorVotosPrefectoUrbano.'</td>
//                                                    <td></td>
//                                                <td>'.$contadorVotosPrefectoRural.'</td>
//                                                    <td></td>
//                                                <td>'.$totalVotosSumadosPrefecto.'</td>
//                                                    <td></td>
//                                            </tr>
//                                    </tbody>
//                                </table></div>';
//                        $tablaJunta = '<div class="table-responsive"><table class="table">
//                                    <thead>
//                                        <tr style="background-color: #B3D5FC;">
//                                            <th>#</th>
//                                            <th>CANDIDATO(A)</th>
//                                            <th>URBANO</th>
//                                            <th>%</th>
//                                            <th>RURAL</th>
//                                            <th>%</th>
//                                            <th>TOTAL</th>
//                                            <th>%</th>
//                                        </tr>
//                                    </tehad>
//                                    <tbody>
//                                        '.$filaJunta.'
//                                            <tr style="background-color: #B3D5FC;">
//                                                <td colspan="2">TOTAL</td>
//                                                <td>'.$contadorVotosJuntaUrbano.'</td>
//                                                    <td></td>
//                                                <td>'.$contadorVotosJuntaRural.'</td>
//                                                    <td></td>
//                                                <td>'.$totalVotosSumadosJunta.'</td>
//                                                    <td></td>
//                                            </tr>
//                                    </tbody>
//                                </table></div>';
//                        
//                        
//                        $mensaje = '';
//                        $validar = TRUE;
//                        return new JsonModel(array(
//                            'mensaje'=>$mensaje,
//                            'validar'=>$validar,
//                            'preguntaAlcalde'=>$preguntaAlcalde,
//                            'charAlcalde'=>$arrayAlcalde,
//                            'charAlcaldeRural'=>$arrayAlcaldeRural,
//                            'tablaAlcalde'=>$tablaAlcalde,
//                            'preguntaConcejal'=>$preguntaConcejal,
//                            'tablaConcejal'=>$tablaConcejal,
//                            'charConcejal'=>$arrayConcejal,
//                            'charConcejalRural'=>$arrayConcejalRural,
//                            'preguntaPrefecto'=>$preguntaPrefecto,
//                            'tablaPrefecto'=>$tablaPrefecto,
//                            'charPrefecto'=>$arrayPrefecto,
//                            'charPrefectoRural'=>$arrayPrefectoRural,
//                            'preguntaJunta'=>$preguntaJunta,
//                            'tablaJunta'=>$tablaJunta,
//                            'charJunta'=>$arrayJunta,
//                            'charJuntaRural'=>$arrayJuntaRural));
//                    }
//                }
//            
//            }
//            
//        }
//        return new JsonModel(array('mensaje'=>$mensaje,'validar'=>$validar));
//    }
//    
//    
//    
//    
//    
//    
//    
//    
//    
//    
//    
//    
//    
//    
//    
//    
//    
//    
//    
//    
//    
//    public function filtrarresultadoporsectorAction()
//    {
//        $mensaje = '<div class="alert alert-danger text-center" role="alert">OCURRIÓ UN ERROR INESPERADO</div>';
//        $validar = false;
//        $request=$this->getRequest();
//        if(!$request->isPost()){
//            $this->redirect()->toUrl($this->getRequest()->getBaseUrl().'/index/index');
//        }else{
//            $this->dbAdapter=$this->getServiceLocator()->get('Zend\Db\Adapter');
//            $objCabeceraEncuesta = new CabeceraEncuesta($this->dbAdapter);
//            $objPreguntas = new Preguntas($this->dbAdapter);
//            $objSector = new Sector($this->dbAdapter);
//            $objMetodos = new Metodos();
//            $post = array_merge_recursive(
//                $request->getPost()->toArray(),
//                $request->getFiles()->toArray()
//            );
//            
//            $idSectorEncriptado = $post['idSectorEncriptado'];
//            
//            if($idSectorEncriptado == "" || $idSectorEncriptado == "0" || $idSectorEncriptado == NULL){
//                $mensaje = '<div class="alert alert-danger text-center" role="alert">SELECCIONE UN SECTOR</div>';
//            }else{
//                $idSector = $objMetodos->desencriptar($idSectorEncriptado);
//                $listaSector = $objSector->filtrarSector($idSector);
//                if(count($listaSector) == 0){
//                    $mensaje = '<div class="alert alert-danger text-center" role="alert">EL SECTOR SELECCIONADO NO EXISTE EN LA BASE DE DATOS</div>';
//                }else {
//                    $listaCabeceraEncuesta= $objCabeceraEncuesta->filtrarCabeceraEncuestaPorSector($idSector);
//                    if(count($listaCabeceraEncuesta) == 0){
//                        $mensaje = '<div class="alert alert-danger text-center" role="alert">AÚN NO SE HAN REALIZADO ENCUESTAS</div>';
//                    }else{
//                        $listaPreguntas = $objPreguntas->obtenerPreguntas();
//                        $arrayAlcalde = array();
//                        $arrayConcejal = array();
//                        $arrayPrefecto = array();
//                        $arrayJunta = array();
//                        $iAlcalde = 0;
//                        $iConcejal = 0;
//                        $iPrefecto = 0;
//                         $iJunta = 0;
//                        $preguntaAlcalde = "";
//                        $preguntaConcejal = "";
//                        $preguntaPrefecto ="";
//                        $preguntaJunta ="";
//                        $contadorAlcalde = 0;
//                        $contadorConcejal = 0;
//                        $contadorPrefecto = 0;
//                        $contadorJunta = 0;
//                        $filaAlcalde = "";
//                        $filaConcejal ="";
//                        $filaPrefecto = "";
//                        $filaJunta = "";
//                        $totalVotosSumadosAlcalde = 0;
//                        $totalVotosSumadosConcejal = 0;
//                        $totalVotosSumadosPrefecto = 0;
//                        $totalVotosSumadosJunta = 0;
//                        foreach ($listaPreguntas as $valuePreguntas) {
//                            $idPregunta = $valuePreguntas['idPregunta'];
//
//
//                            $listaOpcionesPregunta = $this->dbAdapter->query("select count(cuerpoencuesta.idOpcionPregunta) as total, opcionespregunta.idOpcionPregunta, tipocandidato.identificadorTipoCandidato,candidatos.nombreCandidato
//                            from cuerpoencuesta right join opcionespregunta on cuerpoencuesta.idOpcionPregunta = opcionespregunta.idOpcionPregunta
//                            inner join candidatos on opcionespregunta.idCandidato = candidatos.idCandidato
//                            inner join tipocandidato on candidatos.idTipoCandidato = tipocandidato.idTipoCandidato
//                            inner join cabeceraencuesta on cuerpoencuesta.idCabeceraEncuesta = cabeceraencuesta.idCabeceraEncuesta
//                            where opcionespregunta.idPregunta = $idPregunta and cabeceraencuesta.idSector = $idSector
//                            group by opcionespregunta.idCandidato
//                            order by total desc
//                            ",Adapter::QUERY_MODE_EXECUTE)->toArray();
//                            $listaCuerpoEncuestaPorPregunta = $this->dbAdapter->query("select *
//                                from cabeceraencuesta inner join cuerpoencuesta on cabeceraencuesta.idCabeceraEncuesta = cuerpoencuesta.idCabeceraEncuesta
//                                where cabeceraencuesta.idSector = $idSector and  cuerpoencuesta.idPregunta = $idPregunta
//                                ",Adapter::QUERY_MODE_EXECUTE)->toArray();
//                            
//                            foreach ($listaOpcionesPregunta as $valueOpcionesPregunta) {
//                                $idOpcionPregunta = $valueOpcionesPregunta['idOpcionPregunta'];
//                                $listaCuerpoEncuesta = $this->dbAdapter->query("select *
//                                from cabeceraencuesta inner join cuerpoencuesta on cabeceraencuesta.idCabeceraEncuesta = cuerpoencuesta.idCabeceraEncuesta
//                                where cabeceraencuesta.idSector = $idSector and  cuerpoencuesta.idOpcionPregunta = $idOpcionPregunta
//                                ",Adapter::QUERY_MODE_EXECUTE)->toArray();
//                                $totalVotos = count($listaCuerpoEncuesta);
//                                    if($valueOpcionesPregunta['identificadorTipoCandidato'] == 1){
//
//                                        $contadorAlcalde = (count($listaCuerpoEncuesta)/  count($listaCuerpoEncuestaPorPregunta))*100;
//                                         $arrayAlcalde[$iAlcalde] = array(
//                                            'value'=>$contadorAlcalde,
//                                            'label'=>$valueOpcionesPregunta['nombreCandidato'],
//                                        );
//                                        $iAlcalde++;
//                                        $filaAlcalde = $filaAlcalde.'<tr><td>'.$iAlcalde.'</td><td>'.$valueOpcionesPregunta['nombreCandidato'].'</td><td>'.$totalVotos.'</td><td>'.number_format($contadorAlcalde,2).'%</td></tr>';
//                                        $totalVotosSumadosAlcalde = $totalVotosSumadosAlcalde + $totalVotos;
//                                        
//                                    }else if($valueOpcionesPregunta['identificadorTipoCandidato'] == 2){
//                                        $contadorConcejal = (count($listaCuerpoEncuesta)/  count($listaCuerpoEncuestaPorPregunta))*100;
//                                         $arrayConcejal[$iConcejal] = array(
//                                            'value'=>$contadorConcejal,
//                                            'label'=>$valueOpcionesPregunta['nombreCandidato'],
//                                        );
//                                        $iConcejal++;
//                                         $filaConcejal = $filaConcejal.'<tr><td>'.$iConcejal.'</td><td>'.$valueOpcionesPregunta['nombreCandidato'].'</td><td>'.$totalVotos.'</td><td>'.number_format($contadorConcejal,2).'%</td></tr>';
//                                         $totalVotosSumadosConcejal = $totalVotosSumadosConcejal + $totalVotos;
//                                         
//                                    }else if($valueOpcionesPregunta['identificadorTipoCandidato'] == 3){
//                                        $contadorPrefecto = (count($listaCuerpoEncuesta)/  count($listaCuerpoEncuestaPorPregunta))*100;
//                                         $arrayPrefecto[$iPrefecto] = array(
//                                            'value'=>$contadorPrefecto,
//                                            'label'=>$valueOpcionesPregunta['nombreCandidato'],
//                                        );
//                                        $iPrefecto++;
//                                        $filaPrefecto = $filaPrefecto.'<tr><td>'.$iPrefecto.'</td><td>'.$valueOpcionesPregunta['nombreCandidato'].'</td><td>'.$totalVotos.'</td><td>'.number_format($contadorPrefecto,2).'%</td></tr>';
//                                    
//                                        $totalVotosSumadosPrefecto = $totalVotosSumadosPrefecto + $totalVotos;
//                                    }else if($valueOpcionesPregunta['identificadorTipoCandidato'] == 4){
//                                        $contadorJunta = ($totalVotos/  count($listaCuerpoEncuestaPorPregunta))*100;
//                                         $arrayJunta[$iJunta] = array(
//                                            'value'=>$contadorJunta,
//                                            'label'=>$valueOpcionesPregunta['nombreCandidato'],
//                                        );
//                                        $iJunta++;
//                                        $filaJunta = $filaJunta.'<tr><td>'.$iJunta.'</td><td>'.$valueOpcionesPregunta['nombreCandidato'].'</td><td>'.$totalVotos.'</td><td>'.number_format($contadorJunta,2).'%</td></tr>';
//
//                                         $totalVotosSumadosJunta = $totalVotosSumadosJunta + $totalVotos;
//                                    }
//                                
//
//                            }
//                            if($valuePreguntas['identificadorPregunta'] == 1){
//                                $preguntaAlcalde = $valuePreguntas['descripcionPregunta'];
//                            }else if($valuePreguntas['identificadorPregunta'] == 2){
//                                $preguntaConcejal = $valuePreguntas['descripcionPregunta'];
//                            }else if($valuePreguntas['identificadorPregunta'] == 3){
//                                $preguntaPrefecto = $valuePreguntas['descripcionPregunta'];
//                            }else if($valuePreguntas['identificadorPregunta'] == 4){
//                                $preguntaJunta = $valuePreguntas['descripcionPregunta'];
//                            }
//                        }
//                      
//                            $tablaAlcalde = '<div class="table-responsive"><table class="table">
//                                        <thead>
//                                            <tr style="background-color: #B3D5FC;">
//                                                <th>#</th>
//                                                <th>CANDIDATO(A)</th>
//                                                <th>TOTAL</th>
//                                                <th>%</th>
//                                            </tr>
//                                        </tehad>
//                                        <tbody>
//                                            '.$filaAlcalde.'
//                                                <tr style="background-color: #B3D5FC;">
//                                            <td colspan="2">TOTAL</td>
//                                            <td>'.$totalVotosSumadosAlcalde.'</td>
//                                                <td></td>
//                                        </tr>
//                                        </tbody>
//                                    </table></div>';
//                        
//                        
//                        $tablaConcejal = '<div class="table-responsive"><table class="table">
//                                    <thead>
//                                        <tr style="background-color: #B3D5FC;">
//                                            <th>#</th>
//                                            <th>CANDIDATO(A)</th>
//                                            <th>TOTAL</th>
//                                            <th>%</th>
//                                        </tr>
//                                    </tehad>
//                                    <tbody>
//                                        '.$filaConcejal.'
//                                        <tr style="background-color: #B3D5FC;">
//                                            <td colspan="2">TOTAL</td>
//                                            <td>'.$totalVotosSumadosConcejal.'</td>
//                                            <td></td>
//                                        </tr>
//                                    </tbody>
//                                </table></div>';
//                        $tablaPrefecto = '<div class="table-responsive"><table class="table">
//                                    <thead>
//                                        <tr style="background-color: #B3D5FC;">
//                                            <th>#</th>
//                                            <th>CANDIDATO(A)</th>
//                                            <th>TOTAL</th>
//                                            <th>%</th>
//                                        </tr>
//                                    </tehad>
//                                    <tbody>
//                                        '.$filaPrefecto.'
//                                            <tr style="background-color: #B3D5FC;">
//                                                <td colspan="2">TOTAL</td>
//                                                <td>'.$totalVotosSumadosPrefecto.'</td>
//                                                    <td></td>
//                                            </tr>
//                                    </tbody>
//                                </table></div>';
//                        $tablaJunta = '<div class="table-responsive"><table class="table">
//                                    <thead>
//                                        <tr style="background-color: #B3D5FC;">
//                                            <th>#</th>
//                                            <th>CANDIDATO(A)</th>
//                                            <th>TOTAL</th>
//                                            <th>%</th>
//                                        </tr>
//                                    </tehad>
//                                    <tbody>
//                                        '.$filaJunta.'
//                                            <tr style="background-color: #B3D5FC;">
//                                                <td colspan="2">TOTAL</td>
//                                                <td>'.$totalVotosSumadosJunta.'</td>
//                                                    <td></td>
//                                            </tr>
//                                    </tbody>
//                                </table></div>';
//                        
//                        
//                        $mensaje = '';
//                        $validar = TRUE;
//                         return new JsonModel(array(
//                            'mensaje'=>$mensaje,
//                            'validar'=>$validar,
//                            'preguntaAlcalde'=>$preguntaAlcalde,
//                            'charAlcalde'=>$arrayAlcalde,
//                            'tablaAlcalde'=>$tablaAlcalde,
//                            'preguntaConcejal'=>$preguntaConcejal,
//                            'tablaConcejal'=>$tablaConcejal,
//                            'charConcejal'=>$arrayConcejal,
//                            'preguntaPrefecto'=>$preguntaPrefecto,
//                            'tablaPrefecto'=>$tablaPrefecto,
//                            'charPrefecto'=>$arrayPrefecto,
//                             'preguntaJunta'=>$preguntaJunta,
//                            'tablaJunta'=>$tablaJunta,
//                            'charJunta'=>$arrayJunta,
//                            ));
//                    }
//                }
//            
//            }
//            
//        }
//        return new JsonModel(array('mensaje'=>$mensaje,'validar'=>$validar));
//    }
//    
//    
//    
//    
//    
//    
//    
//    
//    
//    
//    
//    
//    
//    
//    
//    
//    
//    
//    
//    
//    
//    
//    
//    
//    public function filtrarresultadoporcandidaturaAction()
//    {
//        $mensaje = '<div class="alert alert-danger text-center" role="alert">OCURRIÓ UN ERROR INESPERADO</div>';
//        $validar = false;
//        $request=$this->getRequest();
//        if(!$request->isPost()){
//            $this->redirect()->toUrl($this->getRequest()->getBaseUrl().'/index/index');
//        }else{
//            $this->dbAdapter=$this->getServiceLocator()->get('Zend\Db\Adapter');
//            $objCabeceraEncuesta = new CabeceraEncuesta($this->dbAdapter);
//            $objCuerpoEncuesta = new CuerpoEncuesta($this->dbAdapter);
//            $objPreguntas = new Preguntas($this->dbAdapter);
//            $listaCabeceraEncuesta= $objCabeceraEncuesta->obtenerCabeceraEncuesta();
//            if(count($listaCabeceraEncuesta) == 0){
//                $mensaje = '<div class="alert alert-danger text-center" role="alert">AÚN NO SE HAN REALIZADO ENCUESTAS</div>';
//            }else{
//                $listaPreguntas = $objPreguntas->obtenerPreguntas();
//                $arrayAlcalde = array();
//                $arrayConcejal = array();
//                $arrayPrefecto = array();
//                $arrayJunta = array();
//                $iAlcalde = 0;
//                $iConcejal = 0;
//                $iPrefecto = 0;
//                $iJunta = 0;
//                $preguntaAlcalde = "";
//                $preguntaConcejal = "";
//                $preguntaPrefecto ="";
//                $preguntaJunta ="";
//                $contadorAlcalde = 0;
//                $contadorConcejal = 0;
//                $contadorPrefecto = 0;
//                $contadorJunta = 0;
//                $filaAlcalde = "";
//                $filaConcejal ="";
//                $filaPrefecto = "";
//                $filaJunta = "";
//                $totalVotosSumadosAlcalde = 0;
//                $totalVotosSumadosConcejal = 0;
//                $totalVotosSumadosPrefecto = 0;
//                $totalVotosSumadosJunta = 0;
//                foreach ($listaPreguntas as $valuePreguntas) {
//                    $idPregunta = $valuePreguntas['idPregunta'];
//                   
//                    
//                    $listaOpcionesPregunta = $this->dbAdapter->query("select count(cuerpoencuesta.idOpcionPregunta) as total, opcionespregunta.idOpcionPregunta, tipocandidato.identificadorTipoCandidato,candidatos.nombreCandidato
//                    from cuerpoencuesta right join opcionespregunta on cuerpoencuesta.idOpcionPregunta = opcionespregunta.idOpcionPregunta
//                    inner join candidatos on opcionespregunta.idCandidato = candidatos.idCandidato
//                    inner join tipocandidato on candidatos.idTipoCandidato = tipocandidato.idTipoCandidato
//                    where opcionespregunta.idPregunta = $idPregunta
//                    group by opcionespregunta.idCandidato
//                    order by total desc
//                    ",Adapter::QUERY_MODE_EXECUTE)->toArray();
//                    
//                    $listaCuerpoEncuestaPorPregunta = $objCuerpoEncuesta->filtrarCuerpoEncuestaPorPregunta($idPregunta);
//                    foreach ($listaOpcionesPregunta as $valueOpcionesPregunta) {
//                        $listaCuerpoEncuesta = $objCuerpoEncuesta->filtrarCuerpoEncuestaPorOpcionPregunta($valueOpcionesPregunta['idOpcionPregunta']);
//                         $totalVotos = count($listaCuerpoEncuesta);
//                        if($valueOpcionesPregunta['identificadorTipoCandidato'] == 1){
//                            $contadorAlcalde = ($totalVotos/  count($listaCuerpoEncuestaPorPregunta))*100;
//                             $arrayAlcalde[$iAlcalde] = array(
//                                'value'=>$contadorAlcalde,
//                                'label'=>$valueOpcionesPregunta['nombreCandidato'],
//                            );
//                            $iAlcalde++;
//                            $filaAlcalde = $filaAlcalde.'<tr><td>'.$iAlcalde.'</td><td>'.$valueOpcionesPregunta['nombreCandidato'].'</td><td>'.$totalVotos.'</td><td>'.number_format($contadorAlcalde,2).'%</td></tr>';
//                             $totalVotosSumadosAlcalde = $totalVotosSumadosAlcalde + $totalVotos;
//                            
//                        }else if($valueOpcionesPregunta['identificadorTipoCandidato'] == 2){
//                            $contadorConcejal = ($totalVotos/  count($listaCuerpoEncuestaPorPregunta))*100;
//                             $arrayConcejal[$iConcejal] = array(
//                                'value'=>$contadorConcejal,
//                                'label'=>$valueOpcionesPregunta['nombreCandidato'],
//                            );
//                            $iConcejal++;
//                            $filaConcejal = $filaConcejal.'<tr><td>'.$iConcejal.'</td><td>'.$valueOpcionesPregunta['nombreCandidato'].'</td><td>'.$totalVotos.'</td><td>'.number_format($contadorConcejal,2).'%</td></tr>';
//                            $totalVotosSumadosConcejal = $totalVotosSumadosConcejal + $totalVotos;
//                        }else if($valueOpcionesPregunta['identificadorTipoCandidato'] == 3){
//                            $contadorPrefecto = ($totalVotos/  count($listaCuerpoEncuestaPorPregunta))*100;
//                             $arrayPrefecto[$iPrefecto] = array(
//                                'value'=>$contadorPrefecto,
//                                'label'=>$valueOpcionesPregunta['nombreCandidato'],
//                            );
//                            $iPrefecto++;
//                            $filaPrefecto = $filaPrefecto.'<tr><td>'.$iPrefecto.'</td><td>'.$valueOpcionesPregunta['nombreCandidato'].'</td><td>'.$totalVotos.'</td><td>'.number_format($contadorPrefecto,2).'%</td></tr>';
//                        
//                             $totalVotosSumadosPrefecto = $totalVotosSumadosPrefecto + $totalVotos;
//                        }else if($valueOpcionesPregunta['identificadorTipoCandidato'] == 4){
//                            $contadorJunta = ($totalVotos/  count($listaCuerpoEncuestaPorPregunta))*100;
//                             $arrayJunta[$iJunta] = array(
//                                'value'=>$contadorJunta,
//                                'label'=>$valueOpcionesPregunta['nombreCandidato'],
//                            );
//                            $iJunta++;
//                            $filaJunta = $filaJunta.'<tr><td>'.$iJunta.'</td><td>'.$valueOpcionesPregunta['nombreCandidato'].'</td><td>'.$totalVotos.'</td><td>'.number_format($contadorJunta,2).'%</td></tr>';
//                        
//                             $totalVotosSumadosJunta = $totalVotosSumadosJunta + $totalVotos;
//                        }
//                       
//                        
//                    }
//                    if($valuePreguntas['identificadorPregunta'] == 1){
//                        $preguntaAlcalde = $valuePreguntas['descripcionPregunta'];
//                    }else if($valuePreguntas['identificadorPregunta'] == 2){
//                        $preguntaConcejal = $valuePreguntas['descripcionPregunta'];
//                    }else if($valuePreguntas['identificadorPregunta'] == 3){
//                        $preguntaPrefecto = $valuePreguntas['descripcionPregunta'];
//                    }else if($valuePreguntas['identificadorPregunta'] == 4){
//                        $preguntaJunta = $valuePreguntas['descripcionPregunta'];
//                    }
//                }
//                $tablaAlcalde = '<div class="table-responsive"><table class="table">
//                            <thead>
//                                <tr style="background-color: #B3D5FC;">
//                                    <th>#</th>
//                                    <th>CANDIDATO(A)</th>
//                                    <th>TOTAL</th>
//                                    <th>%</th>
//                                </tr>
//                            </tehad>
//                            <tbody>
//                                '.$filaAlcalde.'
//                                <tr style="background-color: #B3D5FC;">
//                                    <td colspan="2">TOTAL</td>
//                                    <td>'.$totalVotosSumadosAlcalde.'</td>
//                                        <td></td>
//                                </tr>
//                            </tbody>
//                        </table></div>';
//                $tablaConcejal = '<div class="table-responsive"><table class="table">
//                            <thead>
//                                <tr style="background-color: #B3D5FC;">
//                                    <th>#</th>
//                                    <th>CANDIDATO(A)</th>
//                                    <th>TOTAL</th>
//                                    <th>%</th>
//                                </tr>
//                            </tehad>
//                            <tbody>
//                                '.$filaConcejal.'
//                                <tr style="background-color: #B3D5FC;">
//                                    <td colspan="2">TOTAL</td>
//                                    <td>'.$totalVotosSumadosConcejal.'</td>
//                                        <td></td>
//                                </tr>
//                            </tbody>
//                        </table></div>';
//                $tablaPrefecto = '<div class="table-responsive"><table class="table">
//                            <thead>
//                                <tr style="background-color: #B3D5FC;">
//                                    <th>#</th>
//                                    <th>CANDIDATO(A)</th>
//                                    <th>TOTAL</th>
//                                    <th>%</th>
//                                </tr>
//                            </tehad>
//                            <tbody>
//                                '.$filaPrefecto.'
//                                <tr style="background-color: #B3D5FC;">
//                                    <td colspan="2">TOTAL</td>
//                                    <td>'.$totalVotosSumadosPrefecto.'</td>
//                                        <td></td>
//                                </tr>
//                            </tbody>
//                        </table></div>';
//                 $tablaJunta = '<div class="table-responsive"><table class="table">
//                            <thead>
//                                <tr style="background-color: #B3D5FC;">
//                                    <th>#</th>
//                                    <th>CANDIDATO(A)</th>
//                                    <th>TOTAL</th>
//                                    <th>%</th>
//                                </tr>
//                            </tehad>
//                            <tbody>
//                                '.$filaJunta.'
//                                <tr style="background-color: #B3D5FC;">
//                                    <td colspan="2">TOTAL</td>
//                                    <td>'.$totalVotosSumadosJunta.'</td>
//                                        <td></td>
//                                </tr>
//                            </tbody>
//                        </table></div>';
//                $mensaje = '';
//                $validar = TRUE;
//                return new JsonModel(array(
//                    'mensaje'=>$mensaje,
//                    'validar'=>$validar,
//                    'preguntaAlcalde'=>$preguntaAlcalde,
//                    'charAlcalde'=>$arrayAlcalde,
//                    'tablaAlcalde'=>$tablaAlcalde,
//                    'preguntaConcejal'=>$preguntaConcejal,
//                    'tablaConcejal'=>$tablaConcejal,
//                    'charConcejal'=>$arrayConcejal,
//                    'preguntaPrefecto'=>$preguntaPrefecto,
//                    'tablaPrefecto'=>$tablaPrefecto,
//                    'charPrefecto'=>$arrayPrefecto,
//                     'preguntaJunta'=>$preguntaJunta,
//                    'tablaJunta'=>$tablaJunta,
//                    'charJunta'=>$arrayJunta,
//                    ));
//            }
//            
//              
//            
//        }
//        return new JsonModel(array('mensaje'=>$mensaje,'validar'=>$validar));
//    }
   
}
    
    
