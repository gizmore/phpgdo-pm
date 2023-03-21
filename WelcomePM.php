<?php
namespace GDO\PM;

use GDO\PM\Method\Write;
use GDO\User\GDO_User;

/**
 * Send a welcome PM.
 *
 * @version 7.0.1
 * @since 7.0.1
 * @author gizmore
 * @see ResendWelcome
 */
final class WelcomePM
{

	public static function deliver(GDO_User $user)
	{
		$module = Module_PM::instance();
		if ($bot = $module->cfgBotUser())
		{
			self::sendWelcomePM(Write::make(), $bot, $user);
		}
	}

	private static function sendWelcomePM(Write $method, GDO_User $from, GDO_User $to)
	{
		$title = tusr($to, 'pm_welcome_title', [sitename()]);
		$message = tusr($to, 'pm_welcome_message', [$to->renderName(), sitename()]);
		$method->deliver($from, $to, $title, $message);
	}

}
