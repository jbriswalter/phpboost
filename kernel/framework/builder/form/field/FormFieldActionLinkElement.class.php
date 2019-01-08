<?php
/**
 * This class manage action links.
 *
 * @package     Builder
 * @subpackage  Form\field
 * @copyright   &copy; 2005-2019 PHPBoost
 * @license     https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL-3.0
 * @author      Loic ROUCHON <horn@phpboost.com>
 * @version     PHPBoost 5.2 - last update: 2015 12 14
 * @since       PHPBoost 3.0 - 2010 04 14
 * @contributor Julien BRISWALTER <j1.seth@phpboost.com>
*/

class FormFieldActionLinkElement
{
	private $title;
	private $url;
	private $css_class;
	private $img;

	/**
	 * build an action link
	 * @param string $title the action title
	 * @param Url $url the action url
	 * @param string $css_class the action font awesome css class
	 * @param Url $img the action icon url
	 */
	public function __construct($title, $url, $css_class = '', $img = '')
	{
		$this->title = $title;
		$this->url = $this->convert_url($url);
		$this->css_class = $css_class;
		$this->img = !empty($img) ? $this->convert_url($img) : $img;
	}

	/**
	 * @return string
	 */
	public function get_title()
	{
		return $this->title;
	}

	/**
	 * @return Url
	 */
	public function get_url()
	{
		return $this->url;
	}

	/**
	 * @return bool
	 */
	public function has_css_class()
	{
		return !empty($this->css_class);
	}

	/**
	 * @return string
	 */
	public function get_css_class()
	{
		return $this->css_class;
	}

	/**
	 * @return bool
	 */
	public function has_img()
	{
		return !empty($this->img);
	}

	/**
	 * @return Url
	 */
	public function get_img()
	{
		return $this->img;
	}

	private function convert_url($url)
	{
		return $url instanceof Url ? $url : new Url($url);
	}
}
?>
