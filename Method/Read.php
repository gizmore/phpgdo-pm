<?php
namespace GDO\PM\Method;

use GDO\Core\Method;
use GDO\Date\Time;
use GDO\PM\GDO_PM;
use GDO\PM\PMMethod;
use GDO\User\GDO_User;
use GDO\Util\Common;

final class Read extends Method
{
	use PMMethod;
	
	public function execute()
	{
		if (!($pm = GDO_PM::getByIdAndUser(Common::getRequestString('id'), GDO_User::current())))
		{
			return $this->error('err_pm');
		}
		return $this->pmRead($pm);
	}
	
	public function pmRead(GDO_PM $pm)
	{
		if (!$pm->isRead())
		{
			$pm->saveVar('pm_read_at', Time::getDate());
			$owner = $pm->getOwner();
			$owner->tempUnset('gdo_pm_unread')->recache();
			$pm->getOtherPM()->saveVar('pm_other_read_at', Time::getDate());
		}
		return $this->responsePHP('card_pm.php', ['pm' => $pm]);
	}
	
}
