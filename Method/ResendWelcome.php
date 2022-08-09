<?php
namespace GDO\PM\Method;

use GDO\Core\Method;
use GDO\PM\WelcomePM;
use GDO\User\GDO_User;

/**
 * Resend the welcome PM.
 * 
 * @author gizmore
 * @version 7.0.1
 * @since 7.0.1
 */
final class ResendWelcome extends Method
{
	public function execute()
	{
		$user = GDO_User::current();
		WelcomePM::deliver($user);
		return $this->redirectMessage('msg_welcome_pm_resent');
	}
	
}