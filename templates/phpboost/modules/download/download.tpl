
		<div class="module_position">			
			<div class="module_top_l"></div>		
			<div class="module_top_r">
				# IF C_ADD_FILE #
					<div style="float:right;padding-top:5px;">
						<a href="{U_ADD_FILE}" title="{L_ADD_FILE}">
							<img src="{PATH_TO_ROOT}/templates/{THEME}/images/french/add.png" alt="{L_ADD_FILE}" />
						</a>
					</div>
				# ENDIF #
				# IF C_ADMIN #
					<div style="float:right;padding-right:5px;">
						<a href="{U_ADMIN_CAT}">
							<img class="valign_middle" src="{PATH_TO_ROOT}/templates/{THEME}/images/{LANG}/edit.png" alt="" />
						</a>
					</div>
				# ENDIF #
			</div>
			<div class="module_top">
				<a href="${relative_url(SyndicationUrlBuilder::rss('download',IDCAT))}" title="Rss"><img style="vertical-align:middle;margin-top:-2px;" src="{PATH_TO_ROOT}/templates/{THEME}/images/rss.png" alt="Rss" title="Rss" /></a>
				{TITLE}
			</div>
			<div class="module_contents">

				# IF C_DESCRIPTION #
					<!-- {DESCRIPTION} -->
				# ENDIF #
				<div class="download_entete">
					<img class="download_entete_img" src="{PATH_TO_ROOT}/templates/{THEME}/theme/images/logo.png" alt="" />
					<div class="download_entete_content">
						<p class="download_entete_title">PHPBOOST</p>
						<span class="download_entete_desc">
						Bienvenue sur la page de t�l�chargement de PHPBoost.
						<br /><br />PHPBoost est un logiciel libre distribu� sous licence GNU/GPL.
						<br /><br />PHPBoost 3 innove dans sa fa�on d'�tre distribu�. En effet Tornade est la premi�re version de PHPBoost � �tre distribu�e de diff�rentes fa�ons pour �tre � m�me de s'adapter tr�s rapidement aux besoins de chacun. Aujourd'hui, quatre distributions existent, et vous sont pr�sent�es dans la liste ci-dessous. Vous pourrez les t�l�charger en cliquant sur leur lien associ�.</span>
					</div>
				</div>
				<hr style="margin:25px 0px;" />
				
				Cette page vous proposera de t�l�charger diff�rentes version de PHPBoost, des mises � jours, ou d'acceder � nos sites de test.
				<br /><br />
				<ul class="bb_ul">
					<li class="bb_li">PHPBoost 4.0 est la derni�re version du CMS (Stable)</li>
					<li class="bb_li">PHPBoost 3.x est la version du CMS ayant fait ces preuves, mais ne poss�dant pas les nombreux avantages de la version 4.0</li>
					<li class="bb_li">PHPBoost Archives regroupera toutes les version ant�rieurs � la 3.0 Uniquement pour les nostalgiques</li>
					<li class="bb_li">Vous trouverez aussi des versions "En cours / Unstable" permettant au developpeurs de tester et de contribuer au projet.</li>
				</ul>
				<hr style="margin:25px auto 25px auto;" />
				
				
				<div class="download_container">
				
					<div class="download_content block_container">
					
						<div class="download_entete_content">
							<p class="download_entete_title">T�l�charger PHPBoost 4.0</p>
							<span class="download_entete_desc">
							C'est la versions conseill�e pour tous les nouveaux membres et ceux qui souhaitent b�n�ficier de toute la puissance de PHPBoost.
							</span>
						</div>
						
						<div class="d_button_container">
							<div class="d_button d_button_ddl">
								<a href="#" class="d_button_a">
									<img class="d_button_img" src="{PATH_TO_ROOT}/templates/{THEME}/theme/images/logo.png" alt="" />
									<p class="d_button_title">T�l�charger PHPBoost 4.0</p>
									<p class="d_button_com">R�v. 4.0.0 | PHP 5.1.2 | Ver. Compl�te | Zip </p>
								</a>
							</div>
							
							<div class="d_button d_button_try">
								<a href="#" class="d_button_a">
									<img class="d_button_img" src="{PATH_TO_ROOT}/templates/{THEME}/theme/images/logo.png" alt="" />
									<p class="d_button_title">Mises � jour 4.0</p>
									<p class="d_button_com d_button_com_try">Mises � jour et migration</p>
								</a>
							</div>
						</div>
						
						<div class="d_link_container">
							<a href="#"><h2 class="title" >T�l�charger des modules pour PHPBoost 4.0</h2></a>
							<span class="download_entete_desc">Les derniers modules pour la r�vision 4.0</span>
							<ul class="bb_ul" style="padding-left:20px;">
								<li class="bb_li"><a href="#">Calendrier</a></li>
								<li class="bb_li"><a href="#">Forum</a></li>
							</ul>
						</div>
						<div class="d_link_container">
							<a href="#"><h2 class="title" >T�l�charger des themes  pour PHPBoost 4.0</h2></a>
							<span class="download_entete_desc">Les derniers th�mes pour la r�vision 4.0</span>
							<ul class="bb_ul" style="padding-left:20px;">
								<li class="bb_li"><a href="#">lorem</a></li>
								<li class="bb_li"><a href="#">Sangoten</a></li>
							</ul>
						</div>	
																	
					</div>
															
					<div class="download_content block_container">
						<div class="download_entete_content">
							<p class="download_entete_title">T�l�charger PHPBoost 3.0</p>
							<span class="download_entete_desc">Derni�re r�vision stable de la 3.0</span>
						</div>

						
						<div class="d_button_container">
							<div class="d_button d_button_ddl">
								<a href="#" class="d_button_a">
									<img class="d_button_img" src="{PATH_TO_ROOT}/templates/{THEME}/theme/images/logo.png" alt="" />
									<p class="d_button_title">T�l�charger PHPBoost 3.0</p>
									<p class="d_button_com">R�v. 3.0.11 | PHP 4 | Ver. Compl�te | Zip </p>
								</a>
							</div>
							
							<div class="d_button d_button_try">
								<a href="#" class="d_button_a">
									<img class="d_button_img" src="{PATH_TO_ROOT}/templates/{THEME}/theme/images/logo.png" alt="" />
									<p class="d_button_title">Mises � jour 3.0</p>
									<p class="d_button_com d_button_com_try">Mises � jour et migration</p>
								</a>
							</div>
						</div>
						
						<div class="d_link_container">
							<a href="#"><h2 class="title" >T�l�charger des modules pour PHPBoost 3.0</h2></a>
							<span class="download_entete_desc">Les derniers modules pour la r�vision 3.0</span>
							<ul class="bb_ul" style="padding-left:20px;">
								<li class="bb_li"><a href="#">Calendrier</a></li>
								<li class="bb_li"><a href="#">Forum</a></li>
							</ul>
						</div>
						<div class="d_link_container">
							<a href="#"><h2 class="title" >T�l�charger des themes  pour PHPBoost 3.0</h2></a>
							<span class="download_entete_desc">Les derniers th�mes pour la r�vision 3.0</span>
							<ul class="bb_ul" style="padding-left:20px;">
								<li class="bb_li"><a href="#">lorem</a></li>
								<li class="bb_li"><a href="#">Sangoten</a></li>
							</ul>
						</div>	
						
					</div>
					
					<hr style="margin:20px auto 30px auto;" />
					
					<div style="text-align:center;">	
						<div class="d_button d_button_sea">
							<a href="#" class="d_button_a">
								<p class="d_button_title">Parcourir l'arborescence</p>
							</a>
						</div>
					</div>
				</div>
			</div>
		</div>