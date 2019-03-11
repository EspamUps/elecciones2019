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
use Nel\Modelo\Entity\RangoEdad;
use Nel\Modelo\Entity\Sexo;
use Nel\Modelo\Entity\Sector;
use Nel\Modelo\Entity\Parroquia;
use Nel\Modelo\Entity\Preguntas;
use Nel\Modelo\Entity\OpcionesPregunta;
use Nel\Modelo\Entity\CabeceraEncuesta;
use Nel\Modelo\Entity\CuerpoEncuesta;
use Zend\Db\Adapter\Adapter;

class EncuestaController extends AbstractActionController
{
    public $dbAdapter;
    public function ingresarencuestaAction()
    {
        $mensaje = '<div class="alert alert-danger text-center" role="alert">OCURRIÓ UN ERROR INESPERADO</div>';
        $validar = false;
        $request=$this->getRequest();
        if(!$request->isPost()){
            $this->redirect()->toUrl($this->getRequest()->getBaseUrl().'/index/index');
        }else{
            $this->dbAdapter=$this->getServiceLocator()->get('Zend\Db\Adapter');
            $objRangoEdad = new RangoEdad($this->dbAdapter);
            $objSexo = new Sexo($this->dbAdapter);
            $objSector = new Sector($this->dbAdapter);
            $objParroquia = new Parroquia($this->dbAdapter);
            $objOpcionesPregunta = new OpcionesPregunta($this->dbAdapter);
            $objCabeceraEncuesta = new CabeceraEncuesta($this->dbAdapter);
            $objCuerpoEncueta = new CuerpoEncuesta($this->dbAdapter);
            $objMetodos = new Metodos();
            $post = array_merge_recursive(
                $request->getPost()->toArray(),
                $request->getFiles()->toArray()
            );

            
            if(empty($post['rangoEdad'])){
                $mensaje = '<div class="alert alert-danger text-center" role="alert">POR FAVOR SELECCIONE EL RANGO DE EDAD</div>';
            }else  if(empty($post['sexo'])){
                $mensaje = '<div class="alert alert-danger text-center" role="alert">POR FAVOR SELECCIONE EL SEXO</div>';
            }else  if(empty($post['parroquia'])){
                $mensaje = '<div class="alert alert-danger text-center" role="alert">POR FAVOR SELECCIONE LA PARRÓQUIA</div>';
            }else if(empty( $post['sector'])){
                $mensaje = '<div class="alert alert-danger text-center" role="alert">POR FAVOR SELECCIONE EL SECTOR</div>';
            }else if(empty($post['candidatoAlcalde'])){
                $mensaje = '<div class="alert alert-danger text-center" role="alert">POR FAVOR SELECCIONE EL CANDIDATO A ALCALDE</div>';
            }else if(empty($post['candidatoPrefecto'])){
                $mensaje = '<div class="alert alert-danger text-center" role="alert">POR FAVOR SELECCIONE EL CANDIDATO A PREFECTO</div>';
            }else{
                $idRangoEdad = $objMetodos->desencriptar($post['rangoEdad']);
                $idSexo = $objMetodos->desencriptar($post['sexo']);
                $idSector = $objMetodos->desencriptar($post['sector']);
                $idParroquia = $objMetodos->desencriptar($post['parroquia']);
                $listaRangoEdad = $objRangoEdad->filtrarRangoEdad($idRangoEdad);
                $listaSexo = $objSexo->filtrarSexo($idSexo);
                $listaSector = $objSector->filtrarSector($idSector);
                $listaParroquia = $objParroquia->filtrarParroquia($idParroquia);
                if(count($listaRangoEdad) == 0){
                    $mensaje = '<div class="alert alert-danger text-center" role="alert">EL RANGO DE EDAD SELECCIONADO NO EXISTE EN LA BASE DE DATOS</div>';
                }else if(count($listaSexo) == 0){
                    $mensaje = '<div class="alert alert-danger text-center" role="alert">EL SEXO SELECCIONADO NO EXISTE EN LA BASE DE DATOS</div>';
                }else if(count($listaSector) == 0){
                    $mensaje = '<div class="alert alert-danger text-center" role="alert">EL SECTOR SELECCIONADO NO EXISTE EN LA BASE DE DATOS</div>';
                }else if(count($listaParroquia) == 0){
                    $mensaje = '<div class="alert alert-danger text-center" role="alert">LA PARRÓQUIA SELECCIONADA NO EXISTE EN LA BASE DE DATOS</div>';
                }else{
                     
                    $listaTotalEncuestas = $this->dbAdapter->query("SELECT totalencuestasparroquiasector.*,sector.idSector , sector.descripcionSector 
                    FROM totalencuestasparroquiasector inner join parroquia on totalencuestasparroquiasector.idParroquia = parroquia.idParroquia
                    inner join sector on totalencuestasparroquiasector.idSector = sector.idSector 
                    where totalencuestasparroquiasector.idParroquia = $idParroquia and totalencuestasparroquiasector.idSector = $idSector ",Adapter::QUERY_MODE_EXECUTE)->toArray();
                    $listaCabeceraEncuesta2 = $objCabeceraEncuesta->filtrarCabeceraEncuestaPorParroquiaSector($idParroquia, $idSector);
                    
                    $encuestasDisponibles = $listaTotalEncuestas[0]['totalEncuestas'] - count($listaCabeceraEncuesta2); 
                    if($encuestasDisponibles > 0){
                    
                        $idAlcalde = $objMetodos->desencriptar($post['candidatoAlcalde']);
                        $idPrefecto = $objMetodos->desencriptar($post['candidatoPrefecto']);
                        
                        $listaOpcionAlcalde = $this->dbAdapter->query("SELECT opcionespregunta.idOpcionPregunta,opcionespregunta.idPregunta, candidatos.*,tipocandidato.identificadorTipoCandidato
                                    FROM opcionespregunta 
                                    inner join candidatos on opcionespregunta.idCandidato = candidatos.idCandidato
                                    inner join tipocandidato on candidatos.idTipoCandidato = tipocandidato.idTipoCandidato
                                    where opcionespregunta.idOpcionPregunta = $idAlcalde",Adapter::QUERY_MODE_EXECUTE)->toArray();

                        $listaOpcionPrefecto = $this->dbAdapter->query("SELECT opcionespregunta.idOpcionPregunta,opcionespregunta.idPregunta, candidatos.*,tipocandidato.identificadorTipoCandidato
                                    FROM opcionespregunta 
                                    inner join candidatos on opcionespregunta.idCandidato = candidatos.idCandidato
                                    inner join tipocandidato on candidatos.idTipoCandidato = tipocandidato.idTipoCandidato
                                    where opcionespregunta.idOpcionPregunta = $idPrefecto",Adapter::QUERY_MODE_EXECUTE)->toArray();

                        if(count($listaOpcionAlcalde) == 0){
                            $mensaje = '<div class="alert alert-danger text-center" role="alert">EL ALCALDE SELECCIONADO NO EXISTE EN LA BASE DE DATOS</div>';
                        }else if(count($listaOpcionPrefecto) == 0){
                            $mensaje = '<div class="alert alert-danger text-center" role="alert">EL PREFECTO SELECCIONADO NO EXISTE EN LA BASE DE DATOS</div>';
                        }else{
                            $identificadorCandidatoAlcalde = $listaOpcionAlcalde[0]['identificadorCandidato'];
                            $identificadorCandidatoPrefecto = $listaOpcionPrefecto[0]['identificadorCandidato'];
                            $observacionAlcalde ="";
                            $observacionPrefecto = "";
                            $validarOtroCandidatoAlcalde = TRUE;
                            $validarOtroCandidatoPrefecto = TRUE;
                            if($identificadorCandidatoAlcalde == 7){
                                 $observacionAlcalde = $post["nombreOtroCandidatoAlcalde"];
                                 if($observacionAlcalde == ""){
                                     $validarOtroCandidatoAlcalde = FALSE;
                                 }
                            }
                            if($identificadorCandidatoPrefecto == 7){
                                 $observacionPrefecto = $post["nombreOtroCandidatoPrefecto"];
                                 if($observacionPrefecto == ""){
                                     $validarOtroCandidatoPrefecto = FALSE;
                                 }
                            }
                            if($validarOtroCandidatoAlcalde == FALSE){
                                $mensaje = '<div class="alert alert-danger text-center" role="alert">INGRESE EL NOMBRE DEL OTRO CANDIDATO A ALCALDE</div>';
                            }else if($validarOtroCandidatoPrefecto == FALSE){
                                $mensaje = '<div class="alert alert-danger text-center" role="alert">INGRESE EL NOMBRE DEL OTRO CANDIDATO A PREFECTO</div>';
                            }else{
                                ini_set('date.timezone','America/Bogota'); 
                                $hoy = getdate();
                                $fechaSubida = $hoy['year']."-".$hoy['mon']."-".$hoy['mday']." ".$hoy['hours'].":".$hoy['minutes'].":".$hoy['seconds'];
                                $arrayCabecera = array(
                                    'idRangoEdad'=>$idRangoEdad,
                                    'idSexo'=>$idSexo,
                                    'idSector'=>$idSector,
                                    'idParroquia'=>$idParroquia,
                                    'fechaIngreso'=>$fechaSubida,
                                    'estadoCabecera'=>1
                                );
                                
                                if($listaParroquia[0]['identificadorParroquia'] == 1){
                                    if(empty($post['candidatoConcejal'])){
                                        $mensaje = '<div class="alert alert-danger text-center" role="alert">POR FAVOR SELECCIONE EL CANDIDATO A CONCEJAL</div>';
                                    }else if(empty($post['candidatoJunta'])){
                                        $mensaje = '<div class="alert alert-danger text-center" role="alert">POR FAVOR SELECCIONE EL CANDIDATO A LA JUNTA PARROQUIAL</div>';
                                    }else{
                                        $idConcejal = $objMetodos->desencriptar($post['candidatoConcejal']);
                                        $idJunta = $objMetodos->desencriptar($post['candidatoJunta']);
                                        $listaOpcionConcejal = $this->dbAdapter->query("SELECT opcionespregunta.idOpcionPregunta,opcionespregunta.idPregunta, candidatos.*,tipocandidato.identificadorTipoCandidato
                                        FROM opcionespregunta 
                                        inner join candidatos on opcionespregunta.idCandidato = candidatos.idCandidato
                                        inner join tipocandidato on candidatos.idTipoCandidato = tipocandidato.idTipoCandidato
                                        where opcionespregunta.idOpcionPregunta = $idConcejal",Adapter::QUERY_MODE_EXECUTE)->toArray();
                                        
                                        $listaOpcionJunta = $this->dbAdapter->query("SELECT opcionespregunta.idOpcionPregunta,opcionespregunta.idPregunta, candidatos.*,tipocandidato.identificadorTipoCandidato
                                        FROM opcionespregunta 
                                        inner join candidatos on opcionespregunta.idCandidato = candidatos.idCandidato
                                        inner join tipocandidato on candidatos.idTipoCandidato = tipocandidato.idTipoCandidato
                                        where opcionespregunta.idOpcionPregunta = $idJunta",Adapter::QUERY_MODE_EXECUTE)->toArray();
                                        if(count($listaOpcionConcejal) == 0){
                                            $mensaje = '<div class="alert alert-danger text-center" role="alert">EL CONCEJAL SELECCIONADO NO EXISTE EN LA BASE DE DATOS</div>';
                                        }else if(count($listaOpcionJunta) == 0){
                                            $mensaje = '<div class="alert alert-danger text-center" role="alert">EL CANDIDATO A LA JUNTA PARROQUIAL SELECCIONADO NO EXISTE EN LA BASE DE DATOS</div>';
                                        }else{
                                            $identificadorCandidatoConcejal = $listaOpcionConcejal[0]['identificadorCandidato'];
                                            $identificadorCandidatoJunta = $listaOpcionJunta[0]['identificadorCandidato'];
                                            $validarOtroCandidatoConcejal = TRUE;
                                            $observacionConcejal = "";
                                            
                                            if($identificadorCandidatoConcejal == 7){
                                                $observacionConcejal = $post["nombreOtroCandidatoConcejal"];
                                                if($observacionConcejal == ""){
                                                    $validarOtroCandidatoConcejal = FALSE;
                                                }
                                            }
                                             $validarOtroCandidatoJunta = TRUE;
                                            $observacionJunta = "";
                                             if($identificadorCandidatoJunta == 7){
                                                $observacionJunta = $post["nombreOtroCandidatoJunta"];
                                                if($observacionJunta == ""){
                                                    $validarOtroCandidatoJunta = FALSE;
                                                }
                                            }
                                            
                                            if($validarOtroCandidatoConcejal == FALSE){
                                                $mensaje = '<div class="alert alert-danger text-center" role="alert">INGRESE EL NOMBRE DEL OTRO CANDIDATO A CONCEJAL</div>';
                                            }else  if($validarOtroCandidatoJunta == FALSE){
                                                $mensaje = '<div class="alert alert-danger text-center" role="alert">INGRESE EL NOMBRE DEL OTRO CANDIDATO A LA JUNTA PARROQUIAL</div>';
                                            }else{

                                                $idCabecera = $objCabeceraEncuesta->ingresarCabeceraEncuesta($arrayCabecera);
                                                if($idCabecera == 0){
                                                    $mensaje = '<div class="alert alert-danger text-center" role="alert">NO SE INGRESÓ LA ENCUESTA POR FAVOR INTENTE MÁS TARDE</div>';
                                                }else{
                                                    $validarIngresoCuerpo = TRUE;
                                                    $arrayCuerpoAlcalde = array(
                                                        'idCabeceraEncuesta'=>$idCabecera,
                                                        'idPregunta'=>$listaOpcionAlcalde[0]['idPregunta'],
                                                        'idOpcionPregunta'=>$idAlcalde,
                                                        'observacion'=>strtoupper($observacionAlcalde),
                                                        'estadoCuerpoEncuesta'=>1 
                                                    );
                                                    if($objCuerpoEncueta->ingresarCuerpoEncuesta($arrayCuerpoAlcalde) == 0){
                                                        $validarIngresoCuerpo = FALSE;
                                                    }
                                                    $arrayCuerpoConcejal = array(
                                                        'idCabeceraEncuesta'=>$idCabecera,
                                                        'idPregunta'=>$listaOpcionConcejal[0]['idPregunta'],
                                                        'idOpcionPregunta'=>$idConcejal,
                                                        'observacion'=>strtoupper($observacionConcejal),
                                                        'estadoCuerpoEncuesta'=>1 
                                                    );
                                                    if($objCuerpoEncueta->ingresarCuerpoEncuesta($arrayCuerpoConcejal) == 0){
                                                        $validarIngresoCuerpo = FALSE;
                                                    }
                                                    $arrayCuerpoPrefecto = array(
                                                        'idCabeceraEncuesta'=>$idCabecera,
                                                        'idPregunta'=>$listaOpcionPrefecto[0]['idPregunta'],
                                                        'idOpcionPregunta'=>$idPrefecto,
                                                        'observacion'=>strtoupper($observacionPrefecto),
                                                        'estadoCuerpoEncuesta'=>1 
                                                    );
                                                    if($objCuerpoEncueta->ingresarCuerpoEncuesta($arrayCuerpoPrefecto) == 0){
                                                        $validarIngresoCuerpo = FALSE;
                                                    }
                                                     $arrayCuerpoJunta = array(
                                                        'idCabeceraEncuesta'=>$idCabecera,
                                                        'idPregunta'=>$listaOpcionJunta[0]['idPregunta'],
                                                        'idOpcionPregunta'=>$idJunta,
                                                        'observacion'=>strtoupper($observacionJunta),
                                                        'estadoCuerpoEncuesta'=>1 
                                                    );
                                                    if($objCuerpoEncueta->ingresarCuerpoEncuesta($arrayCuerpoJunta) == 0){
                                                        $validarIngresoCuerpo = FALSE;
                                                    }
                                                    
                                                    if($validarIngresoCuerpo == FALSE){
                                                        $objCabeceraEncuesta->eliminarCabeceraEncuesta($idCabecera);
                                                        $mensaje = '<div class="alert alert-danger text-center" role="alert">NO SE INGRESÓ LA ENCUESTA POR FAVOR INTENTE MÁS TARDE</div>';
                                                    }else{
                                                        $mensaje = '<div class="alert alert-success text-center" role="alert">ENCUESTA INGRESADA CORRECTAMENTE</div>';
                                                        $validar = TRUE;
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }else{
                                    $idCabecera = $objCabeceraEncuesta->ingresarCabeceraEncuesta($arrayCabecera);
                                    if($idCabecera == 0){
                                        $mensaje = '<div class="alert alert-danger text-center" role="alert">NO SE INGRESÓ LA ENCUESTA POR FAVOR INTENTE MÁS TARDE</div>';
                                    }else{
                                        $validarIngresoCuerpo = TRUE;
                                        $arrayCuerpoAlcalde = array(
                                            'idCabeceraEncuesta'=>$idCabecera,
                                            'idPregunta'=>$listaOpcionAlcalde[0]['idPregunta'],
                                            'idOpcionPregunta'=>$idAlcalde,
                                            'observacion'=>strtoupper($observacionAlcalde),
                                            'estadoCuerpoEncuesta'=>1 
                                        );
                                        if($objCuerpoEncueta->ingresarCuerpoEncuesta($arrayCuerpoAlcalde) == 0){
                                            $validarIngresoCuerpo = FALSE;
                                        }
                                        $arrayCuerpoPrefecto = array(
                                            'idCabeceraEncuesta'=>$idCabecera,
                                            'idPregunta'=>$listaOpcionPrefecto[0]['idPregunta'],
                                            'idOpcionPregunta'=>$idPrefecto,
                                            'observacion'=>strtoupper($observacionPrefecto),
                                            'estadoCuerpoEncuesta'=>1 
                                        );
                                        if($objCuerpoEncueta->ingresarCuerpoEncuesta($arrayCuerpoPrefecto) == 0){
                                            $validarIngresoCuerpo = FALSE;
                                        }
                                        if($validarIngresoCuerpo == FALSE){
                                            $objCabeceraEncuesta->eliminarCabeceraEncuesta($idCabecera);
                                            $mensaje = '<div class="alert alert-danger text-center" role="alert">NO SE INGRESÓ LA ENCUESTA POR FAVOR INTENTE MÁS TARDE</div>';
                                        }else{
                                            $mensaje = '<div class="alert alert-success text-center" role="alert">ENCUESTA INGRESADA CORRECTAMENTE</div>';
                                            $validar = TRUE;
                                        }
                                    }
                                }
                                
                            }
                        }
                    }else{
                        $mensaje = '<div class="alert alert-danger text-center" role="alert">YA NO HAY ENCUESTAS DISPONIBLES PARA LA PARRÓQUIA '.$listaParroquia[0]['descripcionParroquia'].' EN EL SECTOR '.$listaSector[0]['descripcionSector'].'</div>';
                    }
                }
            }                  
            
        }
        return new JsonModel(array('mensaje'=>$mensaje,'validar'=>$validar));
    }
    
    
    
    public function obtenerencuestaAction()
    {
        $mensaje = '<div class="alert alert-danger text-center" role="alert">OCURRIÓ UN ERROR INESPERADO</div>';
        $validar = false;
        $request=$this->getRequest();
        if(!$request->isPost()){
            $this->redirect()->toUrl($this->getRequest()->getBaseUrl().'/index/index');
        }else{
            $this->dbAdapter=$this->getServiceLocator()->get('Zend\Db\Adapter');
            $objRangoEdad = new RangoEdad($this->dbAdapter);
            $objSexo = new Sexo($this->dbAdapter);
            $objSector = new Sector($this->dbAdapter);
            $objParroquia = new Parroquia($this->dbAdapter);
            $objCabeceraEncuesta = new CabeceraEncuesta($this->dbAdapter);
            $objMetodos = new Metodos();
            $listaRangoEdad = $objRangoEdad->obtenerRangoEdad();
            $radioRangoEdad = '';
            foreach ($listaRangoEdad as $valueRangoEdad) {
                $idRangoEdadEncriptado = $objMetodos->encriptar($valueRangoEdad['idRangoEdad']);
                $radioRangoEdad = $radioRangoEdad.'<td><h4><b>'.$valueRangoEdad['descripcionEdad'].'</b></h4><input  style="width:50px;height:30px; cursor: pointer;" type="radio"  name="rangoEdad" value="'.$idRangoEdadEncriptado.'"></td>';
            }
            $listaSexo = $objSexo->obtenerSexo();
            $radioSexo = '';
            foreach ($listaSexo as $valueSexo) {
                $idSexoEncriptado = $objMetodos->encriptar($valueSexo['idSexo']);
                $radioSexo = $radioSexo.'<td colspan="3"><h4><b>'.$valueSexo['descripcionSexo'].'</b></h4><input  style="width:50px;height:30px; cursor: pointer;" type="radio" name="sexo" value="'.$idSexoEncriptado.'"></td>';
            }
            $listaSector = $objSector->obtenerSector();
            $radioSector = '';
            foreach ($listaSector as $valueSector) {
                $idSectorEncriptado = $objMetodos->encriptar($valueSector['idSector']);
                $radioSector = $radioSector.'<td colspan="3"><h4><b>'.$valueSector['descripcionSector'].'</b></h4><input   style="width:50px;height:30px; cursor: pointer;" type="radio" name="sector" value="'.$idSectorEncriptado.'"></td>';
            }
            
            $listaParroquia = $objParroquia->obtenerParroquias();
            $radioParroquias = '';
            $filaParroquias = '';
            
            foreach ($listaParroquia as $valueParroquia){
                $idParroquia = $valueParroquia['idParroquia'];
                $listaTotalEncuestas = $this->dbAdapter->query("SELECT totalencuestasparroquiasector.*,sector.idSector , sector.descripcionSector 
                     FROM totalencuestasparroquiasector inner join parroquia on totalencuestasparroquiasector.idParroquia = parroquia.idParroquia
                        inner join sector on totalencuestasparroquiasector.idSector = sector.idSector 
                        where totalencuestasparroquiasector.idParroquia = $idParroquia",Adapter::QUERY_MODE_EXECUTE)->toArray();
                $filaTotalEncuesta = '';
                $primeraFilaTotalEncuesta ='';
                $rowParroquias= 0;
                $encuestasDisponiblesCanton = 0;
                foreach ($listaTotalEncuestas as $valueTotalEncuestas) {
                    $listaCabeceraEncuesta = $objCabeceraEncuesta->filtrarCabeceraEncuestaPorParroquiaSector($valueTotalEncuestas['idParroquia'], $valueTotalEncuestas['idSector']);
                    $encuestasDisponibles =  $valueTotalEncuestas['totalEncuestas'] - count($listaCabeceraEncuesta) ;
                    if($rowParroquias == 0){
                         $primeraFilaTotalEncuesta = $primeraFilaTotalEncuesta.'<td><b>'.$valueTotalEncuestas['descripcionSector'].'</b></td>
                                 <td class="text-center"><b>'.$valueTotalEncuestas['totalEncuestas'].'</b>
                                 <td class="text-center"><b>'.$encuestasDisponibles.'</b></td>
                                 </td>';
                     }else{
                         $filaTotalEncuesta = $filaTotalEncuesta.'<tr><td><b>'.$valueTotalEncuestas['descripcionSector'].'</b></td>
                                 <td class="text-center"><b>'.$valueTotalEncuestas['totalEncuestas'].'</b></td>
                                 <td class="text-center"><b>'.$encuestasDisponibles.'</b></td>
                                 </tr>';
                     }
                     $rowParroquias++;
                     $encuestasDisponiblesCanton = $encuestasDisponiblesCanton + $encuestasDisponibles;
                }
                $filaParroquias = $filaParroquias.'<tr><td rowspan="'.$rowParroquias.'"><b>'.$valueParroquia['descripcionParroquia'].'</b></td>'.$primeraFilaTotalEncuesta.'</tr>'.$filaTotalEncuesta;
                $nombreParroquia = '<h4><b>'.$valueParroquia['descripcionParroquia'].'<br> (SIN ENCUESTAS DISPONIBLES)</b></h4>';
                if($encuestasDisponiblesCanton > 0){
                    $idParroquiaEncriptado = $objMetodos->encriptar($valueParroquia['idParroquia']);
                    $nombreParroquia = '<h4><b>'.$valueParroquia['descripcionParroquia'].'</b></h4><input onchange="filtrarCuerpoEncuesta(\''.$idParroquiaEncriptado.'\');"  style="width:50px;height:30px; cursor: pointer;" type="radio" name="parroquia" value="'.$idParroquiaEncriptado.'">';
                    
                }
                
                $radioParroquias = $radioParroquias.'<td colspan="2">'.$nombreParroquia.'</td>';
            }
             $tablaTotalEncuestas = '<div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr style="background-color: #B3D5FC;"><td><b>PARRÓQUIA</b></td>
                            <td><b>SECTOR</b></td>
                            <td class="text-center"><b>TOTAL ENCUESTAS</b></td>
                            <td class="text-center"><b>DISPONIBLES</b></td></tr>
                        </thead>
                        <tbody>
                            '.$filaParroquias.'
                        </tbody>
                    </table>
                    </div>';
            $tabla = $tablaTotalEncuestas.'<div class="table-responsive">
                    <table class="table">
                        <tbody>
                            <tr><td colspan="6" style="background-color: #B3D5FC;"><h4><b>1)¿Cuál es su edad?</b></h4></td></tr>
                            <tr class="text-center">'.$radioRangoEdad.'</tr>
                            <tr><td colspan="6" style="background-color: #B3D5FC;"><h4><b>2)Sexo</b></h4></td></tr>
                            <tr class="text-center">'.$radioSexo.'</tr>
                            <tr><td colspan="6" style="background-color: #B3D5FC;"><h4><b>3)¿En qué sector vive?</b></h4></td></tr>
                            <tr class="text-center">'.$radioSector.'</tr>
                            <tr><td colspan="6" style="background-color: #B3D5FC;"><h4><b>4)¿En qué parróquia vive?</b></h4></td></tr>
                            <tr class="text-center">'.$radioParroquias.'</tr>
                        </tbody>
                    </table>
                    </div>';
            $mensaje = '';
            $validar = TRUE;
            return new JsonModel(array('mensaje'=>$mensaje,'validar'=>$validar,'tabla'=>$tabla));
        }
        return new JsonModel(array('mensaje'=>$mensaje,'validar'=>$validar));
    }
    
    
    public function filtrarcuerpoencuestaAction()
    {
        $mensaje = '<div class="alert alert-danger text-center" role="alert">OCURRIÓ UN ERROR INESPERADO</div>';
        $validar = false;
        $request=$this->getRequest();
        if(!$request->isPost()){
            $this->redirect()->toUrl($this->getRequest()->getBaseUrl().'/index/index');
        }else{
            $this->dbAdapter=$this->getServiceLocator()->get('Zend\Db\Adapter');
            $objParroquia = new Parroquia($this->dbAdapter);
            $objPreguntas = new Preguntas($this->dbAdapter);
            $objMetodos = new Metodos();
            $post = array_merge_recursive(
                $request->getPost()->toArray(),
                $request->getFiles()->toArray()
            );
            $idParroquiaEncriptado = $post['idParroquiaEncriptado'];
            if($idParroquiaEncriptado == "" || $idParroquiaEncriptado == NULL){
                $mensaje = '<div class="alert alert-danger text-center" role="alert">NO SE ENCUENTRA EL ÍNDICE DE LA PARRÓQUIA</div>';
            }else{
                $idParroquia = $objMetodos->desencriptar($idParroquiaEncriptado);
                $listaParroquia = $objParroquia->filtrarParroquia($idParroquia);
                
                if(count($listaParroquia) == 0){
                    $mensaje = '<div class="alert alert-danger text-center" role="alert">LA PARRÓQUIA SELECCIONADA NO EXISTE EN LA BASE DE DATOS</div>';
                }else{
                    $identificadorParroquia = $listaParroquia[0]['identificadorParroquia'];

                        $listaPreguntas =  $objPreguntas->obtenerPreguntas();
                        $radioPreguntas = '';
                        $i=5;
                        foreach ($listaPreguntas as $valuePreguntas) {
                            $idPregunta = $valuePreguntas['idPregunta'];
                            $identificadorPregunta = $valuePreguntas['identificadorPregunta'];
                            $listaOpciones = $this->dbAdapter->query("SELECT opcionespregunta.idOpcionPregunta, candidatos.*,tipocandidato.identificadorTipoCandidato
                                FROM opcionespregunta 
                                inner join candidatos on opcionespregunta.idCandidato = candidatos.idCandidato
                                inner join tipocandidato on candidatos.idTipoCandidato = tipocandidato.idTipoCandidato
                                where opcionespregunta.idPregunta = $idPregunta order by candidatos.identificadorCandidato asc",Adapter::QUERY_MODE_EXECUTE)->toArray();
                            if($identificadorParroquia == 1){
                                
                                $opcionesPregunta = '';
                                foreach ($listaOpciones as $valueOpciones) {
                                    $identificadorTipoCandidato = $valueOpciones['identificadorTipoCandidato'];
                                    $identificadorCandidato = $valueOpciones['identificadorCandidato'];
                                    $nameCandidato = 'candidatoAlcalde';
                                    $nameObservacion = 'nombreOtroCandidatoAlcalde';
                                    if($identificadorTipoCandidato == 1){
                                        $nameCandidato = 'candidatoAlcalde';
                                        $nameObservacion = 'nombreOtroCandidatoAlcalde';
                                    }else if($identificadorTipoCandidato == 2){
                                        $nameCandidato = 'candidatoConcejal';
                                        $nameObservacion = 'nombreOtroCandidatoConcejal';
                                    }else if($identificadorTipoCandidato == 3){
                                        $nameCandidato = 'candidatoPrefecto';
                                        $nameObservacion = 'nombreOtroCandidatoPrefecto';
                                    }else if($identificadorTipoCandidato == 4){
                                        $nameCandidato = 'candidatoJunta';
                                        $nameObservacion = 'nombreOtroCandidatoJunta';
                                    }
                                    $idOpcionEncriptado = $objMetodos->encriptar($valueOpciones['idOpcionPregunta']);
                                    if($identificadorCandidato == 7){
                                        $opcionesPregunta = $opcionesPregunta.'<tr><td colspan="2"><h4><b><input style="width:50px;height:30px; cursor: pointer;" type="radio" name="'.$nameCandidato.'" value="'.$idOpcionEncriptado.'">'.$valueOpciones['nombreCandidato'].'</b></h4></td><td colspan="4"><label for="'.$nameObservacion.'">NOMBRE DEL CANDIDATO</label><input style="text-transform:uppercase;" class="form-control" type="text" name="'.$nameObservacion.'"></td></tr>';
                                    }else{
                                        $opcionesPregunta = $opcionesPregunta.'<tr><td colspan="6"><h4><b><input style="width:50px;height:30px; cursor: pointer;" type="radio" name="'.$nameCandidato.'" value="'.$idOpcionEncriptado.'">'.$valueOpciones['nombreCandidato'].'</b></h4></td></tr>';
                                    }
                                }
                                $radioPreguntas = $radioPreguntas.'<tr><td colspan="6" style="background-color: #B3D5FC;"><h4><b>'.$i.')'.$valuePreguntas['descripcionPregunta'].'</b></h4></td></tr>
                                        '.$opcionesPregunta;
                                $i++;
                            }else{
                                if($identificadorPregunta != 2){
                                    $opcionesPregunta = '';
                                    foreach ($listaOpciones as $valueOpciones) {
                                         $identificadorCandidato = $valueOpciones['identificadorCandidato'];
                                         $identificadorTipoCandidato = $valueOpciones['identificadorTipoCandidato'];
                                        $nameCandidato = 'candidatoAlcalde';
                                        $nameObservacion = 'nombreOtroCandidatoAlcalde';
                                        if($identificadorTipoCandidato == 1){
                                            $nameCandidato = 'candidatoAlcalde';
                                            $nameObservacion = 'nombreOtroCandidatoAlcalde';
                                        }else if($identificadorTipoCandidato == 2){
                                            $nameCandidato = 'candidatoConcejal';
                                            $nameObservacion = 'nombreOtroCandidatoConcejal';
                                        }else if($identificadorTipoCandidato == 3){
                                            $nameCandidato = 'candidatoPrefecto';
                                            $nameObservacion = 'nombreOtroCandidatoPrefecto';
                                        }
                                       $idOpcionEncriptado = $objMetodos->encriptar($valueOpciones['idOpcionPregunta']);
                                       if($identificadorCandidato == 7){
                                            $opcionesPregunta = $opcionesPregunta.'<tr><td colspan="2"><h4><b><input style="width:50px;height:30px; cursor: pointer;" type="radio" name="'.$nameCandidato.'" value="'.$idOpcionEncriptado.'">'.$valueOpciones['nombreCandidato'].'</b></h4></td><td colspan="4"><label for="'.$nameObservacion.'">NOMBRE DEL CANDIDATO</label><input style="text-transform:uppercase;" class="form-control" type="text" name="'.$nameObservacion.'"></td></tr>';
                                        }else{
                                            $opcionesPregunta = $opcionesPregunta.'<tr><td colspan="6"><h4><b><input style="width:50px;height:30px; cursor: pointer;" type="radio" name="'.$nameCandidato.'" value="'.$idOpcionEncriptado.'">'.$valueOpciones['nombreCandidato'].'</b></h4></td></tr>';
                                        }
                                       
                                    }
                                    $radioPreguntas = $radioPreguntas.'<tr><td colspan="6" style="background-color: #B3D5FC;"><h4><b>'.$i.')'.$valuePreguntas['descripcionPregunta'].'</b></h4></td></tr>
                                            '.$opcionesPregunta;
                                    $i++;
                                }
                            }
                        }
                    $botonGuardar = '<button id="botonGuardarEncuesta" data-loading-text="GUARDANDO..." type="submit" class="btn btn-danger btn-lg btn-flat"><i class="fa fa-save"></i>GUARDAR</button>';
                     $tabla = '<div class="table-responsive">
                    <table class="table">
                        <tbody>
                            '.$radioPreguntas.'
                            <tr><td colspan="6" class="text-center">'.$botonGuardar.'</td></tr>
                        </tbody>
                    </table>
                    </div>';
                    $mensaje = '';
                    $validar = TRUE;
                    return new JsonModel(array('mensaje'=>$mensaje,'validar'=>$validar,'tabla'=>$tabla));
                }                
            }
        }
        return new JsonModel(array('mensaje'=>$mensaje,'validar'=>$validar));
    }
}
    
    
