<?php
namespace GDO\PM\Method;

use GDO\Core\GDT;
use GDO\Core\GDT_Object;
use GDO\Core\GDT_Token;
use GDO\Core\Method;
use GDO\Date\Time;
use GDO\PM\GDO_PM;

/**
 * Delete a PM via mail token.
 * No auth required.
 *
 * @version 7.0.1
 * @author gizmore
 */
final class TokenDelete extends Method
{

	public function getMethodTitle(): string
	{
		return t('mt_pm_delete');
	}

	public function gdoParameters(): array
	{
		return [
			GDT_Object::make('pm')->table(GDO_PM::table())->notNull(),
			GDT_Token::make('token')->initialNull()->notNull(),
		];
	}

	public function execute(): GDT
	{
		if (
			(!($pm = $this->getPM())) ||
			($pm->gdoHashcode() !== $this->getHashcode())
		)
		{
			return $this->error('err_pm')->addField(Overview::make()->execute());
		}
		return $this->onDelete($pm)->addField(Overview::make()->execute());
	}

	private function getPM(): GDO_PM
	{
		return $this->gdoParameterValue('pm');
	}

	private function getHashcode(): string
	{
		return $this->gdoParameterVar('token');
	}

	public function deletePM(GDO_PM $pm)
	{
		$t = Time::getDate();
		$pm->saveVars([
			'pm_read_at' => $t,
			'pm_deleted_at' => $t,
		]);
		$pm->getOtherPM()->saveVar('pm_other_deleted_at', $t);
		return $this->message('msg_pm_deleted');
	}

}
