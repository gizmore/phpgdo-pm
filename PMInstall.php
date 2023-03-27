<?php
namespace GDO\PM;

use GDO\Date\Time;
use GDO\User\GDO_User;
use GDO\User\GDT_UserType;

/**
 * Install INBOX/OUTBOX and BOT USER.
 *
 * @version 6.10
 * @since 3.05
 * @author gizmore
 */
final class PMInstall
{

	public static function install(Module_PM $module)
	{
		self::installFolders($module) .
		self::installPMBotID($module);
	}

	private static function installFolders(Module_PM $module)
	{
		if (!GDO_PMFolder::table()->countWhere('true'))
		{
			$systemID = GDO_User::system()->getID();
			GDO_PMFolder::blank(['pmf_name' => 'INBOX', 'pmf_user' => $systemID])->insert();
			GDO_PMFolder::blank(['pmf_name' => 'OUTBOX', 'pmf_user' => $systemID])->insert();
		}
	}

	private static function installPMBotID(Module_PM $module)
	{
		if (!$module->cfgBotUser())
		{
			if ($module->cfgOwnBot())
			{
				self::installPMBot($module);
			}
			else
			{
				self::installAdminAsPMBot($module);
			}
		}
	}

	private static function installPMBot(Module_PM $module)
	{
		$user = GDO_User::blank([
			'user_name' => '_PM_BOT_',
			'user_real_name' => GDO_BOT_NAME,
			'user_type' => GDT_UserType::BOT,
			'user_email' => GDO_BOT_EMAIL,
			'user_register_time' => Time::getDate(),
		]);
		$user->insert();
		$module->saveConfigVar('pm_bot_uid', $user->getID());
	}

	private static function installAdminAsPMBot(Module_PM $module)
	{
		$user = GDO_User::system();
		$module->saveConfigVar('pm_bot_uid', $user->getID());
	}

}
