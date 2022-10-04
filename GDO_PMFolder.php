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
 * There are two default folders that are shared in DB.
 * Id 1 and 2 - Inbox and Outbox.
 * 
 * @author gizmore
 * @version 7.0.1
 * @since 3.5.0
 * @see GDO_PM
 * @see GDT_PMFolder
 */
final class GDO_PMFolder extends GDO
{
	# Constants
	const INBOX = '1';
	const OUTBOX = '2';
	
	###########
	### GDO ###
	###########
	public function memCached() : bool { return false; }
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
	public function getUserID() : string { return $this->gdoVar('pmf_user'); }
	public function getName() : ?string { return $this->gdoVar('pmf_name'); }
	public function renderName() : string { return $this->gdoDisplay('pmf_name'); }

	##############
	### Events ###
	##############
	public static function init() : void
	{
		self::$INBOX = self::getDefaultFolder(self::INBOX, 'inbox_name');
		self::$OUTBOX = self::getDefaultFolder(self::OUTBOX, 'outbox_name');
	}
	
	##############
	### Static ###
	##############
	/**
	 * Get all folders for a user.
	 * @return GDO_PMFolder[]
	 */
	public static function getFolders(string $userid) : array
	{
		return array_merge(
			GDO_PMFolder::getDefaultFolders(),
			self::table()->select()->where('pmf_user='.quote($userid))->exec()->fetchAllObjects(),
		);
	}

	/**
	 * Get a specified folder for a user.
	 */
	public static function getByIdAndUser(string $folderId, GDO_User $user) : ?self
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
		return null;
	}
	
	#######################
	### Default Folders ###
	#######################
	public static function getDefaultFolders() : array
	{
		return [
			self::getInBox(),
			self::getOutBox(),
		];
	}
	
	public static function getInBox() : self
	{
		return self::getDefaultFolder('1', 'inbox_name');
	}
	
	public static function getOutBox() : self
	{
		return self::getDefaultFolder('2', 'outbox_name');
	}

	private static function getDefaultFolder(string $fid, string $textKey) : self
	{
		$uid = GDO_User::current()->getID();
		return self::blank([
			'pmf_id' => $fid,
			'pmf_user' => $uid,
			'pmf_name' => t($textKey),
			'pmf_count' => GDO_PM::table()->countWhere("pm_folder=$fid AND pm_owner=$uid AND pm_deleted_at IS NULL"),
		]);
	}
	
}
