		<div id="admin_quick_menu">			<ul>				<li class="title_menu">{L_PAGES}</li>				<li>					<a href="admin_pages.php?token={TOKEN}"><img src="pages.png" alt="" /></a>					<br />					<a href="admin_pages.php" class="quick_link">{L_PAGES_CONGIG}</a>				</li>				<li>					<a href="pages.php"><img src="pages.png" alt="" /></a>					<br />					<a href="pages.php" class="quick_link">{L_PAGES_MANAGEMENT}</a>				</li>			</ul>		</div>				<div id="admin_contents">					<form action="admin_pages.php?token={TOKEN}" method="post">				<fieldset>					<legend>						{L_PAGES_CONGIG}					</legend>					<dl>						<dt>							<label for="count_hits">								{L_COUNT_HITS}								<br />								<span class="smaller">({L_COUNT_HITS_EXPLAIN})</span>							</label>						</dt>						<dd>							<input type="checkbox" name="count_hits" {HITS_CHECKED}>						</dd>					</dl>					<dl> 						<dt>							<label for="comments_activated">								{L_COMMENTS_ACTIVATED}							</label>						</dt>						<dd> 							<input type="checkbox" name="comments_activated" {COM_CHECKED}>						</dd>					</dl>				</fieldset>				<fieldset>					<legend>						{L_AUTH}					</legend>					<dl>						<dt>							<label>{L_READ_PAGE}</label>						</dt>						<dd>							{SELECT_READ_PAGE}						</dd>					</dl>					<dl>						<dt>							<label>{L_EDIT_PAGE}</label>						</dt>						<dd>							{SELECT_EDIT_PAGE}						</dd>					</dl>					<dl>						<dt>							<label>{L_READ_COM}</label>						</dt>						<dd>							{SELECT_READ_COM}						</dd>					</dl>				</fieldset>								<fieldset class="fieldset_submit">					<legend>{L_UPDATE}</legend>					<input type="submit" name="update" value="{L_UPDATE}" class="submit">					&nbsp;&nbsp; 					<input type="reset" value="{L_RESET}" class="reset">								</fieldset>				</form>		</div>		