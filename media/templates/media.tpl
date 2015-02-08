		# IF C_CATEGORIES #
			<section>
				<header>
					<h1>
						<a href="${relative_url(SyndicationUrlBuilder::rss('media', ID_CAT))}" class="fa fa-syndication" title="${LangLoader::get_message('syndication', 'common')}"></a>
						{TITLE}
					</h1>
				</header>
				<div class="content">
					# IF C_DESCRIPTION #
						{DESCRIPTION}
						<hr style="margin-top:25px;" />
					# ENDIF #
	
					# IF C_SUB_CATS #
						# START row #
							# START row.list_cats #
								<div style="float:left;width:{row.list_cats.WIDTH}%;text-align:center;margin:20px 0px;">
									<a href="{row.list_cats.U_CAT}" title="{row.list_cats.NAME}">
										<img src="{row.list_cats.SRC}" alt="{row.list_cats.NAME}" />
									</a>
									<br />
									<a href="{row.list_cats.U_CAT}">{row.list_cats.NAME}</a>
									# IF C_ADMIN #
									<a href="{row.list_cats.U_ADMIN_CAT}" title="${LangLoader::get_message('edit', 'common')}" class="fa fa-edit"></a>
									# ENDIF #
									# IF row.list_cats.NUM_MEDIA #
									<div class="smaller">
										{row.list_cats.NUM_MEDIA}
									</div>
									# ENDIF #
								</div>
							# END row.list_cats #
							<div class="spacer">&nbsp;</div>
						# END row #
						<hr />
					# ENDIF #
	
					# IF C_FILES #
						<div class="options" id="form">
							<script>
							<!--
							function change_order()
							{
								window.location = "{TARGET_ON_CHANGE_ORDER}sort=" + document.getElementById("sort").value + "&mode=" + document.getElementById("mode").value;
							}
							-->
							</script>
							{L_ORDER_BY}
							<select name="sort" id="sort" class="nav" onchange="change_order()">
								<option value="alpha"{SELECTED_ALPHA}>{L_ALPHA}</option>
								<option value="date"{SELECTED_DATE}>{L_DATE}</option>
								<option value="nbr"{SELECTED_NBR}>{L_NBR}</option>
								<option value="note"{SELECTED_NOTE}>{L_NOTE}</option>
								<option value="com"{SELECTED_COM}>{L_COM}</option>
							</select>
							<select name="mode" id="mode" class="nav" onchange="change_order()">
								<option value="asc"{SELECTED_ASC}>{L_ASC}</option>
								<option value="desc"{SELECTED_DESC}>{L_DESC}</option>
							</select>
						</div>
						<div class="spacer">&nbsp;</div>
	
						# START file #
							<article class="block">
								<header>
									<h1>
										<a href="{file.U_MEDIA_LINK}">{file.NAME}</a>
										# IF C_MODO #
										<span class="actions">
											<a href="{file.U_ADMIN_UNVISIBLE_MEDIA}" class="fa fa-eye-slash" title="{L_UNAPROBED}"></a>
											<a href="{file.U_ADMIN_EDIT_MEDIA}" title="${LangLoader::get_message('edit', 'common')}" class="fa fa-edit"></a>
											<a href="{file.U_ADMIN_DELETE_MEDIA}" title="${LangLoader::get_message('delete', 'common')}" class="fa fa-delete" data-confirmation="delete-element"></a>
										</span>
										# ENDIF #
									</h1>
								</header>
								<div class="content">
									# IF file.C_DESCRIPTION #
										<div class="media-desc" style="margin-top:10px">
										{file.DESCRIPTION}
										</div>
									# ENDIF #
									<div class="spacer">&nbsp;</div>
									<div class="smaller">
										{L_BY} {file.POSTER}
										<br />
										{file.COUNT}
										<br />
										# IF C_DISPLAY_COMMENTS #
										{file.U_COM_LINK}
										<br />
										# ENDIF #
										# IF C_DISPLAY_NOTATION #
										{L_NOTE} {file.NOTE}
										# ENDIF #
									</div>
								</div>
								<footer></footer>
							</article>
						# END file #
					# ENDIF #
	
					# IF C_DISPLAY_NO_FILE_MSG #
						<div class="notice">${LangLoader::get_message('no_item_now', 'common')}</div>
					# ENDIF #
				</div>
				<footer># IF C_PAGINATION #<span class="center"># INCLUDE PAGINATION #</span># ENDIF #</footer>
			</section>
		# ENDIF #

		# IF C_DISPLAY_MEDIA #
		<article>
			<header>
				<h1>
					{NAME} 
					<span class="actions">
						# IF C_DISPLAY_COMMENTS #
							<a href="{U_COM}"><i class="fa fa-comments-o"></i> {L_COM}</a>
						# ENDIF #
						# IF C_MODO #
							<a href="{U_UNVISIBLE_MEDIA}" class="fa fa-eye-slash" title="{L_UNAPROBED}"></a>
							<a href="{U_EDIT_MEDIA}" title="${LangLoader::get_message('edit', 'common')}" class="fa fa-edit"></a>
							<a href="{U_DELETE_MEDIA}" title="${LangLoader::get_message('delete', 'common')}" class="fa fa-delete" data-confirmation="delete-element"></a>
						# ENDIF #
					</span>
				</h1>
			</header>
			<div class="content">
			
				<div class="options infos">
					<h6>{L_MEDIA_INFOS}</h6>
						<span class="text-strong">{L_DATE} : </span><span>{DATE}</span><br/>
						<span class="text-strong">{L_BY} : </span><span>{BY}</span><br/>
						<span class="text-strong">{L_VIEWED} : </span><span>{HITS}</span><br/>
					# IF C_DISPLAY_NOTATION #
					<div class="center">
						<span class="text-strong">{KERNEL_NOTATION}</span>
					</div>
					# ENDIF #
				</div>
				
				<div class="media-desc">
					{CONTENTS}
				</div>
				
				<div class="media-content">
					# INCLUDE media_format #
				</div>

				{COMMENTS}
			</div>
			<footer></footer>
		</article>
		# ENDIF #