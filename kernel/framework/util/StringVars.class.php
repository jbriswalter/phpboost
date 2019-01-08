<?php
/**
 * Implements the string var replacement method
 * @package     Util
 * @copyright   &copy; 2005-2019 PHPBoost
 * @license     https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL-3.0
 * @author      Loic ROUCHON <horn@phpboost.com>
 * @version     PHPBoost 5.2 - last update: 2016 11 14
 * @since       PHPBoost 3.0 - 2009 10 15
 * @contributor mipel <mipel@phpboost.com>
*/

class StringVars
{
	private $parameters;
	private $strict;

    public static function replace_vars($string, array $parameters, $strict = false)
    {
    	if (empty($parameters))
    	{
    		return $string;
    	}
    	$string_var = new StringVars($strict);
    	return $string_var->replace($string, $parameters);
    }

    public function __construct($strict = false)
    {
    	$this->strict = $strict;
    }

    public function replace($string, array $parameters)
    {
    	$this->parameters = $parameters;
        return preg_replace_callback('`:([a-z][\w_]+)`iu', array($this, 'replace_var'), $string);
    }

    private function replace_var($captures)
    {
        $varname =& $captures[1];
        if (array_key_exists($varname, $this->parameters))
        {
            return $this->set_var($this->parameters[$varname]);
        }
        if ($this->strict)
        {
        	throw new RemainingStringVarException($varname);
        }
        else
        {
        	return ':' . $varname;
        }
    }

    protected function set_var($parameter)
    {
        return $parameter;
    }
}
?>
