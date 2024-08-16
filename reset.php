<?php
include "conexion.php";




// Consulta para truncar la tabla
$sql = "TRUNCATE TABLE lpfExternos2";

if (mysqli_query($mysqli, $sql)) {
    echo "Tabla 'lpfExternos2' truncada exitosamente.";
} else {
    echo "Error al truncar la tabla: " . mysqli_error($mysqli);
}

// Cerrar la conexión
mysqli_close($mysqli);



