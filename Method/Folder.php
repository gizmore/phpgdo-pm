<?php
namespace GDO\PM\Method;

use GDO\Core\GDO;
use GDO\Core\GDT;
use GDO\DB\Query;
use GDO\PM\GDO_PM;
use GDO\PM\GDO_PMFolder;
use GDO\PM\GDT_PMFolder;
use GDO\Table\MethodQueryList;
use GDO\User\GDO_User;

/**
 * Display a PM folder.
 *
 * @version 7.0.1
 * @since 5.3.0
 * @author gizmore
 * @see GDO_PMFolder
 */
final class Folder extends MethodQueryList
{

	private GDO_PMFolder $folder;

	public function gdoTable(): GDO { return GDO_PM::table(); }

	public function isUserRequired(): bool { return true; }

	public function getDefaultOrder(): ?string { return 'gdo_pm.pm_sent_at DESC'; }

	public function gdoParameters(): array
	{
		return [
			GDT_PMFolder::make('folder')->initial('1')->notNull(),
		];
	}

	public function onMethodInit(): ?GDT
	{
		$this->folder = $this->gdoParameterValue('folder');
		return null;
	}

	public function gdoHeaders(): array
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

	public function getQuery(): Query
	{
		$user = GDO_User::current();
		return GDO_PM::table()->select()->
		where('gdo_pm.pm_owner=' . $user->getID())->
		where('gdo_pm.pm_folder=' . $this->folder->getID())->
		where('gdo_pm.pm_deleted_at IS NULL');
	}

	public function getMethodTitle(): string
	{
		$table = $this->getTable();
		return t('pm_folder', [$this->folder->gdoDisplay('pmf_name'), $table->getResult()->numRows()]);
	}

	public function getTableTitle(): string
	{
		return $this->getMethodTitle();
	}

}
