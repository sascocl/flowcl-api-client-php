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
 * Clase abstracta que será hederada por las clases del SDK
 * @author Esteban De La Fuente Rubio, DeLaF (esteban[at]sasco.cl)
 * @version 2019-07-27
 */
abstract class AbstractObject
{

    /**
     * Método que asigna los atributos de un objeto a este si existen en esta
     * clase y tienen valor en el objeto que se está pasando
     * @param data Arreglo con los datos que se desean asignar (atributo => valor)
     * @author Esteban De La Fuente Rubio, DeLaF (esteban[at]sasco.cl)
     * @version 2017-10-12
     */
    public function set($data)
    {
        $vars = array_keys(get_class_vars(get_called_class()));
        foreach ($vars as $var) {
            if (property_exists($data, $var)) {
                $this->$var = $data->$var;
            }
        }
    }

    /**
     * Método mágico para procesar seters y geters de las clases
     * @param method Sólo puede ser del tipo setX o getX (donde X debe ser un atributo válido del Objeto)
     * @param args Sólo puede ser un parámetro args[0] que será el valor que se asignará al atributo X
     * @return Entrega este mismo objeto (si no falla)
     * @author Esteban De La Fuente Rubio, DeLaF (esteban[at]sasco.cl)
     * @version 2019-06-18
     */
    public function __call($method, $args = null)
    {
        // si es método set se trata de usar
        if (substr($method,0,3)=='set' && isset($args[0])) {
            $var = lcfirst(substr($method, 3));
            if (property_exists($this, $var)) {
                $this->$var = $args[0];
            } else {
                throw new \Exception('Atributo '.get_called_class().'::'.$var.' no existe');
            }
            return $this;
        }
        // si es método get se trata de usar
        else if (substr($method,0,3)=='get') {
            $var = lcfirst(substr($method, 3));
            if (property_exists($this, $var)) {
                return $this->$var;
            } else {
                throw new \Exception('Atributo '.get_called_class().'::'.$var.' no existe');
            }
        }
        // si método no existe se informa
        else {
            throw new \Exception('Método '.get_called_class().'::'.$method.'() no existe');
        }
    }

}
