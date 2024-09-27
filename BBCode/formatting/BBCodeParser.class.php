<?php
/**
 * Converts the PHPBoost BBCode language to the XHTML language which is stocked in
 * the database and can be displayed nearly directly.
 * It parses only the authorized tags (defined in the parent class which is ContentFormattingParser).
 * @copyright   &copy; 2005-2024 PHPBoost
 * @license     https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL-3.0
 * @author      Benoit SAUTEL <ben.popeye@phpboost.com>
 * @version     PHPBoost 6.0 - last update: 2023 02 19
 * @since       PHPBoost 2.0 - 2008 07 03
 * @contributor Julien BRISWALTER <j1.seth@phpboost.com>
 * @contributor Arnaud GENET <elenwii@phpboost.com>
 * @contributor mipel <mipel@phpboost.com>
 * @contributor janus57 <janus57@janus57.fr>
 * @contributor Sebastien LARTIGUE <babsolune@phpboost.com>
 * @contributor xela13 <xela@phpboost.com>
*/

class BBCodeParser extends ContentFormattingParser
{
	/**
	 * @desc Parses the parser content from BBCode to XHTML.
	 * @return void You will find the result by using the get_content method
	 */
	public function parse()
	{
		//On remplace les tabulations par des espaces
		$this->content = preg_replace('`\t`suU', '    ', $this->content);

		//On supprime d'abord toutes les occurences de balises CODE que nous réinjecterons à la fin pour ne pas y toucher
		if (!in_array('code', $this->forbidden_tags))
		{
			$this->pick_up_tag('code', '=[^,\s]+(?:,[01]){0,2}');
		}

		//On decode le contenu
		$this->content = TextHelper::html_entity_decode($this->content);

		//On prélève tout le code HTML afin de ne pas l'altérer
		if (!in_array('html', $this->forbidden_tags) && AppContext::get_current_user()->check_auth($this->html_auth, 1))
		{
			$this->pick_up_tag('html');
		}

		//Ajout des espaces pour éviter l'absence de parsage lorsqu'un séparateur de mot est éxigé
		$this->content = ' ' . $this->content . ' ';

		//Traitement du code HTML
		$this->protect_content();

		//Traitement des smilies
		$this->parse_smilies();

		//Interprétation des sauts de ligne
		$this->content = str_replace(array(' <br />', ' <br>', ' <br/>'), '<br />', nl2br($this->content));

		//Module eventual special tags replacement
		$this->parse_module_special_tags();

		// BBCode simple tags
		$this->parse_simple_tags();

		//Tableaux
		if (!in_array('table', $this->forbidden_tags) && TextHelper::strpos($this->content, '[table') !== false)
		{
			$this->parse_table();
		}

		//Listes
		if (!in_array('list', $this->forbidden_tags)&& TextHelper::strpos($this->content, '[list') !== false)
		{
			$this->parse_list();
		}

		parent::parse();

		//On remet le code HTML mis de côté
		if (!empty($this->array_tags['html']))
		{
			$this->array_tags['html'] = array_map(function($string) {return str_replace("[html]", "<!-- START HTML -->\n", str_replace("[/html]", "\n<!-- END HTML -->", $string));}, $this->array_tags['html']);
			$this->reimplant_tag('html');
		}

		//On réinsère les fragments de code qui ont été prévelevés pour ne pas les considérer
		if (!empty($this->array_tags['code']))
		{
			$this->array_tags['code'] = array_map(function($string) {return preg_replace('`^\[code(=.+)?\](.+)\[/code\]$`isuU', '[[CODE$1]]$2[[/CODE]]', TextHelper::htmlspecialchars($string, ENT_NOQUOTES));}, $this->array_tags['code']);
			$this->reimplant_tag('code');
		}
	}

	/**
	 * @desc Protects the incoming content:
	 * <ul>
	 * 	<li>Breaks all HTML tags and javascript code</li>
	 * 	<li>Accepts only the special character's entitites</li>
	 * 	<li>Treats the Word pasted characters</li>
	 * </ul>
	 */
	protected function protect_content()
	{
		//Breaking the HTML code
		$this->content = TextHelper::htmlspecialchars($this->content, ENT_NOQUOTES);
		$this->content = strip_tags($this->content);

		//While we aren't in UTF8 encoding, we have to use HTML entities to display some special chars, we accept them.
		$this->content = preg_replace('`&amp;((?:#[0-9]{2,5})|(?:[a-z0-9]{2,8}));`iu', "&$1;", $this->content);

		//Treatment of the Word pasted characters
		$array_str = array(
			'€', '‚', 'ƒ', '„', '…', '†', '‡', 'ˆ', '‰',
			'Š', '‹', 'Œ', 'Ž', '‘', '’', '“', '”', '•',
			'–', '—',  '˜', '™', 'š', '›', 'œ', 'ž', 'Ÿ'
			);

			$array_str_replace = array(
			'&#8364;', '&#8218;', '&#402;', '&#8222;', '&#8230;', '&#8224;', '&#8225;', '&#710;', '&#8240;',
			'&#352;', '&#8249;', '&#338;', '&#381;', '&#8216;', '&#8217;', '&#8220;', '&#8221;', '&#8226;',
			'&#8211;', '&#8212;', '&#732;', '&#8482;', '&#353;', '&#8250;', '&#339;', '&#382;', '&#376;'
			);
			$this->content = str_replace($array_str, $array_str_replace, $this->content);
	}

	/**
	 * @desc Replaces the smiley's code by the corresponding HTML image tag
	 */
	protected function parse_smilies()
	{
		$smileys_cache = SmileysCache::load()->get_smileys();
		if (!empty($smileys_cache))
		{
			//Création du tableau de remplacement.
			foreach ($smileys_cache as $code => $infos)
			{
				$smiley_code[] = '`(?:(?![a-z0-9]))(?<!&[a-z]{4}|&[a-z]{5}|&[a-z]{6}|")(' . preg_quote($code) . ')(?:(?![a-z0-9]))`';
				$smiley_img_url[] = '<img src="/images/smileys/' . $infos['url_smiley'] . '" alt="' . addslashes($code) . '" class="smiley" />';
			}
			$this->content = preg_replace($smiley_code, $smiley_img_url, $this->content);
		}
	}

	/**
	 * @desc Parses all BBCode simple tags.
	 * The simple tags are those which can be treated enough requiring many different treatments.
	 * The not simple tags are [code], [html], [table] and its content [row] [col] [head], [list].
	 */
	protected function parse_simple_tags()
	{
		$array_preg = array(
			'b' => '`\[b\](.+)\[/b\]`isuU',
			'strong' => '`\[strong\](.+)\[/strong\]`isuU',
			'i' => '`\[i\](.+)\[/i\]`isuU',
			'em' => '`\[em\](.+)\[/em\]`isuU',
			'u' => '`\[u\](.+)\[/u\]`isuU',
			's' => '`\[s\](.+)\[/s\]`isuU',
			'del' => '`\[del\](.+)\[/del\]`isuU',
			'p' => '`\[p\](.+)\[/p\]`isuU',
			'q' => '`\[q\](.+)\[/q\]`isuU',
			'c' => '`\[c\](.+)\[/c\]`isuU',
			'sup' => '`\[sup\](.+)\[/sup\]`isuU',
			'sub' => '`\[sub\](.+)\[/sub\]`isuU',
			'ins' => '`\[ins\](.+)\[/ins\]`isuU',
			'cite' => '`\[cite\](.+)\[/cite\]`isuU',
			'mark' => '`\[mark\](.+)\[/mark\]`isuU',
			'color' => '`\[color=((?:white|black|red|green|blue|yellow|purple|orange|maroon|pink)|(?:#[0-9a-f]{6}))\](.+)\[/color\]`isuU',
			'bgcolor' => '`\[bgcolor=((?:white|black|red|green|blue|yellow|purple|orange|maroon|pink)|(?:#[0-9a-f]{6}))\](.+)\[/bgcolor\]`isuU',
			'size' => '`\[size=([1-9]|(?:[1-4][0-9]))\](.+)\[/size\]`isuU',
			'font' => '`\[font=(andale mono|arial(?: black)?|book antiqua|comic sans ms|courier(?: new)?|georgia|helvetica|impact|symbol|tahoma|terminal|times new roman|trebuchet ms|verdana|webdings|wingdings)\](.+)\[/font\]`isuU',
			'pre' => '`\[pre\](.+)\[/pre\]`isuU',
			'align' => '`\[align=(left|center|right|justify)\](.+)\[/align\]`isuU',
			'float' => '`\[float=(left|right)\](.+)\[/float\]`isuU',
			'anchor' => '`\[anchor=([a-z_][a-z0-9_-]*)\](.*)\[/anchor\]`isuU',
			'acronym' => '`\[acronym\](.*)\[/acronym\]`isuU',
			'acronym2' => '`\[acronym=([^\n[\]<]+)\](.*)\[/acronym\]`isuU',
			'abbr' => '`\[abbr\](.*)\[/abbr\]`isuU',
			'abbr2' => '`\[abbr=([^\n[\]<]+)\](.*)\[/abbr\]`isuU',
			'style' => '`\[style=(success|question|notice|warning|error)\](.+)\[/style\]`isuU',
			'movie' => '`\[movie=([0-9]{1,3}),([0-9]{1,3})\]([a-z0-9_+.:?/=#%@&;,-]*)\[/movie\]`iuU',
			'movie2' => '`\[movie=([0-9]{1,3}),([0-9]{1,3}),([a-z0-9_+.:?/=#%@&;,-]*)\]([a-z0-9_+.:?/=#%@&;,-]*)\[/movie\]`iuU',
			'sound' => '`\[sound\]([a-z0-9_+.:?/=#%@&;,-]*)\[/sound\]`iuU',
			'math' => '`\[math\](.+)\[/math\]`iuU',
			'mail' => '`(?<=\s|^)([a-zA-Z0-9._-]+@[a-z0-9._-]{2,}\.[a-z]{2,4})(?=\s|\n|\r|<|$)`iuU',
			'mail2' => '`\[mail=([a-zA-Z0-9._-]+@[a-z0-9._-]{2,}\.[a-z]{2,4})\]([^\n\r\t\f]+)\[/mail\]`iuU',
			'url1' => '`\[url\]((?!javascript:)' . Url::get_wellformness_regex() . ')\[/url\]`isuU',
			'url2' => '`\[url=((?!javascript:)' . Url::get_wellformness_regex() . ')\](.*)\[/url\]`isuU',
			'url3' => '`\[url=((?!javascript:)' . Url::get_wellformness_regex() . ')\]\[/url\]`isuU',
			'url4' => '`(\s+)(' . Url::get_wellformness_regex(RegexHelper::REGEX_MULTIPLICITY_REQUIRED) . ')<`isuU',
			'url5' => '`(\s+)(' . Url::get_wellformness_regex(RegexHelper::REGEX_MULTIPLICITY_REQUIRED) . ')(\s|<+)`isuU',
			'url6' => '`(\s+)\((' . Url::get_wellformness_regex(RegexHelper::REGEX_MULTIPLICITY_REQUIRED) . ')\)(\s|<+)`isuU',
			'url7' => '`(\s+)\((' . Url::get_wellformness_regex(RegexHelper::REGEX_MULTIPLICITY_REQUIRED) . ') \)(\s|<+)`isuU',
			'youtube1' => '`\[youtube=([0-9]{1,3}),([0-9]{1,3})\]((?:https?)://([a-z0-9-]+\.)*[a-z0-9-]+\.[a-z]{2,4}+(?:[a-z0-9~_-]+/)*(?:[a-z0-9_+.:?/=#%@&;,-])*)\[/youtube\]`iuU',
			'youtube2' => '`\[youtube\]((?:https?)://([a-z0-9-]+\.)*[a-z0-9-]+\.[a-z]{2,4}+(?:[a-z0-9~_-]+/)*(?:[a-z0-9_+.:?/=#%@&;,-])*)\[/youtube\]`iuU',
			'dailymotion1' => '`\[dailymotion=([0-9]{1,3}),([0-9]{1,3})\]((?:https?)://(?:([a-z0-9-]+\.)*)?[a-z0-9-]+\.[a-z]{2,4}(/[a-z0-9~_-]+)*)\[/dailymotion\]`iuU',
			'dailymotion2' => '`\[dailymotion\]((?:https?)://([a-z0-9-]+\.)*[a-z0-9-]+\.[a-z]{2,4}+(?:[a-z0-9~_-]+/)*(?:[a-z0-9_+.:?/=#%@&;,-])*)\[/dailymotion\]`iuU',
			'vimeo1' => '`\[vimeo=([0-9]{1,3}),([0-9]{1,3})\]((?:https?)://(?:([a-z0-9-]+\.)*)?[a-z0-9-]+\.[a-z]{2,4}(/[a-z0-9~_-]+)*)\[/vimeo\]`iuU',
			'vimeo2' => '`\[vimeo\]((?:https?)://(?:([a-z0-9-]+\.)*)[a-z0-9-]+\.[a-z]{2,4}(?:[a-z0-9~_-]+/)*(?:[a-z0-9_+.:?/=#%@&;,-])*)\[/vimeo\]`iuU',
			'lightbox' => '`\[lightbox=((?!javascript:)' . Url::get_wellformness_regex() . ')\](.*)\[/lightbox\]`isuU',
			'figure' => '`\[figure=([^"]+)\](.*)\[/figure\]`isuU',
			'member' => '`\[member\](.*)\[/member\]`isuU',
			'moderator' => '`\[moderator\](.*)\[/moderator\]`isuU',
			'teaser' => '`\[teaser\](.*)\[/teaser\]`isuU',
		);

		$array_preg_replace = array(
			'b' => '<b>$1</b>',
			'strong' => '<strong>$1</strong>',
			'i' => '<i>$1</i>',
			'em' => '<em>$1</em>',
			'u' => '<span style="text-decoration:underline;">$1</span>', // 'u' => '<u>$1</u>',
			's' => '<s>$1</s>',
			'del' => '<del>$1</del>',
			'p' => '<p>$1</p>',
			'q' => '<q>$1</q>',
			'c' => '<code style="display:inline-block;">$1</code>',
			'sup' => '<sup>$1</sup>',
			'sub' => '<sub>$1</sub>',
			'ins' => '<ins>$1</ins>',
			'cite' => '<cite>$1</cite>',
			'mark' => '<mark>$1</mark>',		
			'color' => '<span style="color:$1;">$2</span>',
			'bgcolor' => '<span style="background-color:$1;">$2</span>',
			'size' => '<span style="font-size: $1px;">$2</span>',
			'font' => '<span style="font-family: $1;">$2</span>',
			'pre' => '<pre>$1</pre>',
			'align' => '<p style="text-align: $1;">$2</p>',
			'float' => '<p class="float-$1">$2</p>',
			'anchor' => '<span id="$1">$2</span>',
			'acronym' => '<acronym class="formatter-acronym">$1</acronym>',
			'acronym2' => '<acronym title="$1\" class="formatter-acronym">$2</acronym>',
			'abbr' => '<abbr class="formatter-abbr">$1</abbr>',
			'abbr2' => '<abbr title="$1\" class="formatter-abbr">$2</abbr>',
			'style' => '<span class="message-helper bgc $1">$2</span>',
			'movie' => '[[MEDIA]]insertMoviePlayer(\'$3\', $1, $2);[[/MEDIA]]',
			'movie2' => '[[MEDIA]]insertMoviePlayer(\'$4\', $1, $2, \'$3\');[[/MEDIA]]',
			'sound' => '[[MEDIA]]insertSoundPlayer(\'$1\');[[/MEDIA]]',
			'math' => '[[MATH]]$1[[/MATH]]',
			'mail' => '<a href="mailto:$1">$1</a>',
			'mail2' => '<a href="mailto:$1">$2</a>',
			'url1' => '<a class="offload" href="$1">$1</a>',
			'url2' => '<a class="offload" href="$1">$6</a>',
			'url3' => '<a class="offload" href="$1">$1</a>',
			'url4' => '$1<a class="offload" href="$2">$2</a><',
			'url5' => '$1<a class="offload" href="$2">$2</a> ',
			'url6' => '$1(<a class="offload" href="$2">$2</a>) ',
			'url7' => '$1(<a class="offload" href="$2">$2</a> ) ',
			'youtube1' => '[[MEDIA]]insertYoutubePlayer(\'$3\', $1, $2);[[/MEDIA]]',
			'youtube2' => '[[MEDIA]]insertYoutubePlayer(\'$1\', 560, 315);[[/MEDIA]]',
			'dailymotion1' => '[[MEDIA]]insertDailymotionPlayer(\'$3\', $1, $2);[[/MEDIA]]',
			'dailymotion2' => '[[MEDIA]]insertDailymotionPlayer(\'$1\', 560, 315);[[/MEDIA]]',
			'vimeo1' => '[[MEDIA]]insertVimeoPlayer(\'$3\', $1, $2);[[/MEDIA]]',
			'vimeo2' => '[[MEDIA]]insertVimeoPlayer(\'$1\', 560, 315);[[/MEDIA]]',
			'lightbox' => '<a href="$1" data-lightbox="formatter" class="formatter-lightbox">$6</a>',
			'figure' => '<figure>$2<figcaption>$1</figcaption></figure>',
			'member' => '[[MEMBER]]$1[[/MEMBER]]',
			'moderator' => '[[MODERATOR]]$1[[/MODERATOR]]',
			'teaser' => '[[TEASER]]$1[[/TEASER]]',
		);

		$parse_line = true;

		//Suppression des remplacements des balises interdites.
		if (!empty($this->forbidden_tags))
		{
			//Si on interdit les liens, on ajoute toutes les manières par lesquelles elles peuvent passer
			if (in_array('url', $this->forbidden_tags))
			{
				$this->forbidden_tags[] = 'url1';
				$this->forbidden_tags[] = 'url2';
				$this->forbidden_tags[] = 'url3';
				$this->forbidden_tags[] = 'url4';
				$this->forbidden_tags[] = 'url5';
				$this->forbidden_tags[] = 'url6';
				$this->forbidden_tags[] = 'url7';
			}
			if (in_array('mail', $this->forbidden_tags))
			{
				$this->forbidden_tags[] = 'mail';
				$this->forbidden_tags[] = 'mail2';
			}

			foreach ($this->forbidden_tags as $key => $tag)
			{
				if ($tag == 'line')
				{
					$parse_line = false;
				}
				else
				{
					unset($array_preg[$tag]);
					unset($array_preg_replace[$tag]);
				}
			}
		}

		//Remplacement : on parse les balises classiques
		$this->content = preg_replace($array_preg, $array_preg_replace, $this->content);

		//Line tag
		if ($parse_line)
		$this->content = str_replace('[line]', '<hr class="formatter-hr" />', $this->content);

		//Title tag
		if (!in_array('title', $this->forbidden_tags))
		{
			$this->content = preg_replace_callback('`\[title=([1-6])\](.+)\[/title\]`iuU', array($this, 'parse_title'), $this->content);
		}

		//Title tag
		if (!in_array(['h1','h2','h3','h4','h5','h6'], $this->forbidden_tags))
		{
			$this->content = preg_replace_callback('`\[h([1-6])\](.+)\[/h([1-6])\]`iuU', array($this, 'parse_title'), $this->content);
		}

		//Image tag
		if (!in_array('img', $this->forbidden_tags))
		{
			$this->content = preg_replace_callback('`\[img(?: alt="([^"]+)")?(?: style="([^"]+)")?(?: class="([^"]+)")?\]((?:[./]+|(?:https?|ftps?)://(?:[a-z0-9-]+\.)*[a-z0-9-]+(?:\.[a-z]{2,4})?(?::[0-9]{1,5})?/?)[^,\n\r\t\f]+\.(jpg|jpeg|bmp|webp|gif|png|tiff|svg))\[/img\]`iuU', array($this, 'parse_img'), $this->content);
			$this->content = preg_replace_callback('`\[img(?: alt="([^"]+)")?(?: style="([^"]+)")?(?: class="([^"]+)")?\]data:(.+)\[/img\]`iuU', array($this, 'parse_img'), $this->content);
		}

		//FA tag
		if (!in_array('fa', $this->forbidden_tags))
		{
			$this->content = preg_replace_callback('`\[fa(= ?[a-z0-9-, ]+)?(?: style="([^"]+)")?\]([a-z0-9- ]+)\[/fa\]`iuU', array($this, 'parse_fa_tag'), $this->content);
		}

		//HTML emoji tag
		if (!in_array('emoji', $this->forbidden_tags))
		{
			$this->content = preg_replace_callback('`([^-\p{L}\x00-\x7F]+)`iuU', array($this, 'parse_emoji_tag'), $this->content);
		}

		//Wikipedia tag
		if (!in_array('wikipedia', $this->forbidden_tags))
		{
			$this->content = preg_replace_callback('`\[wikipedia(?: page="([^"]+)")?(?: lang="([a-z]+)")?\](.+)\[/wikipedia\]`isuU', array($this, 'parse_wikipedia_tag'), $this->content);
		}

		##Parsage des balises imbriquées.
		//Quote tag
		if (!in_array('quote', $this->forbidden_tags))
		{
			$this->_parse_imbricated('[quote]', '`\[quote\](.+)\[/quote\]`suU', '<blockquote class="formatter-container formatter-blockquote"><span class="formatter-title">' . LangLoader::get_message('editor.quote', 'editor-lang') . ' :</span><div class="formatter-content">$1</div></blockquote>');
			$this->_parse_imbricated('[quote=', '`\[quote=([^\]]+)\](.+)\[/quote\]`suU', '<blockquote class="formatter-container formatter-blockquote"><span class="formatter-title title-perso">$1 :</span><div class="formatter-content">$2</div></blockquote>');
		}

		//Hide tag
		if (!in_array('hide', $this->forbidden_tags))
		{
			$this->_parse_imbricated('[hide]', '`\[hide\](.+)\[/hide\]`suU', '<div class="formatter-container formatter-hide no-js"><span class="formatter-title">' . LangLoader::get_message('common.hidden', 'common-lang') . ' :</span><div class="formatter-content">$1</div></div>');
			$this->_parse_imbricated('[hide=', '`\[hide=([^\]]+)\](.+)\[/hide\]`suU', '<div class="formatter-container formatter-hide no-js"><span class="formatter-title title-perso">$1 :</span><div class="formatter-content">$2</div></div>');
		}

		//Indent tag
		if (!in_array('indent', $this->forbidden_tags))
		{
			$this->_parse_imbricated('[indent]', '`\[indent\](.+)\[/indent\]`suU', '<div class="indent">$1</div>');
		}

		//Block tag
		if (!in_array('block', $this->forbidden_tags))
		{
			$this->_parse_imbricated('[block]', '`\[block\](.+)\[/block\]`suU', '<div class="formatter-container formatter-block">$1</div>');
			$this->_parse_imbricated('[block style=', '`\[block style="([^"]+)"\](.+)\[/block\]`suU', '<div class="formatter-container formatter-block" style="$1">$2</div>');
		}

		//Fieldset tag
		if (!in_array('fieldset', $this->forbidden_tags))
		{
			$this->_parse_imbricated('[fieldset', '`\[fieldset(?: legend="(.*)")?(?: style="([^"]*)")?\](.+)\[/fieldset\]`suU', '<fieldset class="formatter-container formatter-fieldset" style="$2"><legend>$1</legend><div class="formatter-content">$3</div></fieldset>');
		}

		// Feed tag
		if (!in_array('feed', $this->forbidden_tags))
		{
			$this->parse_feed_tag();
		}

		//Container tag
		if (!in_array('container', $this->forbidden_tags))
		{
			$this->_parse_imbricated('[container', '`\[container(?: id="([^"]*)")?(?: class="([^"]*)")?(?: style="([^"]*)")?\](.+)\[/container\]`suU', '<div id="$1" class="$2" style="$3">$4</div>');
		}
	}

	/**
	 * @desc Serializes a split content according to the table tag and generates the complete HTML code.
	 * @param string[] $content Content of the parser split according to the table tag
	 */
	protected function parse_imbricated_table(&$content)
	{
		if (is_array($content))
		{
			$string_content = '';
			$nbr_occur = count($content);
			for ($i = 0; $i < $nbr_occur; $i++)
			{
				//Si c'est le contenu d'un tableau on le parse
				if ($i % 3 === 2)
				{
					//On parse d'abord les sous tableaux éventuels
					$this->parse_imbricated_table($content[$i]);
					//On parse le tableau concerné (il doit commencer par [row] puis [col] ou [head] et se fermer pareil moyennant espaces et retours à la ligne sinon il n'est pas valide)
					if (preg_match('`^(?:\s|<br />)*\[row(?: style="[^"]+")?\](?:\s|<br />)*\[(?:col|head)(?: colspan="[0-9]+")?(?: rowspan="[0-9]+")?(?: style="[^"]+")?\].*\[/(?:col|head)\](?:\s|<br />)*\[/row\](?:\s|<br />)*$`suU', $content[$i]))
					{
						//On nettoie les caractères éventuels (espaces ou retours à la ligne) entre les différentes cellules du tableau pour éviter les erreurs xhtm
						$content[$i] = preg_replace_callback('`^(\s|<br />)+\[row.*\]`uU', array('BBCodeParser', 'clear_html_br'), $content[$i]);
						$content[$i] = preg_replace_callback('`\[/row\](\s|<br />)+$`uU', array('BBCodeParser', 'clear_html_br'), $content[$i]);
						$content[$i] = preg_replace_callback('`\[/row\](\s|<br />)+\[row.*\]`uU', array('BBCodeParser', 'clear_html_br'), $content[$i]);
						$content[$i] = preg_replace_callback('`\[row\](\s|<br />)+\[col.*\]`suU', array('BBCodeParser', 'clear_html_br'), $content[$i]);
						$content[$i] = preg_replace_callback('`\[row\](\s|<br />)+\[head[^]]*\]`uU', array('BBCodeParser', 'clear_html_br'), $content[$i]);
						$content[$i] = preg_replace_callback('`\[/col\](\s|<br />)+\[col.*\]`suU', array('BBCodeParser', 'clear_html_br'), $content[$i]);
						$content[$i] = preg_replace_callback('`\[/col\](\s|<br />)+\[head[^]]*\]`uU', array('BBCodeParser', 'clear_html_br'), $content[$i]);
						$content[$i] = preg_replace_callback('`\[/head\](\s|<br />)+\[col.*\]`suU', array('BBCodeParser', 'clear_html_br'), $content[$i]);
						$content[$i] = preg_replace_callback('`\[/head\](\s|<br />)+\[head[^]]*\]`uU', array('BBCodeParser', 'clear_html_br'), $content[$i]);
						$content[$i] = preg_replace_callback('`\[/head\](\s|<br />)+\[/row\]`uU', array('BBCodeParser', 'clear_html_br'), $content[$i]);
						$content[$i] = preg_replace_callback('`\[/col\](\s|<br />)+\[/row\]`uU', array('BBCodeParser', 'clear_html_br'), $content[$i]);
						//Parsage de row, col et head
						$content[$i] = preg_replace('`\[row( style="[^"]+")?\](.*)\[/row\]`suU', '<tr class="formatter-table-row"$1>$2</tr>', $content[$i]);
						$content[$i] = preg_replace('`\[col((?: colspan="[0-9]+")?(?: rowspan="[0-9]+")?(?: style="[^"]+")?)?\](.*)\[/col\]`suU', '<td class="formatter-table-col"$1>$2</td>', $content[$i]);
						$content[$i] = preg_replace('`\[head((?: colspan="[0-9]+")?(?: style="[^"]+")?)?\](.*)\[/head\]`suU', '<td class="formatter-table-head"$1>$2</td>', $content[$i]);
						//parsage réussi (tableau valide), on rajoute le tableau devant
						$content[$i] = '<table class="table formatter-table"' . $content[$i - 1] . '>' . $content[$i] . '</table>';

					}
					else
					{
						//le tableau n'est pas valide, on met des balises temporaires afin qu'elles ne soient pas parsées au niveau inférieur
						$content[$i] = str_replace(array('[col', '[row', '[/col', '[/row', '[head', '[/head'), array('[\col', '[\row', '[\/col', '[\/row', '[\head', '[\/head'), $content[$i]);
						$content[$i] = '[table' . $content[$i - 1] . ']' . $content[$i] . '[/table]';
					}
				}
				//On concatène la chaîne finale si ce n'est pas le style du tableau
				if ($i % 3 !== 1)
				$string_content .= $content[$i];
			}
			$content = $string_content;
		}
	}

	/**
	 * @desc Parses the table tag in the content of the parser
	 */
	protected function parse_table()
	{
		//On supprime les éventuels quote qui ont été transformés en leur entité html
		$this->split_imbricated_tag($this->content, 'table', ' style="[^"]+"');
		$this->parse_imbricated_table($this->content);
		//On remet les tableaux invalides tels qu'ils étaient avant
		$this->content = str_replace(array('[\col', '[\row', '[\/col', '[\/row', '[\head', '[\/head'), array('[col', '[row', '[/col', '[/row', '[head', '[/head'), $this->content);
	}

	/**
	 * @descSerializes a split content according to the list tag
	 * Generates the HTML code
	 * @param string[] $content Content split according to the list tag
	 */
	protected function parse_imbricated_list(&$content)
	{
		if (is_array($content))
		{
			$string_content = '';
			$nbr_occur = count($content);
			for ($i = 0; $i < $nbr_occur; $i++)
			{
				//Si c'est le contenu d'une liste on le parse
				if ($i % 3 === 2)
				{
					//On parse d'abord les sous listes éventuelles
					if (is_array($content[$i]))
					$this->parse_imbricated_list($content[$i]);

					if (TextHelper::strpos($content[$i], '[*]') !== false) //Si il contient au moins deux éléments
					{
						//Nettoyage des listes (retours à la ligne)
						$content[$i] = preg_replace_callback('`\[\*\]((?:\s|<br />)+)`u', array('BBCodeParser', 'clear_html_br'), $content[$i]);
						$content[$i] = preg_replace_callback('`((?:\s|<br />)+)\[\*\]`u', array('BBCodeParser', 'clear_html_br'), $content[$i]);
						if (TextHelper::substr($content[$i - 1], 0, 8) == '=ordered')
						{
							$list_tag = 'ol';
							$content[$i - 1] = TextHelper::substr($content[$i - 1], 8);
						}
						else
						{
							$list_tag = 'ul';
						}
						$content[$i] = preg_replace_callback('`^((?:\s|<br />)*)\[\*\]`uU', function($var) {return str_replace("<br />", "", str_replace("[*]", "<li class=\"formatter-li\">", $var[0]));}, $content[$i]);
						$content[$i] = '<' . $list_tag . $content[$i - 1] . ' class="formatter-' . $list_tag . '">' . str_replace('[*]', '</li><li class="formatter-li">', $content[$i]) . '</li></' . $list_tag . '>';
					}
				}
				//On concatène la chaîne finale si ce n'est pas le style ou le type de tableau
				if ($i % 3 !== 1)
				$string_content .= $content[$i];
			}
			$content = $string_content;
		}
	}

	/**
	 * @desc Parses the list tag of the content of the parser.
	 */
	protected function parse_list()
	{
		//On nettoie les guillemets échappés
		//on travaille dessus
		if (preg_match('`\[list(=(?:un)?ordered)?( style="[^"]+")?\](\s|<br />)*\[\*\].*\[/list\]`su', $this->content))
		{
			$this->split_imbricated_tag($this->content, 'list', '(?:=ordered)?(?: style="[^"]+")?');
			$this->parse_imbricated_list($this->content);
		}
	}

	/**
	 * @desc Callback treating the title tag
	 * @param string[] $matches Content matched by a regular expression
	 * @return string The string in which the title tag are parsed
	 */
	protected function parse_title($matches)
	{
		$level = (int)$matches[1] + 1;
		return '<h' . $level . ' class="formatter-title">' . $matches[2] . '</h' . $level . '>';
	}


	protected function parse_img($matches)
	{
		$img_pathinfo = pathinfo($matches[4]);
		$file_array = explode('.', $img_pathinfo['filename']);
		$img_name = $file_array[0];
		$alt = !empty($matches[1]) ? $matches[1] : $img_name;
		$style = !empty($matches[2]) ? ' style="' . $matches[2] . '"' : '';
		$class = !empty($matches[3]) ? ' class="' . $matches[3] . '"' : '';
		if (preg_match('`^image/(jpg|jpeg|bmp|webp|gif|png|tiff|svg);base`su', $matches[4]))
			$matches[4] = 'data:' . $matches[4];

		return '<img src="' . $matches[4] . '" alt="' . $alt . '"' . $class . $style .' />';
	}

	/**
	 * @desc Callback which clears the new line tag in the HTML generated code
	 * @param string[] $matches Content matched by a regular expression
	 * @return string The string in which the new line tag are cleared
	 */
	protected static function clear_html_br($matches)
	{
		return str_replace("<br />", "", $matches[0]);
	}
}
?>
