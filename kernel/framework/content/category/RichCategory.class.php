<?php
/**
 * @package     Content
 * @subpackage  Category
 * @copyright   &copy; 2005-2020 PHPBoost
 * @license     https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL-3.0
 * @author      Kevin MASSY <reidlos@phpboost.com>
 * @version     PHPBoost 5.3 - last update: 2019 12 30
 * @since       PHPBoost 4.0 - 2013 01 29
 * @contributor Arnaud GENET <elenwii@phpboost.com>
 * @contributor Julien BRISWALTER <j1.seth@phpboost.com>
*/

class RichCategory extends Category
{
	public static function __static()
	{
		self::add_additional_attribute('description', array('type' => 'text', 'length' => 65000, 'attribute_field_parameters' => array(
			'field_class' => 'FormFieldRichTextEditor',
			'label' => LangLoader::get_message('form.description', 'common')
			)
		));
		self::add_additional_attribute('thumbnail', array('type' => 'string', 'length' => 255, 'notnull' => 1, 'default' => "''", 'is_url' => true, 'attribute_field_parameters' => array(
			'field_class' => 'FormFieldUploadPictureFile',
			'label' => LangLoader::get_message('form.picture', 'common'),
			'default_value' => self::get_default_thumbnail()->relative()
			)
		));
	}

	public function set_description($description)
	{
		$this->set_additional_property('description', $description);
	}

	public function get_description()
	{
		return $this->get_additional_property('description');
	}

	public function get_thumbnail()
	{
		if (!$this->get_additional_property('thumbnail') instanceof Url)
			return self::get_default_thumbnail();

		return $this->get_additional_property('thumbnail');
	}

	public function set_thumbnail(Url $thumbnail)
	{
		$this->set_additional_property('thumbnail', $thumbnail);
	}

	protected static function get_default_thumbnail()
	{
		$file = new File(PATH_TO_ROOT . '/templates/' . AppContext::get_current_user()->get_theme() . '/images/default_category_thumbnail.png');
		if ($file->exists())
			return new Url('/templates/' . AppContext::get_current_user()->get_theme() . '/images/default_category_thumbnail.png');
		else
			return new Url('/templates/default/images/default_category_thumbnail.png');
	}
}
?>
