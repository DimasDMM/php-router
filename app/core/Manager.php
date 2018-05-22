<?php
namespace Core;

use Utils\Connection;
use Core\Controller\ControllerBase;
use Controller;

final class Manager
{
    /** @var Connection */
    protected $connection;

    /**
     * @param Connection $connection
     * @return void
     */
    public function setConnection(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * @return Connection|null
     */
    public function getConnection()
    {
        return $this->connection;
    }

    /**
     * @param string $urlRaw Opcional Format: param1/param2/.../paramN?key1=value1&key2=...
     * @param array $paramsSpecific Optional Parameters to give to the controller
     * @return void
     */
    public function loadPage($urlRaw = '', array $paramsSpecific = array())
    {
        global $pagesList;

        // Parse input parameters
        $path = $this->parsePath($urlRaw);
        $params = $this->parseParams($urlRaw);
        $params = $this->defaultParams($params);

        // Get controller or redirect to 404
        $idPage = $this->getIdPage($path);
        if (empty($idPage)) {
            if(!empty($path[0])) {
                $this->redirectHttpCode(404);
            } else {
                $idPage = 'index';
            }
        }

        // Check that the contoller file exists
        $controllerPath = PATH_CONTROLLER . $pagesList[$idPage]['controller'];
        if (!file_exists($controllerPath) || empty($pagesList[$idPage]['controller'])) {
            $this->redirectHttpCode(500);
        }

        // Get classname of controller and load it
        $controllerClassName = $this->getClassName($controllerPath);

        require($controllerPath);
        /** @var $controller ControllerBase */
        $controller = new $controllerClassName($this);
        $controller->setHashPage($this->getHashPage($idPage));
        $controller->setHashFull($this->getHashFull($path));

        // Merge default params with additional ones
        $params = array_merge($params, $paramsSpecific);

        // Handle request
        switch($_SERVER['REQUEST_METHOD']) {
            case 'POST':
                $controller->post($path, $params);
                break;
            case 'GET':
                $controller->get($path, $params);
                break;
            case 'PUT':
                $controller->put($path, $params);
                break;
            case 'DELETE':
                $controller->delete($path, $params);
                break;
            default:
                $this->redirectHttpCode(405);
        }
    }

    /**
     * Get the identifier of the web page
     * Check pages.php file for further details about this method
     *
     * @param array $path
     * @return string|false
     */
    protected function getIdPage(array $path)
    {
        $i = 0;
        $urlPage = '';

        for(; $i < count($path); $i++) {
            if ($i > 0) {
                $urlPage .= '/';
            }
            $urlPage .= $path[$i];
        }

        $idPage = $this->getIdPageAux($urlPage);

        if (empty($idPage) && $i > 0) {
            array_pop($path);
            $idPage = $this->getIdPage($path);
        }

        return $idPage;
    }

    /**
     * @param string $urlPage
     * @return string|false
     */
    private function getIdPageAux($urlPage)
    {
        global $pagesList;

        foreach ($pagesList as $pageId => $pageConf) {
            if (
                $pageId != 'index' &&
                $pageConf['url'] == $urlPage &&
                !empty($pageConf['controller'])
            ) {
                return $pageId;
            }
        }

        return false;
    }

    /**
     * Returns the name of the class given the name of the file
     * Example: "MyClass.php" -> "Controller\MyClass"
     *
     * @param $file
     * @return string
     */
    protected function getClassName($file)
    {
        preg_match('/^(?:.*\/)*(.+)\.php$/i', $file, $m);
        $name = ucfirst($m[1]);
        $name = 'Controller\\' . $name;
        return $name;
    }

    /**
     * Returns a 8-characters hash of the current web page
     *
     * @param string $idPage
     * @return string|null
     */
    public function getHashPage($idPage)
    {
        global $pagesList;
        return isset($pagesList[$idPage]['tracking'])
            ? $pagesList[$idPage]['tracking']
            : null;
    }

    /**
     * Returns a 8-characters hash of the full URL
     *
     * @param array $path
     * @return string|null
     */
    public function getHashFull(array $path)
    {
        $url = URL;
        foreach ($path as $param) {
            if (empty($param)) {
                continue;
            }
            $url .= $param.'/';
        }
        return hash('crc32', $url);
    }

    /**
     * @param string $urlRaw
     * @return array
     */
    protected function parsePath($urlRaw)
    {
        $path = explode('/', $urlRaw);

        // Remove first position if empty
        if (empty($path[0])) {
            array_shift($path);
        }

        // Remove params from last position
        $lastPos = count($path) - 1;
        $path[$lastPos] = preg_replace('/\?.*/', '', $path[$lastPos]);

        if (LOCALHOST) {
            $localPath = explode('/', LOCAL_SUBDIR);
            $tmpPath = $path;
            for ($i = 0; $i < count($localPath); $i++) {
                if ($localPath[$i] == $tmpPath[$i]) {
                    unset($tmpPath[$i]);
                } else {
                    return $path;
                }
            }
            $path = array_values($tmpPath);
        }

        return $path;
    }

    /**
     * @param string $urlRaw
     * @return array
     */
    protected function parseParams($urlRaw)
    {
        // Remove path
        $paramsRaw = preg_replace('/.*\?/', '', $urlRaw);

        if (empty($paramsRaw)) {
            return array();
        }

        $paramsRaw = explode('&', $paramsRaw);

        $params = array();
        foreach ($paramsRaw as $param) {
            $tmp = explode('=', $param);
            $params[$tmp[0]] = @$tmp[1];
        }

        return $params;
    }

    /**
     * Redirect to another URL
     *
     * @param string $url
     * @param boolean $local
     * @return void
     */
    public function redirect($url, $local = false)
    {
        if ($local) {
            $subDir = LOCALHOST ? '/' . LOCAL_SUBDIR . '/' : URL;
            $url = $subDir . $url;
        }
        header('Location: ' . $url);
        die();
    }

    /**
     * Redirect to error page depending on the HTTP Code
     *
     * @param int $httpCode
     * @return void
     */
    public function redirectHttpCode($httpCode)
    {
        $subDir = LOCALHOST ? '/' . LOCAL_SUBDIR : '';
        $url = $subDir . '/error/' . $httpCode;

        http_response_code($httpCode);
        echo '<html><head>' .
             '<meta http-equiv="refresh" content="0;URL=' . $url . '">' .
             '</head><body></body></html>';
        die();
    }

    /**
     * @param array $params
     * @return array
     */
    private function defaultParams(array $params = array())
    {
        $params['url'] = LOCALHOST ? '/' . LOCAL_SUBDIR : URL;
        return $params;
    }
}
