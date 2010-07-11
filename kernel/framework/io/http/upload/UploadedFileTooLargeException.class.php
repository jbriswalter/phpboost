<?php
/*##################################################
 *                    UploadedFileTooLargeException.class.php
 *                            -------------------
 *   begin                : January 24 2010
 *   copyright            : (C) 2010 Benoit Sautel
 *   email                : ben.popeye@phpboost.com
 *
 *
 ###################################################
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 *
 ###################################################*/

/**
 * @author Benoit Sautel
 * @desc Represents a HTTP uploaded file
 * @package {@package}
 * @subpackage http/upload
 */
class UploadedFileTooLargeException extends Exception
{
	public function __construct($varname, $filename)
	{
		parent::__construct('The file ' . $filename . ' is couldn\'t be uploaded because it\'s too large');
	}
}
?>