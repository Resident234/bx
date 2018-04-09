<?
function customAutoload($className) {
    if (!CModule::RequireAutoloadClass($className)) {

        $path = $_SERVER["DOCUMENT_ROOT"]."/local/php_interface/classes/" .
            str_replace('\\', DIRECTORY_SEPARATOR, $className) . '.php';

        if (file_exists($path)) {
            require_once $path;
            return true;
        }
        return false;
    }
    return true;
}

spl_autoload_register('customAutoload', false);

require_once($_SERVER['DOCUMENT_ROOT'] . '/local/php_interface/include/handlers.php');

?>