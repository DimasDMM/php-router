<?php
namespace Utils;

class Security {
	/**
     * Given a plain text password, it verifies the minimum requirements of
     * security
     *
	 * @param string $passwordRaw
	 * @return boolean
	 */
	public function passwordValid($passwordRaw)
    {
		$passwordRaw = trim($passwordRaw);

		if (
			empty($passwordRaw) ||
			mb_strlen($passwordRaw) < MIN_PASSWORD_LENGTH ||
			mb_strlen($passwordRaw) > MAX_PASSWORD_LENGTH
		) {
			return false;
		}

		return true;
	}

	/**
	 * Encypte a password using BCRYPT
     *
	 * @param string $password
	 * @param int $cost Optional
	 * @return false|string
	 */
	public function passwordHash($password, $cost = 12)
    {
		$options = array(
			'cost' => $cost
		);
		return password_hash($password, PASSWORD_BCRYPT, $options);
	}

	/**
	 * Compares a plain text password with its hash
     *
	 * @param string $passwordToCompare Plain text password
	 * @param string $passwordHash Hashed password
	 * @return boolean
	 */
	public function passwordCompare($passwordToCompare, $passwordHash)
    {
		return password_verify($passwordToCompare, $passwordHash);
	}

	/**
	 * Generate a random password
     *
	 * @param int $length Optional Size of the password
	 * @return string
	 */
	public function passwordGenerate($length = MIN_PASSWORD_LENGTH) {
		$chars = 'abcdefghijklmnopqrstuvwxyz0123456789';
	    $count = mb_strlen($chars);
		$result = '';

	    for ($i = 0; $i < $length; $i++) {
	        $index = rand(0, $count - 1);
	        $result .= mb_substr($chars, $index, 1);
	    }

		return $result;
	}
}
