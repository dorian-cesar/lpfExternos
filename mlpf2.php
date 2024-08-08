<?php

function lpf($user, $pasw)
{
    include "conexion.php";

    $consulta = "SELECT hash FROM masgps.hash WHERE user='$user' AND pasw='$pasw'";
    $resultado = mysqli_query($mysqli, $consulta);
    $data = mysqli_fetch_array($resultado);
    $hash = $data['hash'];

    date_default_timezone_set("America/Santiago");
    $hoy = date("Y-m-d");

    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL => 'http://www.trackermasgps.com/api-v2/tracker/list',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => '{"hash":"' . $hash . '"}',
        CURLOPT_HTTPHEADER => array(
            'Accept: application/json, text/plain, */*',
            'Accept-Language: es-419,es;q=0.9,en;q=0.8',
            'Connection: keep-alive',
            'Content-Type: application/json',
            'Cookie: _ga=GA1.2.728367267.1665672802; locale=es; _gid=GA1.2.967319985.1673009696; _gat=1; session_key=5d7875e2bf96b5966225688ddea8f098',
            'Origin: http://www.trackermasgps.com',
            'Referer: http://www.trackermasgps.com/',
            'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/108.0.0.0 Safari/537.36'
        ),
    ));

    $response2 = curl_exec($curl);
    $json = json_decode($response2);
    $array = $json->list;

    $chunks = array_chunk($array, 10); // Dividimos el array en partes de 10 elementos cada una

    foreach ($chunks as $chunk) {
        $mh = curl_multi_init();
        $curl_array = array();

        foreach ($chunk as $key => $item) {
            $id = $item->id;
            $imei = $item->source->device_id;
            $group = $item->group_id;

            $curl_array[$key] = curl_init();
            curl_setopt_array($curl_array[$key], array(
                CURLOPT_URL => 'http://www.trackermasgps.com/api-v2/tracker/get_state',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => '{"hash": "' . $hash . '", "tracker_id": ' . $id . '}',
                CURLOPT_HTTPHEADER => array(
                    'Content-Type: application/json'
                ),
            ));

            curl_multi_add_handle($mh, $curl_array[$key]);
        }

        $running = null;
        do {
            curl_multi_exec($mh, $running);
            curl_multi_select($mh);
        } while ($running > 0);

        foreach ($curl_array as $key => $curl) {
            $response2 = curl_multi_getcontent($curl);
            $json2 = json_decode($response2);

            $lat = $json2->state->gps->location->lat;
            $lng = $json2->state->gps->location->lng;
            $last_u = $json2->state->last_update;
            $plate = $chunk[$key]->label;
            $status = $json2->state->connection_status;

            $datosduplicados = mysqli_query($mysqli, "SELECT * FROM lpfExternos WHERE id_tracker='$id'");

            if (mysqli_num_rows($datosduplicados) > 0) {
                // Si el id_tracker ya existe, se actualizan todos los valores del registro
                $sql1 = "UPDATE lpfExternos SET `lat`='$lat', `lng`='$lng', `last_update`='$last_u', `fecha`='$hoy', `cuenta`='$user', `imei`='$imei', `connection_status`='$status', `grupo`='$group' WHERE `id_tracker`='$id'";
                $ejecutar1 = mysqli_query($mysqli, $sql1);
                echo "Actualizado: id_tracker = $id<br>";
            } else {
                // Si el id_tracker no existe, se inserta un nuevo registro
                $sql = "INSERT INTO lpfExternos (cuenta, id_tracker, `lat`, `lng`, `patente`, `fecha`, `last_update`, `imei`, `connection_status`, `grupo`) VALUES ('$user', '$id', '$lat', '$lng', '$plate', '$hoy', '$last_u', '$imei', '$status', '$group')";
                $ejecutar = mysqli_query($mysqli, $sql);
                echo "Creado: id_tracker = $id<br>";
            }

            curl_multi_remove_handle($mh, $curl);
        }

        curl_multi_close($mh);
    }
}
