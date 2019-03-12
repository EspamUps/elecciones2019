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
use Nel\Modelo\Entity\TipoCandidato;
use Nel\Modelo\Entity\Preguntas;
use Nel\Modelo\Entity\Parroquia;
use Nel\Modelo\Entity\Listas;
use Nel\Modelo\Entity\Sexo;
use Nel\Modelo\Entity\CabeceraEncuesta;
use Nel\Modelo\Entity\CuerpoEncuesta;
use Zend\Db\Adapter\Adapter;

class ResultadosController extends AbstractActionController
{
    
    
    public $dbAdapter;
    
    public function filtrarresultadosAction()
    {
        $mensaje = '<div class="alert alert-danger text-center" role="alert">OCURRIÓ UN ERROR INESPERADO</div>';
        $validar = false;
        $request=$this->getRequest();
        if(!$request->isPost()){
            $this->redirect()->toUrl($this->getRequest()->getBaseUrl().'/index/index');
        }else{
            $this->dbAdapter=$this->getServiceLocator()->get('Zend\Db\Adapter');
            $objTipoCandidato = new TipoCandidato($this->dbAdapter);
            $objParroquia = new Parroquia($this->dbAdapter);
            $objSexo = new Sexo($this->dbAdapter);
            $post = array_merge_recursive(
                $request->getPost()->toArray(),
                $request->getFiles()->toArray()
            );
            $idTipoCandidato = $post['idTipoCandidato'];
            $idParroquia = $post['idParroquia'];
            $idConfigurarJunta = $post['idConfigurarJunta'];
            $idSexo = $post['idSexo'];
            
            if($idTipoCandidato == "" || $idTipoCandidato == "0" || $idTipoCandidato == NULL){
                $mensaje = '<div class="alert alert-danger text-center" role="alert">SELECCIONE UN TIPO DE CANDIDATURA</div>';
            }else if($idParroquia == "" || $idParroquia == NULL){
                $mensaje = '<div class="alert alert-danger text-center" role="alert">SELECCIONE UNA ZONA ELECTORAL VÁLIDA</div>';
            }else if($idConfigurarJunta == "" || $idConfigurarJunta == NULL){
                $mensaje = '<div class="alert alert-danger text-center" role="alert">SELECCIONE UNA JUNTA VÁLIDA</div>';
            }else if($idSexo == "" || $idSexo == NULL){
                $mensaje = '<div class="alert alert-danger text-center" role="alert">SELECCIONE UNA JUNTA VÁLIDA</div>';
            }else{ 
                $listaTipoCandidato = $objTipoCandidato->filtrarTipoCandidato($idTipoCandidato);
                if(count($listaTipoCandidato) == 0){
                    $mensaje = '<div class="alert alert-danger text-center" role="alert">EL TIPO DE CANDIDATO SELECCIONADO NO EXISTE EN LA BASE DE DATOS</div>';
                }else{
                    $descripcionTipoCandidato = $listaTipoCandidato[0]['descripcionTipoCandidato'];
                    
                    
               
                    $tabla = '';
                    if($listaTipoCandidato[0]['identificadorTipoCandidato'] == 1 || $listaTipoCandidato[0]['identificadorTipoCandidato'] == 6 || $listaTipoCandidato[0]['identificadorTipoCandidato'] == 3){
                        
                        $tabla = $this->cargarResultadoAlcaldesPrefectoConcejalRural($idTipoCandidato, $idParroquia, $idSexo, $idConfigurarJunta,$descripcionTipoCandidato);
                        if($tabla != ""){
                            $mensaje = '';
                            $validar = TRUE;
                        }else{
                            $mensaje = '<div class="alert alert-danger text-center" role="alert">AÚN NO SE HAN INGRESADO LOS VOTOS</div>';
                        }
                    }else{
                        $mensaje = '';
                        $validar = TRUE;
                    }
                    return new JsonModel(array(
                        'tabla'=>$tabla,
                        'mensaje'=>$mensaje,
                        'validar'=>$validar,
                    ));
                }
            } 
        }
        return new JsonModel(array('mensaje'=>$mensaje,'validar'=>$validar));
    }
    
    
    
    public function cargarResultadoAlcaldesPrefectoConcejalRural($idTipoCandidato, $idParroquia, $idSexo, $idConfigurarJunta,$descripcionTipoCandidato){
        $this->dbAdapter=$this->getServiceLocator()->get('Zend\Db\Adapter');
        $objParroquia = new Parroquia($this->dbAdapter);
        $objSexo = new Sexo($this->dbAdapter);
        $listaResultados = array();
        
        $listaParroquia = array();
        $listaSexo = array();
        $nombreParroquia = "";
        $nombreSexo = "";
        $nombreJunta = "";
        if($idParroquia != "0"){
            $listaParroquia = $objParroquia->filtrarParroquia($idParroquia);
            $nombreParroquia = $listaParroquia[0]['nombreParroquia'];
        }
        if($idSexo != "0"){
            $listaSexo = $objSexo->filtrarSexo($idSexo);
            $nombreSexo = $listaSexo[0]['descripcionSexo'];
        }    
        if($idConfigurarJunta != "0"){
            $listaJunta = $this->dbAdapter->query("SELECT juntaelectoral.*
            from configurarjunta inner JOIN juntaelectoral on configurarjunta.idJuntaElectoral = juntaelectoral.idJuntaElectoral
            where configurarjunta.idConfigurarJunta = $idConfigurarJunta",Adapter::QUERY_MODE_EXECUTE)->toArray();
            $nombreJunta = $listaJunta[0]['numeroJunta'];
        }
        
        
        $titulo ="";
        $tipoCandidato='';
        if($idParroquia == "0" && $idSexo == "0" && $idConfigurarJunta == "0"){
            $tipoCandidato = '<h1 style="text-align:center; color:">'.$descripcionTipoCandidato.'</h1>';
            $titulo = '<h1 style="text-align:center; color:" >RESULTADOS GENERALES</h1>';

            $listaResultados = $this->dbAdapter->query("select listas.nombreLista, listas.numeroLista, listas.rutaFotoLista, 
                tipocandidato.identificadorTipoCandidato,
                candidatos.nombres,candidatos.rutaFotoCandidato, candidatos.puesto,
                SUM(totalvotos.numeroVotos) as numeroVotos
                from totalvotos  
                INNER join candidatos on totalvotos.idCandidato = candidatos.idCandidato
                INNER join listas on candidatos.idListaCandidato = listas.idLista
                INNER join tipocandidato on candidatos.idTipoCandidato = tipocandidato.idTipoCandidato
                where candidatos.idTipoCandidato = $idTipoCandidato
                GROUP by candidatos.idCandidato
                ORDER by candidatos.puesto, numeroVotos DESC",Adapter::QUERY_MODE_EXECUTE)->toArray();
        }else if($idSexo == "0" && $idConfigurarJunta == "0"){
            $titulo = '<h1 style="text-align:center;">RESULTADOS EN LA ZONA ELECTORAL '.$nombreParroquia.'</h1>';
            $listaResultados = $this->dbAdapter->query("select listas.nombreLista, listas.numeroLista, listas.rutaFotoLista, 
                tipocandidato.identificadorTipoCandidato,
                candidatos.nombres,candidatos.rutaFotoCandidato, candidatos.puesto,
                SUM(totalvotos.numeroVotos) as numeroVotos
                from totalvotos  
                INNER join candidatos on totalvotos.idCandidato = candidatos.idCandidato
                INNER join listas on candidatos.idListaCandidato = listas.idLista
                INNER join tipocandidato on candidatos.idTipoCandidato = tipocandidato.idTipoCandidato
                INNER join configurarjunta on totalvotos.idConfigurarJunta = configurarjunta.idConfigurarJunta
                where candidatos.idTipoCandidato = $idTipoCandidato and configurarjunta.idParroquia = $idParroquia
                GROUP by candidatos.idCandidato
                ORDER by candidatos.puesto, numeroVotos DESC",Adapter::QUERY_MODE_EXECUTE)->toArray();
        }else if($idParroquia == "0" && $idConfigurarJunta == "0"){
            $titulo = '<h1 style="text-align:center;">RESULTADOS POR SEXO <br>'.$nombreSexo.'</h1>';
            $listaResultados = $this->dbAdapter->query("select listas.nombreLista, listas.numeroLista, listas.rutaFotoLista, 
                tipocandidato.identificadorTipoCandidato,
                candidatos.nombres,candidatos.rutaFotoCandidato, candidatos.puesto,
                SUM(totalvotos.numeroVotos) as numeroVotos
                from totalvotos  
                INNER join candidatos on totalvotos.idCandidato = candidatos.idCandidato
                INNER join listas on candidatos.idListaCandidato = listas.idLista
                INNER join tipocandidato on candidatos.idTipoCandidato = tipocandidato.idTipoCandidato
                INNER join configurarjunta on totalvotos.idConfigurarJunta = configurarjunta.idConfigurarJunta
                where candidatos.idTipoCandidato = $idTipoCandidato and configurarjunta.idSexo = $idSexo
                GROUP by candidatos.idCandidato
                ORDER by candidatos.puesto, numeroVotos DESC",Adapter::QUERY_MODE_EXECUTE)->toArray();
        }else if($idConfigurarJunta == "0"){
            $titulo = '<h1 style="text-align:center;">RESULTADOS EN LA ZONA ELECTORAL '.$nombreParroquia.' <br> '.$nombreSexo.'</h1>';
            $listaResultados = $this->dbAdapter->query("select listas.nombreLista, listas.numeroLista, listas.rutaFotoLista, 
                tipocandidato.identificadorTipoCandidato,
                candidatos.nombres,candidatos.rutaFotoCandidato, candidatos.puesto,
                SUM(totalvotos.numeroVotos) as numeroVotos
                from totalvotos  
                INNER join candidatos on totalvotos.idCandidato = candidatos.idCandidato
                INNER join listas on candidatos.idListaCandidato = listas.idLista
                INNER join tipocandidato on candidatos.idTipoCandidato = tipocandidato.idTipoCandidato
                INNER join configurarjunta on totalvotos.idConfigurarJunta = configurarjunta.idConfigurarJunta
                where candidatos.idTipoCandidato = $idTipoCandidato and configurarjunta.idParroquia = $idParroquia
                and configurarjunta.idSexo = $idSexo
                GROUP by candidatos.idCandidato
                ORDER by candidatos.puesto, numeroVotos DESC",Adapter::QUERY_MODE_EXECUTE)->toArray();
        }else{
            $titulo = '<h1 style="text-align:center;">RESULTADOS EN LA ZONA ELECTORAL '.$nombreParroquia.'<br> JUNTA '.$nombreSexo.' N° '.$nombreJunta.'</h1>';
            $listaResultados = $this->dbAdapter->query("select listas.nombreLista, listas.numeroLista, listas.rutaFotoLista, 
                tipocandidato.identificadorTipoCandidato,
                candidatos.nombres,candidatos.rutaFotoCandidato, candidatos.puesto,
                SUM(totalvotos.numeroVotos) as numeroVotos
                from totalvotos  
                INNER join candidatos on totalvotos.idCandidato = candidatos.idCandidato
                INNER join listas on candidatos.idListaCandidato = listas.idLista
                INNER join tipocandidato on candidatos.idTipoCandidato = tipocandidato.idTipoCandidato
                INNER join configurarjunta on totalvotos.idConfigurarJunta = configurarjunta.idConfigurarJunta
                where candidatos.idTipoCandidato = $idTipoCandidato and configurarjunta.idParroquia = $idParroquia
                and configurarjunta.idSexo = $idSexo and totalvotos.idConfigurarJunta = $idConfigurarJunta
                GROUP by candidatos.idCandidato
                ORDER by candidatos.puesto, numeroVotos DESC",Adapter::QUERY_MODE_EXECUTE)->toArray();
        }
        $filasResultado = '';
        $contador = 1;
        $totalVotos= 0;
        foreach ($listaResultados as $valueResultado) {
            $colorFondo = '';
            if(($contador%2) == 0){
                $colorFondo = 'background-color: #D0F5FD;';
            }
            $filasResultado = $filasResultado.'
                <tr>
                    <td style="'.$colorFondo.'"><b>'.$contador.'</b></td>
                    <td style="text-align:center;vertical-align: middle;'.$colorFondo.'"><img  style="margin:0 auto 0 auto; text-align:center; width: 100%;" src="'.$this->getRequest()->getBaseUrl().$valueResultado['rutaFotoLista'].'"></td>
                    <td style="text-align:center;vertical-align: middle;'.$colorFondo.'"><b>'.$valueResultado['numeroLista'].'</b></td>
                    <td style="text-align:center;vertical-align: middle;'.$colorFondo.'"><img style="margin:0 auto 0 auto; text-align:center; width: 100%;" src="'.$this->getRequest()->getBaseUrl().$valueResultado['rutaFotoCandidato'].'"></td>
                    <td style="vertical-align: middle;'.$colorFondo.'"><b>'.$valueResultado['nombres'].'</b></td>
                    <td style="text-align:center;vertical-align: middle;'.$colorFondo.'"><b>'.$valueResultado['numeroVotos'].'</b></td>
                </tr>

            ';
            $contador++;
            $totalVotos = $totalVotos + $valueResultado['numeroVotos'];
        }
        $tabla = '';
        if($filasResultado != ""){
            $tabla = '<div class="table-responsive"><table class="table">
                <thead>
                <tr><th colspan="6" style="text-align:center;">'.$tipoCandidato.$titulo.'</th></tr>
                <tr> 
                    <th style="width: 5%;text-align:center;background-color:#3c8dbc">#</th>
                    <th style="width: 5%;text-align:center;background-color:#3c8dbc">LOGO</th>
                    <th style="width: 10%;text-align:center;background-color:#3c8dbc">LISTA</th>
                    <th style="width: 5%;text-align:center;background-color:#3c8dbc">FOTO</th>
                    <th style="width: 30%;text-align:center;background-color:#3c8dbc">CANDIDATO</th>
                    <th style="width: 30%;text-align:center;background-color:#3c8dbc">VOTOS</th>
                </tr>
                </thead>
                <tbody>
                    '.$filasResultado.'
                    <tr>
                        <td colspan="5" style="text-align:center;background-color:#FCFFC9;"><b>TOTAL</b></td>
                        <td style="text-align:center;background-color:#FCFFC9;"><b>'.$totalVotos.'</b></td>
                    </tr>
                </tbody>
            </table></div>';
        }
        
        
        
        return $tabla;
    }

    







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
                          $optionJunta='<option value="0">TODAS LAS JUNTAS</option>';
                         foreach ($listaJuntas as $valueJunta) {
                               $optionJunta=$optionJunta.'<option value="'.$valueJunta['idConfigurarJunta'].'">'.$valueJunta['numeroJunta'].'</option>'; 
                            }
                    $mensaje = '';
                    $validar = TRUE;
                    return new JsonModel(array('mensaje'=>$mensaje,'validar'=>$validar,'select'=>$optionJunta));
                }                
            }}
        }
        return new JsonModel(array('mensaje'=>$mensaje,'validar'=>$validar));
    }
    
    public function obtenerformularioAction()
    {
        $mensaje = '<div class="alert alert-danger text-center" role="alert">OCURRIÓ UN ERROR INESPERADO</div>';
        $validar = false;
        $request=$this->getRequest();
        if(!$request->isPost()){
            $this->redirect()->toUrl($this->getRequest()->getBaseUrl().'/index/index');
        }else{
            $this->dbAdapter=$this->getServiceLocator()->get('Zend\Db\Adapter');
            $objTipoCandidato = new TipoCandidato($this->dbAdapter);
            $objParroquias = new Parroquia($this->dbAdapter);
            $objSexo = new Sexo($this->dbAdapter);
            $post = array_merge_recursive(
                $request->getPost()->toArray(),
                $request->getFiles()->toArray()
            );
            $idFormulario = $post['idFormulario'];
            
            if($idFormulario == "" || $idFormulario == "0" || $idFormulario == NULL){
            }else{
                $tabla = '';
                if($idFormulario == '1'){
                    $listaTipoCandidato = $objTipoCandidato->obtenerTipoCandidato();
                    $optionTipoCandidato = '<option value="0" style="font-weight: bold;">SELECCIONE UN TIPO DE CANDIDATO</option>';
                    
                    foreach ($listaTipoCandidato as $valueTipoCandidato) {
                        $optionTipoCandidato = $optionTipoCandidato.'<option value="'.$valueTipoCandidato['idTipoCandidato'].'" style="font-weight: bold;">'.$valueTipoCandidato['descripcionTipoCandidato'].'</option>';
                    }
                    
                    $listaParroquias = $objParroquias->obtenerParroquias();
                    $optionParroquia = '<option value="0" style="font-weight: bold;">TODAS LAS ZONAS ELECTORALES</option>';
                    foreach ($listaParroquias as $valueParroquias) {
                        $optionParroquia = $optionParroquia.'<option value="'.$valueParroquias['idParroquia'].'" style="font-weight: bold;">'.$valueParroquias['nombreParroquia'].'</option>';
                    }
                    
                    $listaSexo = $objSexo->obtenerSexo();
                    $optionSexo = '<option value="0" style="font-weight: bold;">TODOS LOS SEXOS</option>';
                    foreach ($listaSexo as $valueSexo) {
                        $optionSexo = $optionSexo.'<option value="'.$valueSexo['idSexo'].'" style="font-weight: bold;">'.$valueSexo['descripcionSexo'].'</option>';
                    }
                    
                    
                    $selectJuntas = '<select style="font-weight: bold;" class="form-control" id="selectJuntas" onchange="filtrarResultados();"><option value="0" style="font-weight: bold;">TODAS LAS JUNTAS</option></select>';
                    
                    
                    
                    $selectTipoCandidato = '<select style="font-weight: bold;" class="form-control" id="selectTipoCandidato" onchange="filtrarResultados();">'.$optionTipoCandidato.'</select>';
                    $selectParroquias = '<select style="font-weight: bold;" class="form-control" id="selectParroquias" onchange="filtrarJuntasElectorales();filtrarResultados();">'.$optionParroquia.'</select>';
                    $selectSexo = '<select style="font-weight: bold;" class="form-control" id="selectSexo" onchange="filtrarJuntasElectorales();filtrarResultados();">'.$optionSexo.'</select>';
                    $tabla = '<div id="mensajeFormResultados" class="col-lg-12">
                            
                            </div>
                            <div class="col-lg-12">
                            <div class="form-group col-lg-3">'.$selectTipoCandidato.'</div>
                                <div class="form-group col-lg-3">'.$selectParroquias.'</div>
                                <div class="form-group col-lg-3">'.$selectSexo.'</div>
                                <div class="form-group col-lg-3">'.$selectJuntas.'</div>
                            </div>
                            <div id="contenedorTablaResultados" class="col-lg-12"></div>';
                    $mensaje = '';
                    $validar = TRUE;
                }else{
                    $mensaje = '<div class="alert alert-danger text-center" role="alert">EL FORMULARIO NO EXISTE</div>';
                }
              
                return new JsonModel(array(
                    'tabla'=>$tabla,
                    'mensaje'=>$mensaje,
                    'validar'=>$validar,
                ));
            } 
        }
        return new JsonModel(array('mensaje'=>$mensaje,'validar'=>$validar));
    }
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
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
    
    
