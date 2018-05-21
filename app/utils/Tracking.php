<?php
namespace Utils;

class Tracking {
	public function getBrowser($browserRaw)
    {
		$browser = null;
		$version = null;

		if (preg_match('/Googlebot/i', $browserRaw)) {
            // Google Bot
			$browser = 'Google Bot';
		} elseif (preg_match('/Yahoo/i', $browserRaw)) {
            // Yahoo Bot
			$browser = 'Yahoo Bot';
		} elseif (preg_match('/bingbot/i', $browserRaw)) {
            // Bing Bot
			$browser = 'Bing Bot';
		} elseif (preg_match('/NintendoBrowser(?:\/|\s)([\d\.]+\d)/i', $browserRaw, $match)) {
            // Nintendo browser
			$browser = 'Nintendo Browser';
		} elseif (preg_match('/Edge(?:\/|\s)([\d\.]+)/i', $browserRaw, $match)) {
            // Edge Bot
			$browser = 'Edge';
		} elseif (
			preg_match('/MSIE(?:\/|\s)([\d\.]+)/i',$browserRaw, $match) &&
			!preg_match('/Opera/i', $browserRaw) &&
			!preg_match('/IEMobile/i', $browserRaw)
		) {
            // Internet Explorer para PC
			$browser = 'Internet Explorer';
		} elseif (
			preg_match('/MSIE/i',$browserRaw) &&
			preg_match('/IEMobile(?:\/|\s)([\d\.]+)/i', $browserRaw, $match) ||
			preg_match('/Trident\/[\d]+\.[\d]+/i', $browserRaw, $match)
		) {
            // Internet Explorer para movil
			$browser = 'Internet Explorer';
		} elseif (preg_match('/Firefox(?:\/|\s)([\d\.]+)/i', $browserRaw, $match)) {
            // Firefox
			$browser = 'Firefox';
		} elseif (preg_match('/FxiOS(?:\/|\s)([\d\.]+)/i', $browserRaw, $match)) {
            // Firefox para iOS
			$browser = 'Firefox';
		} elseif (
			preg_match('/Chrome(?:\/|\s)([\d\.]+)/i', $browserRaw, $match) &&
            !preg_match('/Version\/[\d\.]+/i', $browserRaw) ||
			preg_match('/CriOS(?:\/|\s)([\d\.]+)/i', $browserRaw, $match)
		) {
            // Google Chrome para dispositivos no moviles
			$browser = 'Chrome';
		} elseif (
			(
				preg_match('/Safari/i', $browserRaw) && preg_match('/Version(?:\/|\s)([\d\.]+)/i', $browserRaw, $match) ||
				preg_match('/Safari(?:\/|\s)([\d\.]+)/i', $browserRaw, $match)
			) && (
				!preg_match('/Mobile/i', $browserRaw) ||
				preg_match('/iphone|ipad/i', $browserRaw)
			) &&
			!preg_match('/Chrome|CriOS/i', $browserRaw)
		) {
            // Safari para dispositivos no Android
			$browser = 'Safari';
		} elseif (preg_match('/Opera(?:\/|\s)([\d\.]+)/i', $browserRaw, $match)) {
            // Opera
			$browser = 'Opera';
		} elseif (
			preg_match('/Chrome(?:\/|\s)([\d\.]+) Mobile/i', $browserRaw) && preg_match('/Version\/[\d\.]+/i', $browserRaw) ||
			preg_match('/Mobile/i', $browserRaw, $match)
		) {
            // Visor web integrado movil
			$browser = 'Web Mobile';
		}

		if (!empty($match[1])) {
			$version = $match[1];
        }

		$result = array(
			'name' => $browser,
			'version' => $version
		);
		return $result;
	}

	public function getOS($browserRaw)
    {
		$os	= null;

		// Regular expressions and their names
		// IMPORTANT: The order of this regular expressions is important
		$osArray = array(
			'/playstation 4/i'       =>	'PlayStation 4',
			'/playstation/i'         =>	'PlayStation',
			'/xbox one/i'            =>	'Xbox One',
			'/xbox/i'                =>	'Xbox',
			'/nintendo wiiu/i'       =>	'Nintendo Wii U',
			'/nintendo/i'            =>	'Nintendo',
			'/windows nt 10/i'		 =>	'Windows 10',
			'/windows nt 6.3/i'		 =>	'Windows 8.1',
			'/windows nt 6.2/i'		 =>	'Windows 8',
			'/windows nt 6.1/i'		 =>	'Windows 7',
			'/windows nt 6.0/i'		 =>	'Windows Vista',
			'/windows nt 5.2/i'		 =>	'Windows Server 2003/XP x64',
			'/windows nt 5.1/i'		 =>	'Windows XP',
			'/windows xp/i'			 =>	'Windows XP',
			'/windows nt 5.0/i'		 =>	'Windows 2000',
			'/windows me/i'			 =>	'Windows ME',
			'/windows ce/i'			 =>	'Windows CE',
			'/win98/i'				 =>	'Windows 98',
			'/win95/i'				 =>	'Windows 95',
			'/win16/i'				 =>	'Windows 3.11',
			'/windows phone os ([\d\.]+)/i' =>	'Windows Phone',
			'/windows phone ([\d\.]+)/i' =>	'Windows Phone',
			'/windows|win32/i'		 =>	'Windows',
			'/android ([\d\.]+)/i'	 =>	'Android',
			'/android/i'			 =>	'Android',
			'/blackberry/i'			 =>	'BlackBerry',
			'/webos/i'				 =>	'Mobile',
			'/ubuntu\/([\d\.]+)/i'   =>	'Linux Ubuntu',
			'/ubuntu/i'				 =>	'Linux Ubuntu',
			'/linux/i'				 =>	'Linux',
			'/iphone os ([\d_\.]+)/i' => 'iOS',
			'/iphone/i'		 		 =>	'iOS',
			'/ipod/i'		 		 =>	'iPod',
			'/ipad.+os ([\d_\.]+)/i' =>	'iOS',
			'/ipad/i'				 =>	'iOS',
			'/mac os x ([\d_\.]+)/i' => 'Mac OS X',
			'/macintosh|mac os x/i'  =>	'Mac OS X',
			'/mac_powerpc/i'		 =>	'Mac OS 9'
		);

		foreach ($osArray as $regex => $value) {
			if (preg_match($regex, $browserRaw, $match)) {
				$os = $value;
				if (!empty($match[1])) {
					$os = $value .' '. str_replace('_', '.', $match[1]);
                }
				return $os;
			}
		}

		return $os;
	}
}
