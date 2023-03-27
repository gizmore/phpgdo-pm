<?php
declare(strict_types=1);
namespace GDO\PM;

use GDO\Core\GDT;
use GDO\Core\WithGDO;
use GDO\DB\Query;
use GDO\Table\GDT_Filter;
use GDO\UI\WithLabel;

/**
 * The From/To field in PM display.
 *
 * @author gizmore
 */
final class GDT_PMFromTo extends GDT
{

	use WithGDO;
	use WithLabel;

	public function renderHTML(): string
	{
		if (!isset($this->gdo))
		{
			return GDT::EMPTY_STRING;
		}
		return Module_PM::instance()->php('cell_pmfromto.php', [
			'field' => $this, 'pm' => $this->gdo]);
	}

	public function renderFilter(GDT_Filter $f): string
	{
		return Module_PM::instance()->php('filter_pmfromto.php', [
			'field' => $this, 'f' => $f]);
	}

}
