<?php
namespace GDO\PM\Method;

use GDO\DB\ArrayResult;
use GDO\PM\GDO_PMFolder;
use GDO\Table\MethodTable;
use GDO\Core\GDT_Template;
use GDO\User\GDO_User;

final class Folders extends MethodTable
{
	public function isFiltered() { return false; }
	public function isPaginated() { return false; }
	public function isUserRequired() : bool { return true; }
	
	public function getDefaultOrder() { return 'pmf_id'; }
	
	public function gdoTable()
	{
	    return GDO_PMFolder::table();
	}
	
	public function gdoHeaders() : array
	{
		$table = GDO_PMFolder::table();
		return array(
		    $table->gdoColumn('pmf_id')->hidden(),
			GDT_Template::make()->template('PM', 'folder_link.php')->label('folder'),
			$table->gdoColumn('pmf_count'),
		);
	}
	
// 	protected function setupTitlePrefix()
// 	{
// 	}
	
	public function getResult()
	{
		$folders = GDO_PMFolder::getFolders(GDO_User::current()->getID());
		return new ArrayResult($folders, GDO_PMFolder::table());
	}
	
}
