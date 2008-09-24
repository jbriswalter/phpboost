<?php
/*##################################################
 *                              admin_alerts.php
 *                            -------------------
 *   begin                : August 30, 2008
 *   copyright            : (C) 2008 Sautel Benoit
 *   email                : ben.popeye@phpboost.com
 *
 *   
###################################################
 *
 *   This program is free software; you can redistribute it and/or modify
 *   it under the terms of the GNU General Public License as published by
 *   the Free Software Foundation; either version 2 of the License, or
 *   (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.

 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 *
###################################################*/

require_once('../admin/admin_begin.php');
define('TITLE', $LANG['administration']);
require_once('../admin/admin_header.php');

$template = new Template('admin/admin_alerts.tpl');

require_once(PATH_TO_ROOT . '/kernel/framework/events/administrator_alert_service.class.php');

$alerts_list = AdministratorAlertService::get_all_alerts();

define('NUM_ALERTS_PER_PAGE', 20);

require_once(PATH_TO_ROOT . '/kernel/framework/util/pagination.class.php');

$pagination = new Pagination();
$pagination->set_var_name_current_page('p');

//Gestion des crit�res de tri
$criteria = retrieve(GET, 'criteria', 'current_status');
$order = retrieve(GET, 'order', 'asc');

if( !in_array($criteria, array('entitled', 'current_status', 'creation_date', 'priority')) )
	$criteria = 'current_status';
$order = $order == 'desc' ? 'desc' : 'asc';

//On va chercher la liste des alertes
foreach(AdministratorAlertService::get_all_alerts($criteria, $order, ($pagination->get_var_page('p') - 1) * NUM_ALERTS_PER_PAGE, NUM_ALERTS_PER_PAGE) as $alert)
{
	$img_type = '';
	
	switch($alert->get_priority())
	{
		case ADMIN_ALERT_VERY_LOW_PRIORITY:
			$color = 'FFFFFF';
			break;
		case ADMIN_ALERT_LOW_PRIORITY:
			$color = 'ECDBB7';
			break;
		case ADMIN_ALERT_MEDIUM_PRIORITY:
			$color = 'F5D5C6';
			break;
		case ADMIN_ALERT_HIGH_PRIORITY:
			$img_type = 'important.png';
			$color = 'FFD5D1';
			break;
		case ADMIN_ALERT_VERY_HIGH_PRIORITY:
			$img_type = 'errors_mini.png';
			$color = 'F3A29B';
			break;
		default:
		$color = 'FFFFFF';
	}
	
	$creation_date = $alert->get_creation_date();
	
	$template->Assign_block_vars('alerts', array(
		'C_PROCESSED' => $alert->get_status() == ADMIN_ALERT_STATUS_PROCESSED,
		'FIXING_URL' => transid(PATH_TO_ROOT . '/' . $alert->get_fixing_url()),
		'NAME' => $alert->get_entitled(),
		'PRIORITY' => $alert->get_priority_name(),
		'STYLE' => 'background:#' . $color . ';',
		'IMG' => !empty($img_type) ? '<img src="../templates/' . $CONFIG['theme'] . '/images/admin/' . $img_type . '" alt="" class="valign_middle" />' : '',
		'DATE' => $creation_date->format(DATE_FORMAT),
		'ID' => $alert->get_id(),
		'STATUS' => $alert->get_status()
	));
}

$template->Assign_vars(array(
	'C_EXISTING_ALERTS' => ((bool)count($alerts_list)),
	'C_PAGINATION' => AdministratorAlertService::get_number_alerts() > NUM_ALERTS_PER_PAGE,
	'PAGINATION' => $pagination->Display_pagination('admin_alerts.php?p=%d&criteria=' . $criteria . '&order=' . $order, AdministratorAlertService::get_number_alerts(), 'p', NUM_ALERTS_PER_PAGE, 3),	
	'L_ADMIN_ALERTS' => $LANG['administrator_alerts'],
	'L_TYPE' => $LANG['type'],
	'L_DATE' => $LANG['date'],
	'L_PRIORITY' => $LANG['priority'],
	'L_ADMINISTRATOR_ALERTS_LIST' => $LANG['administrator_alerts_list'],
	'L_ACTIONS' => $LANG['administrator_alerts_action'],
	'L_NO_ALERT' => $LANG['no_administrator_alert'],
	'L_CONFIRM_DELETE_ALERT' => $LANG['confirm_delete_administrator_alert'],
	'C_ORDER_ENTITLED_ASC' => $criteria == 'entitled' && $order == 'asc',
	'U_ORDER_ENTITLED_ASC' => transid('admin_alerts.php?p=' . $pagination->get_var_page('p') . '&criteria=entitled&order=asc'),
	'C_ORDER_ENTITLED_DESC' => $criteria == 'entitled' && $order == 'desc',
	'U_ORDER_ENTITLED_DESC' => transid('admin_alerts.php?p=' . $pagination->get_var_page('p') . '&criteria=entitled&order=desc'),
	'C_ORDER_CREATION_DATE_ASC' => $criteria == 'creation_date' && $order == 'asc',
	'U_ORDER_CREATION_DATE_ASC' => transid('admin_alerts.php?p=' . $pagination->get_var_page('p') . '&criteria=creation_date&order=asc'),
	'C_ORDER_CREATION_DATE_DESC' => $criteria == 'creation_date' && $order == 'desc',
	'U_ORDER_CREATION_DATE_DESC' => transid('admin_alerts.php?p=' . $pagination->get_var_page('p') . '&criteria=creation_date&order=desc'),
	'C_ORDER_PRIORITY_ASC' => $criteria == 'priority' && $order == 'asc',
	'U_ORDER_PRIORITY_ASC' => transid('admin_alerts.php?p=' . $pagination->get_var_page('p') . '&criteria=priority&order=asc'),
	'C_ORDER_PRIORITY_DESC' => $criteria == 'priority' && $order == 'desc',
	'U_ORDER_PRIORITY_DESC' => transid('admin_alerts.php?p=' . $pagination->get_var_page('p') . '&criteria=priority&order=desc'),
	'C_ORDER_STATUS_ASC' => $criteria == 'current_status' && $order == 'asc',
	'U_ORDER_STATUS_ASC' => transid('admin_alerts.php?p=' . $pagination->get_var_page('p') . '&criteria=current_status&order=asc'),
	'C_ORDER_STATUS_DESC' => $criteria == 'current_status' && $order == 'desc',
	'U_ORDER_STATUS_DESC' => transid('admin_alerts.php?p=' . $pagination->get_var_page('p') . '&criteria=current_status&order=desc')

));
	
$template->parse();

require_once('../admin/admin_footer.php');

?>