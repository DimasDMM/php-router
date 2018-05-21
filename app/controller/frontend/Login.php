<?php
namespace Controller;

use Core\Controller\ControllerSession;
use Core\Manager;
use Table;
use Entity;
use Utils\Security;
use Utils\Tracking;

class Login extends ControllerSession
{

    protected $security;
    protected $tracking;

	public function __construct(Manager $manager)
    {
		parent::__construct($manager);
        $this->tracking = new Tracking();
        $this->security = new Security();
	}

	/**
	 * @param array $path Optional
	 * @param array $params Optional
	 * @return void
	 */
	public function get(array $path = array(), array $params = array())
    {
		$action = isset($path[1]) ? $path[1] : null;
		switch ($action) {
			case 'logout':
				$this->getLogout();
				break;
			default:
				$this->getDefault($params);
		}
	}

	/**
	 * @param array $path Optional
	 * @param array $params Optional
	 * @return void
	 */
	public function post(array $path = array(), array $params = array())
    {
		$action = isset($_POST['action']) ? $_POST['action'] : null;
		switch ($action) {
			case 'login':
				$this->postLogin();
				break;
			default:
				$this->manager->redirectHttpCode(400);
		}
	}

	/**
     * @param array $params Optional
	 * @return void
	 */
	protected function getDefault(array $params = array())
    {
		global $pagesList;

		// Check if there is a session
		$this->setRolesRequired(array('ROLE_USER'));
		$result = $this->checkSessionInit(false);
		if ($result) {
			$this->manager->redirect($pagesList['index']['url'], true);
        }

		// Otherwise, load login page
		$params['box_info'] = $this->getBoxInfo();
		$view = 'frontend/login.php';
		$this->loadPage($view, $params);
	}

	/**
	 * @return void
	 */
	protected function postLogin()
    {
		global $msgError, $pagesList;

		if (empty($this->getConnection())) {
			$this->setBoxInfo($msgError['connection'], 'error');
			$this->manager->redirect($pagesList['page_login']['url'], true);
		}

		$usernameRaw = trim(@$_POST['username']);
		$passwordRaw = trim(@$_POST['password']);

		if (empty($usernameRaw) || empty($passwordRaw)) {
			// Error: Empty username/password
			$this->setBoxInfo($msgError['user_pass_empty'], 'error');
			$this->manager->redirect($pagesList['page_login']['url'], true);
		}

		$tableUsers = new Table\Users($this->getConnection());
		$user = $tableUsers->getUserByIdOrUsername(null, $usernameRaw);

		if (!$user || !$this->security->passwordCompare($passwordRaw, $user['password'])) {
			// Save in log
			$idUser = (empty($user['id']) ? $usernameRaw : $user['id']);
			$this->insertLogin($idUser, 1, 'pass_match');

			// Error: Username/password doesn't match
			$this->setBoxInfo($msgError['user_pass_match'], 'error');
			$this->manager->redirect($pagesList['page_login']['url'], true);
		} elseif (!$user['is_active']) {
			// Save in log
			$idUser = (empty($user['id']) ? $usernameRaw : $user['id']);
			$this->insertLogin($idUser, 2, 'user_disabled');

			// Error: User disabled
			$this->setBoxInfo($msgError['user_disabled'], 'error');
			$this->manager->redirect($pagesList['page_login']['url'], true);
		}

		$this->insertLogin($user['id']);
		$this->setSession($user['id']);

		$tableUsers->updateLastOnline($user['id']);

		// If rememeber session is set
		if (isset($_POST['remember']) && $_POST['remember']) {
			$cookieHash = hash('md5', rand() . $user['id']);
			$tableSess = new Table\Sess($this->getConnection());
			$tableSess->insert($user['id'], $cookieHash);

			$tmNow = strtotime("+1 year");
			setcookie('sess', $cookieHash, $tmNow, '/', HOST);
			$_COOKIE['sess'] = $cookieHash;
		}

		$this->manager->redirect($pagesList['index']['url'], true);
	}

	/**
	 * @return void
	 */
	protected function getLogout()
    {
		global $msgOk, $pagesList;

		$this->unsetSession();

        $sessCookie = isset($_COOKIE['sess']) ? $_COOKIE['sess'] : '';

		$tableSess = new Table\Session($this->getConnection());
		$tableSess->delete($sessCookie);
		unset($_COOKIE['sess']);
		setcookie('sess', null, -1, '/', HOST);

		$this->setBoxInfo($msgOk['logout'], 'ok');
		$this->manager->redirect($pagesList['index']['url'], true);
	}

	/**
	 * @param int $idUser
	 * @param int $msgType Optional
	 * @param string $msgKey Optional
	 * @return boolean
	 */
	private function insertLogin($idUser, $msgType = 0, $msgKey = '')
    {
		$refererUrl = @$_SERVER['HTTP_REFERER'];
		$browserRaw = @$_SERVER['HTTP_USER_AGENT'];
		$ip = $_SERVER['REMOTE_ADDR'];

		$browserFix = $this->tracking->getBrowser($browserRaw);
		$osFix = $this->tracking->getOS($browserRaw);

		$tableLogLogin = new Table\LogLogin($this->getConnection());
		$result = $tableLogLogin->insert($idUser, $msgType, $msgKey, $ip, $refererUrl, $browserRaw, $browserFix['name'], $browserFix['version'], $osFix);
		return $result;
	}
}
