<?php
namespace GDO\PM\Method;

use GDO\Core\Method;
use GDO\Date\Time;
use GDO\PM\GDO_PM;
use GDO\PM\GDT_PM;
use GDO\PM\PMMethod;
use GDO\User\GDO_User;

/**
 * Read a PM.
 *
 * @version 7.0.1
 * @author gizmore
 */
final class Read extends Method
{

	use PMMethod;

	private GDO_PM $pm;

	public function gdoParameters(): array
	{
		return [
			GDT_PM::make('id')->notNull(),
		];
	}

	public function getMethodTitle(): string
	{
		$user = GDO_User::current();
		$pm = $this->getPM();
		if ($pm->isFrom($user))
		{
			return t('pm_to', [$pm->getReceiver()->renderUserName(), $pm->displayAge(), $pm->displayReadAgo()]);
		}
		elseif ($pm->isTo($user))
		{
			return t('pm_by', [$pm->getSender()->renderUserName(), $pm->displayAge(), $pm->displayReadAgo()]);
		}
		else
		{
			return t('err_pm');
		}
	}

	private function getPM(): GDO_PM
	{
		if (!isset($this->pm))
		{
			$this->pm = $this->gdoParameterValue('id');
		}
		return $this->pm;
	}

	public function execute()
	{
		if (!($pm = $this->getPM()))
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
		return $this->templatePHP('card_pm.php', ['pm' => $pm]);
	}

}
