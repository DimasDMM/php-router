<?php
namespace Controller;

use Core\Controller\Controller;
use Core\Manager;

class Error extends Controller {
    protected $msgHeader = array(
        '-1' => 'Ups! An unknown error has occurred',
        '400' => 'Ups! The given data is corrupted.',
        '403' => 'You have no permission to access to this zone.',
        '404' => 'Ups! We could not found the page.',
        '405' => 'Ups! Method not allowed.',
        '500' => 'Ups! There is something in our server which is working bad.',
        '503' => 'Ups! There is something in our server which is working bad.'
    );
    protected $msgDescription = array(
        '-1' => 'This is an error that we did not expected. Please, try again in a few minutes.',
        '400' => 'The given data is not valid for the requested action.',
        '403' => 'The zone where you are trying to access is protected.',
        '404' => 'The web page where you are trying to access does not exist. Is the URL correct?',
        '405' => 'You cannot access to the website in this way. What are you trying to do?',
        '500' => 'There is an error in our server, but do not worry, we are working on it.',
        '503' => 'There is an error in our server, but do not worry, we are working on it.'
    );

    public function __construct(Manager $manager)
    {
        parent::__construct($manager);
    }

    /**
     * @param array $path Optional
     * @param array $params Optional
     * @return void
     */
    public function get(array $path = array(), array $params = array())
    {
        $n = isset($path[1]) ? $path[1] : -1;

        $params['num_error'] = $n;
        $params['msg_header'] = $this->getMsgHeader($n);
        $params['msg_description'] = $this->getMsgDescription($n);

        $view = 'default/error.php';
        $this->loadPage($view, $params);
    }

    /**
     * @param string $n
     * @return string
     */
    protected function getMsgHeader($n)
    {
        if (!empty($this->msgHeader[$n]) && !empty($n)) {
            return $this->msgHeader[$n];
        }
        return $this->msgHeader['-1'];
    }

    /**
     * @param string $n
     * @return string
     */
    protected function getMsgDescription($n)
    {
        if (!empty($this->msgDescription[$n]) && !empty($n)) {
            return $this->msgDescription[$n];
        }
        return $this->msgDescription['-1'];
    }
}
