<?php 

$user="lareina";
$pasw="123";
$name='Municipalidad de la Reina';

include __DIR__.'/test.php' ;

lpf($user,$pasw,$name);


include __DIR__.'/lpfAgricolaSur.php';
include __DIR__.'/lpfAndesMar.php';
include __DIR__.'/lpfAraucaniaSur.php';
include __DIR__.'/lpfCloudB2B.php';
include __DIR__.'/lpffBeltran.php';
include __DIR__.'/lpfIngeGroup.php';
include __DIR__.'/lpfLaReina.php';
include __DIR__.'/lpfLasCondes.php';
include __DIR__.'/lpfmReyes.php';
include __DIR__.'/lpfParticulares.php';
include __DIR__.'/lpfRentaBus.php';
include __DIR__.'/lpfSanClemente.php';
include __DIR__.'/lpfSantaVictoria.php';
include __DIR__.'/lpfssvq.php';
include __DIR__.'/lpfTacoa.php';
include __DIR__.'/lpfTranspas.php';
include __DIR__.'/lpfTransportesTip.php';
include __DIR__.'/lpfTransportesVillarroel.php';

echo "fin!!";

?>