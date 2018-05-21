<?php
namespace Core\Controller;

use Core\Manager;
use Core\Controller;
use Table;

class ControllerSession extends ControllerDB
{
	private $roles = array();
	private $user = null;

	private $urlRedirect = '';
	private $urlRedirectSession = '';
	private $urlRedirectRole = '';

	private $httpCodeRedirect = 404;

	public function __construct(Manager $manager, $needConnection = false)
    {
		parent::__construct($manager, $needConnection);
	}

	/**
     * If there is a session, load the user data into $user and returns it
     * Otherwise, returns null
     *
	 * @return array|null
	 */
	protected function loadUserSession()
    {
        if (empty($this->getConnection())) {
            return null;
        }

		$tableUsers = new Table\Users($this->getConnection());
		$tableSession = new Table\Session($this->getConnection());
		$this->user = null;

		if (empty($this->getSession())) {
			if (!empty($_COOKIE['sess'])) {
				$cookieSess = $tableSession->get($_COOKIE['sess']);
				$result = $tableUsers->getUserByIdOrUsername($cookieSess['id_user']);

				if (!empty($result) && $user->isActive) {
					$this->user = $user;
				} else {
					$tableSession->delete($_COOKIE['sess']);
					unset($_COOKIE['sess']);
					setcookie('sess', null, -1, '/', HOST, null, true);
				}
			}

		} else {
			$result = $tableUsers->getUserByIdOrUsername($this->getSession());
			if (!empty($result)) {
				$this->user = $result;
            }
		}

        // Update due date of cookie
		if (!empty($_COOKIE['sess'])) {
			$tableSession->update($_COOKIE['sess']);
			$tmNow = strtotime("+1 year");
			setcookie('sess', $_COOKIE['sess'], $tmNow, '/', HOST);
		}

        // Update last online date
		if (!empty($this->user)) {
			$tableUsers->updateLastOnline($this->user['id']);
		}

		return $this->user;
	}

	/**
	 * @return array
	 */
	protected function getUser()
    {
		return $this->user;
	}

	/**
     * Set required role to access to this zone
     *
	 * @param array $roles
	 * @return void
	 */
	public function setRolesRequired(array $roles)
    {
		$this->roles = $roles;
	}

	/**
     * Set the redirection page in case of generic error
     *
	 * @param string $url
	 * @return void
	 */
	public function setUrlRedirect($url)
    {
		$this->urlRedirect = $url;
	}

	/**
     * Set the redirection page in case of error of session
     *
	 * @param string $url
	 * @return void
	 */
	public function setUrlRedirectSession($url)
    {
		$this->urlRedirectSession = $url;
	}

	/**
     * Set the redirection page in case of error of role
     *
	 * @param string $url
	 * @return void
	 */
	public function setUrlRedirectRole($url)
    {
		$this->urlRedirectRole = $url;
	}

	/**
     * If no redirect page has been set, the user will be redirected to the
     * generic error page given an HTTP Code
     *
	 * @param int $code
	 * @return void
	 */
	public function setHttphttpCodeRedirect($code)
    {
		$this->httpCodeRedirect = $code;
	}

	/**
     * Check if there is a session and validates the roles. Also, it fills the
     * $user variable with the data of the user
	 *
	 * IMPORTANT 1:
     * Use setUrlRedirect() and setRolesRequired() before this method (if we want)
	 *
	 * IMPORTANT 2:
     * If there isn't any redirect URL, the box-info variable won't be set
	 *
	 * @param boolean $redirectOnFail Opcional Redirect to the login page if
     *                                session not init
	 * @param boolean $setBoxInfo Opcional Fill box-info with the error info
	 * @return boolean
	 */
	protected function checkSessionInit($redirectOnFail = true, $setBoxInfo = true)
    {
		global $msgError;

		if (empty($this->getConnection())) {
			if (!$redirectOnFail) {
                return false;
            }
			if ($setBoxInfo) {
                $this->setBoxInfo('151', $msgError['connection'], 'error');
            }
			$this->redirect();
		}

		// Comprobar sesion
		$user = $this->loadUserSession();
		if (empty($user) || !$this->checkSessionUserNull($user)) {
            return false;
        }

		// Check if user active
		if (empty($user) || !$this->checkSessionActiveUser($user)) {
            return false;
        }

		// Validate roles
		if (empty($user) || !$this->checkSessionUserRoles($user)) {
            return false;
        }

        return true;
	}

	/**
	 * @param mixed $value
	 * @param string $name Optional Name of the session. By default: sess_id
	 * @return void
	 */
	protected static function setSession($value, $name = 'sess_id')
    {
		$_SESSION[$name] = $value;
	}

	/**
	 * @return string
	 */
	protected static function getSession($name = 'sess_id')
    {
		return empty($_SESSION[$name]) ? '' : $_SESSION[$name];
	}

	/**
	 * @return void
	 */
	protected static function unsetSession($name = 'sess_id')
    {
		$_SESSION[$name] = '';
		unset($_SESSION[$name]);
	}

	/**
     * Redirect to the $this->urlRedirect page. If not set, redirect to the
     * generic error page
     *
	 * @return void
	 */
	protected function redirect()
    {
		if (empty($this->urlRedirect)) {
			$this->manager->redirectHttpCode($this->httpCodeRedirect);
		} else {
			$this->manager->redirect($this->urlRedirect);
		}
	}

    /**
     * @param array $user
     * @return boolean
     */
    private function checkSessionUserNull(array $user)
    {
        if ($user == null) {
			if (!$redirectOnFail) {
                return false;
            }

			if ($setBoxInfo) {
                $this->setBoxInfo($msgError['not_session'], 'error');
            }
			$this->urlRedirect = empty($this->urlRedirectSession)
                ? $this->urlRedirect
                : $this->urlRedirectSession;

			$this->redirect();
		}
        return true;
    }

    /**
     * @param array $user
     * @return boolean
     */
    private function checkSessionActiveUser(array $user)
    {
        if (!$user['isActive']) {
			self::unsetSession();
			if (!$redirectOnFail) {
                return false;
            }
			if ($setBoxInfo) {
                $this->setBoxInfo($msgError['user_disabled'], 'error');
            }

			$this->urlRedirect = empty($this->urlRedirectRole)
                ? $this->urlRedirect
                : $this->urlRedirectRole;

			$this->redirect();
		}
        return true;
    }

    /**
     * @param array $user
     * @return boolean
     */
    private function checkSessionUserRoles(array $user)
    {
        if (empty($this->roles) || in_array('ROLE_SUPERADMIN', $user['roles'])) {
            return true;
        }

		foreach ($this->roles as $role) {
			if (in_array($role, $user['roles'])) {
				return true;
            }
        }

		self::unsetSession();

		if (!$redirectOnFail) {
            return false;
        }
		if ($setBoxInfo) {
            $this->setBoxInfo($msgError['not_role_page'], 'error');
        }

		$this->urlRedirect = empty($this->urlRedirectRole)
            ? $this->urlRedirect
            : $this->urlRedirectRole;
		$this->redirect();
    }
}
