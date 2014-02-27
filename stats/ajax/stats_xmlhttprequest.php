<?php
/*##################################################
 *                          member_xmlhttprequest.php
 *                            -------------------
 *   begin                : January, 25 2007
 *   copyright            : (C) 2007 Viarre R�gis
 *   email                : crowkait@phpboost.com
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
* @package ajax
*
*/

define('PATH_TO_ROOT', '../..');
define('NO_SESSION_LOCATION', true); //Permet de ne pas mettre jour la page dans la session.

include_once(PATH_TO_ROOT . '/kernel/begin.php');
include_once(PATH_TO_ROOT . '/kernel/header_no_display.php');

$sql_querier = PersistenceContext::get_sql();

if (!empty($_GET['stats_referer'])) //Recherche d'un membre pour envoyer le mp.
{
    $idurl = !empty($_GET['id']) ? NumberHelper::numeric($_GET['id']) : '';
    $url = $sql_querier->query("SELECT url FROM " . StatsSetup::$stats_referer_table . " WHERE id = '" . $idurl . "'", __LINE__, __FILE__);

    $result = $sql_querier->query_while("SELECT url, relative_url, total_visit, today_visit, yesterday_visit, nbr_day, last_update
	FROM " . PREFIX . "stats_referer
	WHERE url = '" . addslashes($url) . "' AND type = 0
	ORDER BY total_visit DESC", __LINE__, __FILE__);
    while ($row = $sql_querier->fetch_assoc($result))
    {
        $average = ($row['total_visit'] / $row['nbr_day']);
        if ($row['yesterday_visit'] > $average)
        {
            $trend_img = 'up';
            $sign = '+';
            $trend = NumberHelper::round((($row['yesterday_visit'] * 100) / $average), 1) - 100;
        }
        elseif ($row['yesterday_visit'] < $average)
        {
            $trend_img = 'down';
            $sign = '-';
            $trend = 100 - NumberHelper::round((($row['yesterday_visit'] * 100) / $average), 1);
        }
        else
        {
            $trend_img = 'right';
            $sign = '+';
            $trend = 0;
        }

        echo '<table>
			<tbody>
				<tr>
					<td class="no-separator">
						<a href="' . $row['url'] . $row['relative_url'] . '">' . $row['relative_url'] . '</a>
					</td>
					<td class="no-separator" style="width:60px;">
						' . $row['total_visit'] . '
					</td>
					<td class="no-separator" style="width:60px;">
						' . NumberHelper::round($average, 1) . '
					</td>
					<td class="no-separator" style="width:96px;">
						' . gmdate_format('date_format_short', $row['last_update']) . '
					</td>
					<td class="no-separator" style="width:95px;">
						<i class="fa fa-arrow-' . $trend_img . ' fa-2x"></i> (' . $sign . $trend . '%)
					</td>
				</tr>
			</tbody>
		</table>';
    }
    $sql_querier->query_close($result);
}
elseif (!empty($_GET['stats_keyword'])) //Recherche d'un membre pour envoyer le mp.
{
    $idkeyword = !empty($_GET['id']) ? NumberHelper::numeric($_GET['id']) : '';
    $keyword = $sql_querier->query("SELECT relative_url FROM " . StatsSetup::$stats_referer_table . " WHERE id = '" . $idkeyword . "'", __LINE__, __FILE__);

    $result = $sql_querier->query_while("SELECT url, total_visit, today_visit, yesterday_visit, nbr_day, last_update
	FROM " . PREFIX . "stats_referer
	WHERE relative_url = '" . addslashes($keyword) . "' AND type = 1
	ORDER BY total_visit DESC", __LINE__, __FILE__);
    while ($row = $sql_querier->fetch_assoc($result))
    {
        $average = ($row['total_visit'] / $row['nbr_day']);
        if ($row['yesterday_visit'] > $average)
        {
            $trend_img = 'up';
            $sign = '+';
            $trend = NumberHelper::round((($row['yesterday_visit'] * 100) / $average), 1) - 100;
        }
        elseif ($row['yesterday_visit'] < $average)
        {
            $trend_img = 'down';
            $sign = '-';
            $trend = 100 - NumberHelper::round((($row['yesterday_visit'] * 100) / $average), 1);
        }
        else
        {
            $trend_img = 'right';
            $sign = '+';
            $trend = 0;
        }

        echo '<table>
			<tbody>
				<tr>
					<td class="no-separator">
						' . $row['url'] . '
					</td>
					<td class="no-separator" style="width:70px;">
						' . $row['total_visit'] . '
					</td>
					<td class="no-separator" style="width:60px;">
						' . NumberHelper::round($average, 1) . '
					</td>
					<td class="no-separator" style="width:96px;">
						' . gmdate_format('date_format_short', $row['last_update']) . '
					</td>
					<td class="no-separator" style="width:95px;">
						<i class="fa fa-arrow-' . $trend_img . ' fa-2x"></i> (' . $sign . $trend . '%)
					</td>
				</tr>
			</tbody>
		</table>';
    }
    $sql_querier->query_close($result);
}

include_once(PATH_TO_ROOT . '/kernel/footer_no_display.php');
?>