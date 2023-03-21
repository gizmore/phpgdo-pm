<?php
namespace GDO\PM\lang;

return [
	'module_pm' => 'PM',
	# Config Admin
	'cfg_pm_re' => 'Response title prefix',
	'cfg_pm_limit' => 'Max PMs within PM limit timeout',
	'cfg_pm_limit_timeout' => 'PM limit timeout ',
	'cfg_pm_max_folders' => 'Max custom folders',
	'cfg_pm_for_guests' => 'Allow guests to write PM',
	'cfg_pm_captcha' => 'Enable Captcha for writing PM',
	'cfg_pm_causes_mail' => 'Enable Mail on PM',
	'cfg_pm_bot_uid' => 'Bot UserId',
	'cfg_pm_own_bot' => 'Use non system PM bot?',
	'cfg_pm_per_page' => 'PM per page',
	'cfg_pm_welcome' => 'Write welcome PM?',
	'cfg_pm_sig_len' => 'Max signature length',
	'cfg_pm_msg_len' => 'Max message length',
	'cfg_pm_title_len' => 'Max title length',
	'cfg_pm_fname_len' => 'Max foldername length',
	'cfg_pm_delete' => 'Really delete PM?',
	'cfg_pm_limit_per_level' => 'Level for one more max PM',
	# Config User
	'cfg_pm_level' => 'Required userlevel to PM you',
	'cfg_pm_email' => 'Send mail when you recieve a PM?',
	'cfg_pm_guests' => 'Allow guests to PM you?',
	'cfg_signature' => 'Your signature',
	'link_pm_center' => 'Goto PM Overview',
	# Navbar
	'link_settings' => 'Settings',
	'link_trashcan' => 'Trashcan',
	'link_write_pm' => 'Write New PM',
	# Trashcan
	'name_trashcan' => 'Trashcan',
	'inbox_name' => 'Incoming',
	'outbox_name' => 'Sent',
	'btn_restore' => 'Restore',
	'btn_empty' => 'Empty',
	'btn_delete' => 'Delete',
	'btn_pm' => 'Private Messages',
	'btn_pm_unread' => 'Private Messages [%s]',
	'msg_pm_destroyed' => '%s PMs have been finally deleted.',
	'msg_pm_restored' => '%s PMs have been restored.',
	# Write
	'mt_pm_write' => 'Write Private Message',
	'msg_pm_sent' => 'Your private message has been sent.',
	'err_pm_limit_reached' => 'You have exceeded your PM limit of %s messages within %s.',
	'err_no_pm_self' => 'You cannot write PM to yourself.',
	'err_only_pm_users' => 'You cannot PM this user.',
	# Settings
	'pm_email' => 'E-Mail on PM',
	'pm_level' => 'Userlevel needed to PM you',
	'signature' => 'Your Signature',
	'pm_guests' => 'Guests may PM you',
	# Read
	'pm_by' => 'From: %s, %s; %s',
	'pm_to' => '  To: %s, %s; %s',
	'pm_from' => 'From: %s, %s',
	'pm_sent' => 'Sent on %s',
	'pm_read' => 'Read %s ago',
	'pm_unread' => 'Unread',
	# Overview
	'btn_move' => 'Move to folder',
	'msg_pm_deleted' => 'There were %s PM deleted in total.',
	'err_pm_folder' => 'PM folder could not be found.',
	'err_pm' => 'This PM could not be found.',
	'pm_sent' => 'Sent, %s',
	'pm_received' => 'Received, %s',
	'pm_folder' => 'Folder %s with %s message(s)',
	# PM
	'from_user' => 'From',
	'to_user' => 'To',
	'delete' => 'Delete',
	'reply' => 'Reply',
	'quote' => 'Quote',
	'show' => 'Show',
// 	'pm_fromto_from' => 'From: %s',
// 	'pm_fromto_to' => 'To: %s',
	# Delete
	'mt_pm_delete' => 'Delete PM',
	# Folders
	'list_pm_folders' => '%s Folders',
	################################################
	'pm_welcome_title' => 'Welcome to %s',
	'pm_welcome_message' => '
Hello %s,
	
We wish you a graceful welcome on %s.
We hope you enjoy the site and your sessions.
	
Kind Regards
The %2$s Team',
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
	'list_pm_trashcan' => '0 messages in the trashcan',

	# v7.0.1
	'mt_pm_reply' => 'Reply to PM',
	'mt_pm_folder' => 'PM Folder',
	'mt_pm_folders' => 'PM Folders',
	'mt_pm_trashcan' => 'PM Trashcan',
	'list_pm_folder' => '%s PM in %s',
	'mt_pm_resendwelcome' => 'Resend Welcome PM',
	'msg_welcome_pm_resent' => 'Your Welcome PM has been resent.',
	'div_info_privacy_pm' => 'Private messages are not encrypted and could be read by an administrator. There is currently no other option.',
	'pm_from' => 'From %s, %s ago',
];
