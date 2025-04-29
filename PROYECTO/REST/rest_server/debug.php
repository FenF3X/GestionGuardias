<?php
require 'config.php';
$doc = '010960328X'; // copia uno válido de tu BD
$sql = "
SELECT
  interlocutor_id,
  interlocutor_nombre,
  mensaje,
  fecha,
  hora
FROM (
  SELECT
    CASE WHEN docent_emisor  = '$doc' THEN docent_receptor ELSE docent_emisor END AS interlocutor_id,
    CASE WHEN docent_emisor  = '$doc' THEN nombreReceptor ELSE nombreEmisor END AS interlocutor_nombre,
    mensaje,
    fecha,
    hora,
    ROW_NUMBER() OVER (
      PARTITION BY CASE WHEN docent_emisor = '$doc' THEN docent_receptor ELSE docent_emisor END
      ORDER BY fecha DESC, hora DESC
    ) AS rn
  FROM mensajes
  WHERE (docent_emisor  = '$doc' AND docent_receptor <> '$doc')
     OR (docent_receptor = '$doc' AND docent_emisor   <> '$doc')
) AS t
WHERE t.rn = 1
ORDER BY interlocutor_nombre
";
$profesoresEscritos = conexion_bd(SERVIDOR, USER, PASSWD, BASE_DATOS, $sql);
error_log(print_r($profesoresEscritos,true));
