# INCLUDE UPLOAD_FORM #
<form action="{REWRITED_SCRIPT}" method="post" class="fieldset-content">
	# INCLUDE MSG #
	# IF C_UPDATES #
	<div class="warning message-helper-small">{@modules.updates_available}</div>
	# ENDIF #
	<table>
		<caption>{@modules.updates_available}</caption>

		# IF C_UPDATES #
		<thead>
			<tr>
				<th>{@modules.name}</th>
				<th>{@modules.description}</th>
				<th>{@modules.upgrade_module}</th>
			</tr>
		</thead>
		<tbody>
			# START modules_upgradable #
			<tr>
				<td>
					<img src="{PATH_TO_ROOT}/{modules_upgradable.ICON}/{modules_upgradable.ICON}.png" alt="" /> <span class="text-strong">{modules_upgradable.NAME}</span> <span class="text-italic">({modules_upgradable.VERSION})</span>
				</td>
				<td class="left">
					<span class="text-strong">{@modules.author} :</span> {modules_upgradable.AUTHOR} {modules_upgradable.AUTHOR_WEBSITE}<br />
					<span class="text-strong">{@modules.description} :</span> {modules_upgradable.DESCRIPTION}<br />
					<span class="text-strong">{@modules.compatibility} :</span> PHPBoost {modules_upgradable.COMPATIBILITY}<br />
					<span class="text-strong">{@modules.php_version} :</span> {modules_upgradable.PHP_VERSION}
				</td>
				<td>
					<input type="hidden" name="token" value="{TOKEN}">
					<button type="submit" class="submit" name="upgrade-{modules_upgradable.ID}" value="true">{@modules.upgrade_module}</button>
					<input type="hidden" name="module_id" value="{modules_upgradable.ID}">
				</td>
			</tr>
			# END modules_upgradable #
		</tbody>
	</table>
		# ELSE #
	</table>
	<div class="success message-helper-small">${LangLoader::get_message('no_item_now', 'common')}</div>
		# ENDIF #
</form>
