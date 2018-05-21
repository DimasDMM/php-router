<?php
namespace Utils;

class Dates
{
	/**
     * Returns current date in the following format: YYYY-MM-DD HH:MM:SS
     *
	 * @return string
	 */
	public static function getDatetimeNow()
    {
		return date('Y-m-d H:i:s');
	}

	/**
     * Returns current date in the following format: YYYY-MM-DD
     *
	 * @return string
	 */
	public static function getDateNow()
    {
		return date('Y-m-d');
	}

	/**
	 * @return int
	 */
	public static function getUnixNow()
    {
		return time();
	}

	/**
     * Switch the order of the numbers in a date.
     * Example: "2016-05-30" -> "30-05-2016"
     *
	 * @param string $date
	 * @param string $separator Opcional
	 * @return string|null
	 */
	public static function changeOrder($date, $separator = '-')
    {
		$separator = addslashes($separator);
		preg_match('/(\d+)' . $separator . '(\d+)' . $separator . '(\d+)/', $date, $m);

		if (empty($m[1])) {
            return null;
        }

		return $m[3] . $separator . $m[2] . $separator . $m[1];
	}

	/**
     * Returns name of the month (in spanish)
     *
	 * @param int $nMonth Number of the month (starting in 1)
	 * @return string
	 */
	public static function getMonth($nMonth)
    {
        $months = array(
            'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio',
            'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'
        );

		return isset($months[$nMonth - 1]) ? $months[$nMonth - 1] : '';
	}

	/**
     * @param int $nMonth Number of the month (starting in 1)
	 * @param int $long Optional Max length of the name
	 * @return string
	 */
	public static function getMonthShort($nMonth, $long = 3)
    {
		$name = self::getMonth($nMonth);
		if (empty($name)) {
            return $name;
        }

		$name = mb_substr($name, 0, $long);
		return $name;
	}

	/**
     * Transform a date into a string that a user can read (in spanish)
     * In the result, it's only displayed the date
     *
	 * @param string $date Format: YYYY-MM-DD or YYYY-MM-DD HH:MM:SS
	 * @param boolean $monthShort Optional
	 * @return string|null
	 */
	public static function dateToString($date, $monthShort = false)
    {
		if (empty($date)) {
			return null;
        }

		$date = mb_substr($date, 0, 10);
		$date = explode('-', $date);

        $month = $monthShort ? self::getMonthShort($date[1]) : self::getMonth($date[1]);

		$str = ($date[2] + 0) . ', ';
		$str .= $month . ', ';
		$str .= $date[0];
		return $str;
	}

	/**
     * Transform a date into a string that a user can read (in spanish)
     * In the result, it's only displayed the date and the time
     *
	 * @param string $datetime Formato: YYYY-MM-DD HH:MM:SS
	 * @return string
	 */
	public static function datetimeToString($datetime)
    {
		$datetime = explode(' ', $datetime);
		$date = $datetime[0];
		$time = $datetime[1];

		$date = explode('-', $date);
		$time = explode(':', $time);

		return $date[2] . '-' . $date[1] . '-' . $date[0] . ' a las ' .  $time[0] . ':' . $time[1] . 'h';
	}

	/**
	 * Check if a date is greater than the current day
     *
	 * @param string $date Format: YYYY-MM-DD
	 * @return boolean
	 */
	public static function isDateGreaterThanNow($date)
    {
		$now = strtotime(self::getDateNow());
		$date = strtotime($date);
		return $now < $date;
	}
}
