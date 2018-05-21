<?php
namespace Controller;

use Core\Controller\ControllerSession;
use Core\Manager;

class Home extends ControllerSession {
	public function __construct(Manager $manager)
    {
		parent::__construct($manager, false);
		$this->loadUserSession();
	}

	/**
	 * @param array $path Optional
	 * @param array $params Optional
	 * @return void
	 */
	public function get(array $path = array(), array $params = array())
    {
		$view = 'frontend/home.php';
        $params['user'] = $this->getUser();
		$this->loadPage($view, $params);
	}
}
