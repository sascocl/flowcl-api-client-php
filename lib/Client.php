<?php

/**
 * Cliente para servicios web de Flow.cl
 * Copyright (C) SASCO SpA (https://sasco.cl)
 *
 * Este programa es software libre: usted puede redistribuirlo y/o modificarlo
 * bajo los términos de la GNU Lesser General Public License (LGPL) publicada
 * por la Fundación para el Software Libre, ya sea la versión 3 de la Licencia,
 * o (a su elección) cualquier versión posterior de la misma.
 *
 * Este programa se distribuye con la esperanza de que sea útil, pero SIN
 * GARANTÍA ALGUNA; ni siquiera la garantía implícita MERCANTIL o de APTITUD
 * PARA UN PROPÓSITO DETERMINADO. Consulte los detalles de la GNU Lesser General
 * Public License (LGPL) para obtener una información más detallada.
 *
 * Debería haber recibido una copia de la GNU Lesser General Public License
 * (LGPL) junto a este programa. En caso contrario, consulte
 * <http://www.gnu.org/licenses/lgpl.html>.
 */

namespace sasco\FlowCL;

/**
 * Clase principal con el cliente de Flow.cl
 * @author Esteban De La Fuente Rubio, DeLaF (esteban[at]sasco.cl)
 * @version 2019-07-27
 */
class Client extends AbstractObject
{

    private $_url = [
        'production' => 'https://www.flow.cl/api',
        'sandbox' => 'https://sandbox.flow.cl/api',
    ]; ///< URL base para las llamadas a la API
    protected $site = 'production';
    protected $apiKey; ///< API Key para autenticación
    protected $secretKey; ///< Secret Key para autenticación
    protected $response; ///< Objeto con la respuesta del servicio web de Flow.cl

    /**
     * Constructor del cliente
     * @param api_key API Key de la API de Flow.cl
     * @param secret_key Secret key de la API de Flow.cl
     * @author Esteban De La Fuente Rubio, DeLaF (esteban[at]sasco.cl)
     * @version 2019-06-18
     */
    public function __construct($api_key = null, $api_secret = null, $site = 'production')
    {
        if ($api_key && $api_secret) {
            $this->setApiKey($api_key);
            $this->setSecretKey($api_secret);
        }
        $this->setSite($site);
    }

    /**
     * Método que crea una orden de pago
     * @include 001-crear_orden_pago.php
     * @param PaymentOrder Objeto que representa la orden de pago que se desea crear
     * @return Objeto que representa la orden de pago actualizada al resultado de creación
     * @author Esteban De La Fuente Rubio, DeLaF (esteban[at]sasco.cl)
     * @version 2019-06-18
     */
    public function createPaymentOrder(\sasco\FlowCL\Payment\Order &$PaymentOrder)
    {
        $url = $this->createUrl('/payment/create');
        $this->setResponse($this->consume($url, $PaymentOrder->getData()));
        $body = json_decode($this->getResponse()->getBody());
        if ($this->getResponse()->getStatus()->code!=200) {
            throw new \Exception('No fue posible crear la orden de pago en Flow.cl: '.$body->message);
        }
        $PaymentOrder->set($body);
        return $PaymentOrder;
    }

    /**
     * Método que recupera una orden de pago y su estado
     * @include 002-estado_orden_pago.php
     * @param token Identificador de la orden de pago que se desea buscar
     * @return Objeto que representa la orden de pago que se buscó
     * @author Esteban De La Fuente Rubio, DeLaF (esteban[at]sasco.cl)
     * @version 2019-06-18
     */
    public function getPaymentOrder($token)
    {
        $url = $this->createUrl('/payment/getStatus', compact('token'));
        $this->setResponse($this->consume($url));
        $body = json_decode($this->getResponse()->getBody());
        if ($this->getResponse()->getStatus()->code!=200) {
            throw new \Exception('No fue posible obtener la orden de pago con token '.$token.' desde Flow.cl: '.$body->message);
        }
        $PaymentOrder = new \sasco\FlowCL\Payment\Order();
        $PaymentOrder->set($body);
        return $PaymentOrder;
    }

    /**
     * Método que recupera una orden de pago y su estado
     * @include 002-estado_orden_pago.php
     * @param token Identificador de la orden de pago que se desea buscar
     * @return Objeto que representa la orden de pago que se buscó
     * @author Esteban De La Fuente Rubio, DeLaF (esteban[at]sasco.cl)
     * @version 2019-06-18
     */
    public function getPaymentOrderByFlowOrder($flowOrder)
    {
        $url = $this->createUrl('/payment/getStatusByFlowOrder', compact('flowOrder'));
        $this->setResponse($this->consume($url));
        $body = json_decode($this->getResponse()->getBody());
        if ($this->getResponse()->getStatus()->code!=200) {
            throw new \Exception('No fue posible obtener la orden de pago #'.$flowOrder.' desde Flow.cl: '.$body->message);
        }
        $PaymentOrder = new \sasco\FlowCL\Payment\Order();
        $PaymentOrder->set($body);
        return $PaymentOrder;
    }

    /**
     * Método que recupera una orden de pago y su estado
     * @include 002-estado_orden_pago.php
     * @param token Identificador de la orden de pago que se desea buscar
     * @return Objeto que representa la orden de pago que se buscó
     * @author Esteban De La Fuente Rubio, DeLaF (esteban[at]sasco.cl)
     * @version 2019-06-18
     */
    public function getPaymentOrderByCommerceId($commerceId)
    {
        $url = $this->createUrl('/payment/getStatusByCommerceId', compact('commerceId'));
        $this->setResponse($this->consume($url));
        $body = json_decode($this->getResponse()->getBody());
        if ($this->getResponse()->getStatus()->code!=200) {
            throw new \Exception('No fue posible obtener la orden de pago '.$commerceId.' del comercio desde Flow.cl: '.$body->message);
        }
        $PaymentOrder = new \sasco\FlowCL\Payment\Order();
        $PaymentOrder->set($body);
        return $PaymentOrder;
    }

    /**
     * Método que crea la URL final que se usará para acceder al servicio web
     * @param recurso Recurso dentro de la API de Flow.cl que se desea consumir
     * @param params Parámetros por GET que se pasarán a la API (parámetro => valor)
     * @return String con la URL bien formada, incluyendo host, versión API, recurso y parámetros por GET
     * @author Esteban De La Fuente Rubio, DeLaF (esteban[at]sasco.cl)
     * @version 2019-06-18
     */
    private function createUrl($recurso, array $params = [])
    {
        $url = $this->_url[$this->getSite()].$recurso;
        if (!$params) {
            return $url;
        }
        $query = http_build_query($params);
        return sprintf("%s?%s", $url, $query);
    }

    /**
     * Método que consume el servicio web de Flow.cl
     * Este método crea las cabeceras con la firma de los datos del servicio que
     * se está consultando
     * @param url URL bien formada que se consumirá
     * @param data Datos a pasar por POST al servicio web
     * @return \sasco\FlowCL\Client\Response con la respuesta del servicio
     * @author Esteban De La Fuente Rubio, DeLaF (esteban[at]sasco.cl)
     * @version 2019-06-18
     */
    private function consume($url, $data = null)
    {
        if (!empty($this->getApiKey()) && !empty($this->getSecretKey())) {
            // procesar datos de GET
            $url_info = parse_url($url);
            if (!empty($url_info['query'])) {
                parse_str($url_info['query'], $data_get);
                $data_get['apiKey'] = $this->getApiKey();
                ksort($data_get);
                $msg = [];
                foreach($data_get as $var => $val) {
                    $msg[] = $var.'='.$val;
                }
                $msg['s'] = 's='.hash_hmac('sha256', implode('&', $msg), $this->getSecretKey());
                $url = $url_info['scheme'].'://'.$url_info['host'].$url_info['path'].'?'.implode('&',$msg);
            }
            // procesar datos de POST
            if ($data) {
                $msg = [];
                $data['apiKey'] = $this->getApiKey();
                ksort($data);
                foreach($data as $var => $val) {
                    $msg[] = $var.'='.$val;
                }
                $data['s'] = hash_hmac('sha256', implode('&', $msg), $this->getSecretKey());
            }
        }
        $Socket = new \sasco\FlowCL\Client\Socket();
        $Response = new \sasco\FlowCL\Client\Response($Socket->consume($url, $data));
        return $Response;
    }

}
