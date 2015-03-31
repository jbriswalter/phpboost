# INCLUDE NOT_VISIBLE_MESSAGE #
<article itemscope="itemscope" itemtype="http://schema.org/Article">
	<header>
		<h1>
			<a href="{U_SYNDICATION}" title="${LangLoader::get_message('syndication', 'common')}" class="fa fa-syndication"></a>
			<span itemprop="name">{TITLE}</span>
			<span class="actions">
				# IF C_EDIT #
					<a href="{U_EDIT_ARTICLE}" title="${LangLoader::get_message('edit', 'common')}" class="fa fa-edit"></a>
				# ENDIF #
				# IF C_DELETE #
					<a href="{U_DELETE_ARTICLE}" title="${LangLoader::get_message('delete', 'common')}" class="fa fa-delete" data-confirmation="delete-element"></a>
				# ENDIF #
					<a href="{U_PRINT_ARTICLE}" title="${LangLoader::get_message('printable_version', 'main')}" target="blank" class="fa fa-print"></a>
			</span>
		</h1>
		
		<div class="more">
			# IF C_AUTHOR_DISPLAYED #
			<i class="fa fa-user" title="${LangLoader::get_message('author', 'common')}"></i>
			# IF C_AUTHOR_EXIST #<a itemprop="author" href="{U_AUTHOR}" class="{USER_LEVEL_CLASS}" # IF C_USER_GROUP_COLOR # style="color:{USER_GROUP_COLOR}"# ENDIF #>&nbsp;{PSEUDO}&nbsp;</a># ELSE #{PSEUDO}# ENDIF #|&nbsp;
			# ENDIF #
			<i class="fa fa-calendar" title="${LangLoader::get_message('date', 'date-common')}"></i>&nbsp;<time datetime="{DATE}" itemprop="datePublished">{DATE}</time>&nbsp;|
			&nbsp;<i class="fa fa-eye" title="{NUMBER_VIEW} {@articles.sort_field.views}"></i>&nbsp;<span title="{NUMBER_VIEW} {@articles.sort_field.views}">{NUMBER_VIEW}</span>
			# IF C_COMMENTS_ENABLED #
				&nbsp;|&nbsp;<i class="fa fa-comment" title="${LangLoader::get_message('comments', 'comments-common')}"></i><a itemprop="discussionUrl" class="small" href="{U_COMMENTS}">&nbsp;{L_COMMENTS}</a>
			# ENDIF #
			&nbsp;|&nbsp;<i class="fa fa-folder" title="${LangLoader::get_message('category', 'categories-common')}"></i>&nbsp;<a itemprop="about" class="small" href="{U_CATEGORY}">{CATEGORY_NAME}</a>
			# IF C_KEYWORDS #
			&nbsp;|&nbsp;<i title="${LangLoader::get_message('form.keywords', 'common')}" class="fa fa-tags"></i> 
				# START keywords #
					<a itemprop="keywords" href="{keywords.URL}">{keywords.NAME}</a># IF keywords.C_SEPARATOR #, # ENDIF #
				# END keywords #
			# ENDIF #
		</div>
		
		<meta itemprop="url" content="{U_ARTICLE}">
		<meta itemprop="description" content="${escape(DESCRIPTION)}">
		<meta itemprop="datePublished" content="{DATE_ISO8601}">
		<meta itemprop="discussionUrl" content="{U_COMMENTS}">
		# IF C_HAS_PICTURE #<meta itemprop="thumbnailUrl" content="{PICTURE}"># ENDIF #
		<meta itemprop="interactionCount" content="{NUMBER_COMMENTS} UserComments">
	</header>
	<div class="content">
		# IF C_PAGINATION #
			# INCLUDE FORM #
			<div class="spacer">&nbsp;</div>
		# ENDIF #
		# IF PAGE_NAME #
			<h2 class="title page_name">{PAGE_NAME}</h2>
		# ENDIF #
		<span itemprop="text">{CONTENTS}</span>
		<div class="spacer" style="margin-top:35px;">&nbsp;</div>
		<hr />
		<div class="spacer">&nbsp;</div>
		# IF C_PAGINATION #
			<div class="pages-pagination right">
				# IF C_NEXT_PAGE #
				<a style="text-decoration:none;" href="{U_NEXT_PAGE}">{L_NEXT_TITLE} <i class="fa fa-arrow-right"></i></a>
				# ELSE #
				&nbsp;
				# ENDIF #
			</div>
			<div class="pages-pagination center"># INCLUDE PAGINATION_ARTICLES #</div>
			<div class="pages-pagination">
				# IF C_PREVIOUS_PAGE #
				<a style="text-decoration:none;" href="{U_PREVIOUS_PAGE}"><i class="fa fa-arrow-left"></i> {L_PREVIOUS_TITLE}</a>
				# ENDIF #
			</div>
		# ENDIF #
		<div class="spacer">&nbsp;</div>
	</div>
	<aside>
		# IF C_SOURCES #
		<div id="articles-sources-container">
			<span>${LangLoader::get_message('form.sources', 'common')}</span> :
			# START sources #
			<a itemprop="isBasedOnUrl" href="{sources.URL}" class="small">{sources.NAME}</a># IF sources.C_SEPARATOR #, # ENDIF #
			# END sources #
		</div>
		# ENDIF #
		# IF C_DATE_UPDATED #
		<div><i>{@articles.date_updated}<time datetime="{DATE_UPDATED_ISO8601}" itemprop="datePublished">{DATE_UPDATED}</time></i></div>
		# ENDIF #
		<div class="spacer">&nbsp;</div>
		# IF C_NOTATION_ENABLED #
		<div style="float:left" class="smaller">
			{KERNEL_NOTATION}
		</div>
		# ENDIF #
		<div class="spacer"></div>
		# IF C_COMMENTS_ENABLED #
			# INCLUDE COMMENTS #
		# ENDIF #
	</aside>
	<footer></footer>
</article>