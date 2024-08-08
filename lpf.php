<?php



function lpf($user,$pasw){

include "conexion.php";

$consulta="SELECT hash FROM masgps.hash where user='$user' and pasw='$pasw'";

$resutaldo= mysqli_query($mysqli,$consulta);

$data=mysqli_fetch_array($resutaldo);

$hash=$data['hash'];

date_default_timezone_set("America/Santiago");

$hoy = date("Y-m-d");

$ayer=date('Y-m-d',strtotime("-1 days"));

$listado = '';

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

foreach ($array as $item) {


  $id = $item->id;
  
  $imei=$item->source->device_id;
 
  $group=$item->group_id;



  $curl = curl_init();

  curl_setopt_array($curl, array(
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


  $response2 = curl_exec($curl);

  curl_close($curl);

  $json2 = json_decode($response2);
 
   $lat  = $json2->state->gps->location->lat;
 
   $lng = $json2->state->gps->location->lng;
  
   $last_u = $json2->state->last_update;
  
   $plate = $item->label;

  $status=$json2->state->connection_status;

  
  $sql = "INSERT INTO lpfExternos2 (cuenta,id_tracker,`lat`,`lng`,`patente`,`fecha`,`last_update`, `imei`,`connection_status`,`grupo` ) VALUES ('$user','$id', '$lat', '$lng', '$plate', '$hoy','$last_u','$imei', '$status','$group'  )";
 

   $datosduplicados = mysqli_query($mysqli, "SELECT * FROM lpfExternos2 WHERE id_tracker='$id'");

   echo mysqli_num_rows($datosduplicados);

   if (mysqli_num_rows($datosduplicados) > 0) {

     // LO actualizo conforme a la echa de hoy y tambien a la patente me falta terminar el update
    
     $sql1 = "UPDATE lpfExternos2 SET `lat`='$lat', `lng`='$lng'   ,`last_update`= '$last_u', `fecha`='$hoy', `cuenta`='$user', `imei`='$imei', `connection_status`='$status', `grupo`='$group' WHERE `id_tracker`='$id' ";

    $ejecutar1 = mysqli_query($mysqli, $sql1);
    echo "-actualizado <br>";

   } else {

  // si no se repite entonces se sube
     $ejecutar = mysqli_query($mysqli, $sql);

     echo "-creado : <br>";
   }
}
}

