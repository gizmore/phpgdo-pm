<?php
namespace GDO\PM\Method;

use GDO\Core\Method;
use GDO\DB\Database;
use GDO\Date\Time;
use GDO\PM\GDO_PM;
use GDO\PM\GDO_PMFolder;
use GDO\PM\PMMethod;
use GDO\User\GDO_User;
use GDO\Util\Common;

/**
 * Main PM Functionality / Navigation
 * @author gizmore
 */
final class Overview extends Method
{
	use PMMethod;
	
	public function isUserRequired() : bool { return true; }
	
	public function execute()
	{
		if (isset($_REQUEST['delete']))
		{
			return $this->onDelete()->addField($this->pmOverview());
		}
		elseif (isset($_REQUEST['move']))
		{
		    return $this->onMove()->addField($this->pmOverview());
		}
		return $this->pmOverview();
	}
	
	public function getTitle()
	{
	    return t('btn_pm');
	}
	
	private function pmOverview()
	{
	    $tVars = [
	        'folders' => Folders::make()->executeWithInit(),
	        'folder' => Folder::make()->executeWithInit(),
	    ];
		return $this->templatePHP('overview.php', $tVars);
	}
	
	##############
	### Delete ###
	##############
	private function onDelete()
	{
		if ($ids = $this->getRBX())
		{
			$user = GDO_User::current();
			$now = Time::getDate();
			GDO_PM::table()->update()->set("pm_deleted_at='$now', pm_read_at='$now'")->where("pm_owner={$user->getID()} AND pm_id IN($ids)")->exec();
			$affected = Database::instance()->affectedRows();
			GDO_PM::updateOtherDeleted();
			return $this->message('msg_pm_deleted', [$affected]);
		}
	}
	
	private function onMove()
	{
		$user = GDO_User::current();
		if (!($folder = GDO_PMFolder::getByIdAndUser(Common::getFormString('folder'), $user)))
		{
			return $this->error('err_pm_folder');
		}
		if ($ids = $this->getRBX())
		{
			GDO_PM::table()->update()->set("pm_folder={$folder->getID()}")->where("pm_owner={$user->getID()} AND pm_id IN($ids)")->exec();
			$affected = Database::instance()->affectedRows();
			return $this->message('msg_pm_moved', [$affected, $folder->displayName()]);
		}
	}
	
}
