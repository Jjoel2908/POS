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
define("DB_NAME", "ventas");

/** 
 * Nombre del usuario de la base de datos
 * Esta constante define el nombre de usuario utilizado para acceder a la base de datos.
 */
define("DB_USERNAME", "joy");

/** 
 * Contraseña de la base de datos
 * Esta constante define la contraseña utilizada para acceder a la base de datos.
 */
define("DB_PASSWORD", "#J29#o08");

/** 
 * Codificación de carácteres
 * Esta constante define la codificación de caracteres que se utilizará para la conexión a la base de datos.
 */
define("DB_ENCODE", "utf8");

/** 
 * Nombre del Proyecto
 * Esta constante define el nombre del proyecto o aplicación.
 */
define("WEB_NAME", "GASTALON JR.");


/** 
 * Información para los encabezados de los PDF generados 
 */
define("PDF_HEADER_IMAGE", "../public/images/gastalon.png");
define("PDF_HEADER_COMPANY", "Gastalon JR.");
define("PDF_HEADER_LOCATION", "Calle 12 Poniente Reforma #980");
define("PDF_HEADER_PHONE", "244 123 4567");


/** 
 * Clave secreta para generar tokens
 * Esta constante define una clave secreta utilizada para generar tokens de seguridad.
 */
define('SECRET_KEY', 'QxT7@w!rP$3vNz9Lc^FbYzA6e%K0hLmJ#8Xz*oD!cPqR1tVuW$5gNsMjZ@bCfE2');

/** Permisos */
define('PERMISSION_REPORT_PURCHASE', 32);
define('PERMISSION_REPORT_SALE', 33);

?>