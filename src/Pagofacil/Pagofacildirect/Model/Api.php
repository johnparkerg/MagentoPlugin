<?php
/**
 * API para el consumo de los servicios
 * de PagoFacil
 * @author ivelazquex <isai.velazquez@gmail.com>
 */
class Pagofacil_Pagofacildirect_Model_Api
{
    // --- ATTRIBUTES ---
    /**
     * URL del servicio de PagoFacil para pruebas
     * @var string
     */
    protected $_urlDemo = 'https://www.pagofacil.net/st/public/Wsrtransaccion/index/format/json';
    
    /**
     * URL del servicio de PagoFacil en ambiente de produccion
     * @var string 
     */
    protected $_urlProd = 'https://www.pagofacil.net/ws/public/Wsrtransaccion/index/format/json';

    /**
     * Clave de sucursal para el entorno de pruebasa(STAGE)
     * @var string
     */
    protected $_sucursalKeyDemo = "60f961360ca187d533d5adba7d969d6334771370";
    
    /**
     * Clave de usuario para el entorno de pruebas(STAGE)
     * @var string
     */
    protected $_usuarioKeyDemo = "62ad6f592ecf2faa87ef2437ed85a4d175e73c58";
    
    /**
     * respuesta sin parsear del servicio
     * @var string
     */
    protected $_response = NULL;
    
    
    // --- OPERATIONS ---
    
    public function __construct()
    {
        
    }
    
    /**
     * consume el servicio de pago de PagoFacil
     * @param string[] vector con la informacion de la peticion
     * @return mixed respuesta del consumo del servicio
     * @throws Exception
     */
    public function payment($info)
    {
        $response = null;
        // NOTA la url a la cual s edebe conectar en produccion no viene 
        // la direccion de produccion en el documento 
        
        if (!is_array($info))
        {
            throw new Exception('parameter is not an array');
        }

        $info['url'] = $this->_urlProd;
        // determinar si el entorno es para pruebas
        if ($info['prod'] == '0')
        {
            $info['url'] = $this->_urlDemo;
            $info['idSucursal'] = $this->_sucursalKeyDemo;
            $info['idUsuario'] = $this->_usuarioKeyDemo;
        }

        // datos para la peticion del servicio
        $data = array(
            'idServicio'         => urlencode('3')
            ,'idSucursal'        => urlencode($info['idSucursal'])
            ,'idUsuario'         => urlencode($info['idUsuario'])
            ,'nombre'            => urlencode($info['nombre'])
            ,'apellidos'         => urlencode($info['apellidos'])
            ,'numeroTarjeta'     => urlencode($info['numeroTarjeta'])
            ,'cvt'               => urlencode($info['cvt'])
            ,'cp'                => urlencode($info['cp'])
            ,'mesExpiracion'     => urlencode($info['mesExpiracion'])
            ,'anyoExpiracion'    => urlencode($info['anyoExpiracion'])
            ,'monto'             => urlencode($info['monto'])
            ,'email'             => urlencode($info['email'])
            ,'telefono'          => urlencode($info['telefono'])
            ,'celular'           => urlencode($info['celular'])
            ,'calleyNumero'      => urlencode($info['calleyNumero'])
            ,'colonia'           => urlencode($info['colonia'])
            ,'municipio'         => urlencode($info['municipio'])
            ,'estado'            => urlencode($info['estado'])
            ,'pais'              => urlencode($info['pais'])
            ,'idPedido'          => urlencode($info['idPedido'])
            ,'ip'                => urlencode($info['ipBuyer'])
            ,'noMail'            => urlencode($info['noMail'])
            ,'plan'              => urlencode($info['plan'])
            ,'mensualidades'     => urlencode($info['mensualidades'])
            
            //,'param1'            => urlencode()
            //,'param2'            => urlencode()
            //,'param3'            => urlencode()
            //,'param4'            => urlencode()
            //,'param5'            => urlencode()
            
            //'httpUserAgent'     => urlencode($_SERVER['HTTP_USER_AGENT']) // TABLA CAMPO OPCIONALES
            
        );

        // construccion de la peticion
        $cadena='';
        foreach ($data as $key=>$valor){
            $cadena.="&data[$key]=$valor";
        }        
        $url = $info['url'].'/?method=transaccion'.$cadena;

        // consumo del servicio
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        // Blindly accept the certificate
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $this->_response = curl_exec($ch);
        curl_close($ch);

        // tratamiento de la respuesta del servicio
        $response = json_decode($this->_response,true);
        $response = $response['WebServices_Transacciones']['transaccion'];
                
        return $response;
    }
    
    /**
     * obtiene la respuesta del servicio
     * @return string
     */
    public function getResponse()
    {
        return $this->_response;
    }

}