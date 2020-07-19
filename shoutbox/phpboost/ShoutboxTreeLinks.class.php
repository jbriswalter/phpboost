<?php
/**
 * @copyright 	&copy; 2005-2019 PHPBoost
 * @license 	https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL-3.0
 * @author      Julien BRISWALTER <j1.seth@phpboost.com>
 * @version   	PHPBoost 5.2 - last update: 2017 07 20
 * @since   	PHPBoost 4.0 - 2013 11 23
 * @contributor xela <xela@phpboost.com>
*/

class ShoutboxTreeLinks implements ModuleTreeLinksExtensionPoint
{
	public function get_actions_tree_links()
	{
		$tree = new ModuleTreeLinks();

		$tree->add_link(new AdminModuleLink(LangLoader::get_message('configuration', 'admin-common'), ShoutboxUrlBuilder::configuration()));
		$tree->add_link(new AdminModuleLink(LangLoader::get_message('module.documentation', 'admin-modules-common'), ModulesManager::get_module('shoutbox')->get_configuration()->get_documentation()));

		return $tree;
	}
}
?>