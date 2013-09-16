<script type="text/javascript">
<!--
	function Confirm()
	{
		return confirm(${i18njs('news.message.delete')});
	}
-->
</script>

<div class="module_actions">
	<menu class="buttons">
		<ul><li class="dropdown"><a><i class="icon-cog"></i></a>
			<ul class="right">
				# IF C_ADD #
				<li>
					<a href="${relative_url(NewsUrlBuilder::add_news())}" title="${i18n('news.add')}"><i class="icon-plus"></i> ${i18n('news.add')}</a>
				</li>
				# ENDIF #
				# IF C_PENDING_NEWS #
				<li>
					<a href="${relative_url(NewsUrlBuilder::display_pending_news())}" title="${i18n('news.pending')}"><i class="icon-time"></i> ${i18n('news.pending')}</a>
				</li>
				# ENDIF #
			</ul>
		</li>
		</ul>
	</menu>
</div>

# IF C_NEWS_NO_AVAILABLE #
	<div class="module_position edito">
		<div class="module_top_l"></div>
		<div class="module_top_r"></div>
		<div class="module_top">
			<div class="module_top_title module_top_news">
				<a href="${relative_url(SyndicationUrlBuilder::rss('news'))}" title="${i18n('syndication')}" class="img_link">
					<img class="valign_middle" src="{PATH_TO_ROOT}/templates/{THEME}/images/rss.png" alt="${i18n('syndication')}" />
				</a>
				{L_NEWS_NO_AVAILABLE_TITLE}
			</div>
	        <div class="module_top_com">
				# IF C_ADMIN #
				<a href="{U_ADMIN}" title="{L_ADMIN}" class="img_link">
					<img class="valign_middle" src="{PATH_TO_ROOT}/templates/{THEME}/images/{LANG}/edit.png" alt="{L_ADMIN}" />
				</a>
				# ENDIF #
			</div>
			<div class="spacer"></div>
    	</div>
    	<div class="module_contents" style="text-align:center;">
        	${i18n('news.message.no_items')}
    	</div>
    	<div class="module_bottom_l"></div>
		<div class="module_bottom_r"></div>
		<div class="module_bottom"></div>
	</div>
# ELSE #
<section style="overflow: hidden;">
	<header>
		<h1>${i18n('news')}</h1>
	</header>
	# START news #
		# IF news.C_NEWS_ROW #
			<div class="spacer"></div>
		# ENDIF #
		<article # IF C_NEWS_BLOCK_COLUMN # style="float:left;width:{COLUMN_WIDTH}%" # ENDIF #~itemscope="itemscope" itemtype="http://schema.org/CreativeWork">
			<header>
			
				# IF C_NEWS_BLOCK_COLUMN #
				<ul class="module_top_options block_hidden">
				# ELSE #
				<ul class="module_top_options">
				# ENDIF #
					<li>
						<a class="news_comments" href="{news.U_COMMENTS}">{news.NUMBER_COMMENTS}</a>
					</li>
					<li>
						<a><span class="options"></span><span class="caret"></span></a>
						<ul>
							# IF news.C_EDIT #
							<li>
								<a href="{news.U_EDIT}" title="${LangLoader::get_message('edit', 'main')}" class="img_link">${LangLoader::get_message('edit', 'main')}</a>
							</li>
							# ENDIF #
							# IF news.C_DELETE #
							<li>
								<a href="{news.U_DELETE}" title="${LangLoader::get_message('delete', 'main')}" onclick="javascript:return Confirm();">${LangLoader::get_message('delete', 'main')}</a>
							</li>
							# ENDIF #
						</ul>
					</li>
				</ul>
				
				<h1>
					<a href="{news.U_SYNDICATION}" title="${LangLoader::get_message('syndication', 'main')}" class="img_link">
						<img class="valign_middle" src="{PATH_TO_ROOT}/templates/{THEME}/images/rss.png" alt="${LangLoader::get_message('syndication', 'main')}" />
					</a>
        			<a href="{news.U_LINK}"><span id="name" itemprop="name">{news.NAME}</span></a>
        		</h1>
        		
        		<div class="more">
					Post� par 
					# IF news.PSEUDO #
					<a itemprop="author" class="small_link {news.USER_LEVEL_CLASS}" href="{news.U_AUTHOR_PROFILE}" style="font-size: 12px;" # IF news.C_USER_GROUP_COLOR # style="color:{news.USER_GROUP_COLOR}" # ENDIF #>{news.PSEUDO}</a>, 
					# ENDIF # 
					le <time datetime="{news.DATE_ISO8601}" itemprop="datePublished">{news.DATE}</time>, 
					dans la cat�gorie <a itemprop="about" href="{news.U_CATEGORY}">{news.CATEGORY_NAME}</a>
				</div>
				
        		<meta itemprop="url" content="{news.U_LINK}">
				<meta itemprop="description" content="{news.DESCRIPTION}">
				<meta itemprop="discussionUrl" content="{news.U_COMMENTS}">
				<meta itemprop="interactionCount" content="{news.NUMBER_COMMENTS} UserComments">
				
			</header>
			
			<div class="content">
				# IF news.C_PICTURE #<img itemprop="thumbnailUrl" src="{news.U_PICTURE}" alt="{news.NAME}" title="{news.NAME}" class="right" /># ENDIF #
				<span itemprop="text">{news.CONTENTS}</span>
			</div>
			
			<footer></footer>
		</article>
	# END news #
	<footer>
	# IF C_PAGINATION # # INCLUDE PAGINATION # # ENDIF #
	</footer>
</section>
# ENDIF #