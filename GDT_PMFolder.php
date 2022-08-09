<?php
namespace GDO\PM;

use GDO\User\GDO_User;
use GDO\Core\GDT_ObjectSelect;

/**
 * A PM folder
 * @author gizmore
 */
final class GDT_PMFolder extends GDT_ObjectSelect
{
	protected function __construct()
	{
		$this->name('folder');
		$this->label('folder');
		$this->icon('folder');
		$this->table(GDO_PMFolder::table());
	}
	
	public function user(GDO_User $user)
	{
		$this->gdo($user);
		$this->emptyLabel('choose_folder_move');
		return $this->choices($this->userChoices($user));
	}
	
	private function userChoices(GDO_User $user)
	{
		$choices = [];
		foreach (GDO_PMFolder::getFolders($user->getID()) as $folder)
		{
			$choices[$folder->getID()] = $folder;
		}
		return $choices;
	}
	
	public function toValue($var=null)
	{
	    if ($var === '1') return GDO_PMFolder::getInBox();
	    elseif ($var === '2') return GDO_PMFolder::getOutBox();
	    else { return parent::toValue($var); }
	}
	
}
