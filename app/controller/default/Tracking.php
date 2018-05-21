<?php
namespace Controller;

use Core\Controller\ControllerSession;
use Core\Manager;
use Table\LogTracking;

class Tracking extends ControllerSession
{
    protected $tracking;

	public function __construct(Manager $manager)
    {
		parent::__construct($manager);
		$this->loadUserSession();
        $this->tracking = new \Utils\Tracking();
	}

	/**
	 * @param array $path Optional
	 * @param array $params Optional
	 * @return void
	 */
	public function get(array $path = array(), array $params = array())
    {
		$this->saveTracking($path);
		$this->loadPixel();
	}

	/**
	 * @param array $path Opcional
	 * @return bool
	 */
	public function saveTracking(array $path = array())
    {
		$hashPage = isset($path[1]) ? $path[1] : null;
		$hashFull = isset($path[2]) ? $path[2] : null;
		$hashVisit = isset($path[3]) ? $path[3] : null;
		$refererUrl = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';
		$browserRaw = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
		$ip = $_SERVER['REMOTE_ADDR'];

        $user = $this->getUser();
		$idUser = empty($user) ? null : $user['id'];

		$browserFix = $this->tracking->getBrowser($browserRaw);
		$osFix = $this->tracking->getOS($browserRaw);

		$tableLogTracking = new LogTracking($this->getConnection());
		$result = $tableLogTracking->insert(
            $idUser,
            $hashVisit,
            $ip,
            $refererUrl,
            $hashPage,
            $hashFull,
            $browserRaw,
            $browserFix['name'],
            $browserFix['version'],
            $osFix
        );

		return $result;
	}

	/**
	 * @return void
	 */
	public static function loadPixel()
    {
		$img = imagecreatetruecolor(1,1);
		imagecolortransparent($img, imagecolorallocate($img, 0, 0, 0));
		header('Content-type: image/png');
		imagepng($img);
		imagedestroy($img);
	}
}
