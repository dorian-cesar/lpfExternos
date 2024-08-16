<?php

// Incluir el archivo que define la función lpf()
include_once __DIR__ . '/test.php';

// Listado de archivos a ejecutar
$files = [
    'lpfAgricolaSur.php',
    'lpfAndesMar.php',
    'lpfAraucaniaSur.php',
    'lpfCloudB2B.php',
    'lpfeMorales.php',
    'lpffBeltran.php',
    'lpfIngeGroup.php',
    'lpfLaReina.php',
    'lpfLasCondes.php',
    'lpfmReyes.php',
    'lpfParticulares.php',
    'lpfRentaBus.php',
    'lpfSanClemente.php',
    'lpfSantaVictoria.php',
    'lpfssvq.php',
    'lpfTacoa.php',
    'lpfTranspas.php',
    'lpfTransportesTip.php',
    'lpfTransportesVillarroel.php',
];

// Ruta base al directorio donde se encuentran los archivos
$baseDir = __DIR__ . '/';

// Ejecuta cada archivo en secuencia
foreach ($files as $file) {
    include $baseDir . $file;
}

echo "Todos los archivos han sido ejecutados.\n";

?>

