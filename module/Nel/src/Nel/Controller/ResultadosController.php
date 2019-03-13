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
    
    public function filtrarganadoresAction()
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
            
            if($idTipoCandidato == "" || $idTipoCandidato == "0" || $idTipoCandidato == NULL){
                $mensaje = '<div class="alert alert-danger text-center" role="alert">SELECCIONE UN TIPO DE CANDIDATURA</div>';
            }else{ 
                $listaTipoCandidato = $objTipoCandidato->filtrarTipoCandidato($idTipoCandidato);
                if(count($listaTipoCandidato) == 0){
                    $mensaje = '<div class="alert alert-danger text-center" role="alert">EL TIPO DE CANDIDATO SELECCIONADO NO EXISTE EN LA BASE DE DATOS</div>';
                }else{
                    $descripcionTipoCandidato = $listaTipoCandidato[0]['descripcionTipoCandidato'];
                    
                    
               
                    $tabla = '';
                    if($listaTipoCandidato[0]['identificadorTipoCandidato'] == 1 || $listaTipoCandidato[0]['identificadorTipoCandidato'] == 6 || $listaTipoCandidato[0]['identificadorTipoCandidato'] == 3){
                        
                         $listaGanador = $this->dbAdapter->query("select  listas.nombreLista, listas.numeroLista, listas.rutaFotoLista, 
                            tipocandidato.identificadorTipoCandidato,
                            candidatos.nombres,candidatos.rutaFotoCandidato, candidatos.puesto,
                            SUM(totalvotos.numeroVotos) as numeroVotos
                            from totalvotos  
                            INNER join candidatos on totalvotos.idCandidato = candidatos.idCandidato
                            INNER join listas on candidatos.idListaCandidato = listas.idLista
                            INNER join tipocandidato on candidatos.idTipoCandidato = tipocandidato.idTipoCandidato
                            INNER join configurarjunta on totalvotos.idConfigurarJunta = configurarjunta.idConfigurarJunta
                            where candidatos.idTipoCandidato = $idTipoCandidato
                            GROUP by candidatos.idCandidato 
                            ORDER by candidatos.puesto, numeroVotos DESC
                            LIMIT 1",Adapter::QUERY_MODE_EXECUTE)->toArray();
                         
                         $filasResultado = '';
                         $contador = 1;
                         foreach ($listaGanador as $valueResultado) {
                             $colorFondo = '';
                              $filasResultado = $filasResultado.'
                                <tr>
                                    <td style="text-align:center;vertical-align: middle;'.$colorFondo.'"><b>'.$contador.'</b></td>
                                    <td style="text-align:center;vertical-align: middle;'.$colorFondo.'"><img  style="margin:0 auto 0 auto; text-align:center; width: 100%;" src="'.$this->getRequest()->getBaseUrl().$valueResultado['rutaFotoLista'].'"></td>
                                    <td style="text-align:center;vertical-align: middle;'.$colorFondo.'"><b>'.$valueResultado['numeroLista'].'</b></td>
                                    <td style="text-align:center;vertical-align: middle;'.$colorFondo.'"><img style="margin:0 auto 0 auto; text-align:center; width: 100%;" src="'.$this->getRequest()->getBaseUrl().$valueResultado['rutaFotoCandidato'].'"></td>
                                    <td style="vertical-align: middle;'.$colorFondo.'"><b>'.$valueResultado['nombres'].'</b></td>
                                    <td style="text-align:center;vertical-align: middle;'.$colorFondo.'"><b>'.$valueResultado['numeroVotos'].'</b></td>
                                </tr>
                            ';
                         }
                          if($filasResultado != ""){
                                $tabla = '<div class="table-responsive"><table class="table">
                                    <thead>
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
                                    </tbody>
                                </table></div>';
                            }
                         
                        
                        if($tabla != ""){
                            $mensaje = '';
                            $validar = TRUE;
                        }else{
                            $mensaje = '<div class="alert alert-danger text-center" role="alert">AÚN NO SE HAN INGRESADO LOS VOTOS</div>';
                        }
                    }else if($listaTipoCandidato[0]['identificadorTipoCandidato'] == 2 || $listaTipoCandidato[0]['identificadorTipoCandidato'] == 4 || $listaTipoCandidato[0]['identificadorTipoCandidato'] == 5){

                        if($tabla != ""){
                            $mensaje = '';
                            $validar = TRUE;
                        }else{
                            $mensaje = '<div class="alert alert-danger text-center" role="alert">AÚN NO SE HAN INGRESADO LOS VOTOS</div>';
                        }
                        
                    }else{
                            $mensaje = '<div class="alert alert-danger text-center" role="alert">SELECCIONE UN TIPO DE CANDIDATURA VÁLIDA</div>';
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
            $idLista = $post['idLista'];
            
            if($idTipoCandidato == "" || $idTipoCandidato == "0" || $idTipoCandidato == NULL){
                $mensaje = '<div class="alert alert-danger text-center" role="alert">SELECCIONE UN TIPO DE CANDIDATURA</div>';
            }else if($idParroquia == "" || $idParroquia == NULL){
                $mensaje = '<div class="alert alert-danger text-center" role="alert">SELECCIONE UNA ZONA ELECTORAL VÁLIDA</div>';
            }else if($idConfigurarJunta == "" || $idConfigurarJunta == NULL){
                $mensaje = '<div class="alert alert-danger text-center" role="alert">SELECCIONE UNA JUNTA VÁLIDA</div>';
            }else if($idSexo == "" || $idSexo == NULL){
                $mensaje = '<div class="alert alert-danger text-center" role="alert">SELECCIONE UNA JUNTA VÁLIDA</div>';
            }else if($idLista == "" || $idLista == NULL){
                $mensaje = '<div class="alert alert-danger text-center" role="alert">SELECCIONE UNA LISTA</div>';
            }else{ 
                $listaTipoCandidato = $objTipoCandidato->filtrarTipoCandidato($idTipoCandidato);
                if(count($listaTipoCandidato) == 0){
                    $mensaje = '<div class="alert alert-danger text-center" role="alert">EL TIPO DE CANDIDATO SELECCIONADO NO EXISTE EN LA BASE DE DATOS</div>';
                }else{
                    $descripcionTipoCandidato = $listaTipoCandidato[0]['descripcionTipoCandidato'];
                    
                    
               
                    $tabla = '';
                    if($listaTipoCandidato[0]['identificadorTipoCandidato'] == 1 || $listaTipoCandidato[0]['identificadorTipoCandidato'] == 6 || $listaTipoCandidato[0]['identificadorTipoCandidato'] == 3){
                        
                        $tabla = $this->cargarResultadoAlcaldesPrefectoConcejalRural($idTipoCandidato, $idParroquia, $idSexo, $idConfigurarJunta,$idLista,$descripcionTipoCandidato);
                        if($tabla != ""){
                            $mensaje = '';
                            $validar = TRUE;
                        }else{
                            $mensaje = '<div class="alert alert-danger text-center" role="alert">AÚN NO SE HAN INGRESADO LOS VOTOS</div>';
                        }
                    }else if($listaTipoCandidato[0]['identificadorTipoCandidato'] == 2 || $listaTipoCandidato[0]['identificadorTipoCandidato'] == 4 || $listaTipoCandidato[0]['identificadorTipoCandidato'] == 5){

                        $tabla = $this->cargarResultadoConcejalUrbanoJuntaParroquial($idTipoCandidato, $idParroquia, $idSexo, $idConfigurarJunta, $idLista, $descripcionTipoCandidato);
                        if($tabla != ""){
                            $mensaje = '';
                            $validar = TRUE;
                        }else{
                            $mensaje = '<div class="alert alert-danger text-center" role="alert">AÚN NO SE HAN INGRESADO LOS VOTOS</div>';
                        }
                        
                    }else{
                            $mensaje = '<div class="alert alert-danger text-center" role="alert">SELECCIONE UN TIPO DE CANDIDATURA VÁLIDA</div>';
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
    
    
    public function cargarResultadoConcejalUrbanoJuntaParroquial($idTipoCandidato, $idParroquia, $idSexo, $idConfigurarJunta,$idLista,$descripcionTipoCandidato){
        $this->dbAdapter=$this->getServiceLocator()->get('Zend\Db\Adapter');
        $objParroquia = new Parroquia($this->dbAdapter);
        $objSexo = new Sexo($this->dbAdapter);
        $objLista = new Listas($this->dbAdapter);
       
        
        $listaParroquia = array();
        $listaSexo = array();
        $listaLista = array();
        $nombreParroquia = "";
        $nombreSexo = "";
        $nombreJunta = "";
        $nombreLista = "";
        $whereVotosInvalidos = "";
        $where = "";
//        $titulo = '<h1 style="text-align:center; color:" >RESULTADOS GENERALES</h1>';
//        $tipoCandidato='<h1 style="text-align:center; color:">'.$descripcionTipoCandidato.'</h1>';
        $totalVotos=0;
        if($idParroquia != "0"){
            $whereVotosInvalidos = $whereVotosInvalidos." and configurarjunta.idParroquia = $idParroquia ";
            $where = $where." and configurarjunta.idParroquia = $idParroquia ";
            $listaParroquia = $objParroquia->filtrarParroquia($idParroquia);
            $nombreParroquia = $listaParroquia[0]['nombreParroquia'];
            
        }
        if($idSexo != "0"){
            $whereVotosInvalidos = $whereVotosInvalidos." and configurarjunta.idSexo = $idSexo ";
            $where = $where." and configurarjunta.idSexo = $idSexo ";
            $listaSexo = $objSexo->filtrarSexo($idSexo);
            $nombreSexo = $listaSexo[0]['descripcionSexo'];
        }    
        
        if($idConfigurarJunta != "0"){
            $whereVotosInvalidos = $whereVotosInvalidos." and totalvotosinvalidos.idConfigurarJuntaElectoral = $idConfigurarJunta ";
            $where = $where." and totalvotos.idConfigurarJunta = $idConfigurarJunta ";
            $listaJunta = $this->dbAdapter->query("SELECT juntaelectoral.*
            from configurarjunta inner JOIN juntaelectoral on configurarjunta.idJuntaElectoral = juntaelectoral.idJuntaElectoral
            where configurarjunta.idConfigurarJunta = $idConfigurarJunta",Adapter::QUERY_MODE_EXECUTE)->toArray();
            $nombreJunta = $listaJunta[0]['numeroJunta'];
        }
        $whereLista = "";
        $whereIdLista = "";
        if($idLista != "0"){
            $whereLista = " and candidatos.idListaCandidato = $idLista " ;
            $whereIdLista = " and listas.idLista = $idLista ";
            $listaLista = $objLista->filtrarLista($idLista);
            $nombreLista = $listaLista[0]['nombreLista'];
        }
        
        $listaTotalVotosInvalidos = $this->dbAdapter->query("SELECT SUM(totalvotosinvalidos.numeroVotos) as numeroVotos, tipototalvotosinvalidos.identificadorTipoVotoInvalido
        from totalvotosinvalidos
        inner join tipototalvotosinvalidos on totalvotosinvalidos.idTipoVotoInvalido = tipototalvotosinvalidos.idTipoVotosInvalidos
        inner join configurarjunta on totalvotosinvalidos.idConfigurarJuntaElectoral = configurarjunta.idConfigurarJunta
        where totalvotosinvalidos.idTipoCandidato = $idTipoCandidato ".$whereVotosInvalidos." 
        group by totalvotosinvalidos.idTipoVotoInvalido",Adapter::QUERY_MODE_EXECUTE)->toArray();

        $listaTotalVotos = $this->dbAdapter->query("select SUM(totalvotos.numeroVotos) as numeroTotalVotos
        from totalvotos  
        INNER join candidatos on totalvotos.idCandidato = candidatos.idCandidato
        INNER join listas on candidatos.idListaCandidato = listas.idLista
        INNER join tipocandidato on candidatos.idTipoCandidato = tipocandidato.idTipoCandidato
        INNER join configurarjunta on totalvotos.idConfigurarJunta = configurarjunta.idConfigurarJunta
        where candidatos.idTipoCandidato = $idTipoCandidato ".$where."
        GROUP by candidatos.idTipoCandidato
        ORDER by candidatos.puesto, numeroVotos DESC",Adapter::QUERY_MODE_EXECUTE)->toArray();

        if(count($listaTotalVotos)){
            $totalVotos = $listaTotalVotos[0]['numeroTotalVotos'];
            
        }
        
        
        
        $filaVotosInvalidos = '';
        $filaVotosTotales = '';
        $totalVotosNulos = 0;
        $totalVotosBlanco = 0;
        $porcentajeNulos = 0;
        $porcentajeBlanco = 0;
        if(count($listaTotalVotosInvalidos) > 0){
            foreach ($listaTotalVotosInvalidos as $valueVotosInvalidos) {
                if($valueVotosInvalidos['identificadorTipoVotoInvalido'] == 1){
                    $totalVotosNulos = $valueVotosInvalidos['numeroVotos'];
                }else{
                    $totalVotosBlanco = $valueVotosInvalidos['numeroVotos'];
                }
                $totalVotos = $totalVotos + $valueVotosInvalidos['numeroVotos'];
            }
            
            $filaVotosTotales = '<tr>
                <td colspan="3" style="text-align:center;vertical-align: middle;"><b>VOTOS TOTALES</b></td>
                <td colspan="2" style="text-align:center;vertical-align: middle; "><b>'.$totalVotos.'</b></td>
                <td colspan="2" style="text-align:center;vertical-align: middle; "><b>100%</b></td>
            </tr>';
            
            $porcentajeNulos = 0;
            $porcentajeBlanco = 0;
            foreach ($listaTotalVotosInvalidos as $valueVotosInvalidos) {
                if($valueVotosInvalidos['identificadorTipoVotoInvalido'] == 1){
                    $porcentajeNulos = ($totalVotosNulos/$totalVotos)*100;
                    $filaVotosInvalidos = $filaVotosInvalidos.'<tr>
                    <td colspan="3" style="text-align:center;vertical-align: middle;"><b>VOTOS NULOS</b></td>
                    <td colspan="2" style="text-align:center;vertical-align: middle; "><b>'.$valueVotosInvalidos['numeroVotos'].'</b></td>
                    <td colspan="2" style="text-align:center;vertical-align: middle; "><b>'.  number_format($porcentajeNulos,2).'%</b></td>
                </tr>';
                }else{
                    $porcentajeBlanco = ($totalVotosBlanco/$totalVotos)*100;
                    $filaVotosInvalidos = $filaVotosInvalidos.'<tr>
                    <td colspan="3" style="text-align:center;vertical-align: middle;"><b>VOTOS EN BLANCO</b></td>
                    <td colspan="2" style="text-align:center;vertical-align: middle; "><b>'.$valueVotosInvalidos['numeroVotos'].'</b></td>
                    <td colspan="2" style="text-align:center;vertical-align: middle; "><b>'.  number_format($porcentajeBlanco,2).'%</b></td>
                </tr>';
                }
            }
        }
         
        $tablaVotosInvalidos = "";
        if($filaVotosInvalidos != "" || $filaVotosTotales != ""){
        
            $tablaVotosInvalidos = '<div class="table-responsive"><table class="table">
                        <thead>
                        <tr> 
                            <th colspan="3" style="width: 40%;text-align:center;background-color:#3c8dbc">TIPO</th>
                            <th colspan="2" style="width: 30%;text-align:center;background-color:#3c8dbc">VOTOS</th>
                            <th colspan="2" style="width: 30%;text-align:center;background-color:#3c8dbc">%</th>
                        </tr>
                        </thead>
                        <tbody>
                            '.$filaVotosInvalidos.'
                            '.$filaVotosTotales.'
                        </tbody>
                    </table></div>';
        }
        
        
        
        
       
        
        $listaListas = $this->dbAdapter->query("SELECT DISTINCT listas.* FROM candidatos
            INNER JOIN listas on candidatos.idListaCandidato = listas.idLista
            where candidatos.idTipoCandidato=$idTipoCandidato ".$whereIdLista." ",Adapter::QUERY_MODE_EXECUTE)->toArray();
        
        $tabla = '';
        foreach ($listaListas as $valueLista) {
            $idLista2 = $valueLista['idLista'];
            
            
            $listaTotalVotosLista = $this->dbAdapter->query("select SUM(totalvotos.numeroVotos) as numeroTotalVotos
            from totalvotos  
            INNER join candidatos on totalvotos.idCandidato = candidatos.idCandidato
            INNER join listas on candidatos.idListaCandidato = listas.idLista
            INNER join tipocandidato on candidatos.idTipoCandidato = tipocandidato.idTipoCandidato
            INNER join configurarjunta on totalvotos.idConfigurarJunta = configurarjunta.idConfigurarJunta
            where candidatos.idListaCandidato = $idLista2 and candidatos.idTipoCandidato = $idTipoCandidato ".$where."
            GROUP by candidatos.idTipoCandidato
            ORDER by candidatos.puesto, numeroVotos DESC",Adapter::QUERY_MODE_EXECUTE)->toArray();
            
            
            
            $listaResultados = $this->dbAdapter->query("select listas.nombreLista, listas.numeroLista, listas.rutaFotoLista, 
            tipocandidato.identificadorTipoCandidato,
            candidatos.nombres,candidatos.rutaFotoCandidato, candidatos.puesto,
            totalvotos.numeroVotos as numeroVotos
            from totalvotos  
            inner join candidatos on totalvotos.idCandidato = candidatos.idCandidato
            inner join listas on candidatos.idListaCandidato = listas.idLista
            inner join tipocandidato on candidatos.idTipoCandidato = tipocandidato.idTipoCandidato
            inner join configurarjunta on totalvotos.idConfigurarJunta = configurarjunta.idConfigurarJunta
            where candidatos.idListaCandidato = $idLista2 and candidatos.idTipoCandidato = $idTipoCandidato ".$where." ".$whereLista."
            GROUP by candidatos.idCandidato
            ORDER by candidatos.puesto",Adapter::QUERY_MODE_EXECUTE)->toArray();
            
            
            $filasResultado = '';
            $contador = 1;
            foreach ($listaResultados as $valueResultado){
                $colorFondo = '';
                $totalVotosLista = $listaTotalVotosLista[0]['numeroTotalVotos']/$valueResultado['puesto'];
           
                 $numeroVotos = $valueResultado['numeroVotos'];
        
                
                $filasResultado = $filasResultado.'
                    <tr>
                        <td style="text-align:center;vertical-align: middle;'.$colorFondo.'"><b>'.$valueResultado['puesto'].'</b></td>
                        <td style="text-align:center;vertical-align: middle;'.$colorFondo.'"><img style="margin:0 auto 0 auto; text-align:center; width: 100%;" src="'.$this->getRequest()->getBaseUrl().$valueResultado['rutaFotoCandidato'].'"></td>
                        <td style="vertical-align: middle;'.$colorFondo.'"><b>'.$valueResultado['nombres'].'</b></td>
                        <td style="text-align:center;vertical-align: middle;'.$colorFondo.'"><b>'.$numeroVotos.'</b></td>
                        <td style="text-align:center;vertical-align: middle;'.$colorFondo.'"><b>'.number_format($totalVotosLista,2).'</b></td>
                    </tr>

                ';
                $contador++;
            }
            $filaListaCabecera = '<tr><td colspan="2" style="text-align:center;vertical-align: middle;"><img  style="margin:0 auto 0 auto; text-align:center; width: 100%;" src="'.$this->getRequest()->getBaseUrl().$valueLista['rutaFotoLista'].'"></td>
            <td colspan="2" style="text-align:center;vertical-align: middle;background-color: #D0F5FD;"><b>'.$valueLista['nombreLista'].'</b></td>            
            <td style="text-align:center;vertical-align: middle;background-color: #D0F5FD;"><b>'.$valueLista['numeroLista'].'</b></td></tr>';

            if($filasResultado != ""){
                $filaTotalVotos = '<tr> 
                            <td colspan="3" style="text-align:center;background-color:#D0F5FD"><b>TOTAL</b></td>
                            <td style="text-align:center;background-color:#D0F5FD"><b>'.$listaTotalVotosLista[0]['numeroTotalVotos'].'</b></td>
                            <td style="text-align:center;background-color:#D0F5FD"><b></b></td>
                        </tr>';
                $tabla = $tabla.'<div class="table-responsive"><table class="table" border="1">
                    <thead>
                        '.$filaListaCabecera.'
                        <tr> 
                            <td style="width: 5%;text-align:center;background-color:#D0F5FD"><b>#</b></td>
                            <td style="width: 5%;text-align:center;background-color:#D0F5FD"><b>FOTO</b></td>
                            <td style="width: 30%;text-align:center;background-color:#D0F5FD"><b>CANDIDATO</b></td>
                            <td style="width: 30%;text-align:center;background-color:#D0F5FD"><b>VOTOS</b></td>
                            <td style="width: 30%;text-align:center;background-color:#D0F5FD"><b>VOTOS</b></td>
                        </tr>
                    </thead>
                    <tbody>
                        '.$filasResultado.'
                            '.$filaTotalVotos.'
                    </tbody>
                </table></div>';
            }
            
            
        }
        
        
        $tabla = $tablaVotosInvalidos.$tabla;
        
        


        
        
        
        
        
        
        
        
        return $tabla;
    }
    
    
    
    
    public function cargarResultadoAlcaldesPrefectoConcejalRural($idTipoCandidato, $idParroquia, $idSexo, $idConfigurarJunta,$idLista,$descripcionTipoCandidato){
        $this->dbAdapter=$this->getServiceLocator()->get('Zend\Db\Adapter');
        $objParroquia = new Parroquia($this->dbAdapter);
        $objSexo = new Sexo($this->dbAdapter);
        $objLista = new Listas($this->dbAdapter);
        $listaResultados = array();
        
        $listaParroquia = array();
        $listaSexo = array();
        $listaLista = array();
        $nombreParroquia = "";
        $nombreSexo = "";
        $nombreJunta = "";
        $nombreLista = "";
        
        $whereVotosInvalidos = "";
        
        if($idParroquia != "0"){
            $whereVotosInvalidos = $whereVotosInvalidos." and configurarjunta.idParroquia = $idParroquia ";
            $listaParroquia = $objParroquia->filtrarParroquia($idParroquia);
            $nombreParroquia = $listaParroquia[0]['nombreParroquia'];
        }
        if($idSexo != "0"){
            $whereVotosInvalidos = $whereVotosInvalidos." and configurarjunta.idSexo = $idSexo ";
            $listaSexo = $objSexo->filtrarSexo($idSexo);
            $nombreSexo = $listaSexo[0]['descripcionSexo'];
        }    
        
        if($idConfigurarJunta != "0"){
            $whereVotosInvalidos = $whereVotosInvalidos." and totalvotosinvalidos.idConfigurarJuntaElectoral = $idConfigurarJunta ";
            
            $listaJunta = $this->dbAdapter->query("SELECT juntaelectoral.*
            from configurarjunta inner JOIN juntaelectoral on configurarjunta.idJuntaElectoral = juntaelectoral.idJuntaElectoral
            where configurarjunta.idConfigurarJunta = $idConfigurarJunta",Adapter::QUERY_MODE_EXECUTE)->toArray();
            $nombreJunta = $listaJunta[0]['numeroJunta'];
        }
        $where = "";
        if($idLista != "0"){
            $where = " and candidatos.idListaCandidato = $idLista " ;
            $listaLista = $objLista->filtrarLista($idLista);
            $nombreLista = $listaLista[0]['nombreLista'];
        }
        
        $listaTotalVotosInvalidos = $this->dbAdapter->query("SELECT SUM(totalvotosinvalidos.numeroVotos) as numeroVotos, tipototalvotosinvalidos.identificadorTipoVotoInvalido
        from totalvotosinvalidos
        inner join tipototalvotosinvalidos on totalvotosinvalidos.idTipoVotoInvalido = tipototalvotosinvalidos.idTipoVotosInvalidos
        inner join configurarjunta on totalvotosinvalidos.idConfigurarJuntaElectoral = configurarjunta.idConfigurarJunta
        where totalvotosinvalidos.idTipoCandidato = $idTipoCandidato ".$whereVotosInvalidos." 
        group by totalvotosinvalidos.idTipoVotoInvalido",Adapter::QUERY_MODE_EXECUTE)->toArray();
        

        $titulo ="";
        $tipoCandidato='';
        $totalVotos=0;
        if($idParroquia == "0" && $idSexo == "0" && $idConfigurarJunta == "0"){
            
            $tipoCandidato = '<h1 style="text-align:center; color:">'.$descripcionTipoCandidato.'</h1>';
            $titulo = '<h1 style="text-align:center; color:" >RESULTADOS GENERALES</h1>';
            $listaTotalVotos = $this->dbAdapter->query("select SUM(totalvotos.numeroVotos) as numeroTotalVotos
                from totalvotos  
                INNER join candidatos on totalvotos.idCandidato = candidatos.idCandidato
                INNER join listas on candidatos.idListaCandidato = listas.idLista
                INNER join tipocandidato on candidatos.idTipoCandidato = tipocandidato.idTipoCandidato
                where candidatos.idTipoCandidato = $idTipoCandidato  
                GROUP by candidatos.idTipoCandidato
                ORDER by candidatos.puesto, numeroVotos DESC",Adapter::QUERY_MODE_EXECUTE)->toArray();
            if(count($listaTotalVotos)){
                $totalVotos = $listaTotalVotos[0]['numeroTotalVotos'];
            }
            
            

            $listaResultados = $this->dbAdapter->query("select listas.nombreLista, listas.numeroLista, listas.rutaFotoLista, 
                tipocandidato.identificadorTipoCandidato,
                candidatos.nombres,candidatos.rutaFotoCandidato, candidatos.puesto,
                SUM(totalvotos.numeroVotos) as numeroVotos
                from totalvotos  
                INNER join candidatos on totalvotos.idCandidato = candidatos.idCandidato
                INNER join listas on candidatos.idListaCandidato = listas.idLista
                INNER join tipocandidato on candidatos.idTipoCandidato = tipocandidato.idTipoCandidato
                where candidatos.idTipoCandidato = $idTipoCandidato ".$where."
                GROUP by candidatos.idCandidato
                ORDER by candidatos.puesto, numeroVotos DESC",Adapter::QUERY_MODE_EXECUTE)->toArray();
            
        }else if($idSexo == "0" && $idConfigurarJunta == "0"){
            $titulo = '<h1 style="text-align:center;">RESULTADOS EN LA ZONA ELECTORAL '.$nombreParroquia.'</h1>';
            $listaTotalVotos = $this->dbAdapter->query("select SUM(totalvotos.numeroVotos) as numeroTotalVotos
                from totalvotos  
                INNER join candidatos on totalvotos.idCandidato = candidatos.idCandidato
                INNER join listas on candidatos.idListaCandidato = listas.idLista
                INNER join tipocandidato on candidatos.idTipoCandidato = tipocandidato.idTipoCandidato
                INNER join configurarjunta on totalvotos.idConfigurarJunta = configurarjunta.idConfigurarJunta
                where candidatos.idTipoCandidato = $idTipoCandidato and configurarjunta.idParroquia = $idParroquia  
                GROUP by candidatos.idTipoCandidato
                ORDER by candidatos.puesto, numeroVotos DESC",Adapter::QUERY_MODE_EXECUTE)->toArray();
            
            if(count($listaTotalVotos)){
                $totalVotos = $listaTotalVotos[0]['numeroTotalVotos'];
            }
            
            $listaResultados = $this->dbAdapter->query("select listas.nombreLista, listas.numeroLista, listas.rutaFotoLista, 
                tipocandidato.identificadorTipoCandidato,
                candidatos.nombres,candidatos.rutaFotoCandidato, candidatos.puesto,
                SUM(totalvotos.numeroVotos) as numeroVotos
                from totalvotos  
                INNER join candidatos on totalvotos.idCandidato = candidatos.idCandidato
                INNER join listas on candidatos.idListaCandidato = listas.idLista
                INNER join tipocandidato on candidatos.idTipoCandidato = tipocandidato.idTipoCandidato
                INNER join configurarjunta on totalvotos.idConfigurarJunta = configurarjunta.idConfigurarJunta
                where candidatos.idTipoCandidato = $idTipoCandidato and configurarjunta.idParroquia = $idParroquia ".$where." 
                GROUP by candidatos.idCandidato
                ORDER by candidatos.puesto, numeroVotos DESC",Adapter::QUERY_MODE_EXECUTE)->toArray();
        }else if($idParroquia == "0" && $idConfigurarJunta == "0"){
            $titulo = '<h1 style="text-align:center;">RESULTADOS POR SEXO <br>'.$nombreSexo.'</h1>';
            $listaTotalVotos = $this->dbAdapter->query("select SUM(totalvotos.numeroVotos) as numeroTotalVotos
                from totalvotos  
                INNER join candidatos on totalvotos.idCandidato = candidatos.idCandidato
                INNER join listas on candidatos.idListaCandidato = listas.idLista
                INNER join tipocandidato on candidatos.idTipoCandidato = tipocandidato.idTipoCandidato
                INNER join configurarjunta on totalvotos.idConfigurarJunta = configurarjunta.idConfigurarJunta
                where candidatos.idTipoCandidato = $idTipoCandidato and configurarjunta.idSexo = $idSexo 
                GROUP by candidatos.idTipoCandidato
                ORDER by candidatos.puesto, numeroVotos DESC",Adapter::QUERY_MODE_EXECUTE)->toArray();
            
            if(count($listaTotalVotos)){
                $totalVotos = $listaTotalVotos[0]['numeroTotalVotos'];
            }
            
            $listaResultados = $this->dbAdapter->query("select listas.nombreLista, listas.numeroLista, listas.rutaFotoLista, 
                tipocandidato.identificadorTipoCandidato,
                candidatos.nombres,candidatos.rutaFotoCandidato, candidatos.puesto,
                SUM(totalvotos.numeroVotos) as numeroVotos
                from totalvotos  
                INNER join candidatos on totalvotos.idCandidato = candidatos.idCandidato
                INNER join listas on candidatos.idListaCandidato = listas.idLista
                INNER join tipocandidato on candidatos.idTipoCandidato = tipocandidato.idTipoCandidato
                INNER join configurarjunta on totalvotos.idConfigurarJunta = configurarjunta.idConfigurarJunta
                where candidatos.idTipoCandidato = $idTipoCandidato and configurarjunta.idSexo = $idSexo ".$where." 
                GROUP by candidatos.idCandidato
                ORDER by candidatos.puesto, numeroVotos DESC",Adapter::QUERY_MODE_EXECUTE)->toArray();
        }else if($idConfigurarJunta == "0"){
            $titulo = '<h1 style="text-align:center;">RESULTADOS EN LA ZONA ELECTORAL '.$nombreParroquia.' <br> '.$nombreSexo.'</h1>';
            
            $listaTotalVotos = $this->dbAdapter->query("select SUM(totalvotos.numeroVotos) as numeroTotalVotos
                from totalvotos  
                INNER join candidatos on totalvotos.idCandidato = candidatos.idCandidato
                INNER join listas on candidatos.idListaCandidato = listas.idLista
                INNER join tipocandidato on candidatos.idTipoCandidato = tipocandidato.idTipoCandidato
                INNER join configurarjunta on totalvotos.idConfigurarJunta = configurarjunta.idConfigurarJunta
                where candidatos.idTipoCandidato = $idTipoCandidato and configurarjunta.idParroquia = $idParroquia
                and configurarjunta.idSexo = $idSexo 
                GROUP by candidatos.idTipoCandidato
                ORDER by candidatos.puesto, numeroVotos DESC",Adapter::QUERY_MODE_EXECUTE)->toArray();
            
            if(count($listaTotalVotos)){
                $totalVotos = $listaTotalVotos[0]['numeroTotalVotos'];
            }
            
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
                and configurarjunta.idSexo = $idSexo ".$where." 
                GROUP by candidatos.idCandidato
                ORDER by candidatos.puesto, numeroVotos DESC",Adapter::QUERY_MODE_EXECUTE)->toArray();
        }else{
            $titulo = '<h1 style="text-align:center;">RESULTADOS EN LA ZONA ELECTORAL '.$nombreParroquia.'<br> JUNTA '.$nombreSexo.' N° '.$nombreJunta.'</h1>';
            
            $listaTotalVotos = $this->dbAdapter->query("select SUM(totalvotos.numeroVotos) as numeroTotalVotos
                from totalvotos  
                INNER join candidatos on totalvotos.idCandidato = candidatos.idCandidato
                INNER join listas on candidatos.idListaCandidato = listas.idLista
                INNER join tipocandidato on candidatos.idTipoCandidato = tipocandidato.idTipoCandidato
                INNER join configurarjunta on totalvotos.idConfigurarJunta = configurarjunta.idConfigurarJunta
                where candidatos.idTipoCandidato = $idTipoCandidato and configurarjunta.idParroquia = $idParroquia
                and configurarjunta.idSexo = $idSexo and totalvotos.idConfigurarJunta = $idConfigurarJunta
                GROUP by candidatos.idTipoCandidato
                ORDER by candidatos.puesto, numeroVotos DESC",Adapter::QUERY_MODE_EXECUTE)->toArray();
            
            if(count($listaTotalVotos)){
                $totalVotos = $listaTotalVotos[0]['numeroTotalVotos'];
            }
            
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
                and configurarjunta.idSexo = $idSexo and totalvotos.idConfigurarJunta = $idConfigurarJunta ".$where." 
                GROUP by candidatos.idCandidato
                ORDER by candidatos.puesto, numeroVotos DESC",Adapter::QUERY_MODE_EXECUTE)->toArray();
        }
        
        $filaVotosInvalidos = '';
        $totalVotosNulos = 0;
        $totalVotosBlanco = 0;
        if(count($listaTotalVotosInvalidos) > 0){
            foreach ($listaTotalVotosInvalidos as $valueVotosInvalidos) {
                if($valueVotosInvalidos['identificadorTipoVotoInvalido'] == 1){
                    $totalVotosNulos = $valueVotosInvalidos['numeroVotos'];
                }else{
                    $totalVotosBlanco = $valueVotosInvalidos['numeroVotos'];
                }
                $totalVotos = $totalVotos + $valueVotosInvalidos['numeroVotos'];
            }
            $porcentajeNulos = 0;
            $porcentajeBlanco = 0;
            foreach ($listaTotalVotosInvalidos as $valueVotosInvalidos) {
                if($valueVotosInvalidos['identificadorTipoVotoInvalido'] == 1){
                    $porcentajeNulos = ($totalVotosNulos/$totalVotos)*100;
                    $filaVotosInvalidos = $filaVotosInvalidos.'<tr>
                    <td style="text-align:center;vertical-align: middle; background-color: #FFDCD6;"></td>
                    <td style="text-align:center;vertical-align: middle; background-color: #FFDCD6;"></td>
                    <td style="text-align:center;vertical-align: middle; background-color: #FFDCD6;"></td>
                    <td style="text-align:center;vertical-align: middle; background-color: #FFDCD6;"></td>
                    <td style="vertical-align: middle; background-color: #FFDCD6;"><b>VOTOS NULOS</b></td>
                    <td style="text-align:center;vertical-align: middle; background-color: #FFDCD6;"><b>'.$valueVotosInvalidos['numeroVotos'].'</b></td>
                    <td style="text-align:center;vertical-align: middle; background-color: #FFDCD6;"><b>'.  number_format($porcentajeNulos,2).'%</b></td>
                </tr>';
                }else{
                    $porcentajeBlanco = ($totalVotosBlanco/$totalVotos)*100;
                    $filaVotosInvalidos = $filaVotosInvalidos.'<tr>
                    <td style="text-align:center;vertical-align: middle; background-color: #FFDCD6;"></td>
                    <td style="text-align:center;vertical-align: middle; background-color: #FFDCD6;"></td>
                    <td style="text-align:center;vertical-align: middle; background-color: #FFDCD6;"></td>
                    <td style="text-align:center;vertical-align: middle; background-color: #FFDCD6;"></td>
                    <td style="vertical-align: middle; background-color: #FFDCD6;"><b>VOTOS EN BLANCO</b></td>
                    <td style="text-align:center;vertical-align: middle; background-color: #FFDCD6;"><b>'.$valueVotosInvalidos['numeroVotos'].'</b></td>
                    <td style="text-align:center;vertical-align: middle; background-color: #FFDCD6;"><b>'.  number_format($porcentajeBlanco,2).'%</b></td>
                </tr>';
                }
            }
        }
        
        
        
        $filasResultado = '';
        $contador = 1;
        $porcentaje = 0;
        $totalPorcentaje = 0;
        
        foreach ($listaResultados as $valueResultado) {
            $colorFondo = '';
            if(($contador%2) == 0){
                $colorFondo = 'background-color: #D0F5FD;';
            }
            $porcentaje = ($valueResultado['numeroVotos']/$totalVotos)*100;
            
            $filasResultado = $filasResultado.'
                <tr>
                    <td style="text-align:center;vertical-align: middle;'.$colorFondo.'"><b>'.$contador.'</b></td>
                    <td style="text-align:center;vertical-align: middle;'.$colorFondo.'"><img  style="margin:0 auto 0 auto; text-align:center; width: 100%;" src="'.$this->getRequest()->getBaseUrl().$valueResultado['rutaFotoLista'].'"></td>
                    <td style="text-align:center;vertical-align: middle;'.$colorFondo.'"><b>'.$valueResultado['numeroLista'].'</b></td>
                    <td style="text-align:center;vertical-align: middle;'.$colorFondo.'"><img style="margin:0 auto 0 auto; text-align:center; width: 100%;" src="'.$this->getRequest()->getBaseUrl().$valueResultado['rutaFotoCandidato'].'"></td>
                    <td style="vertical-align: middle;'.$colorFondo.'"><b>'.$valueResultado['nombres'].'</b></td>
                    <td style="text-align:center;vertical-align: middle;'.$colorFondo.'"><b>'.$valueResultado['numeroVotos'].'</b></td>
                    <td style="text-align:center;vertical-align: middle;'.$colorFondo.'"><b>'.number_format($porcentaje,2).'%</b></td>
                </tr>

            ';
            $contador++;
            $totalPorcentaje = $totalPorcentaje + $porcentaje;
            
        }
         
        
        
        $tabla = '';
        if($filasResultado != ""){
            
            $totalPorcentaje = $totalPorcentaje + $porcentajeNulos + $porcentajeBlanco;
            $filaSumaTotal = "";
            if($idLista == "0"){
                $filaSumaTotal = '<tr>
                        <td colspan="5" style="text-align:center;background-color:#FCFFC9;"><b>TOTAL</b></td>
                        <td style="text-align:center;background-color:#FCFFC9;"><b>'.$totalVotos.'</b></td>
                        <td style="text-align:center;background-color:#FCFFC9;"><b>'.number_format($totalPorcentaje,2).'%</b></td>
                    </tr>';
            }
            
            $tabla = '<div class="table-responsive"><table class="table">
                <thead>
                <tr><th colspan="7" style="text-align:center;">'.$tipoCandidato.$titulo.'</th></tr>
                <tr> 
                    <th style="width: 5%;text-align:center;background-color:#3c8dbc">#</th>
                    <th style="width: 5%;text-align:center;background-color:#3c8dbc">LOGO</th>
                    <th style="width: 10%;text-align:center;background-color:#3c8dbc">LISTA</th>
                    <th style="width: 5%;text-align:center;background-color:#3c8dbc">FOTO</th>
                    <th style="width: 30%;text-align:center;background-color:#3c8dbc">CANDIDATO</th>
                    <th style="width: 30%;text-align:center;background-color:#3c8dbc">VOTOS</th>
                    <th style="width: 30%;text-align:center;background-color:#3c8dbc">%</th>
                </tr>
                </thead>
                <tbody>
                    '.$filasResultado.'
                    '.$filaVotosInvalidos.'
                    '.$filaSumaTotal.'
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
                          $optionListasCandidatos='<option value="0" style="font-weight: bold;">TODAS LAS LISTAS</option>';
                         foreach ($listaListas as $valueListasC) {
                               $optionListasCandidatos=$optionListasCandidatos.'<option style="font-weight: bold;" value="'.$valueListasC['idLista'].'">'.$valueListasC['numeroLista'].' - '.$valueListasC['nombreLista'].'</option>'; 
                            }
                    $mensaje = '';
                    $validar = TRUE;
                    return new JsonModel(array('mensaje'=>$mensaje,'validar'=>$validar,'tabla'=>$optionListasCandidatos));
                    
                }
            }
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
                    $selectListas = '<select style="font-weight: bold;" class="form-control" id="selectListas" onchange="filtrarResultados();"><option value="0" style="font-weight: bold;">TODAS LAS LISTAS</option></select>';
                    $selectTipoCandidato = '<select style="font-weight: bold;" class="form-control" id="selectTipoCandidato" onchange="filtrarlistasportipocandidato();filtrarResultados();">'.$optionTipoCandidato.'</select>';
                    $selectParroquias = '<select style="font-weight: bold;" class="form-control" id="selectParroquias" onchange="filtrarJuntasElectorales();filtrarResultados();">'.$optionParroquia.'</select>';
                    $selectSexo = '<select style="font-weight: bold;" class="form-control" id="selectSexo" onchange="filtrarJuntasElectorales();filtrarResultados();">'.$optionSexo.'</select>';
                    $tabla = '<div id="mensajeFormResultados" class="col-lg-12">
                            
                            </div>
                            <div class="col-lg-12">
                            <div class="form-group col-lg-3">'.$selectTipoCandidato.'</div>
                                <div class="form-group col-lg-3">'.$selectParroquias.'</div>
                                <div class="form-group col-lg-3">'.$selectSexo.'</div>
                                <div class="form-group col-lg-3">'.$selectJuntas.'</div>
                                <div class="form-group col-lg-6">'.$selectListas.'</div>
                            </div>
                            <div id="contenedorTablaResultados" class="col-lg-12"></div>';
                    $mensaje = '';
                    $validar = TRUE;
                }else if($idFormulario == '2')
                {
                    $listaTipoCandidato = $objTipoCandidato->obtenerTipoCandidato();
                    $optionTipoCandidato = '<option value="0" style="font-weight: bold;">SELECCIONE UN TIPO DE CANDIDATO</option>';
                    foreach ($listaTipoCandidato as $valueTipoCandidato) {
                        $optionTipoCandidato = $optionTipoCandidato.'<option value="'.$valueTipoCandidato['idTipoCandidato'].'" style="font-weight: bold;">'.$valueTipoCandidato['descripcionTipoCandidato'].'</option>';
                    }
                    $selectTipoCandidato = '<select style="font-weight: bold;" class="form-control" id="selectTipoCandidatoGanador" onchange="filtrarGanadorePorTipo();">'.$optionTipoCandidato.'</select>';
                    $tabla = '<div class="col-lg-12">
                                <div class="form-group col-lg-12">'.$selectTipoCandidato.'</div>
                            </div>
                            <div id="contenedorTablaGanadores" class="col-lg-12"></div>';
                    $mensaje = '';
                    $validar = TRUE;
                }
                else{
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
   
}
    
    
