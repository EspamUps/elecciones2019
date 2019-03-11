<?php
namespace Nel\Metodos;
class ValidarIdentificacion
{
    /**
     * Error
     *
     * Contiene errores globales de la clase
     *
     * @var string
     * @access protected
     */
    protected $error = '';
    /**
     * Validar cédula
     *
     * @param  string  $numero  Número de cédula
     *
     * @return Boolean
     */
    public function validarCedula($numero = '')
    {
        $mensaje = '';
        $validar = FALSE;
        // fuerzo parametro de entrada a string
        $numero = (string)$numero;
        if($numero == '' || $numero == NULL || !is_numeric($numero) || strlen($numero) < 13){
            $mensaje = '<div class="alert alert-danger text-center" role="alert">INGRESE UN RUC VÁLIDO</div>';
        }else{
            $valorInicial =  $this->validarInicial($numero, '10');
            $codigoProvincia =  $this->validarCodigoProvincia(substr($numero, 0, 2));
            $tercerDigito =  $this->validarTercerDigito($numero[2], 'cedula');
            $ultimoDigito =  $this->algoritmoModulo10(substr($numero, 0, 9), $numero[9]);
            if($valorInicial[0] == FALSE){
                $mensaje = $valorInicial[1];
            }else if($codigoProvincia[0] == FALSE){
                $mensaje = $codigoProvincia[1];
            }else if($tercerDigito[0] == FALSE){
                $mensaje = $tercerDigito[1];
            }else if($ultimoDigito[0] == FALSE){
                $mensaje = $ultimoDigito[1];
            }else{
                $validar = TRUE;
            }
        }
        $array = array(
            0=>$validar,
            1=>$mensaje
        );
        return $array;
    }
    /**
     * Validar RUC persona natural
     *
     * @param  string  $numero  Número de RUC persona natural
     *
     * @return Boolean
     */
    public function validarRucPersonaNatural($numero = '')
    {
        $mensaje = '';
        $validar = FALSE;
        // fuerzo parametro de entrada a string
        $numero = (string)$numero;
        if($numero == '' || $numero == NULL || !is_numeric($numero) || strlen($numero) < 13){
            $mensaje = '<div class="alert alert-danger text-center" role="alert">INGRESE UN RUC VÁLIDO</div>';
        }else{
            $valorInicial =  $this->validarInicial($numero, '13');
            $codigoProvincia =  $this->validarCodigoProvincia(substr($numero, 0, 2));
            $tercerDigito =  $this->validarTercerDigito($numero[2], 'ruc_natural');
            $establecimiento = $this->validarCodigoEstablecimiento(substr($numero, 10, 3));
            $ultimoDigito =  $this->algoritmoModulo10(substr($numero, 0, 9), $numero[9]);
            if($valorInicial[0] == FALSE){
                $mensaje = $valorInicial[1];
            }else if($codigoProvincia[0] == FALSE){
                $mensaje = $codigoProvincia[1];
            }else if($tercerDigito[0] == FALSE){
                $mensaje = $tercerDigito[1];
            }else if($ultimoDigito[0] == FALSE){
                $mensaje = $ultimoDigito[1];
            }else if($establecimiento[0] == FALSE){
                $mensaje = $establecimiento[1];
            }else{
                $validar = TRUE;
            }
        }
        $array = array(
            0=>$validar,
            1=>$mensaje
        );
        return $array;
    }
    /**
     * Validar RUC sociedad privada
     *
     * @param  string  $numero  Número de RUC sociedad privada
     *
     * @return Boolean
     */
    public function validarRucSociedadPrivada($numero = '')
    {
        $mensaje = '';
        $validar = FALSE;
        // fuerzo parametro de entrada a string
        $numero = (string)$numero;
        if($numero == '' || $numero == NULL || !is_numeric($numero) || strlen($numero) < 13){
            $mensaje = '<div class="alert alert-danger text-center" role="alert">INGRESE UN RUC VÁLIDO</div>';
        }else{
            $valorInicial =  $this->validarInicial($numero, '13');
            $codigoProvincia =  $this->validarCodigoProvincia(substr($numero, 0, 2));
            $tercerDigito =  $this->validarTercerDigito($numero[2], 'ruc_privada');
            $establecimiento = $this->validarCodigoEstablecimiento(substr($numero, 10, 3));
            $ultimoDigito =  $this->algoritmoModulo11(substr($numero, 0, 9), $numero[9], 'ruc_privada');
            if($valorInicial[0] == FALSE){
                $mensaje = $valorInicial[1];
            }else if($codigoProvincia[0] == FALSE){
                $mensaje = $codigoProvincia[1];
            }else if($tercerDigito[0] == FALSE){
                $mensaje = $tercerDigito[1];
            }else if($ultimoDigito[0] == FALSE){
                $mensaje = $ultimoDigito[1];
            }else if($establecimiento[0] == FALSE){
                $mensaje = $establecimiento[1];
            }else{
                $validar = TRUE;
            }
        }
        $array = array(
            0=>$validar,
            1=>$mensaje
        );
        return $array;
    }
    /**
     * Validar RUC sociedad publica
     *
     * @param  string  $numero  Número de RUC sociedad publica
     *
     * @return Boolean
     */
    public function validarRucSociedadPublica($numero = '')
    {
         $mensaje = '';
        $validar = FALSE;
        // fuerzo parametro de entrada a string
        $numero = (string)$numero;
        if($numero == '' || $numero == NULL || !is_numeric($numero) || strlen($numero) < 13){
            $mensaje = '<div class="alert alert-danger text-center" role="alert">INGRESE UN RUC VÁLIDO</div>';
        }else{
            $valorInicial =  $this->validarInicial($numero, '13');
            $codigoProvincia =  $this->validarCodigoProvincia(substr($numero, 0, 2));
            $tercerDigito =  $this->validarTercerDigito($numero[2], 'ruc_publica');
            $establecimiento = $this->validarCodigoEstablecimiento(substr($numero, 9, 4));
            $ultimoDigito =  $this->algoritmoModulo11(substr($numero, 0, 8), $numero[8], 'ruc_publica');
            if($valorInicial[0] == FALSE){
                $mensaje = $valorInicial[1];
            }else if($codigoProvincia[0] == FALSE){
                $mensaje = $codigoProvincia[1];
            }else if($tercerDigito[0] == FALSE){
                $mensaje = $tercerDigito[1];
            }else if($ultimoDigito[0] == FALSE){
                $mensaje = $ultimoDigito[1];
            }else if($establecimiento[0] == FALSE){
                $mensaje = $establecimiento[1];
            }else{
                $validar = TRUE;
            }
        }
        $array = array(
            0=>$validar,
            1=>$mensaje
        );
        return $array;
    }
    /**
     * Validaciones iniciales para CI y RUC
     *
     * @param  string  $numero      CI o RUC
     * @param  integer $caracteres  Cantidad de caracteres requeridos
     *
     * @return Boolean
     *
     * @throws exception Cuando valor esta vacio, cuando no es dígito y
     * cuando no tiene cantidad requerida de caracteres
     */
    protected function validarInicial($numero, $caracteres)
    {
        $mensaje = '';
        $validar = FALSE;
        if (empty($numero)) {
            $mensaje = '<div class="alert alert-danger text-center" role="alert">VALOR NO PUEDE ESTAR VACIO</div>';
        }else if (!ctype_digit($numero)) {
            $mensaje = '<div class="alert alert-danger text-center" role="alert">VALOR INGRESADO SOLO PUEDE TENER DÍGITOS</div>';
        }else if (strlen($numero) != $caracteres) {
            $mensaje = '<div class="alert alert-danger text-center" role="alert">VALOR INGRESADO DEBE TENER '.$caracteres.' CARACTERES</div>';
        }else{
            $validar = TRUE;
        }
        $array = array(
            0=>$validar,
            1=>$mensaje
        );
        return $array;
    }
    /**
     * Validación de código de provincia (dos primeros dígitos de CI/RUC)
     *
     * @param  string  $numero  Dos primeros dígitos de CI/RUC
     *
     * @return boolean
     *
     * @throws exception Cuando el código de provincia no esta entre 00 y 24
     */
    protected function validarCodigoProvincia($numero)
    {
        $mensaje = '';
        $validar = FALSE;
        if ($numero < 0 OR $numero > 24) {
            $mensaje = '<div class="alert alert-danger text-center" role="alert">CODIGO DE PROVINCIA (DOS PRIMEROS DÍGITOS) NO DEBEN SER MAYOR A 24 NI MENORES A 0</div>';
        }else{
            $validar = TRUE;
        }
        $array = array(
            0=>$validar,
            1=>$mensaje
        );
        return $array;
    }
    /**
     * Validación de tercer dígito
     *
     * Permite validad el tercer dígito del documento. Dependiendo
     * del campo tipo (tipo de identificación) se realizan las validaciones.
     * Los posibles valores del campo tipo son: cedula, ruc_natural, ruc_privada
     *
     * Para Cédulas y RUC de personas naturales el terder dígito debe
     * estar entre 0 y 5 (0,1,2,3,4,5)
     *
     * Para RUC de sociedades privadas el terder dígito debe ser
     * igual a 9.
     *
     * Para RUC de sociedades públicas el terder dígito debe ser 
     * igual a 6.
     *
     * @param  string $numero  tercer dígito de CI/RUC
     * @param  string $tipo  tipo de identificador
     *
     * @return boolean
     *
     * @throws exception Cuando el tercer digito no es válido. El mensaje
     * de error depende del tipo de Idenficiación.
     */
    protected function validarTercerDigito($numero, $tipo)
    {
        $mensaje = '';
        $validar = FALSE;
        switch ($tipo) {
            case 'cedula':
                $validar = TRUE;
                break;
            case 'ruc_natural':
                if ($numero < 0 OR $numero > 5) {
                    $mensaje = '<div class="alert alert-danger text-center" role="alert">TERCER DÍGITO DEBE SER MAYOR O IGUAL A 0 Y MENOR A 6 PARA CÉDULAS Y RUC DE PERSONA NATURAL</div>';
                }else{
                    $validar = TRUE;
                }
                break;
            case 'ruc_privada':
                if ($numero != 9) {
                    $mensaje = '<div class="alert alert-danger text-center" role="alert">TERCER DÍGITO DEBE SER IGUAL A 9 PARA SOCIEDADES PRIVADAS</div>';
                }else{
                    $validar = TRUE;
                }
                break;
            case 'ruc_publica':
                if ($numero != 6) {
                    $mensaje = '<div class="alert alert-danger text-center" role="alert">TERCER DÍGITO DEBE SER IGUAL A 6 PARA SOCIEDADES PÚBLICAS</div>';
                }else{
                    $validar = TRUE;
                }
                break;
            default:
                $mensaje = '<div class="alert alert-danger text-center" role="alert">TIPO DE IDENTIFICACIÓN NO EXISTE.</div>';
                break;
        }
        $array = array(
            0=>$validar,
            1=>$mensaje
        );
        return $array;
    }
    /**
     * Validación de código de establecimiento
     *
     * @param  string $numero  tercer dígito de CI/RUC
     *
     * @return boolean
     *
     * @throws exception Cuando el establecimiento es menor a 1
     */
    protected function validarCodigoEstablecimiento($numero)
    {
        $mensaje = '';
        $validar = FALSE;
        if ($numero < 1) {
            $mensaje = '<div class="alert alert-danger text-center" role="alert">CÓDIGO DE ESTABLECIMIENTO NO PUEDE SER 0</div>';
        }else{
            $validar = TRUE;
        }
        $array = array(
            0=>$validar,
            1=>$mensaje
        );
        return $array;
    }
    /**
     * Algoritmo Modulo10 para validar si CI y RUC de persona natural son válidos.
     *
     * Los coeficientes usados para verificar el décimo dígito de la cédula,
     * mediante el algoritmo “Módulo 10” son:  2. 1. 2. 1. 2. 1. 2. 1. 2
     *
     * Paso 1: Multiplicar cada dígito de los digitosIniciales por su respectivo
     * coeficiente.
     *
     *  Ejemplo
     *  digitosIniciales posicion 1  x 2
     *  digitosIniciales posicion 2  x 1
     *  digitosIniciales posicion 3  x 2
     *  digitosIniciales posicion 4  x 1
     *  digitosIniciales posicion 5  x 2
     *  digitosIniciales posicion 6  x 1
     *  digitosIniciales posicion 7  x 2
     *  digitosIniciales posicion 8  x 1
     *  digitosIniciales posicion 9  x 2
     *
     * Paso 2: Sí alguno de los resultados de cada multiplicación es mayor a o igual a 10,
     * se suma entre ambos dígitos de dicho resultado. Ex. 12->1+2->3
     *
     * Paso 3: Se suman los resultados y se obtiene total
     *
     * Paso 4: Divido total para 10, se guarda residuo. Se resta 10 menos el residuo.
     * El valor obtenido debe concordar con el digitoVerificador
     *
     * Nota: Cuando el residuo es cero(0) el dígito verificador debe ser 0.
     *
     * @param  string $digitosIniciales   Nueve primeros dígitos de CI/RUC
     * @param  string $digitoVerificador  Décimo dígito de CI/RUC
     *
     * @return boolean
     *
     * @throws exception Cuando los digitosIniciales no concuerdan contra
     * el código verificador.
     */
    protected function algoritmoModulo10($digitosIniciales, $digitoVerificador)
    {
        $mensaje = '';
        $validar = FALSE;
        
        $arrayCoeficientes = array(2,1,2,1,2,1,2,1,2);
        $digitoVerificador = (int)$digitoVerificador;
        $digitosIniciales = str_split($digitosIniciales);
        $total = 0;
        foreach ($digitosIniciales as $key => $value) {
            $valorPosicion = ( (int)$value * $arrayCoeficientes[$key] );
            if ($valorPosicion >= 10) {
                $valorPosicion = str_split($valorPosicion);
                $valorPosicion = array_sum($valorPosicion);
                $valorPosicion = (int)$valorPosicion;
            }
            $total = $total + $valorPosicion;
        }
        $residuo =  $total % 10;
        if ($residuo == 0) {
            $resultado = 0;
        } else {
            $resultado = 10 - $residuo;
        }
        if ($resultado != $digitoVerificador) {
            $mensaje = '<div class="alert alert-danger text-center" role="alert">DÍGITOS INICIALES NO VALIDAN CONTRA DÍGITO IDENFICADOR</div>';
        }else{
            $validar = TRUE;
        }
        $array = array(
            0=>$validar,
            1=>$mensaje
        );
        return $array;
    }
    /**
     * Algoritmo Modulo11 para validar RUC de sociedades privadas y públicas
     *
     * El código verificador es el decimo digito para RUC de empresas privadas
     * y el noveno dígito para RUC de empresas públicas
     *
     * Paso 1: Multiplicar cada dígito de los digitosIniciales por su respectivo
     * coeficiente.
     *
     * Para RUC privadas el coeficiente esta definido y se multiplica con las siguientes
     * posiciones del RUC:
     *
     *  Ejemplo
     *  digitosIniciales posicion 1  x 4
     *  digitosIniciales posicion 2  x 3
     *  digitosIniciales posicion 3  x 2
     *  digitosIniciales posicion 4  x 7
     *  digitosIniciales posicion 5  x 6
     *  digitosIniciales posicion 6  x 5
     *  digitosIniciales posicion 7  x 4
     *  digitosIniciales posicion 8  x 3
     *  digitosIniciales posicion 9  x 2
     *
     * Para RUC privadas el coeficiente esta definido y se multiplica con las siguientes
     * posiciones del RUC:
     *
     *  digitosIniciales posicion 1  x 3
     *  digitosIniciales posicion 2  x 2
     *  digitosIniciales posicion 3  x 7
     *  digitosIniciales posicion 4  x 6
     *  digitosIniciales posicion 5  x 5
     *  digitosIniciales posicion 6  x 4
     *  digitosIniciales posicion 7  x 3
     *  digitosIniciales posicion 8  x 2
     *
     * Paso 2: Se suman los resultados y se obtiene total
     *
     * Paso 3: Divido total para 11, se guarda residuo. Se resta 11 menos el residuo.
     * El valor obtenido debe concordar con el digitoVerificador
     *
     * Nota: Cuando el residuo es cero(0) el dígito verificador debe ser 0.
     *
     * @param  string $digitosIniciales   Nueve primeros dígitos de RUC
     * @param  string $digitoVerificador  Décimo dígito de RUC
     * @param  string $tipo Tipo de identificador
     *
     * @return boolean
     *
     * @throws exception Cuando los digitosIniciales no concuerdan contra
     * el código verificador.
     */
    protected function algoritmoModulo11($digitosIniciales, $digitoVerificador, $tipo)
    {
        $mensaje = '';
        $validar = FALSE;
        switch ($tipo) {
            case 'ruc_privada':
                $arrayCoeficientes = array(4, 3, 2, 7, 6, 5, 4, 3, 2);
                break;
            case 'ruc_publica':
                $arrayCoeficientes = array(3, 2, 7, 6, 5, 4, 3, 2);
                break;
            default:
                $mensaje = '<div class="alert alert-danger text-center" role="alert">TIPO DE IDENTIFICACIÓN NO EXISTE</div>';
                break;
        }
        $digitoVerificador = (int)$digitoVerificador;
        $digitosIniciales = str_split($digitosIniciales);
        $total = 0;
        foreach ($digitosIniciales as $key => $value) {
            $valorPosicion = ( (int)$value * $arrayCoeficientes[$key] );
            $total = $total + $valorPosicion;
        }
        $residuo =  $total % 11;
        if ($residuo == 0) {
            $resultado = 0;
        } else {
            $resultado = 11 - $residuo;
        }
        if ($resultado != $digitoVerificador) {
            $mensaje = '<div class="alert alert-danger text-center" role="alert">DÍGITOS INICIALES NO VALIDAN CONTRA DÍGITO IDENFICADOR</div>';
        }else{
            $validar = TRUE;
        }
        $array = array(
            0=>$validar,
            1=>$mensaje
        );
        return $array;
    }
    /**
     * Get error
     *
     * @return string Mensaje de error
     */
    public function getError()
    {
        return $this->error;
    }
    /**
     * Set error
     *
     * @param  string $newError
     * @return object $this
     */
    public function setError($newError)
    {
        $this->error = $newError;
        return $this;
    }
}
?>