<?php
namespace Core\Controller;

use Utils\Connection;
use Core\Manager;

class Controller
{
    /** @var Manager */
    protected $manager;

    /** @var Connection|null */
    protected $connection;

    protected $hashPage = '';
    protected $hashFull = '';

    public function __construct(Manager $manager)
    {
        $this->manager = $manager;
    }

    /**
     * @param array $path Optional
     * @param array $params Optional
     * @return void
     */
    public function get(array $path = array(), array $params = array())
    {
        $this->getManager()->redirectHttpCode(405);
    }

    /**
     * @param array $path Optional
     * @param array $params Optional
     * @return void
     */
    public function post(array $path = array(), array $params = array())
    {
        $this->getManager()->redirectHttpCode(405);
    }

    /**
     * @param array $path Optional
     * @param array $params Optional
     * @return void
     */
    public function put(array $path = array(), array $params = array())
    {
        $this->getManager()->redirectHttpCode(405);
    }

    /**
     * @param array $path Optional
     * @param array $params Optional
     * @return void
     */
    public function delete(array $path = array(), array $params = array())
    {
        $this->getManager()->redirectHttpCode(405);
    }

    /**
     * @param string $view Path to the file of the view (without the content of PATH_VIEW)
     * @param array $params Optional Params to load the view
     * @return void
     */
    public function loadPage($view, array $params = array())
    {
        global $pagesList;

        $params['tracking'] = $this->getTrackingHtml();

        include(PATH_VIEW . $view);
        die();
    }

    /**
     * @return Manager
     */
    public function getManager()
    {
        return $this->manager;
    }

    /**
     * @return Connection|null
     */
    public function getConnection()
    {
        return $this->manager->getConnection();
    }

    /**
     * @param string $msg
     * @param string $type Values: error, success, info, warning
     * @return void
     */
    public function setBoxInfo($msg, $type)
    {
        $_SESSION['msg'] = $msg;
        $_SESSION['type'] = $type;
        $_SESSION['time'] = date('YmdHis');
    }

    /**
     * @param boolean $clear Optional
     * @return array
     */
    public function getBoxInfo($clear = true)
    {
        $msg = empty($_SESSION['msg']) ? '' : $_SESSION['msg'];
        $type = empty($_SESSION['type']) ? '' : $_SESSION['type'];
        $time = empty($_SESSION['time']) ? '' : $_SESSION['time'];

        if ($clear) {
            $this->clearLastError();
        }
        if ($time < date('YmdHis') - 100) {
            // Don't return it if it's a very old message
            return array();
        }

        return array(
            'msg' => $msg,
            'type' => $type,
            'time' => $time
        );
    }

    /**
     * @return void
     */
    public function clearLastError()
    {
        $_SESSION['msg'] = '';
        $_SESSION['type'] = '';
        $_SESSION['time'] = '';
    }

    /**
     * If we'll track the page (need to have a tracking id in conf/pages.php),
     * then this method returns an HTML to insert at the very beginning of the body
     *
     * @return string
     */
    public function getTrackingHtml()
    {
        if (empty($this->hashPage)) {
            return '';
        }

        $subUrl = LOCALHOST ? '/' . LOCAL_SUBDIR . '/' : URL;

        $hashVisit = hash('crc32', rand(0,1000));
        $html = '<img src="'. $subUrl .'tracker/'. $this->hashPage .'/'. $this->hashFull .'/'. $hashVisit .'" class="hide" />';
        return $html;
    }

    /**
     * @param string $hashPage
     * @return void
     */
    public function setHashPage($hashPage)
    {
        $this->hashPage = $hashPage;
    }

    /**
     * @param string $hashFull
     * @return void
     */
    public function setHashFull($hashFull)
    {
        $this->hashFull = $hashFull;
    }
}
