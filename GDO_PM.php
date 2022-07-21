<?php
namespace GDO\PM;

use GDO\Core\GDO;
use GDO\Core\GDT_AutoInc;
use GDO\Core\GDT_CreatedAt;
use GDO\DB\GDT_DeletedAt;
use GDO\Core\GDT_Object;
use GDO\Date\GDT_DateTime;
use GDO\Date\Time;
use GDO\Core\GDT_Template;
use GDO\UI\GDT_Message;
use GDO\User\GDT_User;
use GDO\User\GDO_User;
use GDO\UI\GDT_Title;
use GDO\Core\GDT_Index;
use GDO\Date\GDT_Timestamp;

/**
 * A PM entity.
 * 
 * @author gizmore
 * @version 6.11.0
 * @since 3.5.0
 */
final class GDO_PM extends GDO
{
	public function gdoCached() : bool { return false; }
	
	###########
	### GDO ###
	###########
	public function gdoColumns() : array
	{
		return array(
			GDT_AutoInc::make('pm_id'),
			GDT_CreatedAt::make('pm_sent_at'),
			GDT_DeletedAt::make('pm_deleted_at'),
			GDT_DateTime::make('pm_read_at'),
			GDT_User::make('pm_owner')->notNull(),
		    GDT_User::make('pm_from')->cascadeNull()->label('from_user'),
		    GDT_User::make('pm_to')->cascadeNull()->label('to_user'),
			GDT_Object::make('pm_folder')->table(GDO_PMFolder::table())->notNull(),
			GDT_Object::make('pm_parent')->table(GDO_PM::table())->cascadeNull(),
			GDT_Object::make('pm_other')->table(GDO_PM::table())->cascadeNull(),
			GDT_Title::make('pm_title')->notNull(),
			GDT_Message::make('pm_message')->notNull(),
			GDT_Timestamp::make('pm_other_read_at'),
			GDT_Timestamp::make('pm_other_deleted_at'),
		    GDT_Index::make('index_pm_read_at')->indexColumns('pm_read_at'),
		);
	}
	
	##############
	### Render ###
	##############
	public function renderList() : string { return GDT_Template::php('PM', 'listitem_pm.php', ['pm' => $this]); }
	
	##################
	### Convinient ###
	##################
	/**
	 * @return GDT_Message
	 */
	public function messageColumn() { return $this->gdoColumn('pm_message'); }
	public function isRead() { return $this->gdoVar('pm_read_at') !== null; }
	public function displayDate() { return Time::displayDate($this->gdoVar('pm_sent_at')); }
	public function getTitle() { return $this->gdoVar('pm_title'); }
	public function displayTitle() { return $this->gdoDisplay('pm_title'); }
	public function displayMessage() { return $this->messageColumn()->renderCell(); }
	public function getMessage() { return $this->messageColumn()->getVarInput(); }
	public function displaySignature() { return Module_PM::instance()->userSetting($this->getSender(), 'signature')->renderCell(); }
	
	/**
	 * @return GDO_User
	 */
	public function getSender() { return $this->gdoValue('pm_from'); }
	
	/**
	 * @return GDO_User
	 */
	public function getReceiver() { return $this->gdoValue('pm_to'); }
	
	/**
	 * @return GDO_User
	 */
	public function getOwner() { return $this->gdoValue('pm_owner'); }
	public function getOwnerID() { return $this->gdoVar('pm_owner'); }
	public function getOtherID() { return $this->gdoVar('pm_other'); }

	/**
	 * Get the other user that differs from param user.
	 * One of the two users has to match.
	 * @param GDO_User $user
	 * @return GDO_User
	 */
	public function getOtherUser(GDO_User $user)
	{
		if ($user->getID() === $this->getFromID())
		{
			return $this->getReceiver();
		}
		elseif ($user->getID() === $this->getToID())
		{
			return $this->getSender();
		}
	}
	
	/**
	 * @return self
	 */
	public function getOtherPM() { return $this->gdoValue('pm_other'); }

	public function getFromID() { return $this->gdoVar('pm_from'); }
	public function getToID() { return $this->gdoVar('pm_to'); }
	
	/**
	 * @return self
	 */
	public function getParent() { return $this->gdoValue('pm_parent'); }
	
	/**
	 * @param GDO_User $owner
	 * @return self
	 */
	public function getPMFor(GDO_User $owner) { return $this->getOwnerID() === $owner->getID() ? $this : $this->getOtherPM(); }
	
	public function isFrom(GDO_User $user) { return $this->getFromID() === $user->getID(); }
	public function isTo(GDO_User $user) { return $this->getToID() === $user->getID(); }
	
	#############
	### HREFs ###
	#############
	public function href_show() { return href('PM', 'Read', "&id={$this->getID()}"); }
	public function href_delete() { return href('PM', 'Overview', "&delete=1&rbx[{$this->getID()}]=1"); }
	public function href_reply() { return href('PM', 'Write', '&reply='.$this->getID()); }
	public function href_quote() { return href('PM', 'Write', '&quote=yes&reply='.$this->getID()); }
	
	##############
	### Static ###
	##############
	public static function updateOtherDeleted()
	{
		self::table()->update()->set("pm_other_deleted_at=".quote(Time::getDate()))->
		where(" ( SELECT pm_id FROM ( SELECT * FROM gdo_pm ) b WHERE gdo_pm.pm_other = b.pm_id ) IS NULL ")->
		orWhere(" ( SELECT pm_deleted_at FROM ( SELECT * FROM gdo_pm ) b WHERE b.pm_id = gdo_pm.pm_other ) IS NOT NULL ")->exec();
	}
	
	public static function getByIdAndUser($id, GDO_User $user)
	{
		$id = self::quoteS($id);
		return self::table()->select('*')->where("pm_id={$id} AND pm_owner={$user->getID()}")->exec()->fetchObject();
	}
	
	##############
	### Unread ###
	##############
	public static function countUnread(GDO_User $user)
	{
		if (null === ($cache = $user->tempGet('gdo_pm_unread')))
		{
			$cache = self::table()->countWhere("pm_to={$user->getID()} AND pm_read_at IS NULL");
			$user->tempSet('gdo_pm_unread', $cache);
			$user->recache();
		}
		return $cache;
	}

}
