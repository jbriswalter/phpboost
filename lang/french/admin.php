<?php
/*##################################################
 *                                admin.php
 *                            -------------------
 *   begin                : November 20, 2005
 *   copyright          : (C) 2005 Viarre R�gis
 *   email                : crowkait@phpboost.com
 *
 *  
 ###################################################
 *
 *   This program is free software; you can redistribute it and/or modify
 *   it under the terms of the GNU General Public License as published by
 *   the Free Software Foundation; either version 2 of the License, or
 *   (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 *
###################################################*/


####################################################
#                                                           French                                                                #
####################################################

$LANG['xml_lang'] = 'fr';
$LANG['date_format'] = 'd/m/y';
$LANG['date_format_rss'] = 'd/m';
$LANG['administration'] = 'Administration';
$LANG['no_administration'] = 'Aucune administration associ�e avec ce module!';

//Titre Modules par d�fauts
$LANG['index'] = 'Index';
$LANG['tools'] = 'Outils';
$LANG['contents'] = 'Contenu';
$LANG['link_management'] = 'Gestion des liens';
$LANG['menu_management'] = 'Menus';
$LANG['moderation'] = 'Panneau mod�ration';
$LANG['maintain'] = 'Maintenance';
$LANG['updater'] = 'Mises � jour';
$LANG['database'] = 'Base de donnn�es';
$LANG['extend_field'] = 'Champs membres';
$LANG['ranks'] = 'Rangs';
$LANG['terms'] = 'R�glement';
$LANG['pages'] = 'Pages';
$LANG['files'] = 'Fichiers';
$LANG['themes'] = 'Th�mes';
$LANG['languages'] = 'Langues';
$LANG['smile'] = 'Smileys';
$LANG['comments'] = 'Commentaires';
$LANG['group'] = 'Groupes';
$LANG['stats'] = 'Statistiques';
$LANG['errors'] = 'Erreurs archiv�es';
$LANG['cache'] = 'Cache';

//Form
$LANG['add'] = 'Ajouter';

//Alertes formulaires
$LANG['alert_same_pass'] = 'Les mots de passe ne sont pas identiques!';
$LANG['alert_max_dim'] = 'Le fichier d�passe les largeurs et hauteurs maximum sp�cifi�es !';
$LANG['alert_error_avatar'] = 'Erreur d\'enregistrement de l\'avatar!';
$LANG['alert_error_img'] = 'Erreur d\'enregistrement de l\'image!';
$LANG['alert_invalid_file'] = 'Le fichier image n\'est pas valide (jpg, gif, png!)';
$LANG['alert_max_weight'] = 'Image trop lourde';
$LANG['alert_s_already_use'] = 'Code du smiley d�j� utilis�!';
$LANG['alert_no_cat'] = 'Aucun nom/cat�gorie saisi';
$LANG['alert_fct_unlink'] = 'Suppression des miniatures impossible. Vous devez supprimer manuellement sur le ftp!';
$LANG['alert_no_login'] = 'Le pseudo entr� n\'existe pas!';

//Requis
$LANG['require'] = 'Les Champs marqu�s * sont obligatoires!';
$LANG['require_title'] = 'Veuillez entrer un titre !';
$LANG['require_text'] = 'Veuillez entrer un texte!';
$LANG['require_pseudo'] = 'Veuillez entrer un pseudo!';
$LANG['require_password'] = 'Veuillez entrer un password!';
$LANG['require_mail'] = 'Veuillez entrer un mail valide!';
$LANG['require_cat'] = 'Veuillez entrer une cat�gorie!';
$LANG['require_cat_create'] = 'Aucune cat�gorie trouv�e, veuillez d\'abord en cr�er une';
$LANG['require_url'] = 'Veuillez entrer une url valide!';
$LANG['require_serv'] = 'Veuillez entrer un nom pour le serveur!';
$LANG['require_name'] = 'Veuillez entrer un nom!';
$LANG['require_cookie_name'] = 'Veuillez entrer un nom de cookie!';
$LANG['require_session_time'] = 'Veuillez entrer une dur�e pour la session!';
$LANG['require_session_invit'] = 'Veuillez entrer une dur�e pour la session invit�!';
$LANG['require_pass'] = 'Veuillez entrer un mot de passe!';
$LANG['require_rank'] = 'Veuillez entrer un rang!';
$LANG['require_code'] = 'Veuillez entrer un code pour le smiley!';
$LANG['require_poll_entry'] = 'Veuillez entrer un nombre d\'entr�es pour le sondage!';
$LANG['require_question'] = 'Veuillez entrer une question pour le sondage!';
$LANG['require_answer'] = 'Veuillez entrer une r�ponse';
$LANG['require_answer_type'] = 'Veuillez entrer le type de r�ponse du sondage!';
$LANG['require_topic_p'] = 'Veuillez entrer le nombre de sujets par page!';
$LANG['require_nbr_msg_p'] = 'Veuillez entrer le nombre de messages par page!';
$LANG['require_time_new_msg'] = 'Veuillez entrer une dur�e pour la vue des nouveaux messages!';
$LANG['require_topic_track_max'] = 'Veuillez entrer le nombre maximum de sujet suivis!';
$LANG['require_max_width'] = 'Veuillez entrer une largeur maximale pour les avatars!';
$LANG['require_height'] = 'Veuillez entrer une hauteur maximale pour les avatars!';
$LANG['require_weight'] = 'Veuillez entrer un poids maximal pour les avatars!';
$LANG['require_rank_name'] = 'Veuillez entrer un nom pour le rang!';
$LANG['require_nbr_msg_rank'] = 'Veuillez entrer un nombre de messages pour le rang!';
$LANG['require_subcat'] = 'Veuillez s�lectionner une sous-cat�gorie!';
$LANG['require_height'] = 'Veuillez entrer une hauteur maximale pour les miniatures!';
$LANG['require_height_max'] = 'Veuillez entrer une hauteur maximale pour les photos!';
$LANG['require_width_max'] = 'Veuillez entrer une largeur maximale pour les photos!';
$LANG['require_width'] = 'Veuillez entrer une largeur maximale pour les miniatures!'; 
$LANG['require_weight_max'] = 'Veuillez entrez un poids maximum pour la photo!'; 
$LANG['require_row'] = 'Veuillez entrer un nombre de colonne(s) pour la galerie!'; 
$LANG['require_img_p'] = 'Veuillez entrer le nombre de photos par page!'; 
$LANG['require_quality'] = 'Veuillez entrer une qualit� pour les miniatures!';
$LANG['require_file_name'] = 'Vous devez rentrer un nom de fichier';

//Confirmations.
$LANG['redirect'] = 'Redirection en cours...';
$LANG['del_entry'] = 'Supprimer l\'entr�e?';
$LANG['confirm_del_member'] = 'Supprimer le membre? (d�finitif !)';
$LANG['confirm_theme'] = 'Supprimer le th�me?';
$LANG['confirm_del_smiley'] = 'Supprimer le smiley?';
$LANG['confirm_del_cat'] = 'Supprimer cette cat�gorie ?';
$LANG['confirm_del_article'] = 'Supprimer cet article?';
$LANG['confirm_del_rank'] = 'Supprimer ce rang ?';
$LANG['confirm_del_group'] = 'Supprimer ce groupe ?';
$LANG['confirm_del_member_group'] = 'Supprimer ce membre du groupe ?';

//bbcode
$LANG['bb_bold'] = 'Texte en gras: [b]texte[/b]';
$LANG['bb_italic'] = 'Texte en italique: [i]texte[/i]';
$LANG['bb_underline'] = 'Texte soulign�: [u]texte[/u]';
$LANG['bb_link'] = 'Ajouter un lien: [url]lien[/url], ou [url=lien]nom du lien[/url]';
$LANG['bb_picture'] = 'Ajouter une image: [img]url image[/img]';
$LANG['bb_size'] = 'Taille du texte (X entre 0 - 49): [size=X]texte de taille X[/size]';
$LANG['bb_color'] = 'Couleur du texte: [color=X]texte de taille X[/color]';
$LANG['bb_quote'] = 'Faire une citation [quote=pseudo]texte[/quote]';
$LANG['bb_code'] = 'Ins�rer du code (PHP color�) [code]texte[/code]';
$LANG['bb_left'] = 'Positionner � gauche: [align=left]objet � gauche[/align]';
$LANG['bb_center'] = 'Centrer: [align=center]objet centr�[/align]';
$LANG['bb_right'] = 'Positionner � droite: [align=right]objet � droite[/align]';

//Commun
$LANG['pseudo'] = 'Pseudo';
$LANG['yes'] = 'Oui';
$LANG['no'] = 'Non';
$LANG['description'] = 'Description';
$LANG['view'] = 'Vu';
$LANG['views'] = 'Vues';
$LANG['name'] = 'Nom';
$LANG['title'] = 'Titre';
$LANG['message'] = 'Message';
$LANG['contents'] = 'Contenu';
$LANG['aprob'] = 'Approbation';
$LANG['unaprob'] = 'D�sapprobation';
$LANG['url'] = 'Adresse';
$LANG['categorie'] = 'Cat�gorie';
$LANG['note'] = 'Note';
$LANG['date'] = 'Date';
$LANG['com'] = 'Commentaires';
$LANG['size'] = 'Taille';
$LANG['file'] = 'Fichier';
$LANG['download'] = 'T�l�charg�';
$LANG['delete'] = 'Supprimer';
$LANG['total_users'] = 'Utilisateur(s) enregistr�(s)';
$LANG['user_ip'] = 'Adresse ip';
$LANG['localisation'] = 'Localisation';
$LANG['achieved'] = 'Execut�e en';
$LANG['req'] = 'Requ�tes SQL';
$LANG['activ'] = 'Activ�';
$LANG['unactiv'] = 'D�sactiv�';
$LANG['img'] = 'Image';
$LANG['activation'] = 'Activation';
$LANG['position'] = 'Position';
$LANG['path'] = 'Chemin';
$LANG['on'] = 'Le';
$LANG['at'] = '�';
$LANG['registered'] = 'Enregistr�';
$LANG['website'] = 'Site web';
$LANG['list'] = 'Liste';
$LANG['search'] = 'Recherche';
$LANG['mail'] = 'Mail';
$LANG['password'] = 'Mot de passe';
$LANG['contact'] = 'Contact';
$LANG['info'] = 'Informations';
$LANG['language'] = 'Langue';
$LANG['sanction'] = 'Sanction';
$LANG['ban'] = 'Banni';
$LANG['theme'] = 'Th�me';	
$LANG['code'] = 'Code';
$LANG['status'] = 'Statut';
$LANG['question'] = 'Question';
$LANG['answers'] = 'R�ponses';
$LANG['archived'] = 'Archiv�';
$LANG['galerie'] = 'Galerie' ;
$LANG['select'] = 'S�lectionner';
$LANG['pics'] = 'Photos';
$LANG['empty'] = 'Vider';
$LANG['show'] = 'Consulter';
$LANG['link'] = 'Lien';
$LANG['type'] = 'Type';
$LANG['of'] = 'de';
$LANG['autoconnect'] = 'Connexion automatique';	
$LANG['unspecified'] = 'Non sp�cifi�';
$LANG['configuration'] = 'Configuration';
$LANG['management'] = 'Gestion';
$LANG['add'] = 'Ajouter';
$LANG['category'] = 'Cat�gorie';
$LANG['site'] = 'Site';
$LANG['modules'] = 'Modules';
$LANG['powered_by'] = 'Boost� par';
$LANG['release_date'] = 'Date de parution jj/mm/aa';
$LANG['immediate'] = 'Imm�diate';
$LANG['waiting'] = 'En attente';
$LANG['stats'] = 'Statistiques'; 
$LANG['cat_management'] = 'Gestion des cat�gories';
$LANG['cat_add'] = 'Ajouter une cat�gorie';
$LANG['visible'] = 'Visible';
$LANG['undefined'] = 'Ind�termin�';
$LANG['nbr_cat_max'] = 'Nombre de cat�gories maximum affich�es';
$LANG['nbr_column_max'] = 'Nombre de colonnes';
$LANG['note_max'] = 'Echelle de notation';
$LANG['max_link'] = 'Nombre de liens maximum dans le message';
$LANG['max_link_explain'] = 'Mettre -1 pour illimit�';
$LANG['generate'] = 'G�n�rer';
$LANG['or_direct_path'] = 'Ou chemin direct';

//Connexion
$LANG['unlock_admin_panel'] = 'D�verrouillage de l\'administration';
$LANG['flood_block'] = 'Il vous reste %d essai(s) apr�s cela il vous faudra attendre 5 minutes pour obtenir 2 nouveaux essais (10min pour 5)!';
$LANG['flood_max'] = 'Vous avez �puis� tous vos essais de connexion, votre compte est verrouill� pendant 5 minutes';

//Rang
$LANG['rank_management'] = 'Gestion des rangs';
$LANG['upload_rank'] = 'Uploader une image de rang';
$LANG['upload_rank_format'] = 'JPG, GIF, PNG, BMP autoris�s';
$LANG['rank_add'] = 'Ajouter un rang';
$LANG['rank'] = 'Rang';
$LANG['special_rank'] = 'Rang sp�cial';
$LANG['rank_name'] = 'Nom du Rang';
$LANG['nbr_msg'] = 'Nombre de message(s)';
$LANG['img_assoc'] = 'Image associ�e';
$LANG['guest'] = 'Visiteur';
$LANG['a_member'] = 'member';
$LANG['member'] = 'Membre';
$LANG['a_modo'] = 'modo';
$LANG['modo'] = 'Mod�rateur';
$LANG['a_admin'] = 'admin';
$LANG['admin'] = 'Administrateur';

//Champs suppl�mentaires
$LANG['extend_field_management'] = 'Gestion des champs membres';
$LANG['extend_field_add'] = 'Ajouter un champ membre';
$LANG['regex'] = 'Regex';
$LANG['regex_explain'] = 'Permet de controller de fa�on s�re l\'entr�e utilisateur';
$LANG['possible_values'] = 'Valeurs possibles';
$LANG['possible_values_explain'] = 'S�parez les diff�rentes valeurs par le symbole |';
$LANG['default_values'] = 'Valeurs par d�faut';
$LANG['default_values_explain'] = 'S�parez les diff�rentes valeurs par le symbole |';
$LANG['short_text'] = 'Texte court (max 255 caract�res)';
$LANG['long_text'] = 'Texte long (illimit�)';
$LANG['sel_uniq'] = 'S�lection unique (parmi plusieurs valeurs)';
$LANG['sel_mult'] = 'S�lection multiple (parmi plusieurs valeurs)';
$LANG['check_uniq'] = 'Choix unique (parmi plusieurs valeurs)';
$LANG['check_mult'] = 'Choix multiple (parmi plusieurs valeurs)';
$LANG['personnal_regex'] = 'Regex personalis�e';
$LANG['predef_regexp'] = 'Regex pr�d�finie';
$LANG['figures'] = 'Chiffres';
$LANG['letters'] = 'Lettres';
$LANG['figures_letters'] = 'Chiffres et lettres';
$LANG['default_field_possible_values'] = 'Oui|Non';
$LANG['extend_field_edit'] = 'Editer le champs';

//Index
$LANG['admin_index'] = 'Bienvenue sur <a href="http://www.phpboost.com" title="PHPBoost">PHPBoost %s</a>, et merci d\'avoir choisi ce portail pour votre site.<br /><br />Vous pouvez administrer l\'ensemble du site � partir de ce panneau d\'administration.<br />
Celui-ci est divis� en sous-cat�gories dans lesquelles vous trouverez tous les outils n�cessaires de maintenance et de configuration du site.<br /><br />Pour tout bug ou suggestion contactez le support sur le <a href="http://www.phpboost.com/forum/index.php" title="Forum PHPBoost officiel">forum officiel</a>.<br />Pensez �galement � consulter la <a href="http://www.phpboost.com/wiki/wiki.php">documentation officielle</a>.<br /><br /><br />Suivez les mises � jour sur <a href="http://www.phpboost.com">http://www.phpboost.com</a>';
$LANG['update_available'] = 'Mises � jour disponibles';
$LANG['core_update_available'] = 'Nouvelle version <strong>%s</strong> du noyau disponible, pensez � mettre � jour PHPBoost! <a href="http://www.phpboost.com">Plus d\'informations</a>';
$LANG['no_core_update_available'] = 'Aucune nouvelle version du noyau, vous �tes � jour!';
$LANG['module_update_available'] = 'Des mises � jour des modules sont disponibles!';
$LANG['no_module_update_available'] = 'Aucune mise � jour des modules, vous �tes � jour!';
$LANG['unknow_update'] = 'Impossible de d�terminer si une mise � jour est disponible!';
$LANG['user_online'] = 'Utilisateur(s) enregistr�(s)';
$LANG['last_update'] = 'Derni�re mise � jour';
	
//Config
$LANG['config_main'] = 'Configuration g�n�rale';
$LANG['config_advanced'] = 'Configuration avanc�e';
$LANG['serv_name'] = 'Nom du serveur';
$LANG['serv_path'] = 'Chemin de PHPBoost';
$LANG['serv_path_explain'] = 'Vide par d�faut: site � la racine du serveur';
$LANG['site_name'] = 'Nom du site';
$LANG['serv_name_explain'] = 'Ex: http://www.phpboost.com';
$LANG['site_desc'] = 'Description du site';
$LANG['site_keyword'] = 'Mots cl�s du site';
$LANG['defaut_language'] = 'Langue (par d�faut) du site';
$LANG['defaut_theme'] = 'Th�me du site';
$LANG['start_page'] = 'Page de d�marrage du site';
$LANG['no_module_starteable'] = 'Aucun module de d�marrage trouv�';
$LANG['other_start_page'] = 'Autre adresse relative ou absolue';
$LANG['activ_gzhandler'] = 'Activation de la compression des pages, ceci acc�l�re la vitesse d\'affichage';
$LANG['activ_gzhandler_explain'] = 'Attention votre serveur doit le supporter';
$LANG['view_com'] = 'Affichage des commentaires';
$LANG['rewrite'] = 'Activation de la r�ecriture des urls';
$LANG['explain_rewrite'] = 'L\'activation de la r��criture des urls permet d\'obtenir des urls bien plus simples et claires sur votre site. Ces adresses seront donc bien mieux compr�hensibles pour vos visiteurs, mais surtout pour les robots d\'indexation. Votre r�f�rencement sera grandement optimis� gr�ce � cette option.<br /><br />Cette option n\'est malheureusement pas disponible chez tout les h�bergeurs. Cette page va vous permettre de tester si votre serveur supporte la r��criture des urls. Si apr�s le tests vous tombez sur des erreurs serveur, ou page blanche, supprimez le fichier <strong>.htaccess</strong> � la racine de votre site.';
$LANG['server_rewrite'] = 'R�ecriture des urls sur votre serveur';
$LANG['current_page'] = 'Page courante';
$LANG['new_page'] = 'Nouvelle fen�tre';
$LANG['compt'] = 'Compteur';
$LANG['user_connexion'] = 'Connexion utilisateurs';
$LANG['cookie_name'] = 'Nom du cookie';
$LANG['session_time'] = 'Dur�e de la session';
$LANG['session_time_explain'] = '3600 secondes conseill�';
$LANG['session invit'] = 'Dur�e utilisateurs actifs';
$LANG['session invit_explain'] = '300 secondes conseill�';
$LANG['post_management'] = 'Gestion des posts';
$LANG['pm_max'] = 'Nombre de messages priv�s maximum';
$LANG['anti_flood'] = 'Anti-flood';
$LANG['int_flood'] = 'Intervalle minimal de temps entre les messages';
$LANG['pm_max_explain'] = 'Illimit� pour administrateurs et mod�rateurs';
$LANG['anti_flood_explain'] = 'Emp�che les messages trop rapproch�s, sauf si les visiteurs sont autoris�s';
$LANG['int_flood_explain'] = '7 secondes par d�faut';
$LANG['email_management'] = 'Gestion des emails';
$LANG['email_admin'] = 'Emails des administrateurs';
$LANG['admin_admin_status'] = 'Envoi de mail � l\'admin';
$LANG['admin_sign'] = 'Signature du mail';
$LANG['email_admin_explain'] = 'S�parez les mails par ;';
$LANG['admin_admin_status_explain'] = 'Lors d\'ajout d\'alertes g�n�r�es par le site';
$LANG['admin_sign_explain'] = 'En bas de tous les mails envoy�s par le site';
$LANG['cache_success'] = 'Le cache a �t� r�g�n�r� avec succ�s!';
$LANG['explain_site_cache'] = 'R�n�g�ration totale du cache du site � partir de la base de donn�es.
<br /><br />Le cache permet d\'am�liorer notablement la vitesse d\'ex�cution des pages, et all�ge le travail du serveur SQL. A noter que si vous faites des modifications vous-m�me dans la base de donn�es, elles ne seront visibles qu\'apr�s avoir reg�n�r� le cache';
$LANG['confirm_unlock_admin'] = 'Un email va vous �tre envoy� avec le code de d�verrouillage';
$LANG['unlock_admin_confirm'] = 'Le code de d�verrouillage a �t� renvoy� avec succ�s';
$LANG['unlock_admin'] = 'Code de d�verrouillage';
$LANG['unlock_admin_explain'] = 'Ce code permet le d�verrouillage de l\'administration en cas de tentative d\'intrusion dans l\'administration par un utilisateur mal intentionn�.';
$LANG['send_unlock_admin'] = 'Renvoyer le code de d�verrouillage';
$LANG['unlock_title_mail'] = 'Mail � conserver';
$LANG['unlock_mail'] = 'A conserver ce code (Il ne vous sera plus d�livr�) : %s

Ce code permet le d�verrouillage de l\'administration en cas de tentative d\'intrusion dans l\'administration par un utilisateur mal intentionn�.
Il vous sera demand� dans le formulaire de connexion directe � l\'administration (votreserveur/admin/admin_index.php) 

' . $CONFIG['sign'];	

//Maintain
$LANG['maintain_for'] = 'Mettre le site en maintenance';
$LANG['maintain_delay'] = 'Afficher la dur�e de la maintenance';
$LANG['maintain_text'] = 'Texte � afficher lorsque la maintenance du site est en cours';
	
//Gestion des modules
$LANG['modules_management'] = 'Gestion des modules';
$LANG['add_modules'] = 'Ajouter un module';
$LANG['update_modules'] = 'Mettre � jour un module';
$LANG['update_module'] = 'Mettre � jour';
$LANG['upload_module'] = 'Uploader un module';
$LANG['del_module'] = 'Supprimer le module';
$LANG['del_module_data'] = 'Les donn�es du module vont �tre supprim�es, attention vous ne pourrez plus les r�cup�rer!';
$LANG['del_module_files'] = 'Supprimer les fichiers du module';
$LANG['author'] = 'Auteurs';
$LANG['compat'] = 'Compatibilit�';
$LANG['use_sql'] = 'Utilise SQL';
$LANG['use_cache'] = 'Utilise le cache';
$LANG['alternative_css'] = 'Utilise un css alternatif';
$LANG['modules_installed'] = 'Modules install�s';
$LANG['modules_available'] = 'Modules disponibles';
$LANG['no_modules_installed'] = 'Aucun module install�';
$LANG['no_modules_available'] = 'Aucun module disponible';
$LANG['install'] = 'Installer';
$LANG['uninstall'] = 'D�sinstaller';
$LANG['starteable_page'] = 'Page de d�marrage';
$LANG['table'] = 'Table';
$LANG['tables'] = 'Tables';
$LANG['new_version'] = 'Nouvelle version';
$LANG['installed_version'] = 'Version install�e';

//Gestion de l'upload
$LANG['explain_upload_img'] = 'L\'image upload�e doit �tre au format jpg, gif, png ou bmp';
$LANG['explain_archive_upload'] = 'L\'archive upload�e doit �tre au format zip ou tar';

//Gestion des fichiers
$LANG['auth_files'] = 'Autorisation requise pour l\'activation de l\'interface de fichiers';
$LANG['size_limit'] = 'Taille maximum des uploads autoris�e aux membres';
$LANG['bandwidth_protect'] = 'Protection de la bande passante';
$LANG['bandwidth_protect_explain'] = 'Interdiction d\'acc�s aux fichiers du r�pertoire upload depuis un autre serveur';

//Gestion des menus
$LANG['confirm_del_menu'] = 'Supprimer ce menu?';
$LANG['menus_management'] = 'Gestion des menus';
$LANG['menus_add'] = 'Ajouter un menu';
$LANG['menus_edit'] = 'Modifier menu';
$LANG['menus_available'] = 'Menus disponibles';
$LANG['menus_explain'] = 'Le contenu des menus peut �tre r�dig� en HTML ou en BBcode. Les deux peuvent �galement �tre m�lang�.';

//Smiley
$LANG['upload_smiley'] = 'Uploader un smiley';
$LANG['smiley'] = 'Smiley';
$LANG['add_smiley'] = 'Ajouter smiley';
$LANG['smiley_code'] = 'Code du smiley (ex: :D)';
$LANG['smiley_available'] = 'Smileys disponibles';
$LANG['edit_smiley'] = 'Edition des smileys';
$LANG['smiley_management'] = 'Gestion des smileys';
$LANG['e_smiley_already_exist'] = 'Le smiley existe d�j�';	
		
//Th�mes
$LANG['upload_theme'] = 'Uploader un th�me';
$LANG['theme_on_serv'] = 'Th�mes disponibles sur le serveur';
$LANG['no_theme_on_serv'] = 'Aucun th�me <strong>compatible</strong> disponible sur le serveur';
$LANG['theme_management'] = 'Gestion des th�mes';
$LANG['theme_add'] = 'Ajouter un th�me';
$LANG['theme'] = 'Th�me';
$LANG['e_theme_already_exist'] = 'Le th�me existe d�j�';
$LANG['xhtml_version'] = 'Version Html';
$LANG['css_version'] = 'Version Css';
$LANG['main_colors'] = 'Couleurs dominantes';
$LANG['width'] = 'Largeur';
$LANG['exensible'] = 'Extensible';
$LANG['del_theme'] = 'Suppression du th�me';
$LANG['del_theme_files'] = 'Supprimer tous les fichiers du th�me';
$LANG['explain_default_theme'] = 'Le th�me par d�faut ne peut pas �tre d�sinstall�, d�sactiv�, ou r�serv�';
		
//Langues
$LANG['upload_lang'] = 'Uploader une langue';
$LANG['lang_on_serv'] = 'Langues disponibles sur le serveur';
$LANG['no_lang_on_serv'] = 'Aucune langue disponible sur le serveur';
$LANG['lang_management'] = 'Gestion des langues';
$LANG['lang_add'] = 'Ajouter une langue';
$LANG['lang'] = 'Langue';
$LANG['e_lang_already_exist'] = 'La langue existe d�j�';
$LANG['del_lang'] = 'Suppression de la langue';
$LANG['del_lang_files'] = 'Supprimer les fichiers de la langue';
$LANG['explain_default_lang'] = 'La langue par d�faut ne peut pas �tre d�sinstall�e, d�sactiv�e, ou r�serv�e';
	
//Comments
$LANG['com_config'] = 'Configuration des commentaires';		
$LANG['com_max'] = 'Nombre de commentaires par page';
$LANG['rank_com_post'] = 'Rang pour pouvoir poster des commentaires';

//Gestion membre
$LANG['job'] = 'Emploi';
$LANG['hobbies'] = 'Loisirs';
$LANG['members_management'] = 'Gestion des Membres';
$LANG['members_add'] = 'Ajouter un membre';
$LANG['members_config'] = 'Configuration des membres';
$LANG['members_punishment'] = 'Gestion des sanctions';
$LANG['members_msg'] = 'Message � tous les membres';
$LANG['search_member'] = 'Rechercher un membre';
$LANG['joker'] = 'Utilisez * pour joker';
$LANG['no_result'] = 'Aucun r�sultat';
$LANG['minute'] = 'minute';
$LANG['minutes'] = 'minutes';
$LANG['hour'] = 'heure';
$LANG['hours'] = 'heures';
$LANG['day'] = 'jour';
$LANG['days'] = 'jours';
$LANG['week'] = 'semaine';
$LANG['month'] = 'mois';
$LANG['life'] = 'A vie';
$LANG['confirm_password'] = 'Confirmer le mot de passe';
$LANG['confirm_password_explain'] = 'Remplir seulement en cas de modification';
$LANG['hide_mail'] = 'Cacher l\'email';
$LANG['hide_mail_explain'] = 'Aux autres utilisateurs';
$LANG['explain_select_multiple'] = 'Maintenez ctrl puis cliquez dans la liste pour faire plusieurs choix';
$LANG['select_all'] = 'Tout s�lectionner';
$LANG['select_none'] = 'Tout d�s�lectionner';
$LANG['website_explain'] = 'Valide sinon non pris en compte';
$LANG['member_sign'] = 'Signature';
$LANG['member_sign_explain'] = 'Appara�t sous chacun de vos messages';
$LANG['avatar_management'] = 'Gestion avatar';
$LANG['activ_up_avatar'] = 'Autoriser l\'upload d\'avatar sur le serveur';
$LANG['current_avatar'] = 'Avatar actuel';
$LANG['upload_avatar'] = 'Uploader avatar';
$LANG['upload_avatar_where'] = 'Avatar directement h�berg� sur le serveur';
$LANG['avatar_link'] = 'Lien avatar';
$LANG['avatar_link_where'] = 'Adresse directe de l\'avatar';
$LANG['avatar_del'] = 'Supprimer l\'avatar courant';
$LANG['no_avatar'] = 'Aucun avatar';
$LANG['weight_max'] = 'Poids maximum';
$LANG['height_max'] = 'Hauteur maximum';
$LANG['width_max'] = 'Largeur maximum';			
$LANG['sex'] = 'Sexe';
$LANG['male'] = 'Homme';
$LANG['female'] = 'Femme';
$LANG['verif_code'] = 'Code de v�rification visuel';
$LANG['verif_code_explain'] = 'Code demand� � l\'inscription, �vite les faux comptes';
$LANG['delay_activ_max'] = 'Dur�e apr�s laquelle les membres non activ�s sont effac�s';
$LANG['delay_activ_max_explain'] = 'Laisser vide pour ignorer cette option (Non pris en compte si validation par administrateur)';
$LANG['activ_mbr'] = 'Mode d\'activation du compte membre';
$LANG['no_activ_mbr'] = 'Automatique';
$LANG['allow_theme_mbr'] = 'Permission aux membres de choisir leur th�me';
$LANG['width_max_avatar'] = 'Largeur maximale de l\'avatar';
$LANG['width_max_avatar_explain'] = 'Par d�faut 120';
$LANG['height_max_avatar'] = 'Hauteur maximale de l\'avatar';
$LANG['height_max_avatar_explain'] = 'Par d�faut 120';
$LANG['weight_max_avatar'] = 'Poids maximal de l\'avatar en ko';
$LANG['weight_max_avatar_explain'] = 'Par d�faut 20';
$LANG['avatar_management'] = 'Gestion des avatars';
$LANG['activ_defaut_avatar'] = 'Activer l\'avatar par d�faut';
$LANG['activ_defaut_avatar_explain'] = 'Met un avatar aux membres qui n\'en ont pas';
$LANG['url_defaut_avatar'] = 'Adresse de l\'avatar par d�faut';
$LANG['url_defaut_avatar_explain'] = 'Mettre dans votre th�me dans le dossier images ';
$LANG['user_punish_until'] = 'Sanction jusqu\'au';
$LANG['user_readonly_explain'] = 'Membre en lecture seule, celui-ci peut lire mais ne peut plus poster sur le site entier (commentaires, etc...)';
$LANG['weeks'] = 'semaines';
$LANG['life'] = 'A vie';
$LANG['readonly_user'] = 'Membre en lecture seule';
$LANG['activ_register'] = 'Activer l\'inscription des membres';

//R�glement
$LANG['explain_terms'] = 'Entrez ci-dessous le r�glement � afficher lors de l\'enregistrement des membres, ils devront l\'accepter pour s\'enregistrer. Laissez vide pour aucun r�glement.';

//Gestion des groupes
$LANG['groups_management'] = 'Gestion des groupes';
$LANG['groups_add'] = 'Ajouter un groupe';
$LANG['auth_flood'] = 'Autorisation de flooder';
$LANG['pm_no_limit'] = 'Messages priv�s illimit�s';
$LANG['data_no_limit'] = 'Donn�es uploadables illimit�es';
$LANG['img_assoc_group'] = 'Image associ�e au groupe';
$LANG['img_assoc_group_explain'] = 'Mettre dans le dossier images/group/';
$LANG['add_mbr_group'] = 'Ajouter un membre au groupe';
$LANG['mbrs_group'] = 'Membres du groupe';
$LANG['auth_access'] = 'Autorisation d\'acc�s';
$LANG['auth_read'] = 'Droits de lecture';
$LANG['auth_write'] = 'Droits d\'�criture';
$LANG['auth_edit'] = 'Droits de mod�ration';
		
//Robots
$LANG['robot'] = 'Robot';
$LANG['robots'] = 'Robots';
$LANG['erase_rapport'] = 'Effacer le rapport';
$LANG['number_r_visit'] = 'Nombre de visite(s)';

//Erreurs
$LANG['all_errors'] = 'Afficher toutes les erreurs';
$LANG['error_management'] = 'Gestionnaire d\'erreurs';

//Divers
$LANG['select_type_bbcode'] = 'BBCode';
$LANG['select_type_html'] = 'HTML';

//Module de gestion de la base de donn�es
$LANG['database_management'] = 'Gestion de la base de donn�es';
$LANG['db_explain_actions'] = 'Ce panneau vous permet de g�rer votre base de donn�es. Vous pouvez y voir la liste des tables utilis�es par PHPBoost, leurs propri�t�s. Aussi divers outils vous permettront de faire quelques op�rations basiques sur certaines tables. Vous pouvez aussi effectuer une sauvegarde de votre base de donn�es, ou de seulement quelques tables que vous s�lectionnerez ici.
<br /><br />
<div class="question">L\'optimisation de la base de donn�es permet de r�organiser la structure de la table afin de faciliter les op�rations au serveur SQL. Cette op�ration est effectu�e automatiquement sur chaque table une fois par jour. Vous pouvez optimiser les tables manuellement via ce panneau d\'administration.
<br />
La r�paration n\'est normalement pas � envisager mais en cas de probl�me elle peut s\'av�rer utile. Le support vous dira de l\'effectuer quand cela sera n�cessaire.
<br />
<strong>Attention : </strong>C\'est une op�ration lourde, elle consomme beaucoup de ressources, il est donc conseill� d\'�viter de r�parer les tables si ce n\'est pas utile !</div>';
$LANG['db_restore'] = 'Restaurer la base de donn�es depuis un fichier de sauvegarde';
$LANG['db_restore_from_server'] = 'Vous pouvez utiliser les fichiers que vous n\'aviez pas supprim� lors de restaurations ant�rieures.';
$LANG['db_view_file_list'] = 'Voir la liste des fichiers disponibles (<em>cache/backup</em>)';
$LANG['import_file_explain'] = 'Vous pouvez restaurer votre base de donn�es par un fichier que vous poss�dez sur votre ordinateur. Si votre fichier d�passe la taille maximale autoris�e par votre serveur, c\'est-�-dire %s, vous devez utiliser l\'autre m�thode en envoyant par FTP votre fichier dans le r�pertoire <em>cache/backup</em>.';
$LANG['db_restore'] = 'Restaurer';
$LANG['db_table_list'] = 'Liste des tables';
$LANG['db_table_name'] = 'Nom de la table';
$LANG['db_table_rows'] = 'Enregistrements';
$LANG['db_table_engine'] = 'Type';
$LANG['db_table_collation'] = 'Interclassement';
$LANG['db_table_data'] = 'Taille';
$LANG['db_table_free'] = 'Perte';
$LANG['db_selected_tables'] = 'Tables s�lectionn�es';
$LANG['db_select_all'] = 'toutes';
$LANG['db_for_selected_tables'] = 'Actions � r�aliser sur la s�lection de tables';
$LANG['db_optimize'] = 'Optimiser';
$LANG['db_repair'] = 'R�parer';
$LANG['db_backup'] = 'Sauvegarder';
$LANG['db_succes_repair_tables'] = 'La s�lection de tables (<em>%s</em>) a �t� r�par�e avec succ�s';
$LANG['db_succes_optimize_tables'] = 'La s�lection de tables (<em>%s</em>) a �t� optimis�e avec succ�s';
$LANG['db_backup_database'] = 'Sauvegarder la base de donn�es';
$LANG['db_selected_tables'] = 'Tables s�lectionn�es';
$LANG['db_backup_explain'] = 'Vous pouvez encore modifier la liste des tables que vous souhaitez s�lectionner dans le formulaire.
<br />
Ensuite vous devez choisir ce que vous souhaitez sauvegarder.';
$LANG['db_backup_all'] = 'Donn�es et structure';
$LANG['db_backup_struct'] = 'Structure seulement';
$LANG['db_backup_data'] = 'Donn�es seulement';
$LANG['db_backup_success'] = 'Votre base de donn�es a �t� correctement sauvegard�e. Vous pouvez la t�l�charger en suivant le lien suivant : <a href="admin_database.php?read_file=%s">%s</a>';
$LANG['db_execute_query'] = 'Ex�cuter une requ�te dans la base de donn�es';
$LANG['db_tools'] = 'Outils de gestion de la base de donn�es';
$LANG['db_query_explain'] = 'Vous pouvez dans ce panneau d\'administration ex�cuter des requ�tes dans la base de donn�es. Cette interface ne devrait servir que lorsque le support vous demande d\'ex�cuter une requ�te dans la base de donn�es qui vous sera communiqu�e.<br />
<strong>Attention:</strong> si cette requ�te n\'a pas �t� propos�e par le support vous �tes responsable de son ex�cution et des pertes de donn�es qu\'elle pourrait provoquer. Il est donc fortement d�conseill� d\'utiliser ce module si vous ne ma�trisez pas compl�tement la structure des tables de PHPBoost.';
$LANG['db_submit_query'] = 'Ex�cuter';
$LANG['db_query_result'] = 'R�sultat de la requ�te suivante';
$LANG['db_executed_query'] = 'Requ�te SQL';
$LANG['db_file_list'] = 'Liste des fichiers';
$LANG['db_confirm_restore'] = 'Etes-vous s�r de vouloir restaurer votre base de donn�es � partir de la sauvegarde s�lectionn�e?';
$LANG['db_restore_file'] = 'Cliquez sur le fichier que vous voulez restaurer.';
$LANG['db_restore_success'] = 'La restauration de la base de donn�es a �t� effectu�e avec succ�s';
$LANG['db_restore_failure'] = 'Une erreur est survenue pendant la restauration de la base de donn�es';
$LANG['db_upload_failure'] = 'Une erreur est survenue lors du transfert du fichier � partir duquel vous souhaitez importer votre base de donn�es';
$LANG['db_file_already_exists'] = 'Un fichier du r�pertoire cache/backup porte le m�me nom que celui que vous souhaitez importer. Merci de renommer un des deux fichiers pour pouvoir l\'importer.';
$LANG['db_unlink_success'] = 'Le fichier a �t� supprim� avec succ�s !';
$LANG['db_unlink_failure'] = 'Le fichier n\'a pas pu �tre supprim�';
$LANG['db_file_does_not_exist'] = 'Le fichier que vous souhaitez supprimer n\'existe pas ou n\'est pas un fichier SQL';
$LANG['db_empty_dir'] = 'Le dossier est vide';
$LANG['db_file_name'] = 'Nom du fichier';
$LANG['db_file_weight'] = 'Taille du fichier';

//Statistiques
$LANG['stats'] = 'Statistiques';
$LANG['more_stats'] = 'Plus de stats';
$LANG['site'] = 'Site';
$LANG['browser_s'] = 'Navigateurs';
$LANG['fai'] = 'Fournisseurs d\'acc�s Internet';
$LANG['all_fai'] = 'Voir la liste compl�te des fournisseurs d\'acc�s Internet';
$LANG['10_fai'] = 'Voir les 10 principaux fournisseurs d\'acc�s Internet';
$LANG['os'] = 'Syst�mes d\'exploitation';
$LANG['other'] = 'Autres';
$LANG['number'] = 'Nombre ';
$LANG['start'] = 'Cr�ation du site';
$LANG['stat_lang'] = 'Pays des visiteurs';
$LANG['all_langs'] = 'Voir la liste compl�te des pays des visiteurs';
$LANG['10_langs'] = 'Voir les 10 principaux pays des visiteurs';
$LANG['visits_year'] = 'Voir les statistiques de l\'ann�e';
$LANG['unknown'] = 'Inconnu';
$LANG['last_member'] = 'Dernier membre';
$LANG['top_10_posters'] = 'Top 10: posteurs';
$LANG['version'] = 'Version';
$LANG['colors'] = 'Couleurs';
$LANG['calendar'] = 'Calendrier';
$LANG['events'] = 'Ev�nements';
$LANG['january'] = 'Janvier';
$LANG['february'] = 'F�vrier';
$LANG['march'] = 'Mars';
$LANG['april'] = 'Avril';
$LANG['may'] = 'Mai';
$LANG['june'] = 'Juin';
$LANG['july'] = 'Juillet';
$LANG['august'] = 'Ao�t';
$LANG['september'] = 'Septembre';
$LANG['october'] = 'Octobre';
$LANG['november'] = 'Novembre';
$LANG['december'] = 'D�cembre';
$LANG['monday'] = 'Lun';
$LANG['tuesday'] = 'Mar';
$LANG['wenesday'] = 'Mer';
$LANG['thursday'] = 'Jeu';
$LANG['friday'] = 'Ven';
$LANG['saturday'] = 'Sam';
$LANG['sunday']	= 'Dim';
?>