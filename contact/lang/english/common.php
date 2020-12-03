<?php
/**
 * @copyright   &copy; 2005-2020 PHPBoost
 * @license     https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL-3.0
 * @author      Julien BRISWALTER <j1.seth@phpboost.com>
 * @version     PHPBoost 6.0 - last update: 2020 12 03
 * @since       PHPBoost 4.0 - 2013 08 01
 * @contributor Sebastien LARTIGUE <babsolune@phpboost.com>
*/

####################################################
#                      English                     #
####################################################

// Module title
$lang['module.title'] = 'Contact';
$lang['module.config.title'] = 'Contact module configuration';

// Form
$lang['contact.form.message'] = 'Message';
$lang['contact.send.another.mail'] = 'Send another mail';
$lang['contact.tracking.number'] = 'tracking number';
$lang['contact.acknowledgment.title'] = 'Confirmation';
$lang['contact.acknowledgment'] = 'Your message has been successfully sent.';

// Configuration
$lang['contact.form.title'] = 'Form title';
$lang['contact.informations.enabled'] = 'Display the informations bloc';
$lang['contact.informations.description'] = 'This bloc permits to display informations (i.e. a contact phone number) on the left, top, right or bottom the contact form.';
$lang['contact.informations.content'] = 'Informations bloc content';
$lang['contact.informations.position'] = 'Informations bloc position';
$lang['contact.informations.position.left'] = 'Left';
$lang['contact.informations.position.top'] = 'Top';
$lang['contact.informations.position.right'] = 'Right';
$lang['contact.informations.position.bottom'] = 'Bottom';
$lang['contact.tracking.number.enabled'] = 'Generate a tracking number for each email sent';
$lang['contact.date.in.tracking.number.enabled'] = 'Display day date in the tracking number';
$lang['contact.date.in.tracking.number.description'] = 'Allows to generate a tracking number like <b>yyyymmdd-number</b>';
$lang['contact.sender.acknowledgment.enabled'] = 'Send a copy of the email to the sender';
$lang['contact.authorizations.read']  = 'Permission to display the contact form';
$lang['contact.authorizations.display.field']  = 'Permission to display the field';

// Map
$lang['contact.map.location'] = 'Location on a map';
$lang['contact.map.enabled'] = 'Display the map';
$lang['contact.map.position'] = 'Position of the map';
$lang['contact.map.position.top'] = 'Above the form';
$lang['contact.map.position.bottom'] = 'Below the form';
$lang['contact.map.markers'] = 'Marker(s)';

// Fields
$lang['contact.fields.management'] = 'Fields management';
$lang['contact.fields.management.title'] = 'Contact module form fields management';
$lang['contact.fields.add.field'] = 'Add a new field';
$lang['contact.fields.add.field.title'] = 'Add a new field in the contact form';
$lang['contact.fields.edit.field'] = 'Field edition';
$lang['contact.fields.edit.field.title'] = 'Field edition in the contact form';

// Field
$lang['contact.field.possible.values.email'] = 'Mail address(es)';
$lang['contact.field.possible.values.email.explain'] = 'It is possible to put more than one mail address separated by a comma';
$lang['contact.field.possible.values.subject'] = 'Subject';
$lang['contact.field.possible.values.recipient'] = 'Recipient(s)';
$lang['contact.field.possible.values.recipient.explain'] = 'The mail will ve sent to the selected recipient(s) if the recipients field is not displayed';

// SEO
$lang['contact.seo.description'] = ':site\'s contact form.';

// Alert messages
$lang['contact.message.success.add'] = 'The field <b>:name</b> has been added';
$lang['contact.message.success.edit'] = 'The field <b>:name</b> has been modified';
$lang['contact.message.field.name.already.used'] = 'The entered field name is already used!';
$lang['contact.message.success.mail'] = 'Thank you, your e-mail has been sent successfully.';
$lang['contact.message.acknowledgment'] = 'You should receive a confirmation email in a few minutes.';
$lang['contact.message.error.mail'] = 'Sorry, your e-mail couldn\'t be sent.';
?>
