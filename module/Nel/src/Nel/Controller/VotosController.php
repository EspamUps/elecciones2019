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
use Nel\Modelo\Entity\Candidatos;
use Nel\Modelo\Entity\TipoCandidato;
use Nel\Modelo\Entity\Listas;
use Nel\Modelo\Entity\ConfigurarJunta;
use Nel\Modelo\Entity\Sexo;
use Nel\Modelo\Entity\Parroquia;
use Zend\Db\Adapter\Adapter;

class VotosController extends AbstractActionController
{
    public $dbAdapter;
//    public function ingresarencuestaAction()
//    {
//        $mensaje = '<div class="alert alert-danger text-center" role="alert">OCURRIÓ UN ERROR INESPERADO</div>';
//        $validar = false;
//        $request=$this->getRequest();
//        if(!$request->isPost()){
//            $this->redirect()->toUrl($this->getRequest()->getBaseUrl().'/index/index');
//        }else{
//            $this->dbAdapter=$this->getServiceLocator()->get('Zend\Db\Adapter');
//            $objRangoEdad = new RangoEdad($this->dbAdapter);
//            $objSexo = new Sexo($this->dbAdapter);
//            $objSector = new Sector($this->dbAdapter);
//            $objParroquia = new Parroquia($this->dbAdapter);
//            $objOpcionesPregunta = new OpcionesPregunta($this->dbAdapter);
//            $objCabeceraEncuesta = new CabeceraEncuesta($this->dbAdapter);
//            $objCuerpoEncueta = new CuerpoEncuesta($this->dbAdapter);
//            $objMetodos = new Metodos();
//            $post = array_merge_recursive(
//                $request->getPost()->toArray(),
//                $request->getFiles()->toArray()
//            );
//
//            
//            if(empty($post['rangoEdad'])){
//                $mensaje = '<div class="alert alert-danger text-center" role="alert">POR FAVOR SELECCIONE EL RANGO DE EDAD</div>';
//            }else  if(empty($post['sexo'])){
//                $mensaje = '<div class="alert alert-danger text-center" role="alert">POR FAVOR SELECCIONE EL SEXO</div>';
//            }else  if(empty($post['parroquia'])){
//                $mensaje = '<div class="alert alert-danger text-center" role="alert">POR FAVOR SELECCIONE LA PARRÓQUIA</div>';
//            }else if(empty( $post['sector'])){
//                $mensaje = '<div class="alert alert-danger text-center" role="alert">POR FAVOR SELECCIONE EL SECTOR</div>';
//            }else if(empty($post['candidatoAlcalde'])){
//                $mensaje = '<div class="alert alert-danger text-center" role="alert">POR FAVOR SELECCIONE EL CANDIDATO A ALCALDE</div>';
//            }else if(empty($post['candidatoPrefecto'])){
//                $mensaje = '<div class="alert alert-danger text-center" role="alert">POR FAVOR SELECCIONE EL CANDIDATO A PREFECTO</div>';
//            }else{
//                $idRangoEdad = $objMetodos->desencriptar($post['rangoEdad']);
//                $idSexo = $objMetodos->desencriptar($post['sexo']);
//                $idSector = $objMetodos->desencriptar($post['sector']);
//                $idParroquia = $objMetodos->desencriptar($post['parroquia']);
//                $listaRangoEdad = $objRangoEdad->filtrarRangoEdad($idRangoEdad);
//                $listaSexo = $objSexo->filtrarSexo($idSexo);
//                $listaSector = $objSector->filtrarSector($idSector);
//                $listaParroquia = $objParroquia->filtrarParroquia($idParroquia);
//                if(count($listaRangoEdad) == 0){
//                    $mensaje = '<div class="alert alert-danger text-center" role="alert">EL RANGO DE EDAD SELECCIONADO NO EXISTE EN LA BASE DE DATOS</div>';
//                }else if(count($listaSexo) == 0){
//                    $mensaje = '<div class="alert alert-danger text-center" role="alert">EL SEXO SELECCIONADO NO EXISTE EN LA BASE DE DATOS</div>';
//                }else if(count($listaSector) == 0){
//                    $mensaje = '<div class="alert alert-danger text-center" role="alert">EL SECTOR SELECCIONADO NO EXISTE EN LA BASE DE DATOS</div>';
//                }else if(count($listaParroquia) == 0){
//                    $mensaje = '<div class="alert alert-danger text-center" role="alert">LA PARRÓQUIA SELECCIONADA NO EXISTE EN LA BASE DE DATOS</div>';
//                }else{
//                     
//                    $listaTotalEncuestas = $this->dbAdapter->query("SELECT totalencuestasparroquiasector.*,sector.idSector , sector.descripcionSector 
//                    FROM totalencuestasparroquiasector inner join parroquia on totalencuestasparroquiasector.idParroquia = parroquia.idParroquia
//                    inner join sector on totalencuestasparroquiasector.idSector = sector.idSector 
//                    where totalencuestasparroquiasector.idParroquia = $idParroquia and totalencuestasparroquiasector.idSector = $idSector ",Adapter::QUERY_MODE_EXECUTE)->toArray();
//                    $listaCabeceraEncuesta2 = $objCabeceraEncuesta->filtrarCabeceraEncuestaPorParroquiaSector($idParroquia, $idSector);
//                    
//                    $encuestasDisponibles = $listaTotalEncuestas[0]['totalEncuestas'] - count($listaCabeceraEncuesta2); 
//                    if($encuestasDisponibles > 0){
//                    
//                        $idAlcalde = $objMetodos->desencriptar($post['candidatoAlcalde']);
//                        $idPrefecto = $objMetodos->desencriptar($post['candidatoPrefecto']);
//                        
//                        $listaOpcionAlcalde = $this->dbAdapter->query("SELECT opcionespregunta.idOpcionPregunta,opcionespregunta.idPregunta, candidatos.*,tipocandidato.identificadorTipoCandidato
//                                    FROM opcionespregunta 
//                                    inner join candidatos on opcionespregunta.idCandidato = candidatos.idCandidato
//                                    inner join tipocandidato on candidatos.idTipoCandidato = tipocandidato.idTipoCandidato
//                                    where opcionespregunta.idOpcionPregunta = $idAlcalde",Adapter::QUERY_MODE_EXECUTE)->toArray();
//
//                        $listaOpcionPrefecto = $this->dbAdapter->query("SELECT opcionespregunta.idOpcionPregunta,opcionespregunta.idPregunta, candidatos.*,tipocandidato.identificadorTipoCandidato
//                                    FROM opcionespregunta 
//                                    inner join candidatos on opcionespregunta.idCandidato = candidatos.idCandidato
//                                    inner join tipocandidato on candidatos.idTipoCandidato = tipocandidato.idTipoCandidato
//                                    where opcionespregunta.idOpcionPregunta = $idPrefecto",Adapter::QUERY_MODE_EXECUTE)->toArray();
//
//                        if(count($listaOpcionAlcalde) == 0){
//                            $mensaje = '<div class="alert alert-danger text-center" role="alert">EL ALCALDE SELECCIONADO NO EXISTE EN LA BASE DE DATOS</div>';
//                        }else if(count($listaOpcionPrefecto) == 0){
//                            $mensaje = '<div class="alert alert-danger text-center" role="alert">EL PREFECTO SELECCIONADO NO EXISTE EN LA BASE DE DATOS</div>';
//                        }else{
//                            $identificadorCandidatoAlcalde = $listaOpcionAlcalde[0]['identificadorCandidato'];
//                            $identificadorCandidatoPrefecto = $listaOpcionPrefecto[0]['identificadorCandidato'];
//                            $observacionAlcalde ="";
//                            $observacionPrefecto = "";
//                            $validarOtroCandidatoAlcalde = TRUE;
//                            $validarOtroCandidatoPrefecto = TRUE;
//                            if($identificadorCandidatoAlcalde == 7){
//                                 $observacionAlcalde = $post["nombreOtroCandidatoAlcalde"];
//                                 if($observacionAlcalde == ""){
//                                     $validarOtroCandidatoAlcalde = FALSE;
//                                 }
//                            }
//                            if($identificadorCandidatoPrefecto == 7){
//                                 $observacionPrefecto = $post["nombreOtroCandidatoPrefecto"];
//                                 if($observacionPrefecto == ""){
//                                     $validarOtroCandidatoPrefecto = FALSE;
//                                 }
//                            }
//                            if($validarOtroCandidatoAlcalde == FALSE){
//                                $mensaje = '<div class="alert alert-danger text-center" role="alert">INGRESE EL NOMBRE DEL OTRO CANDIDATO A ALCALDE</div>';
//                            }else if($validarOtroCandidatoPrefecto == FALSE){
//                                $mensaje = '<div class="alert alert-danger text-center" role="alert">INGRESE EL NOMBRE DEL OTRO CANDIDATO A PREFECTO</div>';
//                            }else{
//                                ini_set('date.timezone','America/Bogota'); 
//                                $hoy = getdate();
//                                $fechaSubida = $hoy['year']."-".$hoy['mon']."-".$hoy['mday']." ".$hoy['hours'].":".$hoy['minutes'].":".$hoy['seconds'];
//                                $arrayCabecera = array(
//                                    'idRangoEdad'=>$idRangoEdad,
//                                    'idSexo'=>$idSexo,
//                                    'idSector'=>$idSector,
//                                    'idParroquia'=>$idParroquia,
//                                    'fechaIngreso'=>$fechaSubida,
//                                    'estadoCabecera'=>1
//                                );
//                                
//                                if($listaParroquia[0]['identificadorParroquia'] == 1){
//                                    if(empty($post['candidatoConcejal'])){
//                                        $mensaje = '<div class="alert alert-danger text-center" role="alert">POR FAVOR SELECCIONE EL CANDIDATO A CONCEJAL</div>';
//                                    }else if(empty($post['candidatoJunta'])){
//                                        $mensaje = '<div class="alert alert-danger text-center" role="alert">POR FAVOR SELECCIONE EL CANDIDATO A LA JUNTA PARROQUIAL</div>';
//                                    }else{
//                                        $idConcejal = $objMetodos->desencriptar($post['candidatoConcejal']);
//                                        $idJunta = $objMetodos->desencriptar($post['candidatoJunta']);
//                                        $listaOpcionConcejal = $this->dbAdapter->query("SELECT opcionespregunta.idOpcionPregunta,opcionespregunta.idPregunta, candidatos.*,tipocandidato.identificadorTipoCandidato
//                                        FROM opcionespregunta 
//                                        inner join candidatos on opcionespregunta.idCandidato = candidatos.idCandidato
//                                        inner join tipocandidato on candidatos.idTipoCandidato = tipocandidato.idTipoCandidato
//                                        where opcionespregunta.idOpcionPregunta = $idConcejal",Adapter::QUERY_MODE_EXECUTE)->toArray();
//                                        
//                                        $listaOpcionJunta = $this->dbAdapter->query("SELECT opcionespregunta.idOpcionPregunta,opcionespregunta.idPregunta, candidatos.*,tipocandidato.identificadorTipoCandidato
//                                        FROM opcionespregunta 
//                                        inner join candidatos on opcionespregunta.idCandidato = candidatos.idCandidato
//                                        inner join tipocandidato on candidatos.idTipoCandidato = tipocandidato.idTipoCandidato
//                                        where opcionespregunta.idOpcionPregunta = $idJunta",Adapter::QUERY_MODE_EXECUTE)->toArray();
//                                        if(count($listaOpcionConcejal) == 0){
//                                            $mensaje = '<div class="alert alert-danger text-center" role="alert">EL CONCEJAL SELECCIONADO NO EXISTE EN LA BASE DE DATOS</div>';
//                                        }else if(count($listaOpcionJunta) == 0){
//                                            $mensaje = '<div class="alert alert-danger text-center" role="alert">EL CANDIDATO A LA JUNTA PARROQUIAL SELECCIONADO NO EXISTE EN LA BASE DE DATOS</div>';
//                                        }else{
//                                            $identificadorCandidatoConcejal = $listaOpcionConcejal[0]['identificadorCandidato'];
//                                            $identificadorCandidatoJunta = $listaOpcionJunta[0]['identificadorCandidato'];
//                                            $validarOtroCandidatoConcejal = TRUE;
//                                            $observacionConcejal = "";
//                                            
//                                            if($identificadorCandidatoConcejal == 7){
//                                                $observacionConcejal = $post["nombreOtroCandidatoConcejal"];
//                                                if($observacionConcejal == ""){
//                                                    $validarOtroCandidatoConcejal = FALSE;
//                                                }
//                                            }
//                                             $validarOtroCandidatoJunta = TRUE;
//                                            $observacionJunta = "";
//                                             if($identificadorCandidatoJunta == 7){
//                                                $observacionJunta = $post["nombreOtroCandidatoJunta"];
//                                                if($observacionJunta == ""){
//                                                    $validarOtroCandidatoJunta = FALSE;
//                                                }
//                                            }
//                                            
//                                            if($validarOtroCandidatoConcejal == FALSE){
//                                                $mensaje = '<div class="alert alert-danger text-center" role="alert">INGRESE EL NOMBRE DEL OTRO CANDIDATO A CONCEJAL</div>';
//                                            }else  if($validarOtroCandidatoJunta == FALSE){
//                                                $mensaje = '<div class="alert alert-danger text-center" role="alert">INGRESE EL NOMBRE DEL OTRO CANDIDATO A LA JUNTA PARROQUIAL</div>';
//                                            }else{
//
//                                                $idCabecera = $objCabeceraEncuesta->ingresarCabeceraEncuesta($arrayCabecera);
//                                                if($idCabecera == 0){
//                                                    $mensaje = '<div class="alert alert-danger text-center" role="alert">NO SE INGRESÓ LA ENCUESTA POR FAVOR INTENTE MÁS TARDE</div>';
//                                                }else{
//                                                    $validarIngresoCuerpo = TRUE;
//                                                    $arrayCuerpoAlcalde = array(
//                                                        'idCabeceraEncuesta'=>$idCabecera,
//                                                        'idPregunta'=>$listaOpcionAlcalde[0]['idPregunta'],
//                                                        'idOpcionPregunta'=>$idAlcalde,
//                                                        'observacion'=>strtoupper($observacionAlcalde),
//                                                        'estadoCuerpoEncuesta'=>1 
//                                                    );
//                                                    if($objCuerpoEncueta->ingresarCuerpoEncuesta($arrayCuerpoAlcalde) == 0){
//                                                        $validarIngresoCuerpo = FALSE;
//                                                    }
//                                                    $arrayCuerpoConcejal = array(
//                                                        'idCabeceraEncuesta'=>$idCabecera,
//                                                        'idPregunta'=>$listaOpcionConcejal[0]['idPregunta'],
//                                                        'idOpcionPregunta'=>$idConcejal,
//                                                        'observacion'=>strtoupper($observacionConcejal),
//                                                        'estadoCuerpoEncuesta'=>1 
//                                                    );
//                                                    if($objCuerpoEncueta->ingresarCuerpoEncuesta($arrayCuerpoConcejal) == 0){
//                                                        $validarIngresoCuerpo = FALSE;
//                                                    }
//                                                    $arrayCuerpoPrefecto = array(
//                                                        'idCabeceraEncuesta'=>$idCabecera,
//                                                        'idPregunta'=>$listaOpcionPrefecto[0]['idPregunta'],
//                                                        'idOpcionPregunta'=>$idPrefecto,
//                                                        'observacion'=>strtoupper($observacionPrefecto),
//                                                        'estadoCuerpoEncuesta'=>1 
//                                                    );
//                                                    if($objCuerpoEncueta->ingresarCuerpoEncuesta($arrayCuerpoPrefecto) == 0){
//                                                        $validarIngresoCuerpo = FALSE;
//                                                    }
//                                                     $arrayCuerpoJunta = array(
//                                                        'idCabeceraEncuesta'=>$idCabecera,
//                                                        'idPregunta'=>$listaOpcionJunta[0]['idPregunta'],
//                                                        'idOpcionPregunta'=>$idJunta,
//                                                        'observacion'=>strtoupper($observacionJunta),
//                                                        'estadoCuerpoEncuesta'=>1 
//                                                    );
//                                                    if($objCuerpoEncueta->ingresarCuerpoEncuesta($arrayCuerpoJunta) == 0){
//                                                        $validarIngresoCuerpo = FALSE;
//                                                    }
//                                                    
//                                                    if($validarIngresoCuerpo == FALSE){
//                                                        $objCabeceraEncuesta->eliminarCabeceraEncuesta($idCabecera);
//                                                        $mensaje = '<div class="alert alert-danger text-center" role="alert">NO SE INGRESÓ LA ENCUESTA POR FAVOR INTENTE MÁS TARDE</div>';
//                                                    }else{
//                                                        $mensaje = '<div class="alert alert-success text-center" role="alert">ENCUESTA INGRESADA CORRECTAMENTE</div>';
//                                                        $validar = TRUE;
//                                                    }
//                                                }
//                                            }
//                                        }
//                                    }
//                                }else{
//                                    $idCabecera = $objCabeceraEncuesta->ingresarCabeceraEncuesta($arrayCabecera);
//                                    if($idCabecera == 0){
//                                        $mensaje = '<div class="alert alert-danger text-center" role="alert">NO SE INGRESÓ LA ENCUESTA POR FAVOR INTENTE MÁS TARDE</div>';
//                                    }else{
//                                        $validarIngresoCuerpo = TRUE;
//                                        $arrayCuerpoAlcalde = array(
//                                            'idCabeceraEncuesta'=>$idCabecera,
//                                            'idPregunta'=>$listaOpcionAlcalde[0]['idPregunta'],
//                                            'idOpcionPregunta'=>$idAlcalde,
//                                            'observacion'=>strtoupper($observacionAlcalde),
//                                            'estadoCuerpoEncuesta'=>1 
//                                        );
//                                        if($objCuerpoEncueta->ingresarCuerpoEncuesta($arrayCuerpoAlcalde) == 0){
//                                            $validarIngresoCuerpo = FALSE;
//                                        }
//                                        $arrayCuerpoPrefecto = array(
//                                            'idCabeceraEncuesta'=>$idCabecera,
//                                            'idPregunta'=>$listaOpcionPrefecto[0]['idPregunta'],
//                                            'idOpcionPregunta'=>$idPrefecto,
//                                            'observacion'=>strtoupper($observacionPrefecto),
//                                            'estadoCuerpoEncuesta'=>1 
//                                        );
//                                        if($objCuerpoEncueta->ingresarCuerpoEncuesta($arrayCuerpoPrefecto) == 0){
//                                            $validarIngresoCuerpo = FALSE;
//                                        }
//                                        if($validarIngresoCuerpo == FALSE){
//                                            $objCabeceraEncuesta->eliminarCabeceraEncuesta($idCabecera);
//                                            $mensaje = '<div class="alert alert-danger text-center" role="alert">NO SE INGRESÓ LA ENCUESTA POR FAVOR INTENTE MÁS TARDE</div>';
//                                        }else{
//                                            $mensaje = '<div class="alert alert-success text-center" role="alert">ENCUESTA INGRESADA CORRECTAMENTE</div>';
//                                            $validar = TRUE;
//                                        }
//                                    }
//                                }
//                                
//                            }
//                        }
//                    }else{
//                        $mensaje = '<div class="alert alert-danger text-center" role="alert">YA NO HAY ENCUESTAS DISPONIBLES PARA LA PARRÓQUIA '.$listaParroquia[0]['descripcionParroquia'].' EN EL SECTOR '.$listaSector[0]['descripcionSector'].'</div>';
//                    }
//                }
//            }                  
//            
//        }
//        return new JsonModel(array('mensaje'=>$mensaje,'validar'=>$validar));
//    }
//    
//    
//    
//    public function obtenerencuestaAction()
//    {
//        $mensaje = '<div class="alert alert-danger text-center" role="alert">OCURRIÓ UN ERROR INESPERADO</div>';
//        $validar = false;
//        $request=$this->getRequest();
//        if(!$request->isPost()){
//            $this->redirect()->toUrl($this->getRequest()->getBaseUrl().'/index/index');
//        }else{
//            $this->dbAdapter=$this->getServiceLocator()->get('Zend\Db\Adapter');
//            $objRangoEdad = new RangoEdad($this->dbAdapter);
//            $objSexo = new Sexo($this->dbAdapter);
//            $objSector = new Sector($this->dbAdapter);
//            $objParroquia = new Parroquia($this->dbAdapter);
//            $objCabeceraEncuesta = new CabeceraEncuesta($this->dbAdapter);
//            $objMetodos = new Metodos();
//            $listaRangoEdad = $objRangoEdad->obtenerRangoEdad();
//            $radioRangoEdad = '';
//            foreach ($listaRangoEdad as $valueRangoEdad) {
//                $idRangoEdadEncriptado = $objMetodos->encriptar($valueRangoEdad['idRangoEdad']);
//                $radioRangoEdad = $radioRangoEdad.'<td><h4><b>'.$valueRangoEdad['descripcionEdad'].'</b></h4><input  style="width:50px;height:30px; cursor: pointer;" type="radio"  name="rangoEdad" value="'.$idRangoEdadEncriptado.'"></td>';
//            }
//            $listaSexo = $objSexo->obtenerSexo();
//            $radioSexo = '';
//            foreach ($listaSexo as $valueSexo) {
//                $idSexoEncriptado = $objMetodos->encriptar($valueSexo['idSexo']);
//                $radioSexo = $radioSexo.'<td colspan="3"><h4><b>'.$valueSexo['descripcionSexo'].'</b></h4><input  style="width:50px;height:30px; cursor: pointer;" type="radio" name="sexo" value="'.$idSexoEncriptado.'"></td>';
//            }
//            $listaSector = $objSector->obtenerSector();
//            $radioSector = '';
//            foreach ($listaSector as $valueSector) {
//                $idSectorEncriptado = $objMetodos->encriptar($valueSector['idSector']);
//                $radioSector = $radioSector.'<td colspan="3"><h4><b>'.$valueSector['descripcionSector'].'</b></h4><input   style="width:50px;height:30px; cursor: pointer;" type="radio" name="sector" value="'.$idSectorEncriptado.'"></td>';
//            }
//            
//            $listaParroquia = $objParroquia->obtenerParroquias();
//            $radioParroquias = '';
//            $filaParroquias = '';
//            
//            foreach ($listaParroquia as $valueParroquia){
//                $idParroquia = $valueParroquia['idParroquia'];
//                $listaTotalEncuestas = $this->dbAdapter->query("SELECT totalencuestasparroquiasector.*,sector.idSector , sector.descripcionSector 
//                     FROM totalencuestasparroquiasector inner join parroquia on totalencuestasparroquiasector.idParroquia = parroquia.idParroquia
//                        inner join sector on totalencuestasparroquiasector.idSector = sector.idSector 
//                        where totalencuestasparroquiasector.idParroquia = $idParroquia",Adapter::QUERY_MODE_EXECUTE)->toArray();
//                $filaTotalEncuesta = '';
//                $primeraFilaTotalEncuesta ='';
//                $rowParroquias= 0;
//                $encuestasDisponiblesCanton = 0;
//                foreach ($listaTotalEncuestas as $valueTotalEncuestas) {
//                    $listaCabeceraEncuesta = $objCabeceraEncuesta->filtrarCabeceraEncuestaPorParroquiaSector($valueTotalEncuestas['idParroquia'], $valueTotalEncuestas['idSector']);
//                    $encuestasDisponibles =  $valueTotalEncuestas['totalEncuestas'] - count($listaCabeceraEncuesta) ;
//                    if($rowParroquias == 0){
//                         $primeraFilaTotalEncuesta = $primeraFilaTotalEncuesta.'<td><b>'.$valueTotalEncuestas['descripcionSector'].'</b></td>
//                                 <td class="text-center"><b>'.$valueTotalEncuestas['totalEncuestas'].'</b>
//                                 <td class="text-center"><b>'.$encuestasDisponibles.'</b></td>
//                                 </td>';
//                     }else{
//                         $filaTotalEncuesta = $filaTotalEncuesta.'<tr><td><b>'.$valueTotalEncuestas['descripcionSector'].'</b></td>
//                                 <td class="text-center"><b>'.$valueTotalEncuestas['totalEncuestas'].'</b></td>
//                                 <td class="text-center"><b>'.$encuestasDisponibles.'</b></td>
//                                 </tr>';
//                     }
//                     $rowParroquias++;
//                     $encuestasDisponiblesCanton = $encuestasDisponiblesCanton + $encuestasDisponibles;
//                }
//                $filaParroquias = $filaParroquias.'<tr><td rowspan="'.$rowParroquias.'"><b>'.$valueParroquia['descripcionParroquia'].'</b></td>'.$primeraFilaTotalEncuesta.'</tr>'.$filaTotalEncuesta;
//                $nombreParroquia = '<h4><b>'.$valueParroquia['descripcionParroquia'].'<br> (SIN ENCUESTAS DISPONIBLES)</b></h4>';
//                if($encuestasDisponiblesCanton > 0){
//                    $idParroquiaEncriptado = $objMetodos->encriptar($valueParroquia['idParroquia']);
//                    $nombreParroquia = '<h4><b>'.$valueParroquia['descripcionParroquia'].'</b></h4><input onchange="filtrarCuerpoEncuesta(\''.$idParroquiaEncriptado.'\');"  style="width:50px;height:30px; cursor: pointer;" type="radio" name="parroquia" value="'.$idParroquiaEncriptado.'">';
//                    
//                }
//                
//                $radioParroquias = $radioParroquias.'<td colspan="2">'.$nombreParroquia.'</td>';
//            }
//             $tablaTotalEncuestas = '<div class="table-responsive">
//                    <table class="table">
//                        <thead>
//                            <tr style="background-color: #B3D5FC;"><td><b>PARRÓQUIA</b></td>
//                            <td><b>SECTOR</b></td>
//                            <td class="text-center"><b>TOTAL ENCUESTAS</b></td>
//                            <td class="text-center"><b>DISPONIBLES</b></td></tr>
//                        </thead>
//                        <tbody>
//                            '.$filaParroquias.'
//                        </tbody>
//                    </table>
//                    </div>';
//            $tabla = $tablaTotalEncuestas.'<div class="table-responsive">
//                    <table class="table">
//                        <tbody>
//                            <tr><td colspan="6" style="background-color: #B3D5FC;"><h4><b>1)¿Cuál es su edad?</b></h4></td></tr>
//                            <tr class="text-center">'.$radioRangoEdad.'</tr>
//                            <tr><td colspan="6" style="background-color: #B3D5FC;"><h4><b>2)Sexo</b></h4></td></tr>
//                            <tr class="text-center">'.$radioSexo.'</tr>
//                            <tr><td colspan="6" style="background-color: #B3D5FC;"><h4><b>3)¿En qué sector vive?</b></h4></td></tr>
//                            <tr class="text-center">'.$radioSector.'</tr>
//                            <tr><td colspan="6" style="background-color: #B3D5FC;"><h4><b>4)¿En qué parróquia vive?</b></h4></td></tr>
//                            <tr class="text-center">'.$radioParroquias.'</tr>
//                        </tbody>
//                    </table>
//                    </div>';
//            $mensaje = '';
//            $validar = TRUE;
//            return new JsonModel(array('mensaje'=>$mensaje,'validar'=>$validar,'tabla'=>$tabla));
//        }
//        return new JsonModel(array('mensaje'=>$mensaje,'validar'=>$validar));
//    }
    
    
    public function filtrarjuntaselectoralesAction()
    {
        $mensaje = '<div class="alert alert-danger text-center" role="alert">OCURRIÓ UN ERROR INESPERADO</div>';
        $validar = false;
        $request=$this->getRequest();
        if(!$request->isPost()){
            $this->redirect()->toUrl($this->getRequest()->getBaseUrl().'/index/index');
        }else{
            $this->dbAdapter=$this->getServiceLocator()->get('Zend\Db\Adapter');
           
            $post = array_merge_recursive(
                $request->getPost()->toArray(),
                $request->getFiles()->toArray()
            );
            $idParroquia = $post['idParroquia'];
            $idSexo=$post['idSexo'];
            if($idParroquia == "" || $idParroquia == NULL){
                $mensaje = '<div class="alert alert-danger text-center" role="alert">NO SE ENCUENTRA EL ÍNDICE DE LA PARRÓQUIA</div>';
            }else if($idSexo == "" || $idSexo == NULL){
                $mensaje = '<div class="alert alert-danger text-center" role="alert">NO SE ENCUENTRA EL ÍNDICE DEL SEXO</div>';
            }else{
                $objParroquia = new Parroquia($this->dbAdapter);
                $listaParroquia = $objParroquia->filtrarParroquia($idParroquia);
                
                if(count($listaParroquia) == 0){
                    $mensaje = '<div class="alert alert-danger text-center" role="alert">LA PARROQUIA SELECCIONADA NO EXISTE EN LA BASE DE DATOS</div>';
                }else{
                    $objSexo = new Sexo($this->dbAdapter);
                    $listaSexo = $objSexo->filtrarSexo($idSexo);

                    if(count($listaSexo) == 0){
                        $mensaje = '<div class="alert alert-danger text-center" role="alert">EL SEXO SELECCIONADO NO EXISTE EN LA BASE DE DATOS</div>';
                    }else{
                    
                          $listaJuntas = $this->dbAdapter->query("SELECT configurarjunta.idConfigurarJunta, juntaelectoral.* FROM configurarjunta
                            INNER JOIN juntaelectoral ON configurarjunta.idJuntaElectoral=juntaelectoral.idJuntaElectoral
                            WHERE configurarjunta.idParroquia=$idParroquia and configurarjunta.idSexo=$idSexo",Adapter::QUERY_MODE_EXECUTE)->toArray();
                          $optionJunta='<option value="0">SELECCIONE UNA JUNTA</option>';
                         foreach ($listaJuntas as $valueJunta) {
                               $optionJunta=$optionJunta.'<option value="'.$valueJunta['idConfigurarJunta'].'">'.$valueJunta['numeroJunta'].'</option>'; 
                            }
                    $mensaje = '';
                    $validar = TRUE;
                    return new JsonModel(array('mensaje'=>$mensaje,'validar'=>$validar,'select'=>$optionJunta));
                    }
                }
            }
        }        
        return new JsonModel(array('mensaje'=>$mensaje,'validar'=>$validar));
    }
    
    
    
     public function filtrarlistaportipocandidatoAction()
    {
        $mensaje = '<div class="alert alert-danger text-center" role="alert">OCURRIÓ UN ERROR INESPERADO</div>';
        $validar = false;
        $request=$this->getRequest();
        if(!$request->isPost()){
            $this->redirect()->toUrl($this->getRequest()->getBaseUrl().'/index/index');
        }else{
            $this->dbAdapter=$this->getServiceLocator()->get('Zend\Db\Adapter');
           
            $post = array_merge_recursive(
                $request->getPost()->toArray(),
                $request->getFiles()->toArray()
            );
            $idTipoCandidato = $post['idTipoCandidato'];
            if($idTipoCandidato == "" || $idTipoCandidato == NULL){
                $mensaje = '<div class="alert alert-danger text-center" role="alert">NO SE ENCUENTRA EL ÍNDICE DEL TIPO DE CANDIDATO</div>';
            }else{
                $objTipoCandidato = new TipoCandidato($this->dbAdapter);
                $listaTipoCandidato = $objTipoCandidato->filtrarTipoCandidato($idTipoCandidato);
                
                if(count($listaTipoCandidato) == 0){
                    $mensaje = '<div class="alert alert-danger text-center" role="alert">EL TIPO DE CANDIDATO SELECCIONADO NO EXISTE EN LA BASE DE DATOS</div>';
                }else{
                    
                    $listaListas = $this->dbAdapter->query("SELECT DISTINCT listas.* FROM candidatos
                        INNER JOIN listas on candidatos.idListaCandidato = listas.idLista
                        where candidatos.idTipoCandidato=$idTipoCandidato",Adapter::QUERY_MODE_EXECUTE)->toArray();
                          $optionListasCandidatos='<option value="0">SELECCIONE UNA JUNTA</option>';
                         foreach ($listaListas as $valueListasC) {
                               $optionListasCandidatos=$optionListasCandidatos.'<option value="'.$valueListasC['idLista'].'">'.$valueListasC['nombreLista'].'</option>'; 
                            }
                    $mensaje = '';
                    $validar = TRUE;
                    return new JsonModel(array('mensaje'=>$mensaje,'validar'=>$validar,'select'=>$optionListasCandidatos));
                    
                }
            }
        }        
        return new JsonModel(array('mensaje'=>$mensaje,'validar'=>$validar));
    }
    
    
    
    
    public function cargarformularioingresovotosAction()
    {
        $mensaje = '<div class="alert alert-danger text-center" role="alert">OCURRIÓ UN ERROR INESPERADO</div>';
        $validar = false;
        $request=$this->getRequest();
        if(!$request->isPost()){
            $this->redirect()->toUrl($this->getRequest()->getBaseUrl().'/index/index');
        }
        else
        {
            $this->dbAdapter=$this->getServiceLocator()->get('Zend\Db\Adapter');
           
            $post = array_merge_recursive(
                $request->getPost()->toArray(),
                $request->getFiles()->toArray()
            );
            $idTipoCandidato = $post['idTipoCandidato'];
            $idListaCandidato=$post['idListaCandidato'];            
            $idConfigurarJunta=$post['idConfigurarJunta'];

            if($idTipoCandidato == "" || $idTipoCandidato == NULL){
                $mensaje = '<div class="alert alert-danger text-center" role="alert">NO SE ENCUENTRA EL ÍNDICE DEL TIPO DE CANDIDATO</div>';
            }else if($idListaCandidato == "" || $idListaCandidato == NULL){
                $mensaje = '<div class="alert alert-danger text-center" role="alert">NO SE ENCUENTRA EL ÍNDICE DE LA LISTA DEL CANDIDATO</div>';
            }else if($idConfigurarJunta == "" || $idConfigurarJunta == NULL){
                $mensaje = '<div class="alert alert-danger text-center" role="alert">NO SE ENCUENTRA EL ÍNDICE DE LA JUNTA</div>';
            }else{
                $objTipoCandidato = new TipoCandidato($this->dbAdapter);
                $listaTipoCandidato = $objTipoCandidato->filtrarTipoCandidato($idTipoCandidato);
                
                if(count($listaTipoCandidato) == 0){
                    $mensaje = '<div class="alert alert-danger text-center" role="alert">EL TIPO DE CANDIDATO SELECCIONADO NO EXISTE EN LA BASE DE DATOS</div>';
                }else{
                        $objConfigurarJunta = new ConfigurarJunta($this->dbAdapter);
                        $listaConfigurarJunta = $objConfigurarJunta->filtrarConfigurarJunta($idConfigurarJunta);
                        
                        if(count($listaConfigurarJunta) == 0){
                            $mensaje = '<div class="alert alert-danger text-center" role="alert">LA JUNTA SELECCIONADA NO EXISTE EN LA BASE DE DATOS</div>';
                        }else
                        {
                            $objListaCandidato = new Listas($this->dbAdapter);
                            $validarLista =true;
                            if($idListaCandidato=="0")
                            {
                               $listaListasCandidato =$objListaCandidato->obtenerListas();                               
                            }
                            else
                            {
                                $listaListasCandidato = $objListaCandidato->filtrarLista($idListaCandidato);

                                if(count($listaListasCandidato) == 0){
                                    $validarLista=FALSE;
                                    $mensaje = '<div class="alert alert-danger text-center" role="alert">LA LISTA SELECCIONADA NO EXISTE EN LA BASE DE DATOS</div>';
                                }
                            }
                            if($validarLista==true)
                            {
                                $grupoDeTablas='';
                                
                                $tablaCandidatosEncabezado='<thead><tr>'
                                                            .'<td style="width: 10%;"  class="text-center">#</td>'
                                                            .'<td style="width: 10%;"  class="text-center">CANDIDATO</td>'                                            
                                                            .'<td style="width: 50%;"  class="text-center">APELLIDOS Y NOMBRES</td>'
                                                            .'<td style="width: 30%;"  class="text-center">NÚMERO DE VOTOS</td>'
                                                        . '</tr></thead>';
                                
                                    
                                        
                                $tablaCandidadosPorLista ='';
                                $objCandidato = new Candidatos($this->dbAdapter);
                                foreach ($listaListasCandidato as $valueLista) {  
                                    
                                    $encabezadoLista='<div class="text-center" style="background-color:#fff;">'
                                                       .'<br>'
                                                        .'<img src="'.$this->getRequest()->getBaseUrl().'/'.$valueLista['rutaFotoLista'].'" class="text-center" style="width: 7%;" >'
                                                        .'<h4><b>'.$valueLista['nombreLista'].'</b></h4>'
                                                        .'<br>'
                                                        . '</div>';
                                    $listaCandidatos = $objCandidato->filtrarCandidatoPorListaPorTipoCandidato($idTipoCandidato, $valueLista['idLista']);
                                    $contador=1;
                                    $cuerpoTablaCandidatos='';
                                    if(count($listaCandidatos)>0)
                                    {
                                            foreach ($listaCandidatos as $valueCandidato) {
                                            $cuerpoTablaCandidatos=$cuerpoTablaCandidatos.'<tr>'
                                                        .'<th class="text-center" style="vertical-align: middle;">'.$valueCandidato['puesto'].'</th>'
                                                        .'<th class="text-center"  style="vertical-align: middle;"><img src="'.$this->getRequest()->getBaseUrl().'/'.$valueCandidato['rutaFotoCandidato'].'" style="width: 100%;"></th>'
                                                        .'<th class="text-center" style="vertical-align: middle;">'.$valueCandidato['nombres'].'</th>'
                                                        .'<th class="text-center"  style="vertical-align: middle;"></th>'
                                                    . '</tr>';
                                            }
                                        $tablaCandidadosPorLista= '<div class="col-lg-12 table-responsive">'
                                                .$encabezadoLista
                                                . '<table  style="background-color:#fff" class="table table-hover">'
                                                    .$tablaCandidatosEncabezado
                                                .'<tbody>'
                                                    .$cuerpoTablaCandidatos
                                                .'<t/body>'
                                                .'</table></div><br><br>';
                                        $grupoDeTablas=$grupoDeTablas.$tablaCandidadosPorLista;
                                    }
                                    
                                    $encabezadoLista='';                                    
                                }
                                $tabla='<div class="col-lg-2"></div>'
                                        .'<div class="col-lg-8">'
                                            .$grupoDeTablas
                                        .'</div>'
                                        .'<div class="col-lg-2"></div>';
                            
                               $mensaje = '';
                               $validar = TRUE;
                               return new JsonModel(array('mensaje'=>$mensaje,'validar'=>$validar,'tabla'=>$tabla));
                            }
                        }
                    }
                }
        }
        return new JsonModel(array('mensaje'=>$mensaje,'validar'=>$validar));
    }
}
    
    
