<?php
namespace GDO\PM;

use GDO\Core\GDO;
use GDO\Core\GDT_Object;
use GDO\DB\Query;
use GDO\User\GDO_User;

/**
 * A PM selection for replying.
 * Not rendered there.
 * Can only select own owned PM.
 *
 * @since 7.0.l
 * @author gizmore
 */
final class GDT_PM extends GDT_Object
{

	protected function __construct()
	{
		parent::__construct();
		$this->table(GDO_PM::table());
	}

	public function gdoBeforeRead(GDO $gdo, Query $query): void
	{
		$uid = GDO_User::current()->getID();
		echo $query->where("pm_owner={$uid}");
	}

	public function plugVars(): array
	{
		$uid = GDO_User::current()->getID();
		$pmid = $this->table->select('pm_id')->where("pm_owner=$uid")->exec()->fetchVar();
		return [
			[$this->getName() => $pmid],
		];
	}

}
