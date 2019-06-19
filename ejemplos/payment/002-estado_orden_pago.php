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

/**
 * @example 002-estado_orden_pago.php
 * Ejemplo para obtener el estado de una orden de pago
 * @link https://www.flow.cl/docs/api.html#tag/payment/paths/~1payment~1getStatus/get
 * @author Esteban De La Fuente Rubio, DeLaF (esteban[at]sasco.cl)
 * @version 2019-06-18
 */

// credenciales
$api_key = '';
$secret_key = '';
$site = 'sandbox'; // sandbox o production

// incluir autoload composer
require '../../vendor/autoload.php';

// crear cliente y mercado
$Client = new \sasco\FlowCL\Client($api_key, $secret_key, $site);

// obtener estado de la orden de pago
try {
    print_r($Client->getPaymentOrder('9A0AFBBFCA36F8E9357CFB222E9133AF8B65FE0L'));
    //print_r($Client->getPaymentOrderByFlowOrder(99811));
    //print_r($Client->getPaymentOrderByCommerceId('T33F22'));
} catch (\Exception $e) {
    die('[error] '.$e->getMessage()."\n");
}
