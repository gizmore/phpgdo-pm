<?php
namespace GDO\PM\Method;

use GDO\Core\GDO;
use GDO\Core\GDT_Template;
use GDO\DB\ArrayResult;
use GDO\PM\GDO_PMFolder;
use GDO\Table\MethodTable;
use GDO\User\GDO_User;

final class Folders extends MethodTable
{

	public function isFiltered(): bool { return false; }

	public function isPaginated(): bool { return false; }

	public function isUserRequired(): bool { return true; }

	public function getDefaultOrder(): ?string { return 'pmf_id'; }

	public function gdoTable(): GDO
	{
		return GDO_PMFolder::table();
	}

	public function gdoHeaders(): array
	{
		$table = GDO_PMFolder::table();
		return [
			$table->gdoColumn('pmf_id')->hidden(),
			GDT_Template::make()->template('PM', 'folder_link.php')->label('folder'),
			$table->gdoColumn('pmf_count'),
		];
	}

	public function getResult(): ArrayResult
	{
		$folders = GDO_PMFolder::getFolders(GDO_User::current()->getID());
		return new ArrayResult($folders, GDO_PMFolder::table());
	}

}
