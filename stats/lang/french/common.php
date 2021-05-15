<?php
/**
 * @copyright   &copy; 2005-2020 PHPBoost
 * @license     https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL-3.0
 * @author      Regis VIARRE <crowkait@phpboost.com>
 * @version     PHPBoost 6.0 - last update: 2021 04 17
 * @since       PHPBoost 1.6 - 2007 09 27
 * @contributor Julien BRISWALTER <j1.seth@phpboost.com>
 * @contributor Arnaud GENET <elenwii@phpboost.com>
 * @contributor Sebastien LARTIGUE <babsolune@phpboost.com>
*/

####################################################
#                    French                       #
####################################################

$lang['stats.module.title'] = 'Statistiques';
$lang['stats.config.module.title'] = 'Configuration du module Statistiques';

$lang['stats.items.per.page.clue'] = 'Pour les sites référents et les mots clés.';
$lang['stats.require.items.number'] = 'Le nombre d\'éléments par page ne peut pas être nul.';

// Stats
$lang['stats.more.stats'] = 'Plus de stats';
$lang['stats.see.year.stats'] = 'Voir les statistiques de l\'année';
$lang['stats.total.visits'] = 'Total des visites';
$lang['stats.average.visits'] = 'Visites moyennes';
$lang['stats.last.visit.date'] = 'Dernière visite';
$lang['stats.trend'] = 'Tendance';
	// Website
$lang['stats.website'] = 'Site';
$lang['stats.website.creation.date'] = 'Création du site';
$lang['stats.phpboost.version'] = 'Version installée';
	// Members
$lang['stats.last.member'] = 'Dernier membre';
$lang['stats.top.10.contributors'] = 'Top 10 des contributeurs';
$lang['stats.registered.member'] = 'membre inscrit';
$lang['stats.registered.members'] = 'membres inscrits';
	// Browsers
$lang['stats.browsers'] = 'Navigateurs';
	// Operating systems
$lang['stats.os'] = 'Systèmes d\'exploitation';
	// Countries
$lang['stats.countries'] = 'Pays des visiteurs';
	// Referent websites
$lang['stats.referers'] = 'Sites référents';
	// Robots
$lang['stats.robot'] = 'Robot';
$lang['stats.robots'] = 'Robots';
$lang['stats.erase.list'] = 'Effacer la liste';
$lang['stats.erase.occasional'] = 'Effacer les robots occasionnels (0%)';
$lang['stats.last.visit'] = 'Dernière visite';

// Browsers
global $stats_array_browsers;
$stats_array_browsers = array(
	'brave'            => array('Brave', 'brave.png'),
	'chrome'           => array('Chrome', 'chrome.png'),
	'chromium'         => array('Chromium', 'chromium.png'),
	'edge'             => array('Edge', 'edge.png'),
	'firefox'          => array('Firefox', 'firefox.png'),
	'opera'            => array('Opera', 'opera.png'),
	'safari'           => array('Safari', 'safari.png'),
	'tor'              => array('Tor', 'tor.png'),

	'epic'             => array('Epic Privacy', 'epic.png'),
	'falcon'           => array('Falcon', 'falcon.png'),
	'internetexplorer' => array('Internet Explorer', 'internetexplorer.png'),
	'icab'             => array('Icab', 'icab.png'),
	'iron'             => array('SRWare Iron', 'iron.png'),
	'konqueror'        => array('Konqueror', 'konqueror.png'),
	'lynx'             => array('Lynx', 'lynx.png'),
	'links'            => array('Links', 'links.png'),
	'lunascape'        => array('Lunascape', 'lunascape.png'),
	'maxthon'          => array('Maxthon', 'maxthon.png'),
	'phoenix'          => array('Phoenix', 'phoenix.png'),
	'silk'             => array('Amazone Silk', 'silk.png'),
	'seamonkey'        => array('SeaMonkey', 'seamonkey.png'),
	'uc'               => array('UC Browser', 'uc.png'),
	'vivaldi'          => array('Vivaldi', 'vivaldi.png'),
	'yandex'           => array('Yandex', 'yandex.png'),

	'phone'            => array('Mobile', 'phone.png'),
	'other'            => array('Autres', 'other.png')
);

// Operating systems
global $stats_array_os;
$stats_array_os = array(
	'macintosh'         => array('Mac OS', 'mac.png'),

	'windows10'         => array('Windows 10', 'windows10.png'),
	'windows8.1'        => array('Windows 8.1', 'windows8.png'),
	'windows8'          => array('Windows 8', 'windows8.png'),
	'windowsseven'      => array('Windows 7', 'windowsseven.png'),

	'linux'             => array('Linux', 'linux.png'),

	'android'           => array('Android', 'android.png'),
	'ios'               => array('IOS', 'iphone.png'),
	'phone'             => array('Mobile', 'phone.png'),

	'other'             => array('Autres', 'other.png')
);

// Countries
global $stats_array_lang;
$stats_array_lang = array(
	'ad' => array('Andorre', 'ad.png'),
	'ae' => array('Emirats Arabes Unis', 'ae.png'),
	'af' => array('Afghanistan', 'af.png'),
	'ag' => array('Antigua et Barbuda', 'ag.png'),
	'ai' => array('Anguilla', 'ai.png'),
	'al' => array('Albanie', 'al.png'),
	'am' => array('Arménie', 'am.png'),
	'an' => array('Antilles Neerlandaises', 'an.png'),
	'ao' => array('Angola', 'ao.png'),
	'aq' => array('Antarctique', 'aq.png'),
	'ar' => array('Argentine', 'ar.png'),
	'as' => array('Samoa américaines', 'as.png'),
	'at' => array('Autriche', 'at.png'),
	'au' => array('Australie', 'au.png'),
	'aw' => array('Aruba', 'aw.png'),
	'az' => array('Azerbaidjan', 'az.png'),
	'ba' => array('Bosnie Herzégovine', 'ba.png'),
	'bb' => array('Barbade', 'bb.png'),
	'bd' => array('Bangladesh', 'bd.png'),
	'be' => array('Belgique', 'be.png'),
	'bf' => array('Burkina Faso', 'bf.png'),
	'bg' => array('Bulgarie', 'bg.png'),
	'bh' => array('Bahrein', 'bh.png'),
	'bi' => array('Burundi', 'bi.png'),
	'bj' => array('Bénin', 'bj.png'),
	'bm' => array('Bermudes', 'bm.png'),
	'bn' => array('Brunei', 'bn.png'),
	'bo' => array('Bolivie', 'bo.png'),
	'br' => array('Brésil', 'br.png'),
	'bs' => array('Bahamas', 'bs.png'),
	'bt' => array('Bhoutan', 'bt.png'),
	'bv' => array('Île Bouvet', 'bv.png'),
	'bw' => array('Botswana', 'bw.png'),
	'by' => array('Biélorussie', 'by.png'),
	'bz' => array('Bélize', 'bz.png'),
	'ca' => array('Canada', 'ca.png'),
	'cc' => array('Îles Cocos', 'cc.png'),
	'cd' => array('R.D. du Congo', 'cd.png'),
	'cf' => array('R. Centrafricaine', 'cf.png'),
	'cg' => array('Congo', 'cg.png'),
	'ch' => array('Suisse', 'ch.png'),
	'ci' => array('Côte d\'Ivoire', 'ci.png'),
	'ck' => array('Îles Cook', 'ck.png'),
	'cl' => array('Chili', 'cl.png'),
	'cm' => array('Cameroun', 'cm.png'),
	'cn' => array('Chine', 'cn.png'),
	'co' => array('Colombie', 'co.png'),
	'cr' => array('Costa Rica', 'cr.png'),
	'cu' => array('Cuba', 'cu.png'),
	'cv' => array('Cap Vert', 'cv.png'),
	'cx' => array('Îles Christmas', 'cx.png'),
	'cy' => array('Chypre', 'cy.png'),
	'cz' => array('R. Tchèque', 'cz.png'),
	'de' => array('Allemagne', 'de.png'),
	'dj' => array('Djibouti', 'dj.png'),
	'dk' => array('Danemark', 'dk.png'),
	'dm' => array('Dominique', 'dm.png'),
	'do' => array('R. Dominicaine', 'do.png'),
	'dz' => array('Algérie', 'dz.png'),
	'ec' => array('Equateur', 'ec.png'),
	'ee' => array('Estonie', 'ee.png'),
	'eg' => array('Egypte', 'eg.png'),
	'eh' => array('Sahara Occidental', 'eh.png'),
	'er' => array('Erythrée', 'er.png'),
	'es' => array('Espagne', 'es.png'),
	'et' => array('Ethiopie', 'et.png'),
	'fi' => array('Finlande', 'fi.png'),
	'fj' => array('Fidji', 'fj.png'),
	'fk' => array('Îles Falkland', 'fk.png'),
	'fm' => array('Micronésie', 'fm.png'),
	'fo' => array('Îles Féroé', 'fo.png'),
	'fr' => array('France', 'fr.png'),
	'ga' => array('Gabon', 'ga.png'),
	'gb' => array('Grande Bretagne', 'gb.png'),
	'gd' => array('Grenade', 'gd.png'),
	'ge' => array('Géorgie', 'ge.png'),
	'gf' => array('Guyane Française', 'gf.png'),
	'gg' => array('Guernsey', 'gg.png'),
	'gh' => array('Ghana', 'gh.png'),
	'gi' => array('Gibraltar', 'gi.png'),
	'gl' => array('Groenland', 'gl.png'),
	'gm' => array('Gambie', 'gm.png'),
	'gn' => array('Guinée', 'gn.png'),
	'gp' => array('Guadeloupe', 'gp.png'),
	'gq' => array('Guinée Equatoriale', 'gq.png'),
	'gr' => array('Grèce', 'gr.png'),
	'gs' => array('Géorgie du sud', 'gs.png'),
	'gt' => array('Guatemala', 'gt.png'),
	'gu' => array('Guam', 'gu.png'),
	'gw' => array('Guinée-Bissau', 'gw.png'),
	'gy' => array('Guyane', 'gy.png'),
	'hk' => array('Hong Kong', 'hk.png'),
	'hm' => array('Îles Heard et McDonald', 'hm.png'),
	'hn' => array('Honduras', 'hn.png'),
	'hr' => array('Croatie', 'hr.png'),
	'ht' => array('Haiti', 'ht.png'),
	'hu' => array('Hongrie', 'hu.png'),
	'id' => array('Indonésie', 'id.png'),
	'ie' => array('Irlande', 'ie.png'),
	'il' => array('Israël', 'il.png'),
	'im' => array('Île de Man', 'im.png'),
	'in' => array('Inde', 'in.png'),
	'io' => array('Ter.Br. Océan Indien', 'io.png'),
	'iq' => array('Iraq', 'iq.png'),
	'ir' => array('Iran', 'ir.png'),
	'is' => array('Islande', 'is.png'),
	'it' => array('Italie', 'it.png'),
	'je' => array('Jersey', 'je.png'),
	'jm' => array('Jamaïque', 'jm.png'),
	'jo' => array('Jordanie', 'jo.png'),
	'jp' => array('Japon', 'jp.png'),
	'ke' => array('Kenya', 'ke.png'),
	'kg' => array('Kirghizistan', 'kg.png'),
	'kh' => array('Cambodge', 'kh.png'),
	'ki' => array('Kiribati', 'ki.png'),
	'km' => array('Comores', 'km.png'),
	'kn' => array('Saint Kitts et Nevis', 'kn.png'),
	'kp' => array('Corée du nord', 'kp.png'),
	'kr' => array('Corée du sud', 'kr.png'),
	'kw' => array('Koweït', 'kw.png'),
	'ky' => array('Îles Caïmans', 'ky.png'),
	'kz' => array('Kazakhstan', 'kz.png'),
	'la' => array('Laos', 'la.png'),
	'lb' => array('Liban', 'lb.png'),
	'lc' => array('Sainte Lucie', 'lc.png'),
	'li' => array('Liechtenstein', 'li.png'),
	'lk' => array('Sri Lanka', 'lk.png'),
	'lr' => array('Liberia', 'lr.png'),
	'ls' => array('Lesotho', 'ls.png'),
	'lt' => array('Lituanie', 'lt.png'),
	'lu' => array('Luxembourg', 'lu.png'),
	'lv' => array('Lettonie', 'lv.png'),
	'ly' => array('Libye', 'ly.png'),
	'ma' => array('Maroc', 'ma.png'),
	'mc' => array('Monaco', 'mc.png'),
	'md' => array('Moldavie', 'md.png'),
	'me' => array('Monténégro', 'me.png'),
	'mg' => array('Madagascar', 'mg.png'),
	'mh' => array('Îles Marshall', 'mh.png'),
	'mk' => array('Macédoine', 'mk.png'),
	'ml' => array('Mali', 'ml.png'),
	'mm' => array('Myanmar', 'mm.png'),
	'mn' => array('Mongolie', 'mn.png'),
	'mo' => array('Macao', 'mo.png'),
	'mp' => array('Îles Mariannes du nord', 'mp.png'),
	'mq' => array('Martinique', 'mq.png'),
	'mr' => array('Mauritanie', 'mr.png'),
	'ms' => array('Montserrat', 'ms.png'),
	'mt' => array('Malte', 'mt.png'),
	'mu' => array('Île Maurice', 'mu.png'),
	'mv' => array('Maldives', 'mv.png'),
	'mw' => array('Malawi', 'mw.png'),
	'mx' => array('Mexique', 'mx.png'),
	'my' => array('Malaisie', 'my.png'),
	'mz' => array('Mozambique', 'mz.png'),
	'na' => array('Namibie', 'na.png'),
	'nc' => array('Nouvelle Calédonie', 'nc.png'),
	'ne' => array('Niger', 'ne.png'),
	'nf' => array('Île Norfolk', 'nf.png'),
	'ng' => array('Nigéria', 'ng.png'),
	'ni' => array('Nicaragua', 'ni.png'),
	'nl' => array('Pays Bas', 'nl.png'),
	'no' => array('Norvège', 'no.png'),
	'np' => array('Népal', 'np.png'),
	'nr' => array('Nauru', 'nr.png'),
	'nu' => array('Niue', 'nu.png'),
	'nz' => array('Nouvelle Zélande', 'nz.png'),
	'om' => array('Oman', 'om.png'),
	'pa' => array('Panama', 'pa.png'),
	'pe' => array('Pérou', 'pe.png'),
	'pf' => array('Polynésie Française', 'pf.png'),
	'pg' => array('Papouasie Nouvelle Guinée', 'pg.png'),
	'ph' => array('Philippines', 'ph.png'),
	'pk' => array('Pakistan', 'pk.png'),
	'pl' => array('Pologne', 'pl.png'),
	'pm' => array('St. Pierre et Miquelon', 'pm.png'),
	'pn' => array('Île Pitcairn', 'pn.png'),
	'pr' => array('Porto Rico', 'pr.png'),
	'ps' => array('Palestine', 'ps.png'),
	'pt' => array('Portugal', 'pt.png'),
	'pw' => array('Palau', 'pw.png'),
	'py' => array('Paraguay', 'py.png'),
	'qa' => array('Qatar', 'qa.png'),
	're' => array('Île de la Réunion', 're.png'),
	'ro' => array('Roumanie', 'ro.png'),
	'ru' => array('Russie', 'ru.png'),
	'rs' => array('Serbie', 'rs.png'),
	'rw' => array('Rwanda', 'rw.png'),
	'sa' => array('Arabie Saoudite', 'sa.png'),
	'sb' => array('Îles Salomon', 'sb.png'),
	'sc' => array('Seychelles', 'sc.png'),
	'sd' => array('Soudan', 'sd.png'),
	'se' => array('Suède', 'se.png'),
	'sg' => array('Singapour', 'sg.png'),
	'sh' => array('St. Hélène', 'sh.png'),
	'si' => array('Slovénie', 'si.png'),
	'sj' => array('Îles Svalbard et Jan Mayen', 'sj.png'),
	'sk' => array('Slovaquie', 'sk.png'),
	'sl' => array('Sierra Leone', 'sl.png'),
	'sm' => array('Saint-Marin', 'sm.png'),
	'sn' => array('Sénégal', 'sn.png'),
	'so' => array('Somalie', 'so.png'),
	'sr' => array('Suriname', 'sr.png'),
	'st' => array('Sao Tome et Principe', 'st.png'),
	'sv' => array('Salvador', 'sv.png'),
	'sy' => array('Syrie', 'sy.png'),
	'sz' => array('Swaziland', 'sz.png'),
	'tc' => array('Îles Turks et Caïques', 'tc.png'),
	'td' => array('Tchad', 'td.png'),
	'tf' => array('Ter.Fr. du sud', 'tf.png'),
	'tg' => array('Togo', 'tg.png'),
	'th' => array('Thailande', 'th.png'),
	'tj' => array('Tadjikistan', 'tj.png'),
	'tk' => array('Tokelau', 'tk.png'),
	'tm' => array('Turkménistan', 'tm.png'),
	'tn' => array('Tunisie', 'tn.png'),
	'to' => array('Tonga', 'to.png'),
	'tp' => array('Timor Oriental', 'tp.png'),
	'tr' => array('Turquie', 'tr.png'),
	'tt' => array('Trinité et Tobago', 'tt.png'),
	'tv' => array('Tuvalu', 'tv.png'),
	'tw' => array('Taiwan', 'tw.png'),
	'tz' => array('Tanzanie', 'tz.png'),
	'ua' => array('Ukraine', 'ua.png'),
	'ug' => array('Ouganda', 'ug.png'),
	'uk' => array('Royaume Uni', 'uk.png'),
	'um' => array('Îles Mineures éloignées des États-Unis', 'um.png'),
	'us' => array('États-Unis', 'us.png'),
	'uy' => array('Uruguay', 'uy.png'),
	'uz' => array('Ouzbékistan', 'uz.png'),
	'va' => array('Vatican', 'va.png'),
	'vc' => array('St Vincent et les Grenadines', 'vc.png'),
	've' => array('Venezuela', 've.png'),
	'vg' => array('Îles Vierges (UK)', 'vg.png'),
	'vi' => array('Îles Vierges (US)', 'vi.png'),
	'vn' => array('Viêt Nam', 'vn.png'),
	'vu' => array('Vanuatu', 'vu.png'),
	'wf' => array('Îles Wallis et Futuna', 'wf.png'),
	'ws' => array('Samoa', 'ws.png'),
	'xk' => array('Kosovo', 'rs.png'),
	'ye' => array('Yemen', 'ye.png'),
	'yt' => array('Mayotte', 'yt.png'),
	'za' => array('Afrique du Sud', 'za.png'),
	'zm' => array('Zambie', 'zm.png'),
	'zw' => array('Zimbabwe', 'zw.png'),
	'other' => array('Autres', 'other.png')
);
?>
