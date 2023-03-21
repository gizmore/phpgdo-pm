<?php
namespace GDO\PM\lang;

return [
	'module_pm' => 'Privatnachrichten',
	# Config Admin
	'cfg_pm_re' => 'Titelvoranstellung für Antworten',
	'cfg_pm_limit' => 'Maximal PN innerhalb des Timeouts',
	'cfg_pm_limit_timeout' => 'PM Limit Timeout',
	'cfg_pm_max_folders' => 'Maximale Anzahl eigener Ordner',
	'cfg_pm_for_guests' => 'Erlaube Gästen PN zu schreiben?',
	'cfg_pm_captcha' => 'Captcha für PN schreiben?',
	'cfg_pm_causes_mail' => 'Email bei PN senden?',
	'cfg_pm_bot_uid' => 'Bot UserId',
	'cfg_pm_own_bot' => 'Einen extra PN bot verwenden?',
	'cfg_pm_per_page' => 'PN pro Seite',
	'cfg_pm_welcome' => 'Willkommens-PN senden?',
	'cfg_pm_sig_len' => 'Maximale Signatur Länge',
	'cfg_pm_msg_len' => 'Maximale Nachrichten Länge',
	'cfg_pm_title_len' => 'Maximale Titel Länge',
	'cfg_pm_fname_len' => 'Maximale Ordnernamen Länge',
	'cfg_pm_delete' => 'PN wirklich löschen?',
	'cfg_pm_limit_per_level' => 'Nutzerlevel für eine PN mehr',
	# Config User
	'cfg_pm_level' => 'Benötigter Nutzerlevel um Ihnen eine PN zu senden',
	'cfg_pm_email' => 'Sie möchten per Email bei einer neuen PN benachrichtigt werden?',
	'cfg_pm_guests' => 'Gästen erlauben Ihnen eine PN zu schreiben?',
	'cfg_signature' => 'Ihre Signatur',
	'link_pm_center' => 'Zur PN Übersicht',
	# Navbar
	'link_settings' => 'Einstellungen',
	'link_trashcan' => 'Mülleimer',
	'link_write_pm' => 'PN verfassen',
	# Trashcan
	'name_trashcan' => 'Mülleimer',
	'inbox_name' => 'Eingehend',
	'outbox_name' => 'Gesendet',
	'btn_restore' => 'Wiederherstellen',
	'btn_empty' => 'Leeren',
	'btn_delete' => 'Löschen',
	'btn_pm' => 'Private Nachrichten',
	'btn_pm_unread' => 'Private Nachrichten [%s]',
	'msg_pm_destroyed' => '%s PN wurde(n) gelöscht.',
	'msg_pm_restored' => '%s PN wurde(n) wiederhergestellt.',
	# Write
	'mt_pm_write' => 'Private Nachricht verfassen',
	'msg_pm_sent' => 'Ihre private Nachricht wurde gesendet.',
	'err_pm_limit_reached' => 'Sie haben Ihr PN Limit von %s Nachrichten innerhalb %s erreicht.',
	'err_no_pm_self' => 'Sie können sich selbst keine PN schreiben.',
	'err_only_pm_users' => 'Sie können diesem Nutzer keine PN schreiben.',
	# Settings
	'pm_email' => 'Email bei neuer PN?',
	'pm_level' => 'Benötigter Nutzerlevel um Ihnen eine PN zu schreiben',
	'signature' => 'Ihre Signatur',
	'pm_guests' => 'Gäste dürfen Ihnen PN schreiben?',
	# Read
	'pm_by' => 'Von: %s, vor %s; %s.',
	'pm_to' => '  An: %s, vor %s; %s',
	'pm_sent' => 'Gesendet am %s',
	'pm_read' => 'Gelesen vor %s',
	'pm_unread' => 'Ungelesen',

	# Overview
	'btn_move' => 'In Ordner verschieben',
	'msg_pm_deleted' => 'Es wurden %s PN geöschtet.',
	'err_pm_folder' => 'Der PN Ordner konnte nicht gefunden werden.',
	'err_pm' => 'Die PN konnte nicht gefunden werden.',
	'pm_sent' => 'Gesendet, %s',
	'pm_received' => 'Empfangen, %s',
	'pm_folder' => 'Ordner %s mit %s Nachricht(en)',
	# PM
	'from_user' => 'Von',
	'to_user' => 'An',
	'delete' => 'Löschen',
	'reply' => 'Antworten',
	'quote' => 'Zitieren',
	'show' => 'Anzeigen',
// 	'pm_fromto_from' => 'Von: %s',
// 	'pm_fromto_to' => 'An: %s',
	# Delete
	'mt_pm_delete' => 'PN löschen',
	# Folders
	'list_pm_folders' => '%s Ordner',
	################################################
	'pm_welcome_title' => 'Willkommen auf %s',
	'pm_welcome_message' => '
Hallo %s,
	
Wir heissen Dich herzlich Willkommen auf %s.
Wir hoffen Du hast Spass mit dieser Webseite.
	
Viele Grüße
Das %2$s Team',
################################################
	'msg_pm_mail_sent' => 'We have also sent an email to %s.',
	'mail_subj_pm' => '%s: PM from %s',
	'mail_body_pm' => '
Hello %s,
	
The user %s has sent you a private message on %s.
	
Title: %s
	
=======================================================
	
%s
	
=======================================================
	
You can delete this message with one click: %s
	
Kind Regards
The %3$s Team',

	# v6.10.6
	'list_pm_trashcan' => '0 Nachrichten im Mülleimer',

	# v7.0.1
	'mt_pm_reply' => 'PN Antworten',
	'mt_pm_folder' => 'PN Ordner',
	'mt_pm_folders' => 'PN Ordner',
	'mt_pm_trashcan' => 'PN Mülleimer',
	'list_pm_folder' => '%s PM in %s',
	'mt_pm_resendwelcome' => 'Resend Welcome PM',
	'msg_welcome_pm_resent' => 'Your Welcome PM has been resent.',
	'div_info_privacy_pm' => 'Private Nachrichten sind nicht verschlüsselt. Dies lässt sich z.Zt. auch nicht einstellen.',
	'pm_from' => 'Von %s, vor/am %s',
];
