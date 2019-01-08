<?php
/**
 * This class represents the comments topic
 * <div class="message-helper notice">Do not use this class, but one of its children like for your module</div>
 * @package     Content
 * @subpackage  Comments
 * @copyright   &copy; 2005-2019 PHPBoost
 * @license     https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL-3.0
 * @author      Kevin MASSY <reidlos@phpboost.com>
 * @version     PHPBoost 5.2 - last update: 2017 06 23
 * @since       PHPBoost 3.0 - 2012 05 02
 * @contributor Julien BRISWALTER <j1.seth@phpboost.com>
 * @contributor Arnaud GENET <elenwii@phpboost.com>
*/

class CommentsUrlBuilder
{
	 /**
	 * @param string $comment_path
	 * @param integer $id
	 * @return Url
	 */
	public static function edit($comment_path, $id)
	{
		return self::build_url($comment_path, 'edit_comment=' . $id . '#comments_message');
	}

	 /**
	 * @param string $comment_path
	 * @param integer $id
	 * @return Url
	 */
	public static function delete($comment_path, $id, $return_path = '')
	{
		return self::build_url($comment_path, 'delete_comment=' . $id . ($return_path ? '&return_path=' . $return_path : '#comments-list'));
	}

	/**
	 * @param string $comment_path
	 * @param integer $lock
	 * @return Url
	 */
	public static function lock_and_unlock($comment_path, $lock)
	{
		return self::build_url($comment_path, 'lock=' . (int)$lock . '#comments-list');
	}

	/**
	 * @param string $comment_path
	 * @param integer $id_comment
	 */
	public static function comment_added($comment_path, $id_comment)
	{
		return new Url($comment_path . '#com' . $id_comment);
	}

	private static function build_url($comment_path, $parameters)
	{
		if (TextHelper::strpos($comment_path, '?') === false)
		{
			return new Url($comment_path . '?' . $parameters);
		}
		else
		{
			return new Url($comment_path . '&' . $parameters);
		}
	}
}
?>
