<?php 

/** 
 * IP del servidor de la base de datos
 * Esta constante define la dirección IP del servidor de la base de datos.
 */ 
define("DB_HOST", 'localhost');

/** 
 * Nombre de la base de datos
 * Esta constante define el nombre de la base de datos a la que se conectará la aplicación.
 */
define("DB_NAME", "pos");

/** 
 * Nombre del usuario de la base de datos
 * Esta constante define el nombre de usuario utilizado para acceder a la base de datos.
 */
define("DB_USERNAME", "root");

/** 
 * Contraseña de la base de datos
 * Esta constante define la contraseña utilizada para acceder a la base de datos.
 */
define("DB_PASSWORD", "");

/** 
 * Codificación de carácteres
 * Esta constante define la codificación de caracteres que se utilizará para la conexión a la base de datos.
 */
define("DB_ENCODE", "utf8");

/** 
 * Nombre del Proyecto
 * Esta constante define el nombre del proyecto o aplicación.
 */
define("WEB_NAME", "POS");


/** 
 * Información para los encabezados de los PDF generados 
 */
define("PDF_HEADER_IMAGE", "../public/images/logo.jpg");
define("PDF_HEADER_COMPANY", "SIRI CONSULTING");
define("PDF_HEADER_LOCATION", "Calle 12 Poniente #980");
define("PDF_HEADER_PHONE", "+129839324234");

?>