		<script type="text/javascript">
		<!--
			function check_form_list()
			{
				if(document.getElementById('name').value == "") {
					alert("{L_REQUIRE}");
					return false;
			    }		
				return true;
			}
		-->
		</script>
		
		<div id="admin_quick_menu">
			<ul>
				<li class="title_menu">{L_GALLERY_MANAGEMENT}</li>
				<li>
					<a href="admin_gallery.php"><img src="gallery.png" alt="" /></a>
					<br />
					<a href="admin_gallery.php" class="quick_link">{L_GALLERY_MANAGEMENT}</a>
				</li>
				<li>
					<a href="admin_gallery_add.php"><img src="gallery.png" alt="" /></a>
					<br />
					<a href="admin_gallery_add.php" class="quick_link">{L_GALLERY_PICS_ADD}</a>
				</li>
				<li>
					<a href="admin_gallery_cat.php"><img src="gallery.png" alt="" /></a>
					<br />
					<a href="admin_gallery_cat.php" class="quick_link">{L_GALLERY_CAT_MANAGEMENT}</a>
				</li>
				<li>
					<a href="admin_gallery_cat_add.php"><img src="gallery.png" alt="" /></a>
					<br />
					<a href="admin_gallery_cat_add.php" class="quick_link">{L_GALLERY_CAT_ADD}</a>
				</li>
				<li>
					<a href="admin_gallery_config.php"><img src="gallery.png" alt="" /></a>
					<br />
					<a href="admin_gallery_config.php" class="quick_link">{L_GALLERY_CONFIG}</a>
				</li>
			</ul>
		</div>

		<div id="admin_contents">
			# INCLUDE message_helper #
				
			<form action="admin_gallery_cat.php?token={TOKEN}" method="post" class="fieldset_content">
				<fieldset>
					<legend>{L_EDIT_CAT}</legend>
					<p>{L_REQUIRE}</p>
					<p><strong>{L_ROOT}</strong></p>
					<div class="form-element">
						<label>{L_AUTH_READ}</label>
						<div class="form-field">{AUTH_READ}</div>
					</div>
					<div class="form-element">
						<label>{L_AUTH_WRITE}</label>
						<div class="form-field">{AUTH_WRITE}</div>
					</div>
					<div class="form-element">
						<label>{L_AUTH_EDIT}</label>
						<div class="form-field">{AUTH_EDIT}</div>
					</div>
				</fieldset>
				
				<fieldset class="fieldset-submit">
					<legend>{L_UPDATE}</legend>
					<button type="submit" name="valid_root" value="true">{L_UPDATE}</button>
					&nbsp;&nbsp; 
					<button type="reset" value="true">{L_RESET}</button>
				</fieldset>	
			</form>
		</div>
