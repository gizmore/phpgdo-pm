<?php
namespace GDO\PM\Method;

use GDO\Core\GDO;
use GDO\DB\Query;
use GDO\PM\GDO_PM;
use GDO\PM\GDO_PMFolder;
use GDO\Table\MethodQueryList;
use GDO\User\GDO_User;
use GDO\PM\GDT_PMFolder;

/**
 * Display a PM folder.
 * 
 * @author gizmore
 * @version 7.0.1
 * @since 5.3.0
 * @see GDO_PMFolder
 */
final class Folder extends MethodQueryList
{
	public function gdoTable() : GDO { return GDO_PM::table(); }

	public function isUserRequired() : bool { return true; }
	
	public function getDefaultOrder() : ?string { return 'gdo_pm.pm_sent_at DESC'; }
	
	public function gdoParameters() : array
	{
	    return [
	        GDT_PMFolder::make('folder')->initial('1')->notNull(),
	    ];
	}
	
	private GDO_PMFolder $folder;
	
	public function onMethodInit()
	{
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
	
	public function getQuery() : Query
	{
		$user = GDO_User::current();
		return GDO_PM::table()->select()->
    		where('gdo_pm.pm_owner='.$user->getID())->
    		where('gdo_pm.pm_folder='.$this->folder->getID())->
    		where("gdo_pm.pm_deleted_at IS NULL");
	}
	
	public function getMethodTitle() : string
	{
		$table = $this->getTable();
		return t('pm_folder', [$this->folder->gdoDisplay('pmf_name'), $table->getResult()->numRows()]);
	}
	
	public function getTableTitle()
	{
		return $this->getMethodTitle();
	}
	
}
