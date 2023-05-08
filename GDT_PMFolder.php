<?php
namespace GDO\PM;

use GDO\Core\GDT_ObjectSelect;
use GDO\Core\WithGDO;
use GDO\User\GDO_User;

/**
 * A PM folder.
 *
 * @version 7.0.1
 * @since 3.5.0
 * @author gizmore
 */
final class GDT_PMFolder extends GDT_ObjectSelect
{

	use WithGDO;

	public const INBOX = '1';
	public const OUTBOX = '2';

	protected function __construct()
	{
		parent::__construct();
		$this->name('folder');
		$this->icon('folder');
		$this->label('folder');
		$this->table(GDO_PMFolder::table());
		$this->emptyLabel('choose_folder_move');
	}

	protected function getChoices(): array
	{
		$user = isset($this->gdo) ? $this->gdo : GDO_User::current();
		$choices = [];
		foreach (GDO_PMFolder::getFolders($user->getID()) as $folder)
		{
			$choices[$folder->getID()] = $folder;
		}
		return $choices;
	}

	/**
	 * The special two var cases inbox and outbox get a static folder that is the same for all users.
	 */
	public function toValue(null|string|array $var): null|bool|int|float|string|object|array
	{
		if ($var === self::INBOX)
		{
			return GDO_PMFolder::getInBox();
		}
		elseif ($var === self::OUTBOX)
		{
			return GDO_PMFolder::getOutBox();
		}
		else
		{
			return parent::toValue($var);
		}
	}

}
