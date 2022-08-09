<?php
namespace GDO\PM;

use GDO\Core\GDT_ObjectSelect;
use GDO\User\WithUser;

/**
 * A PM folder.
 * 
 * @author gizmore
 * @version 7.0.1
 * @since 3.5.0
 */
final class GDT_PMFolder extends GDT_ObjectSelect
{
	use WithUser;
	
	const INBOX = '1';
	const OUTBOX = '2';
	
	protected function __construct()
	{
		parent::__construct();
		$this->name('folder');
		$this->icon('folder');
		$this->label('folder');
		$this->table(GDO_PMFolder::table());
		$this->emptyLabel('choose_folder_move');
		$this->currentUser();
	}
	
	public function getChoices() : array
	{
		$user = $this->user;
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
	public function toValue($var=null)
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
