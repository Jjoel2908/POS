<?php

/** 
 * @var string DIRECTORY_SEPARATOR 
 * Este define una constante que representa el separador de directorio del sistema operativo actual.
 */
define('DS', DIRECTORY_SEPARATOR);

/** 
 * @var string Ubicación de la raíz del sistema
 * Este define crea una constante llamada PATH_ROOT que representa la ubicación de la raíz del sistema.
 * Utiliza la función dirname() para obtener el directorio padre del archivo actual (__FILE__) y luego concatena el separador de directorio del sistema (DS).
 */
define('PATH_ROOT', dirname(__FILE__) . DS);

/** 
 * @var string Ubicación de la carpeta PHP
 */
define('PATH_PHP', PATH_ROOT . 'assets' . DS . 'php' . DS);


/** 
 * @var string Ubicación de la carpeta 'P U B L I C'
 */
define('PATH_PUBLIC', PATH_ROOT . 'public' . DS);

/**
 * Página Login
 * Esta constante define el nombre de la página de inicio de sesión de la aplicación.
 */
define("LOGIN_NAME", "Inicio de Sesión");

?>