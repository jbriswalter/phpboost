<section id="module-user-users-list">
	<header class="section-header">
		<h1>{@users}</h1>
	</header>
	<div class="sub-section">
		<div class="content-container">
			<div class="content">
				# INCLUDE FORM #
				<div class="cell-flex cell-tile cell-columns-2">
					<div class="jplist-type-filter cell">
						<div class="cell-body">
							<div class="cell-content">
								<span>{@groups.select} :</span>
								<div class="type-filter-radio">
									<div class="selected-label">
										<span>{@groups.all}</span> <i class="fa fa-fw fa-caret-down" aria-hidden="true"></i>
									</div>
									<div class="label-list dropdown-container">
										<label class="jplist-label" for="default-radio">
											<input
												id="default-radio"
												type="radio"
												data-jplist-control="radio-buttons-path-filter"
												data-path="default"
												data-group="users-items"
												name="groups-filter"
												checked /> {@groups.all}
										</label>
										<label class="jplist-label" for="is-administrator">
											<input
												id="is-administrator"
												type="radio"
												data-jplist-control="radio-buttons-path-filter"
												data-path=".is-administrator"
												data-group="users-items"
												name="groups-filter"
												value="${LangLoader::get_message('admin_s', 'main')}"/>${LangLoader::get_message('admin_s', 'main')}
										</label>
										<label class="jplist-label" for="is-moderator">
											<input
												id="is-moderator"
												type="radio"
												data-jplist-control="radio-buttons-path-filter"
												data-path=".is-moderator"
												data-group="users-items"
												name="groups-filter"
												value="${LangLoader::get_message('modo_s', 'main')}"/>${LangLoader::get_message('modo_s', 'main')}
										</label>
										# START groups #
											<label class="jplist-label" for="{groups.GROUP_NAME_FILTER}">
												<input
													id="{groups.GROUP_NAME_FILTER}"
													type="radio"
													data-jplist-control="radio-buttons-path-filter"
													data-path=".{groups.GROUP_NAME_FILTER}"
													data-group="users-items"
													name="groups-filter"
													value="{groups.GROUP_NAME}"/>{groups.GROUP_NAME}
											</label>
										# END groups #
									</div>
								</div>
							</div>
						</div>
					</div>

					<!-- sort dropdown -->
					<div class="sort-list cell">
						<div class="cell-body">
							<div class="cell-content">
								<span>${LangLoader::get_message('sort_by', 'common')} :</span>
								<div
									data-jplist-control="dropdown-sort"
									class="jplist-drop-down"
									data-group="users-items"
									data-name="sorttitle">
									<div data-type="panel" class="jplist-dd-panel"></div>
									<ul data-type="content" class="dropdown-container">
										<li> {@display_name}
											<em class="sort-type" data-path=".jp-name" data-order="asc" data-type="text" data-selected="true"><span class="sr-only">{@display_name} &#8593;</span> <i class="fa fa-sort-alpha-down"></i></em>
											<em class="sort-type" data-path=".jp-name" data-order="desc" data-type="text"><span class="sr-only">{@display_name} &#8595;</span> <i class="fa fa-sort-alpha-down-alt"></i></em>
										</li>
										<li> {@registration_date}
											<em class="sort-type" data-path=".jp-registration-date" data-order="asc" data-type="number"><span class="sr-only">{@registration_date} &#8593;</span> <i class="fa fa-sort-numeric-down"></i></em>
											<em class="sort-type" data-path=".jp-registration-date" data-order="desc" data-type="number"><span class="sr-only">{@registration_date} &#8595;</span> <i class="fa fa-sort-numeric-down-alt"></i></em>
										</li>
										<li> {@last_connection}
											<em class="sort-type" data-path=".jp-last-connection" data-order="asc" data-type="number"><span class="sr-only">{@last_connection} &#8593;</span> <i class="fa fa-sort-numeric-down"></i></em>
											<em class="sort-type" data-path=".jp-last-connection" data-order="desc" data-type="number"><span class="sr-only">{@last_connection} &#8595;</span> <i class="fa fa-sort-numeric-down-alt"></i></em>
										</li>
										<li> {@publications.number}
											<em class="sort-type" data-path=".jp-publications-number" data-order="asc" data-type="number"><span class="sr-only">{@publications.number} &#8593;</span> <i class="fa fa-sort-numeric-down"></i></em>
											<em class="sort-type" data-path=".jp-publications-number" data-order="desc" data-type="number"><span class="sr-only">{@publications.number} &#8595;</span> <i class="fa fa-sort-numeric-down-alt"></i></em>
										</li>
									</ul>
								</div>
							</div>
						</div>
					</div>
				</div>

				# IF C_TABLE_VIEW #
					<div class="spacer"></div>
					<div class="responsive-table">
						<table class="table">
							<thead>
								<tr>
									<th>{@display_name}</th>
									<th>{@registration_date}</th>
									<th>{@last_connection}</th>
									<th>{@publications.number}</th>
									<th>${LangLoader::get_message('contact', 'main')}</th>
									<th>${LangLoader::get_message('groups', 'main')}</th>
									# IF IS_ADMIN #<th></th># ENDIF #
								</tr>
							</thead>
							<tbody data-jplist-group="users-items">
								# START users #
									<tr data-jplist-item>
										<td class="jp-name">
											<a href="{users.U_PROFILE}"# IF users.C_IS_GROUP # style="color: {users.GROUP_COLOR};"# ELSE # class="{users.LEVEL_COLOR}"# ENDIF #>{users.DISPLAYED_NAME}</a>
										</td>
										<td>{users.REGISTRATION_DATE}<span class="jp-registration-date hidden">{users.REGISTRATION_DATE_TIMESTAMP}</span></td>
										<td>{users.LAST_CONNECTION}<span class="jp-last-connection hidden">{users.LAST_CONNECTION_TIMESTAMP}</span></td>
										<td class="jp-publications-number" aria-label="# START users.modules # {users.modules.MODULE_NAME}: {users.modules.MODULE_PUBLICATIONS_NUMBER}<br /># END users.modules #">
											{users.PUBLICATIONS_NUMBER}
										</td>
										<td>
											<a href="{users.U_MP}" class="pinned bgc-full notice" aria-label="{@private_message}"}><i class="fa fa-fw fa-people-arrows"></i></a>
											# IF users.C_ENABLED_EMAIL #
												<span><a href="mailto:{users.U_EMAIL}" class="pinned bgc-full member" aria-label="{@email}"><i class="iboost fa fa-iboost-email"></i></a></span>
											# ENDIF #
											# IF users.C_HAS_WEBSITE #
												<a href="{users.U_WEBSITE}" class="pinned bgc-full link-color" aria-label="${LangLoader::get_message('regex.website', 'admin-user-common')}"><i class="fa fa-globe"></i></a>
											# ENDIF #
										</td>
										<td>
											# IF users.C_CONTROLS #<span class="pinned small is-{users.LEVEL_COLOR}">{users.RANK_LEVEL}</span># ENDIF #
											# START users.groups #
												<span class="pinned small {users.groups.GROUP_NAME_FILTER}" data-color-surround="{users.groups.GROUP_COLOR}">{users.groups.GROUP_NAME}</span><br />
											# END users.groups #
										</td>
										# IF IS_ADMIN #
											<td>
												<a href="{users.U_EDIT}" aria-label="${LangLoader::get_message('edit', 'common')}"><i class="far fa-edit"></i></a>
												# IF users.C_DELETE #<a href="{users.U_DELETE}" aria-label="${LangLoader::get_message('delete', 'common')}" data-confirmation="delete-element"><i class="far fa-trash-alt"></i></a># ENDIF #
											</td>
										# ENDIF #
									</tr>
								# END users #
							</tbody>
						</table>
					</div>
				# ELSE #
					<div class="cell-flex cell-tile cell-columns-2 user-card " data-jplist-group="users-items">
						# START users #
							<article data-jplist-item class="cell">
								<header class="cell-header">
									<h5 class="cell-name jp-name">
										<a href="{users.U_PROFILE}"# IF users.C_IS_GROUP # style="color: {users.GROUP_COLOR};"# ELSE # class="{users.LEVEL_COLOR} is-{users.LEVEL_COLOR}"# ENDIF #>{users.DISPLAYED_NAME}</a>
										# IF users.C_CONTROLS #<span class="description-field smaller">{users.RANK_LEVEL}</span># ENDIF #
									</h5>
									# IF C_ENABLED_AVATAR #<img class="user-card-avatar" src="{users.U_AVATAR}" alt="{users.DISPLAYED_NAME}"># ENDIF #
								</header>
								<div class="cell-list">
									<ul>
										<li class="li-stretch">
											<span class="small">{@registration_date}<span class="jp-registration-date hidden">{users.REGISTRATION_DATE_TIMESTAMP}</span></span>
											<span>{users.REGISTRATION_DATE}</span>
										</li>
										<li class="li-stretch">
											<span class="small">{@last_connection}<span class="jp-last-connection hidden">{users.LAST_CONNECTION_TIMESTAMP}</span></span>
											<span>{users.LAST_CONNECTION}</span>
										</li>
										<li class="li-stretch jp-publications-number" aria-label="# START users.modules # {users.modules.MODULE_NAME}: {users.modules.MODULE_PUBLICATIONS_NUMBER}<br /># END users.modules #">
											<span class="small">{@publications.number}</span>
											<span>{users.PUBLICATIONS_NUMBER}</span>
										</li>
										<li class="li-stretch">
											<span class="small">${LangLoader::get_message('contact', 'main')}</span>
											<span>
												<a href="{users.U_MP}" class="pinned bgc-full notice" aria-label="{@private_message}"}><i class="fa fa-fw fa-people-arrows"></i></a>
												# IF users.C_ENABLED_EMAIL #
													<span><a href="mailto:{users.U_EMAIL}" class="pinned bgc-full member" aria-label="{@email}"><i class="iboost fa fa-iboost-email"></i></a></span>
												# ENDIF #
												# IF users.C_HAS_WEBSITE #
													<a href="{users.U_WEBSITE}" class="pinned bgc-full link-color" aria-label="${LangLoader::get_message('regex.website', 'admin-user-common')}"><i class="fa fa-globe"></i></a>
												# ENDIF #
											</span>
										</li>
										# IF users.C_HAS_GROUP #
											<li class="li-stretch">
												<span class="small">${LangLoader::get_message('groups', 'main')}</span>
												<span>
													# START users.groups #<span class="pinned small {users.groups.GROUP_NAME_FILTER}" data-color-surround="{users.groups.GROUP_COLOR}">{users.groups.GROUP_NAME}</span><br /># END users.groups #
												</span>
											</li>
										# ENDIF #
										# IF IS_ADMIN #
											<li class="li-stretch controls">
												<a href="{users.U_EDIT}" aria-label="${LangLoader::get_message('edit', 'common')}"><i class="far fa-edit"></i></a>
												# IF users.C_DELETE #<a href="{users.U_DELETE}" aria-label="${LangLoader::get_message('delete', 'common')}" data-confirmation="delete-element"><i class="far fa-trash-alt"></i></a># ENDIF #
											</li>
										# ENDIF #
									</ul>
								</div>
							</article>
						# END users #
					</div>
				# ENDIF #
			</div>
		</div>
	</div>

	# IF C_PAGINATION #
		<div class="sub-section">
			<div class="content-container">
				<div class="pagination options no-style">
					<p class="align-center"></p>
					<div
				   	class="jplist-pagination"
				   	data-jplist-control="pagination"
			        data-group="users-items"
			        data-items-per-page="{ITEMS_PER_PAGE}"
			        data-current-page="0"
			        data-name="pagination1"
				   	data-name="paging">
						<button class="button small" type="button" data-type="first" aria-label="${LangLoader::get_message('pagination.first', 'common')}"><i class="fa fa-chevron-circle-left" aria-hidden="true"></i> </button>
					    <button class="button small" type="button" data-type="prev" aria-label="${LangLoader::get_message('pagination.previous', 'common')}"><i class="fa fa-chevron-left" aria-hidden="true"></i> </button>

					    <div class="jplist-holder" data-type="pages">
					        <button type="button" class="button small" data-type="page">{L_PAGE_NUMBER}</button>
					    </div>

					    <button class="button small" type="button" data-type="next" aria-label="${LangLoader::get_message('pagination.next', 'common')}"><i class="fa fa-chevron-right" aria-hidden="true"></i> </button>
					    <button class="button small" type="button" data-type="last" aria-label="${LangLoader::get_message('pagination.last', 'common')}"><i class="fa fa-chevron-circle-right" aria-hidden="true"></i> </button>

			            <select data-type="items-per-page">
			                <option value="{ITEMS_PER_PAGE}"> {ITEMS_PER_PAGE} ${LangLoader::get_message('pagination.per', 'common')}</option>
			                <option value="50"> 50 ${LangLoader::get_message('pagination.per', 'common')}</option>
			                <option value="100"> 100 ${LangLoader::get_message('pagination.per', 'common')}</option>
			                <option value="0"> {@members.all} </option>
			            </select>
					</div>
				</div>
				<div class="spacer"></div>
			</div>
		</div>
	# ENDIF #
	<footer></footer>
</section>

<script>
	jQuery('document').ready(function(){
		// jpList
		jplist.init();

		// Filters
			// toggle sub-menu on click (close on click outside)
		jQuery('.selected-label').on('click', function(e){
			jQuery('.label-list').toggleClass('reveal-list');
    		e.stopPropagation();
		});
		jQuery(document).click(function(e) {
		    if (jQuery(e.target).is('.selected-label') === false) {
		      jQuery('.label-list').removeClass('reveal-list');
		    }
		});
			// send label text of selected input to title on click
		jQuery('.label-list input').on('click', function(e) {
		    var radioText = e.currentTarget.nextSibling.data;
		    jQuery('.selected-label span').html(radioText);
		});
	});
</script>
