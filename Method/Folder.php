<?php
namespace GDO\PM\Method;

use GDO\PM\GDO_PM;
use GDO\PM\GDO_PMFolder;
use GDO\Table\MethodQueryList;
use GDO\User\GDO_User;
use GDO\Table\GDT_Table;
use GDO\PM\GDT_PMFolder;

/**
 * Display a PM folder.
 * 
 * @author gizmore
 * @version 6.10.3
 * @since 5.3.0
 *
 * @see GDO_PMFolder
 */
final class Folder extends MethodQueryList
{
	public function gdoTable() { return GDO_PM::table(); }

	public function isUserRequired() : bool { return true; }
	
	public function getDefaultOrder() { return 'gdo_pm.pm_sent_at DESC'; }
	
	public function gdoParameters() : array
	{
	    return [
	        GDT_PMFolder::make('folder')->initial('1')->user(GDO_User::current())->notNull(),
	    ];
	}
	
	/**
	 * @var GDO_PMFolder
	 */
	private $folder;
	
	public function onInit() : void
	{
		parent::onInit();
   		$this->folder = $this->gdoParameterValue('folder');
	}
	
	public function gdoHeaders() : array
	{
		$table = GDO_PM::table();
		return [
		    $table->gdoColumn('pm_to'),
		    $table->gdoColumn('pm_from'),
		    $table->gdoColumn('pm_sent_at'),
		    $table->gdoColumn('pm_title'),
		    $table->gdoColumn('pm_message'),
		];
	}
	
	public function getQuery()
	{
		$user = GDO_User::current();
		return GDO_PM::table()->select()->
    		where('gdo_pm.pm_owner='.$user->getID())->
    		where('gdo_pm.pm_folder='.$this->folder->getID())->
    		where("gdo_pm.pm_deleted_at IS NULL");
	}
	
    protected function setupTitle(GDT_Table $table)
    {
        $list = $table;
	    $list->title('pm_folder', [$this->folder->display('pmf_name'), $table->pagemenu->numItems]);
		$list->href(href('PM', 'Overview', '&folder=' . $this->folder->getID()));
    }
	
}
