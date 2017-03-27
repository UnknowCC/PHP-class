<?php 


/**
* some regular expressions
*/
class Regular
{
	/**
	 * email address check
	 * @param  string  $email 
	 * @return boolean        
	 */
	public static function isValidEmailAddress(string $email) : bool
	{
		static $re = '';

		if ($re === '') {
			$atext = "[-!#-'*+/0-9=?A-Z^-~]{1,}";
			$atom = $atext.'+';
			$dot_atom = $atom."(\\.$atom)*";
			$re = ":^".$dot_atom.'@'.$dot_atom."\$:";
		}
		return preg_match($re, $email) === 1;
	}

	/**
	 * China mobile phone check
	 * @param  string  $phone 
	 * @return boolean        
	 */
	public static function isValidChinaMobile($phone) : bool
	{
		static $re = '';
		if ($re === '') {
			$r = array();
			$r['13'] = '13\d{2}';
			$r['15'] = '15[0-35-9]\d';
			$r['17'] = '17[13678]\d';
			$r['170'] = '170[^346]';
			$r['18'] = '18\d{2}';
			$rs = implode('|', $r);
			$re = ':^('.$rs.')\d{7}$:';
		}

		return preg_match($re, $phone) === 1;
	}

	public static function echo() : bool
	{
		return true;
	}
}

