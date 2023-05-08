<?php
namespace GDO\PM;

use GDO\Core\GDO;
use GDO\Core\GDO_ExceptionFatal;
use GDO\Core\GDT_AutoInc;
use GDO\Core\GDT_CreatedAt;
use GDO\Core\GDT_DeletedAt;
use GDO\Core\GDT_Index;
use GDO\Core\GDT_Object;
use GDO\Core\GDT_Template;
use GDO\Date\GDT_Timestamp;
use GDO\Date\Time;
use GDO\UI\GDT_Message;
use GDO\UI\GDT_Title;
use GDO\User\GDO_User;
use GDO\User\GDT_User;

/**
 * A PM entity.
 *
 * @version 7.0.1
 * @since 3.5.0
 * @author gizmore
 */
final class GDO_PM extends GDO
{

	public static function updateOtherDeleted()
	{
		self::table()->update()->set('pm_other_deleted_at=' . quote(Time::getDate()))->
		where(' ( SELECT pm_id FROM ( SELECT * FROM gdo_pm ) b WHERE gdo_pm.pm_other = b.pm_id ) IS NULL ')->
		orWhere(' ( SELECT pm_deleted_at FROM ( SELECT * FROM gdo_pm ) b WHERE b.pm_id = gdo_pm.pm_other ) IS NOT NULL ')->exec();
	}

	public static function getByIdAndUser($id, GDO_User $user)
	{
		$id = self::quoteS($id);
		return self::table()->select()->where("pm_id={$id} AND pm_owner={$user->getID()}")->exec()->fetchObject();
	}

	###########
	### GDO ###
	###########

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

	##############
	### Render ###
	##############

	public function gdoCached(): bool { return false; }

	public function isTestable(): bool
	{
		return false;
	}

	##################
	### Convinient ###
	##################

	public function gdoColumns(): array
	{
		return [
			GDT_AutoInc::make('pm_id'),
			GDT_CreatedAt::make('pm_sent_at'),
			GDT_DeletedAt::make('pm_deleted_at'),
			GDT_Timestamp::make('pm_read_at'),
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
		];
	}

	public function renderList(): string { return GDT_Template::php('PM', 'listitem_pm.php', ['pm' => $this]); }

	public function renderCard(): string { return GDT_Template::php('PM', 'card_pm.php', ['pm' => $this, 'noactions' => true]); }

	public function isRead(): bool { return $this->gdoVar('pm_read_at') !== null; }

	public function displayReadAgo(): string { return Time::displayAge($this->gdoVar('pm_read_at')); }

	public function displayAge(): string { return Time::displayAge($this->gdoVar('pm_sent_at')); }

	public function displayDate(): string { return Time::displayDate($this->gdoVar('pm_sent_at')); }

	public function getTitle(): string { return $this->gdoVar('pm_title'); }

	public function displayTitle(): string { return $this->gdoDisplay('pm_title'); }

	public function displayMessage(): string { return $this->messageColumn()->renderHTML(); }

	public function messageColumn(): GDT_Message { return $this->gdoColumn('pm_message'); }

	public function getMessage(): string { return $this->messageColumn()->getVarInput(); }

	public function displaySignature(): string { return Module_PM::instance()->userSetting($this->getSender(), 'signature')->renderHTML(); }

	public function getSender(): GDO_User
	{
		if ($user = $this->gdoValue('pm_from'))
		{
			return $user;
		}
		return GDO_User::current();
	}

	public function getOwner(): GDO_User { return $this->gdoValue('pm_owner'); }

	public function getOtherID(): string { return $this->gdoVar('pm_other'); }

	/**
	 * Get the other user that differs from param user.
	 * One of the two users has to match.
	 */
	public function getOtherUser(GDO_User $user): GDO_User
	{
		if ($user->getID() === $this->getFromID())
		{
			return $this->getReceiver();
		}
		elseif ($user->getID() === $this->getToID())
		{
			return $this->getSender();
		}
		else
		{
			throw new GDO_ExceptionFatal('err_pm_user_no_belong', [$user->renderUserName()]);
		}
	}

	public function getFromID(): string { return $this->gdoVar('pm_from'); }

	public function getReceiver(): GDO_User { return $this->gdoValue('pm_to'); }

	public function getToID(): string { return $this->gdoVar('pm_to'); }

	public function getParent(): self { return $this->gdoValue('pm_parent'); }

	public function getPMFor(GDO_User $owner): ?self { return $this->getOwnerID() === $owner->getID() ? $this : $this->getOtherPM(); }

	public function getOwnerID(): string { return $this->gdoVar('pm_owner'); }

	#############
	### HREFs ###
	#############

	public function getOtherPM(): ?self { return $this->gdoValue('pm_other'); }

	public function isFrom(GDO_User $user): bool { return $this->getFromID() === $user->getID(); }

	public function isTo(GDO_User $user): bool { return $this->getToID() === $user->getID(); }

	public function href_show(): string { return href('PM', 'Read', "&id={$this->getID()}"); }

	##############
	### Static ###
	##############

	public function href_reply(): string { return href('PM', 'Reply', '&to=' . $this->getID()); }

	public function href_quote(): string { return href('PM', 'Quote', '&to=' . $this->getID()); }

	##############
	### Unread ###
	##############

	public function href_delete(): string { return href('PM', 'Overview', "&delete=1&rbx[{$this->getID()}]=1"); }

}
