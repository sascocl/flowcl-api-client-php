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
 * @example 001-crear_orden_pago.php
 * Ejemplo para crear una orden de pago
 * @link https://www.flow.cl/docs/api.html#tag/payment/paths/~1payment~1create/post
 * @author Esteban De La Fuente Rubio, DeLaF (esteban[at]sasco.cl)
 * @version 2019-06-18
 */

// credenciales
$api_key = '';
$secret_key = '';
$site = 'sandbox'; // sandbox o production

// incluir autoload composer
require '../../vendor/autoload.php';

// crear cliente
$Client = new \sasco\FlowCL\Client($api_key, $secret_key, $site);

// crear orden de pago
// $Client->createPaymentOrder() modifica $PaymentOrder asignando la respuesta con los datos de la orden
// también lo retorna, pero como lo modifica directamente, no sería necesario ocupar lo retornado
$PaymentOrder = new \sasco\FlowCL\Payment\Order();
$PaymentOrder->setCommerceOrder('T33F22');
$PaymentOrder->setSubject('Cobro de SASCO SpA');
$PaymentOrder->setCurrency('CLP');
$PaymentOrder->setAmount(10000);
$PaymentOrder->setEmail('cliente@gmail.com');
$PaymentOrder->setPaymentMethod(9);
$PaymentOrder->setUrlConfirmation('https://example.com/api/flow/notification');
$PaymentOrder->setUrlReturn('https://example.com/flow/thanks');
$PaymentOrder->setOptional([
    'rut' => '66666666-6',
    'razon_social' => 'Sin razón social informada',
]);
try {
    $Client->createPaymentOrder($PaymentOrder);
    echo $PaymentOrder->getUrl()."\n";
} catch (\Exception $e) {
    die('[error] '.$e->getMessage()."\n");
}
