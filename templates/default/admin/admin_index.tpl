		<nav id="admin-quick-menu">
			<a href="" class="js-menu-button" onclick="open_submenu('admin-quick-menu');return false;" title="{L_PROFIL}">
				<i class="fa fa-bars"></i> {L_QUICK_LINKS}
			</a>
			<ul>
				<li>
					<a href="{PATH_TO_ROOT}/admin/admin_alerts.php" class="quick-link">{L_ADMINISTRATOR_ALERTS}</a>
				</li>
				<li>
					<a href="${relative_url(AdminMembersUrlBuilder::management())}" class="quick-link">{L_USERS_MANAGMENT}</a>
				</li>
				<li>
					<a href="{PATH_TO_ROOT}/admin/menus/menus.php" class="quick-link">{L_MENUS_MANAGMENT}</a>
				</li>
				<li>
					<a href="${relative_url(AdminModulesUrlBuilder::list_installed_modules())}" class="quick-link">{L_MODULES_MANAGMENT}</a>
				</li>
				<li>
					<a href="{PATH_TO_ROOT}/admin/updates/updates.php" class="quick-link">{L_WEBSITE_UPDATES}</a>
				</li>
			</ul>
		</nav>
		
		<div id="admin-contents">
			
			<div class="block welcome">
				<img class="float-left" src="{PATH_TO_ROOT}/templates/{THEME}/theme/images/logo.png" alt="Logo" />
				<div class="welcome-desc">
					<h2>{L_WELCOME_TITLE}</h2>
					<p>{L_WELCOME_DESC}</p>
				</div>
			</div>
			
			<fieldset class="quick-acces">
				<legend><i class="fa fa-angle-double-right"></i> {L_QUICK_ACCESS}</legend>
				<div class="fieldset-inset">
					<div class="small-block">
						<h3><i class="fa fa-plus"></i> {L_ADD_CONTENT}</h3>
						<ul>
							<li><a href="${relative_url(AdminModulesUrlBuilder::list_installed_modules())}" title="{L_MODULES_MANAGEMENT}">{L_MODULES_MANAGEMENT}</a></li>
							# IF ModulesManager::is_module_installed('articles') #
							<li><a href="${relative_url(ArticlesUrlBuilder::add_article())}" title="{L_ADD_ARTICLES}">{L_ADD_ARTICLES}</a></li>
							# ENDIF #
							# IF ModulesManager::is_module_installed('news') #
							<li><a href="${relative_url(NewsUrlBuilder::add_news())}" title="{L_ADD_NEWS}">{L_ADD_NEWS}</a></li>
							# ENDIF #
						</ul>
					</div>
					<div class="small-block">
						<h3><i class="fa fa-picture-o"></i> {L_CUSTOMIZE_SITE}</h3>
						<ul>
							<li><a href="${relative_url(AdminThemeUrlBuilder::add_theme())}" title="{L_ADD_TEMPLATE}">{L_ADD_TEMPLATE}</a></li>
							<li><a href="{PATH_TO_ROOT}/admin/menus" title="{L_MENUS_MANAGEMENT}">{L_MENUS_MANAGEMENT}</a></li>
							# IF ModulesManager::is_module_installed('customization') #
							<li><a href="${relative_url(AdminCustomizeUrlBuilder::editor_css_file())}" title="{L_CUSTOMIZE_TEMPLATE}">{L_CUSTOMIZE_TEMPLATE}</a></li>
							# ENDIF #
						</ul>
					</div>
					<div class="small-block">
						<h3><i class="fa fa-cogs"></i> {L_SITE_MANAGEMENT}</h3>
						<ul>
							<li><a href="${relative_url(AdminConfigUrlBuilder::general_config())}" title="{L_GENERAL_CONFIG}">{L_GENERAL_CONFIG}</a></li>
							<li><a href="${relative_url(AdminCacheUrlBuilder::clear_cache())}" title="{L_EMPTY_CACHE}">{L_EMPTY_CACHE}</a></li>
							# IF ModulesManager::is_module_installed('database') #
							<li><a href="{PATH_TO_ROOT}/database/admin_database.php" title="{L_SAVE_DATABASE}">{L_SAVE_DATABASE}</a></li>
							# ENDIF #
						</ul>
					</div>
				</div>
			</fieldset>
			
			<div class="medium-block">
				<fieldset>
					<legend><i class="fa fa-bell"></i> {L_ADMIN_ALERTS}</legend>
					<div class="fieldset-inset">
						<div class="form-element">
							# IF C_UNREAD_ALERTS #
								<div class="warning">{L_UNREAD_ALERT}</div>
							# ELSE #
								<div class="success">{L_NO_UNREAD_ALERT}</div>
							# ENDIF #
						</div>
						<p class="smaller center">
							<a href="admin_alerts.php">{L_DISPLAY_ALL_ALERTS}</a>
						</p>
					</div>
				</fieldset>
				<fieldset>
					<legend><i class="fa fa-comment-o"></i> {L_LAST_COMMENTS}</legend>
					<div class="fieldset-inset">
						<div class="form-element">
							# START comments_list #	
									<a href="{comments_list.U_LINK}">
										<i class="fa fa-hand-o-right"></i>
									</a>
									<span class="smaller">{L_BY} {comments_list.U_PSEUDO}</span> : {comments_list.CONTENT}
									<br /><br />
							# END comments_list #
							# IF C_NO_COM #
							<p><em>{L_NO_COMMENT}</em></p>
							# ENDIF #
						</div>
						<p class="smaller center"><a href="${relative_url(UserUrlBuilder::comments())}">{L_VIEW_ALL_COMMENTS}</a></p>
					</div>
				</fieldset>
				
				<form action="admin_index.php" method="post">
					<fieldset>
						<legend><i class="fa fa-user"></i> {L_USER_ONLINE}</legend>
						<div class="fieldset-inset" style="padding:15px 0">
							<div class="form-element">
								<table id="AdminTable" style="width:100%; margin:0">
									<thead>
										<tr> 
											<th>
												{L_USER_ONLINE}
											</th>
											<th>
												{L_USER_IP}
											</th>
											<th>
												{L_LOCALISATION}
											</th>
											<th>
												{L_LAST_UPDATE}
											</th>
										</tr>
									</thead>
									<tbody>
										# START user #
										<tr> 
											<td>
												{user.USER}
											</td>
											<td>
												{user.USER_IP}
											</td>
											<td>
												{user.WHERE}
											</td>
											<td>
												{user.TIME}
											</td>					
										</tr>
										# END user #
									</tbody>
								</table>
							</div>
						</div>
					</fieldset>
				</form>
			</div>
			
			<div class="medium-block">
				
				# INCLUDE ADVISES #
				
				<form action="admin_index.php" method="post">
					<fieldset>
						<legend><i class="fa fa-edit"></i> {L_WRITING_PAD}</legend>
						<div class="fieldset-inset">
							<div class="form-element">
								<textarea id="writing_pad_content" name="writing_pad_content">{WRITING_PAD_CONTENT}</textarea> 
							</div>
							<p class="center">
								<button type="submit" class="submit" name="writingpad" value="true">{L_UPDATE}</button>
								<button type="reset" value="true">{L_RESET}</button>
							</p>
						</div>
					</fieldset>
					<input type="hidden" name="token" value="{TOKEN}">
				</form>
			</div>
			
		</div><!-- admin-contents -->
			