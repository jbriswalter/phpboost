		<script type="text/javascript">
		<!--
			function Confirm() {
				return confirm("{L_CONFIRM_DEL_RANK}");
			}
			
			function img_change(id, url)
			{
				if (document.getElementById(id))
					document.getElementById(id).src = "{PATH_TO_ROOT}/forum/templates/images/ranks/" + url;
			}
		-->
		</script>
		
		<div id="admin_quick_menu">
			<ul>
				<li class="title_menu">{L_FORUM_MANAGEMENT}</li>
				<li>
					<a href="admin_forum.php"><img src="forum.png" alt="" /></a>
					<br />
					<a href="admin_forum.php" class="quick_link">{L_CAT_MANAGEMENT}</a>
				</li>
				<li>
					<a href="admin_forum_add.php"><img src="forum.png" alt="" /></a>
					<br />
					<a href="admin_forum_add.php" class="quick_link">{L_ADD_CAT}</a>
				</li>
				<li>
					<a href="admin_forum_config.php"><img src="forum.png" alt="" /></a>
					<br />
					<a href="admin_forum_config.php" class="quick_link">{L_FORUM_CONFIG}</a>
				</li>
				<li>
					<a href="admin_forum_groups.php"><img src="forum.png" alt="" /></a>
					<br />
					<a href="admin_forum_groups.php" class="quick_link">{L_FORUM_GROUPS}</a>
				</li>
				<li>
					<a href="admin_ranks.php"><img src="templates/images/ranks.png" alt="" /></a>
					<br />
					<a href="admin_ranks.php" class="quick_link">{L_FORUM_RANKS_MANAGEMENT}</a>
				</li>
				<li>
					<a href="admin_ranks_add.php"><img src="templates/images/ranks.png" alt="" /></a>
					<br />
					<a href="admin_ranks_add.php" class="quick_link">{L_FORUM_ADD_RANKS}</a>
				</li>
			</ul>
		</div>	
		
		<div id="admin_contents">
			<form action="admin_ranks.php?token={TOKEN}" method="post">
				<fieldset>
					<legend>{L_RANKS_MANAGEMENT}</legend>
					<table class="module_table">
						<tr style="text-align:center;"> 
							<th>
								{L_RANK_NAME}
							</th>
							<th>
								{L_NBR_MSG}
							</th>
							<th>
								{L_IMG_ASSOC}
							</th>
							<th>
								{L_DELETE}
							</th>
						</tr>
						# START rank #
						<tr>
							<td class="row2" style="text-align:center;"> 
								<input type="text" maxlength="30" size="20" name="{rank.ID}name" value="{rank.RANK}" class="text">
							</td>
							<td class="row2" style="text-align:center;">
								{rank.MSG}
							</td>
							<td class="row2" style="text-align:center;"> 						
								<select name="{rank.ID}icon" onchange="img_change('icon{rank.ID}', this.options[selectedIndex].value)">
									{rank.RANK_OPTIONS}
								</select>
								<br />
								# IF rank.IMG_RANK # <img src="{PATH_TO_ROOT}/forum/templates/images/ranks/{rank.IMG_RANK}" id="icon{rank.ID}" alt="" /> # ENDIF #
							</td>
							<td class="row2" style="text-align:center;">
								{rank.DELETE}
							</td>
						</tr>
						# END rank #
					</table>					
				</fieldset>
				<fieldset class="fieldset_submit">
					<legend>{L_UPDATE}</legend>
					<input type="submit" name="valid" value="{L_UPDATE}" class="submit">
					&nbsp;&nbsp; 
					<input type="reset" value="{L_RESET}" class="reset">
				</fieldset>
			</form>
		</div>
		