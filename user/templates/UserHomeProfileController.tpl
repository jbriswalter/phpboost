<section id="module-user-home">
	<header class="section-header">
		<h1>{@dashboard}</h1>
	</header>
	<div class="sub-section">
		<div class="content">
			<p class="align-center text-strong">{@welcome} {PSEUDO}</p>
			# IF C_AVATAR_IMG #
				<p class="align-center">
					<img src="{U_AVATAR_IMG}" alt="{@avatar}" />
				</p>
			# ENDIF #
		</div>

		<div id="profile-container" class="cell-flex cell-tile cell-columns-3">
			<article class="dashboard-item several-items cell">
				<div class="cell-body">
					<div class="cell-content align-center">
						<a href="{U_VIEW_PROFILE}">
							<i class="fa fa-user fa-2x" aria-hidden="true"></i>
							<span class="d-block">${LangLoader::get_message('my_private_profile', 'main')}</span>
						</a>
					</div>
				</div>
			</article>
			<article class="dashboard-item several-items cell">
				<div class="cell-body">
					<div class="cell-content align-center">
						<a href="{U_USER_PM}">
							# IF C_HAS_PM #
								<span class="stacked blink">
									<i class="fa fa-people-arrows fa-2x" aria-hidden="true"></i>
									<span class="stack-event stack-right stack-circle bgc-full error text-strong">{NUMBER_PM}</span>
								</span>
							# ELSE #
								<i class="fa fa-people-arrows fa-2x"></i>
							# END IF #
							<span class="d-block">${LangLoader::get_message('private_message', 'main')}</span>
						</a>
					</div>
				</div>
			</article>
			# IF C_USER_AUTH_FILES #
				<article class="dashboard-item several-items cell">
					<div class="cell-body">
						<div class="cell-content align-center">
							<a href="{U_UPLOAD}">
								<i class="fa fa-cloud-upload-alt fa-2x" aria-hidden="true"></i>
								<span class="d-block">${LangLoader::get_message('files_management', 'main')}</span>
							</a>
						</div>
					</div>
				</article>
			# ENDIF #
			# IF IS_ADMIN #
				<article class="dashboard-item several-items cell">
					<div class="cell-body">
						<div class="cell-content align-center">
							<a href="{PATH_TO_ROOT}/admin/">
								# IF C_UNREAD_ALERT #
									<span class="stacked blink">
										<i class="fa fa-wrench fa-2x" aria-hidden="true"></i>
										<span class="stack-event stack-right stack-circle bgc-full error text-strong">{NUMBER_UNREAD_ALERTS}</span>
									</span>
								# ELSE #
									<i class="fa fa-wrench fa-2x" aria-hidden="true"></i>
								# ENDIF #
								<span class="d-block">${LangLoader::get_message('admin_panel', 'main')}</span>
							</a>
						</div>
					</div>
				</article>
			# ENDIF #
			# IF C_IS_MODERATOR #
				<article class="dashboard-item several-items cell">
					<div class="cell-body">
						<div class="cell-content align-center">
							<a href="{U_MODERATION_PANEL}">
								<i class="fa fa-gavel fa-2x" aria-hidden="true"></i>
								<span class="d-block">${LangLoader::get_message('moderation_panel', 'main')}</span>
							</a>
						</div>
					</div>
				</article>
			# ENDIF #
			<article class="dashboard-item several-items cell">
				<div class="cell-body">
					<div class="cell-content align-center">
						<a href="{U_CONTRIBUTION_PANEL}">
							# IF C_KNOWN_NUMBER_OF_UNREAD_CONTRIBUTION #
								<span class="stacked blink">
									<i class="fa fa-file-alt fa-2x" aria-hidden="true"></i>
									<span class="stack-event stack-right stack-circle bgc-full error text-strong">{NUMBER_UNREAD_CONTRIBUTIONS}</span>
								</span>
							# ELSE #
								<i class="fa fa-file-alt fa-2x" aria-hidden="true"></i>
							# ENDIF #
							<span class="d-block">${LangLoader::get_message('contribution_panel', 'main')}</span>
						</a>
					</div>
				</div>
			</article>
			# START modules_messages #
				<article class="dashboard-item several-items cell">
					<div class="cell-body">
						<div class="cell-content align-center">
							<a href="{modules_messages.U_LINK_USER_MSG}">
								# IF modules_messages.C_IMG_USER_MSG #<i class="{modules_messages.IMG_USER_MSG} fa-2x" aria-hidden="true"></i># ENDIF #
								<span class="d-block">{modules_messages.NAME_USER_MSG} : {modules_messages.NUMBER_MESSAGES}</span>
							</a>
						</div>
					</div>
				</article>
			# END modules_messages #
			<article class="dashboard-item several-items cell">
				<div class="cell-body">
					<div class="cell-content align-center">
						<a href="${relative_url(UserUrlBuilder::disconnect())}">
							<i class="fa fa-sign-out-alt fa-2x" aria-hidden="true"></i>
							<span class="d-block">{@disconnect}</span>
						</a>
					</div>
				</div>
			</article>
		</div>
		<div class="content">
			{MSG_MBR}
		</div>

	</div>
	<footer></footer>
</section>
