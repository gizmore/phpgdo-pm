<?php
namespace GDO\PM\Method;

use GDO\Core\GDO;
use GDO\Core\GDT;
use GDO\DB\Database;
use GDO\DB\Query;
use GDO\Form\GDT_Submit;
use GDO\PM\GDO_PM;
use GDO\PM\GDT_PMFromTo;
use GDO\PM\PMMethod;
use GDO\Table\GDT_RowNum;
use GDO\Table\GDT_Table;
use GDO\Table\MethodQueryTable;
use GDO\UI\GDT_Link;
use GDO\UI\GDT_Title;
use GDO\User\GDO_User;

/**
 * Trashcan features restore, delete, and empty bin.
 *
 * @version 6.10.6
 * @since 3.4.0
 * @author gizmore
 */
final class Trashcan extends MethodQueryTable
{

	use PMMethod;

	public function isUserRequired(): bool { return true; }

	public function getMethodTitle(): string
	{
		return t('list_pm_trashcan', [$this->getTable()->getResult()->numRows()]);
	}

	public function gdoTable(): GDO
	{
		return GDO_PM::table();
	}

	public function execute(): GDT
	{
		if (isset($_REQUEST['delete']))
		{
			return $this->onDelete()->addField(parent::execute());
		}
		elseif (isset($_REQUEST['restore']))
		{
			return $this->onRestore()->addField(parent::execute());
		}
		elseif (isset($_REQUEST['trash']))
		{
			return $this->onEmpty()->addField(parent::execute());
		}
		return parent::execute();
	}

	public function onDelete()
	{
		if ($ids = $this->getRBX())
		{
			$user = GDO_User::current();
			GDO_PM::table()->deleteWhere("pm_owner={$user->getID()} AND pm_id IN($ids)");
			$affected = Database::instance()->affectedRows();
			return $this->message('msg_pm_destroyed', [$affected]);
		}
		return $this->error('err_nothing_happened');
	}

	public function onRestore()
	{
		if ($ids = $this->getRBX())
		{
			$user = GDO_User::current();
			GDO_PM::table()->update()->set('pm_deleted_at = NULL')->where("pm_owner={$user->getID()} AND pm_id IN($ids)")->exec();
			$affected = Database::instance()->affectedRows();
			GDO_PM::updateOtherDeleted();
			return $this->message('msg_pm_restored', [$affected]);
		}
		return $this->error('err_nothing_happened');
	}

	public function onEmpty()
	{
		$user = GDO_User::current();
		$affected = GDO_PM::table()->deleteWhere("pm_owner={$user->getID()} AND pm_deleted_at IS NOT NULL");
		return $this->message('msg_pm_destroyed', [$affected]);
	}

	###############
	### Actions ###
	###############

	public function gdoHeaders(): array
	{
		return [
			GDT_RowNum::make(),
			GDT_PMFromTo::make(),
			GDT_Title::make('pm_title'),
			GDT_Link::make('show'),
		];
	}

	public function getQuery(): Query
	{
		$user = GDO_User::current();
		return GDO_PM::table()->select()->
		where('pm_owner=' . $user->getID())->
		where('pm_deleted_at IS NOT NULL');
	}

	public function createTable(GDT_Table $table)
	{
		$table->title('name_trashcan');
		$table->actions()->addFields(
			GDT_Submit::make('restore')->primary()->label('btn_restore'),
			GDT_Submit::make('delete')->secondary()->label('btn_delete'),
			GDT_Submit::make('trash')->unadvised()->label('btn_empty'),
		);
	}

}
