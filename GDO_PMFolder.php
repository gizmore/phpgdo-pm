<?php
namespace GDO\PM;

use GDO\Core\GDO;
use GDO\Core\GDT_AutoInc;
use GDO\Core\GDT_Int;
use GDO\User\GDT_User;
use GDO\User\GDO_User;
use GDO\UI\GDT_Title;

/**
 * A PM folder.
 * There are two default folders that are shared in DB. Id 1 and 2 - Inbox and Outbox.
 * 
 * @author gizmore
 * @version 6.10.1
 * @since 3.5.0
 */
final class GDO_PMFolder extends GDO
{
	# Constants
	const INBOX = '1';
	const OUTBOX = '2';
	
	###########
	### GDO ###
	###########
	public function memCached() { return false; }
	public function gdoColumns() : array
	{
		return [
			GDT_AutoInc::make('pmf_id'),
			GDT_User::make('pmf_user')->notNull(),
			GDT_Title::make('pmf_name')->notNull(),
			GDT_Int::make('pmf_count')->unsigned()->initial('0')->label('count'),
		];
	}
	public function getID() : ?string { return $this->gdoVar('pmf_id'); }
	public function getUserID() { return $this->gdoVar('pmf_user'); }
	public function getName() { return $this->gdoVar('pmf_name'); }
	public function displayName() { return $this->gdoDisplay('pmf_name'); }
	
	/**
	 * @param string $userid
	 * @return array
	 */
	public static function getFolders($userid)
	{
		static $folders;
		if (!isset($folders))
		{
			$folders = array_merge(
				GDO_PMFolder::getDefaultFolders(),
				self::table()->select()->where('pmf_user='.quote($userid))->exec()->fetchAllObjects()
			);
		}
		return $folders;
	}
	
	/**
	 * @param int $folderId
	 * @param GDO_User $user
	 * @return GDO_PMFolder
	 */
	public static function getByIdAndUser($folderId, GDO_User $user)
	{
		$folderId = $folderId;
		switch ($folderId)
		{
			case self::INBOX: return self::getInBox();
			case self::OUTBOX: return self::getOutBox();
		}
		if ($folder = self::table()->find($folderId, false))
		{
			if ($folder->getUserID() === $user->getID())
			{
				return $folder;
			}
		}
	}
	
	#######################
	### Default Folders ###
	#######################
	public static function getDefaultFolders()
	{
		return [self::getInBox(), self::getOutBox()];
	}
	
	public static function getInBox()
	{
		static $inbox;
		if (!isset($inbox))
		{
			$uid = GDO_User::current()->getID();
			$fid = self::INBOX;
			$inbox = self::blank([
				'pmf_id' => $fid,
				'pmf_user' => $uid,
				'pmf_name' => t('inbox_name'),
				'pmf_count' => GDO_PM::table()->countWhere("pm_folder=$fid AND pm_owner=$uid AND pm_deleted_at IS NULL"),
			]);
		}
		return $inbox;
	}
	
	public static function getOutBox()
	{
		static $outbox;
		if (!isset($outbox))
		{
			$uid = GDO_User::current()->getID();
			$fid = self::OUTBOX;
			$outbox = self::blank([
				'pmf_id' => $fid,
				'pmf_user' => $uid,
				'pmf_name' => t('outbox_name'),
				'pmf_count' => GDO_PM::table()->countWhere("pm_folder=$fid AND pm_owner=$uid AND pm_deleted_at IS NULL"),
			]);
		}
		return $outbox;
	}

}
