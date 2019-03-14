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
use Nel\Modelo\Entity\TipoTotalVotosInvalidos;
use Nel\Modelo\Entity\TotalVotosInvalidos;
use Nel\Modelo\Entity\TotalVotos;
use Nel\Modelo\Entity\Sexo;
use Nel\Modelo\Entity\Parroquia;
use Zend\Db\Adapter\Adapter;

class VotosController extends AbstractActionController
{
    public $dbAdapter;

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
                          $optionListasCandidatos='<option value="0">SELECCIONE UNA LISTA</option>';
                         foreach ($listaListas as $valueListasC) {
                               $optionListasCandidatos=$optionListasCandidatos.'<option value="'.$valueListasC['idLista'].'">'.$valueListasC['numeroLista'].' - '.$valueListasC['nombreLista'].'</option>'; 
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
            $idTipoVoto =$post['idTipoVoto'];

            if($idTipoCandidato == "" || $idTipoCandidato == NULL){
                $mensaje = '<div class="alert alert-danger text-center" role="alert">NO SE ENCUENTRA EL ÍNDICE DEL TIPO DE CANDIDATO</div>';
            }else if($idListaCandidato == "" || $idListaCandidato == NULL){
                $mensaje = '<div class="alert alert-danger text-center" role="alert">NO SE ENCUENTRA EL ÍNDICE DE LA LISTA DEL CANDIDATO</div>';
            }else if($idConfigurarJunta == "" || $idConfigurarJunta == NULL){
                $mensaje = '<div class="alert alert-danger text-center" role="alert">NO SE ENCUENTRA EL ÍNDICE DE LA JUNTA</div>';
            }else if($idTipoVoto == "" || $idTipoVoto == NULL){
                $mensaje = '<div class="alert alert-danger text-center" role="alert">NO SE ENCUENTRA EL ÍNDICE DEL TIPO DE VOTO</div>';
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
                            $listaListasCandidato = array();
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
                                
                                if($idTipoVoto=="0")
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
                                    

                                        $contadorPorLista=0;
                                        foreach ($listaListasCandidato as $valueLista) {
                                            $encabezadoLista='<div class="text-center" style="background-color:#fff;">'
                                                                .'<img src="'.$this->getRequest()->getBaseUrl().'/'.$valueLista['rutaFotoLista'].'" class="text-center" style="width: 7%;" >'
                                                                .'<h4><b>'.$valueLista['numeroLista'].' - '.$valueLista['nombreLista'].'</b></h4>'
                                                                . '</div>';
                                            $listaCandidatos = $objCandidato->filtrarCandidatoPorListaPorTipoCandidato($idTipoCandidato, $valueLista['idLista']);
                                            $contador=0;
                                            $cuerpoTablaCandidatos='';
                                            if(count($listaCandidatos)>0)
                                            {
                                                    $objTotalVoto = new TotalVotos($this->dbAdapter);
                                                    foreach ($listaCandidatos as $valueCandidato) {       
                                                        $valor='';
                                                        $colorCelda='#fff';
                                                        $totalVoto = $objTotalVoto->filtrarTotalVotosPorCandidatoPorJuntaElectoral($valueCandidato['idCandidato'],$idConfigurarJunta);
                                                        if(count($totalVoto)>0)
                                                        {
                                                            $valor=$totalVoto[0]['numeroVotos'];
                                                            $colorCelda='#bede9985';
                                                        }

                                                        $contador++;
                                                        $input = '<input type="hidden" id="candidato'.$contador.''.$contadorPorLista.'" name="candidato'.$contador.''.$contadorPorLista.'" value="'.$valueCandidato['idCandidato'].'"></input>';
                                                        $cuerpoTablaCandidatos=$cuerpoTablaCandidatos.'<tr style="background-color:'.$colorCelda.'">'
                                                                    .'<th class="text-center" style="vertical-align: middle;">'.$input.' '.$valueCandidato['puesto'].'</th>'
                                                                    .'<th class="text-center"  style="vertical-align: middle;"><img src="'.$this->getRequest()->getBaseUrl().'/'.$valueCandidato['rutaFotoCandidato'].'" style="width: 100%;"></th>'
                                                                    .'<th class="text-center" style="vertical-align: middle;">'.$valueCandidato['nombres'].'</th>'
                                                                    .'<th class="text-center"  style="vertical-align: middle;"><input id="votos'.$contador.''.$contadorPorLista.'" name="votos'.$contador.''.$contadorPorLista.'" type="number" value="'.$valor.'"></input></th>'
                                                                . '</tr>';
                                                        }

                                                    $tablaCandidadosPorLista= '<div class="col-lg-12" style="background-color:#ecf0f5;"><h4 style="color:#ecf0f5;">22</h4></div>'
                                                                .'<div class="col-lg-12" id="mensajeVotosPorLista'.$contadorPorLista.'"></div>'
                                                                .'<div class="col-lg-12"  style="background-color:#fff">'
                                                                .'<br>'
                                                                .'<br>'
                                                                    .$encabezadoLista       
                                                                 .'<br>'
                                                                .'<br>'
                                                                .'<div class="table-responsive">'
                                                                    . '<table  class="table table-hover" >'
                                                                        .$tablaCandidatosEncabezado
                                                                    .'<tbody  id="contenedorVotosPorLista'.$contadorPorLista.'">'
                                                                        .$cuerpoTablaCandidatos
                                                                        .'<tr><th></th><th></th><th></th>'
                                                                        . '<th class="text-center">'
                                                                            .'<button type="button" class="btn btn-success" onclick="guardarVotosLista('.$contador.','.$contadorPorLista.');"><i class="fa fa-check"></i>GUARDAR VOTOS LISTA '.$valueLista['numeroLista'].'</button>'
                                                                        . '</th>'
                                                                    . '</tr>'
                                                                    .'</tbody>'
                                                                    .'</table>'
                                                                .'</div>'
                                                            .'</div>';
                                                    $grupoDeTablas=$grupoDeTablas.$tablaCandidadosPorLista;
                                            }
                                            $encabezadoLista='';     
                                            $contadorPorLista++;
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
                                else
                                {
                                    
                                    $tablaVotosInvalidosEncabezado='<thead><tr>'
                                                                .'<td style="width: 10%;"  class="text-center">#</td>'                               
                                                                .'<td style="width: 60%;"  class="text-center">TIPO DE VOTO</td>'
                                                                .'<td style="width: 30%;"  class="text-center">NÚMERO DE VOTOS</td>'
                                                            . '</tr></thead>';
                                   
                                    $objTiposVotosInvalidos = new TipoTotalVotosInvalidos($this->dbAdapter);
                                    $listaTiposVotosInvalidos =$objTiposVotosInvalidos->obtenerTipoTotalVotosInvalidos();
                                    $contadorInvalidos =1;
                                    $cuerpoVotosI='';
                                    $objTotalVotoInvalido = new TotalVotosInvalidos($this->dbAdapter);
                                    foreach ($listaTiposVotosInvalidos as $valueTipoVotosInvalidos) {
                                        $valor='';
                                        $colorCelda='#fff';
                                        $totalVotoInvalido = $objTotalVotoInvalido->filtrarTotalVotosInvalidosPorTipoCandidatoPorTipoVotoPorJuntaElectoral($valueTipoVotosInvalidos['idTipoVotosInvalidos'], $idTipoCandidato, $idConfigurarJunta);
                                        if(count($totalVotoInvalido)>0)
                                        {
                                            $valor=$totalVotoInvalido[0]['numeroVotos'];
                                            $colorCelda='#bede9985';
                                        }
                                        
                                                $input = '<input type="hidden" id="tipoVotoInvalido'.$contadorInvalidos.'" name="tipoVotoInvalido'.$contadorInvalidos.'" value="'.$valueTipoVotosInvalidos['idTipoVotosInvalidos'].'"></input>';
                                                $cuerpoVotosI = $cuerpoVotosI.'<tr style="background-color:'.$colorCelda.'">'
                                                                                .'<th class="text-center" style="vertical-align: middle;">'.$input.''.$contadorInvalidos.'</th>'
                                                                                .'<th class="text-center" style="vertical-align: middle;">'.$valueTipoVotosInvalidos['descripcionTipoVotosInvalidos'].'</th>'
                                                                                .'<th class="text-center" style="vertical-align: middle;"><input value="'.$valor.'" id="votosInvalidos'.$contadorInvalidos.'" name="votosInvalidos'.$contadorInvalidos.'" type="number"></input></th>'
                                                                             . '</tr>';
                                                $contadorInvalidos++;
                                            }

                                    $tablaVotosInvalidos = '<div class="table-responsive"><table style="background-color:#fff" class="table table-hover">'
                                                                        .$tablaVotosInvalidosEncabezado
                                                                        .'<tbody id="contenedorVotosInvalidos">'
                                                                            .$cuerpoVotosI
                                                                                .'<tr><th></th><th></th>'
                                                                               . '<th class="text-center">'
                                                                                   .'<button type="button" class="btn btn-success" onclick="guardarVotosEspeciales('.$contadorInvalidos.');"><i class="fa fa-check"></i>GUARDAR VOTOS ESPECIALES</button>'
                                                                               . '</th>'
                                                                        . '</tbody>'
                                                                    .'</table></div>';
                                    $tabla='<div class="col-lg-2"></div>'
                                            .'<div class="col-lg-8">'
                                                .$tablaVotosInvalidos
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
        }
        return new JsonModel(array('mensaje'=>$mensaje,'validar'=>$validar));
    }
    
    
    
    public function guardarvotosporlistaAction()
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
            $contadorPorLista=$post['contadorPorLista'];
            $idConfigurarJunta=$post['idConfigurarJunta'];
            if($idConfigurarJunta=="" || $idConfigurarJunta==NULL)
            {
                $mensaje = '<div class="alert alert-danger text-center" role="alert">NO SE ENCUENTRA EL ÍNDICE DE LA JUNTA</div>';
            }
            $arrayVotos = $post['arrayVotos'];
            
            $totalIngresados =0;
            foreach ($arrayVotos as $valueVotos)
            {
                if($valueVotos[1]!=NULL)
                {                       
                    $totalIngresados++;
                }                
            }
            if(count($arrayVotos)!=$totalIngresados)
            {
                $mensaje = '<div class="alert alert-danger text-center" role="alert">ES NECESARIO QUE REGISTRE LOS VOTOS DE TODOS LOS CANDIDATOS DE LA LISTA</div>';
            }
            else 
            {
                $objTotalVotos = new TotalVotos($this->dbAdapter);
                ini_set('date.timezone','America/Bogota'); 
                $hoy = getdate();
                $fechaIngreso = $hoy['year']."-".$hoy['mon']."-".$hoy['mday']." ".$hoy['hours'].":".$hoy['minutes'].":".$hoy['seconds'];

                $objTotalVoto = new TotalVotos($this->dbAdapter);
                foreach ($arrayVotos as $valueVotos)
                {
                    $resultado=FALSE;
                    if($valueVotos[1]!="")
                    {      
                        $totalVoto = $objTotalVoto->filtrarTotalVotosPorCandidatoPorJuntaElectoral($valueVotos[0],$idConfigurarJunta);
                        if(count($totalVoto)>0)
                        {
                               $arrayTotalVotos = array(
                                'numeroVotos'=>$valueVotos[1],
                                'fechaIngreso'=>$fechaIngreso,
                                );
                                $resultado= $objTotalVotos->editarTotalVotos($totalVoto[0]['idTotalVotos'],$arrayTotalVotos);
                        }
                        else
                        {
                            $arrayTotalVotos2 = array(
                                'idCandidato'=>$valueVotos[0],
                                'idConfigurarJunta'=>$idConfigurarJunta,
                                'numeroVotos'=>$valueVotos[1],
                                'fechaIngreso'=>$fechaIngreso,
                                'estado'=>1
                            );
                            $resultado= $objTotalVotos->ingresarTotalVotos($arrayTotalVotos2);
                        }
                        if($resultado==false)
                        {
                               $mensaje = '<div class="alert alert-danger text-center" role="alert">OCURRIÓ UN ERROR AL INTENTAR GUARDAR LOS VOTOS</div>';
                               break;                          
                        }                        
                    }                
                }
                $contador=0;
                $objCandidato = new Candidatos($this->dbAdapter);
                $objLista = new Listas($this->dbAdapter);
                $cuerpoTablaCandidatos='';
                foreach ($arrayVotos as $valueCandidato) {       
                    $listaCandidato = $objCandidato->filtrarCandidato($valueCandidato[0]);
                    $valor='';
                    $colorCelda='#fff';
                    $totalVoto = $objTotalVoto->filtrarTotalVotosPorCandidatoPorJuntaElectoral($valueCandidato[0],$idConfigurarJunta);
                    if(count($totalVoto)>0)
                    {
                        $valor=$totalVoto[0]['numeroVotos'];
                        $colorCelda='#bede9985';
                    }

                    $contador++;
                    $input = '<input type="hidden" id="candidato'.$contador.''.$contadorPorLista.'" name="candidato'.$contador.''.$contadorPorLista.'" value="'.$valueCandidato[0].'"></input>';
                    $cuerpoTablaCandidatos=$cuerpoTablaCandidatos.'<tr style="background-color:'.$colorCelda.'">'
                                .'<th class="text-center" style="vertical-align: middle;">'.$input.' '.$listaCandidato[0]['puesto'].'</th>'
                                .'<th class="text-center"  style="vertical-align: middle;"><img src="'.$this->getRequest()->getBaseUrl().'/'.$listaCandidato[0]['rutaFotoCandidato'].'" style="width: 100%;"></th>'
                                .'<th class="text-center" style="vertical-align: middle;">'.$listaCandidato[0]['nombres'].'</th>'
                                .'<th class="text-center"  style="vertical-align: middle;"><input id="votos'.$contador.''.$contadorPorLista.'" name="votos'.$contador.''.$contadorPorLista.'" type="number" value="'.$valor.'"></input></th>'
                            . '</tr>';
                }
                $lista = $objLista->filtrarLista($listaCandidato[0]['idListaCandidato']);
                $tabla =   $cuerpoTablaCandidatos. '<tr><th></th><th></th><th></th>'
                                . '<th class="text-center">'
                                        .'<button type="button" class="btn btn-success" onclick="guardarVotosLista('.$contador.','.$contadorPorLista.');"><i class="fa fa-check"></i>GUARDAR VOTOS LISTA '.$lista[0]['numeroLista'].'</button>'
                                . '</th>'
                            . '</tr>';
                                                        
                $mensaje='';
                $validar=TRUE;
                return new JsonModel(array('mensaje'=>$mensaje,'validar'=>$validar, 'tabla'=>$tabla));
            }
        }
         return new JsonModel(array('mensaje'=>$mensaje,'validar'=>$validar));
    }
    
    
    public function guardarvotosespecialesAction()
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
            $idTipoCandidato=$post['idTipoCandidato'];
            $idConfigurarJunta=$post['idConfigurarJunta'];
            if($idConfigurarJunta=="" || $idConfigurarJunta==NULL)
            {
                $mensaje = '<div class="alert alert-danger text-center" role="alert">NO SE ENCUENTRA EL ÍNDICE DE LA JUNTA</div>';
            }
            else
            {
                $arrayVotos = $post['arrayVotos'];
            
            $totalIngresados =0;
            foreach ($arrayVotos as $valueVotos)
            {
                if($valueVotos[1]!=NULL)
                {                       
                    $totalIngresados++;
                }                
            }
            if(count($arrayVotos)!=$totalIngresados)
            {
                $mensaje = '<div class="alert alert-danger text-center" role="alert">ES NECESARIO QUE AMBOS CASILLEROS ESTÉN COMPLETOS</div>';
            }
            else 
            {
                $objTotalVotosInvalidos = new TotalVotosInvalidos($this->dbAdapter);
                foreach ($arrayVotos as $valueVotos)
                {
                    $resultado=FALSE;
                    if($valueVotos[1]!="")
                    {      
                        $totalVoto = $objTotalVotosInvalidos->filtrarTotalVotosInvalidosPorTipoCandidatoPorTipoVotoPorJuntaElectoral($valueVotos[0], $idTipoCandidato, $idConfigurarJunta);
                        if(count($totalVoto)>0)
                        {
                               $arrayTotalVotos = array(
                                'numeroVotos'=>$valueVotos[1]
                                );
                                $resultado= $objTotalVotosInvalidos->editarTotalVotosInvalidos($totalVoto[0]['idTotalVotosInvalidos'],$arrayTotalVotos);
                        }
                        else
                        {
                            $arrayTotalVotos2 = array(
                                'idConfigurarJuntaElectoral'=>$idConfigurarJunta,
                                'idTipoCandidato'=>$idTipoCandidato,
                                'numeroVotos'=>$valueVotos[1],
                                'idTipoVotoInvalido'=>$valueVotos[0],
                                'estado'=>1
                            );
                            $resultado= $objTotalVotosInvalidos->ingresarTotalVotosInvalidos($arrayTotalVotos2);
                        }
                        if($resultado==false)
                        {
                               $mensaje = '<div class="alert alert-danger text-center" role="alert">OCURRIÓ UN ERROR AL INTENTAR GUARDAR LOS VOTOS</div>';
                               break;                          
                        }                        
                    }                    
                }
                                   
                $objTiposVotosInvalidos = new TipoTotalVotosInvalidos($this->dbAdapter);
                $listaTiposVotosInvalidos =$objTiposVotosInvalidos->obtenerTipoTotalVotosInvalidos();
                $contadorInvalidos =1;
                $cuerpoVotosI='';
                foreach ($listaTiposVotosInvalidos as $valueTipoVotosInvalidos) {
                    $valor='';
                    $colorCelda='#fff';
                    $totalVotoInvalido = $objTotalVotosInvalidos->filtrarTotalVotosInvalidosPorTipoCandidatoPorTipoVotoPorJuntaElectoral($valueTipoVotosInvalidos['idTipoVotosInvalidos'], $idTipoCandidato, $idConfigurarJunta);
                    if(count($totalVotoInvalido)>0)
                        {
                            $valor=$totalVotoInvalido[0]['numeroVotos'];
                            $colorCelda='#bede9985';
                        }
                                        
                       $input = '<input type="hidden" id="tipoVotoInvalido'.$contadorInvalidos.'" name="tipoVotoInvalido'.$contadorInvalidos.'" value="'.$valueTipoVotosInvalidos['idTipoVotosInvalidos'].'"></input>';
                       $cuerpoVotosI = $cuerpoVotosI.'<tr style="background-color:'.$colorCelda.'">'
                                                    .'<th class="text-center" style="vertical-align: middle;">'.$input.' '.$contadorInvalidos.'</th>'
                                                    .'<th class="text-center" style="vertical-align: middle;">'.$valueTipoVotosInvalidos['descripcionTipoVotosInvalidos'].'</th>'
                                                    .'<th class="text-center" style="vertical-align: middle;"><input value="'.$valor.'" id="votosInvalidos'.$contadorInvalidos.'" name="votosInvalidos'.$contadorInvalidos.'" type="number"></input></th>'
                                                    . '</tr>';
                        $contadorInvalidos++;
                }

                $tablaVotosInvalidos =  $cuerpoVotosI
                                         .'<tr><th></th><th></th>'
                                         . '<th class="text-center">'
                                        .'<button type="button" class="btn btn-success" onclick="guardarVotosEspeciales('.$contadorInvalidos.');"><i class="fa fa-check"></i>GUARDAR VOTOS ESPECIALES</button>'
                                         . '</th>';
                
                                                        
                $mensaje='';
                $validar=TRUE;
                return new JsonModel(array('mensaje'=>$mensaje,'validar'=>$validar, 'tabla'=>$tablaVotosInvalidos));
                }
            }
        }
    return new JsonModel(array('mensaje'=>$mensaje,'validar'=>$validar));
    }
}