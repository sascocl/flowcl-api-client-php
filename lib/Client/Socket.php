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

namespace sasco\FlowCL\Client;

/**
 * Clase para manejar conexiones HTTP
 * @author Esteban De La Fuente Rubio, DeLaF (esteban[at]sasco.cl)
 * @version 2019-06-18
 */
class Socket
{

    protected $header = [
        'User-Agent' => 'SASCO Flow.cl Client',
        'Content-Type' => 'application/x-www-form-urlencoded',
    ]; ///< Cabeceras por defecto

    /**
     * Método que hace la consulta al servicio web
     * @param url Dirección web que se desea obtener
     * @param data Datos que se enviarán por POST
     * @param header Cabeceras que se deben pasar a la solicitud HTTP
     * @return Arreglo con la respuesta de cURL, índices: status, header, body
     * @author Esteban De La Fuente Rubio, DeLaF (esteban[at]sasco.cl)
     * @version 2019-06-18
     */
    public function consume($url, $data = null, $header = [])
    {
        $header = array_merge($this->header, $header);
        // crear curl con opciones estándares
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_PORT , 443);
        curl_setopt($curl, CURLOPT_VERBOSE, 0);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 1);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_HEADER, 1);
        // si hay datos se asignan
        if ($data) {
            if (is_array($data)) {
                $aux = [];
                foreach ($data as $key => $value) {
                    $aux[] = $key.'='.$value;
                }
                $data = implode('&', $aux);
            }
            $header['Content-Length'] = strlen($data);
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        }
        if ($header) {
            foreach ($header as $name => $value) {
                if (!is_numeric($name)) {
                    $header[$name] = $name.': '.$value;
                }
            }
            curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
        }
        $response = curl_exec($curl);
        if (!$response) {
            throw new \Exception(curl_error($curl));
        }
        $header_size = curl_getinfo($curl, CURLINFO_HEADER_SIZE);
        // cerrar conexión de curl y entregar respuesta de la solicitud
        $response_header = $this->parseHeader(substr($response, 0, $header_size));
        curl_close($curl);
        return [
            'status' => $this->parseStatus($response_header[0]),
            'header' => $response_header,
            'body' => substr($response, $header_size),
        ];
    }

    /**
     * Método que procesa la cabecera en texto plano y la convierte a un arreglo
     * con los nombres de la cabecera como índices y sus valores.
     * Si una cabecera aparece más de una vez, por tener varios valores,
     * entonces dicha cabecerá tendrá como valor un arreglo con todos sus
     * valores.
     * @param header Cabecera HTTP en texto plano
     * @return Arreglo asociativo con la cabecera
     * @author Esteban De La Fuente Rubio, DeLaF (esteban[at]sasco.cl)
     * @version 2014-12-03
     */
    private function parseHeader($header)
    {
        $headers = [];
        $lineas = explode("\n", $header);
        foreach ($lineas as &$linea) {
            $linea = trim($linea);
            if (!isset($linea[0])) continue;
            if (strpos($linea, ':')) {
                list($key, $value) = explode(':', $linea, 2);
            } else {
                $key = 0;
                $value = $linea;
            }
            $key = trim($key);
            $value = trim($value);
            if (!isset($headers[$key])) {
                $headers[$key] = $value;
            } else if (!is_array($headers[$key])) {
                $aux = $headers[$key];
                $headers[$key] = [$aux, $value];
            } else {
                $headers[$key][] = $value;
            }
        }
        return $headers;
    }

    /**
     * Método que procesa la línea de respuesta y extrae el protocolo, código de
     * estado y el mensaje del estado
     * @param response_line
     * @return Arreglo con índices: protocol, code, message
     * @author Esteban De La Fuente Rubio, DeLaF (esteban[at]sasco.cl)
     * @version 2019-11-07
     */
    private function parseStatus($response_line)
    {
        if (is_array($response_line)) {
            $response_line = $response_line[count($response_line)-1];
        }
        $aux = explode(' ', $response_line, 3);
        return [
            'protocol' => $aux[0],
            'code' => $aux[1],
            'message' => !empty($aux[2]) ? $aux[2] : null,
        ];
    }

}
