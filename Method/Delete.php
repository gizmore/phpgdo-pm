<?php
namespace GDO\PM\Method;

use GDO\Core\Method;
use GDO\Date\Time;
use GDO\PM\GDO_PM;
use GDO\PM\PMMethod;
use GDO\Util\Common;

/**
 * Delete a PM via tokenhash from mail.
 * 
 * @author gizmore
 */
final class Delete extends Method
{
	use PMMethod;
	
	public function execute()
	{
		if ( (!($pm = GDO_PM::getById(Common::getRequestString('pm')))) || 
				($pm->gdoHashcode() !== Common::getRequestString('token')) )
		{
			return $this->error('err_pm')->addField(Overview::make()->execute());
		}
		return $this->onDelete($pm)->addField(Overview::make()->execute());
	}
	
	public function deletePM(GDO_PM $pm)
	{
		$t = Time::getDate();
		$pm->saveVars([
			'pm_read_at' => $t,
			'pm_deleted_at' => $t,
		]);
		$pm->getOtherPM()->saveVar('pm_other_deleted_at', $t);
		return $this->message('msg_pm_deleted');
	}
	
}
