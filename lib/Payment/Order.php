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

namespace sasco\FlowCL\Payment;

/**
 * Clase que representa una orden de pago de Flow.cl
 * @author Esteban De La Fuente Rubio, DeLaF (esteban[at]sasco.cl)
 * @version 2019-07-27
 */
class Order extends \sasco\FlowCL\AbstractObject
{

    // atributos obligatorios para crear una orden
    protected $commerceOrder; ///< Orden del comercio
    protected $subject; ///< Descripción de la orden
    protected $amount; ///< Monto de la orden
    protected $email; ///< email del pagador
    protected $urlConfirmation; ///< url callbak del comercio donde Flow confirmará el pago
    protected $urlReturn; ///< url de retorno del comercio donde Flow redirigirá al pagador


    // atributos opcionales para crear una orden
    protected $currency; ///< Moneda de la orden
    protected $paymentMethod; ///< Identificador del medio de pago
    protected $optional; ///< Datos opcionales en formato JSON clave = valor, ejemplo: {"rut":"76192083-9","razon_social":"SASCO SpA"}

    // atributos que se asignan a través del servicio web de Flow.cl
    protected $url; ///< URL redirección
    protected $token; ///< Token para redirección
    protected $flowOrder; ///< Número de orden den Flow.cl
    protected $requestDate;
    protected $status;
    protected $payer;
    protected $pending_info;
    protected $paymentData;

    protected $paymentMethods = [
        1 => 'Webpay',
        2 => 'Servipag',
        3 => 'Multicaja',
        5 => 'Onepay',
        8 => 'Cryptocompra',
        //9 => 'Todos los medios contratados',
    ]; ///< Métodos de pagos posibles en la API de Flow.cl

    /**
     * Método que entrega los datos que se asignaron a la orden para que sea creada
     * @return Arreglo con los datos necesarios para la creación de la orden de pago
     * @author Esteban De La Fuente Rubio, DeLaF (esteban[at]sasco.cl)
     * @version 2019-06-18
     */
    public function getData()
    {
        $data = [];
        foreach (['commerceOrder', 'subject', 'amount', 'email', 'urlConfirmation', 'urlReturn', 'currency', 'paymentMethod', 'optional'] as $var) {
            if (isset($this->$var)) {
                if ($var == 'optional') {
                    $data[$var] = json_encode($this->$var);
                } else {
                    $data[$var] = $this->$var;
                }
            }
        }
        return $data;
    }

    /**
     * Método que entrega la URL
     * @return String con la URL para el pago en Flow.cl
     * @author Esteban De La Fuente Rubio, DeLaF (esteban[at]sasco.cl)
     * @version 2019-06-18
     */
    public function getUrl()
    {
        return sprintf('%s?token=%s', $this->url, $this->token);
    }

}
