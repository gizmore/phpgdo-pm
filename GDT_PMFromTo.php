<?php
namespace GDO\PM;

use GDO\Core\GDT;
use GDO\UI\WithLabel;
use GDO\Core\WithGDO;
use GDO\Table\GDT_Filter;

/**
 * The From/To field in PM display.
 * 
 * @author gizmore
 */
final class GDT_PMFromTo extends GDT
{
	use WithGDO;
	use WithLabel;
	
	public function displayHeaderLabel() { return ''; }
	
	public function renderHTML() : string
	{
		if (!isset($this->gdo))
		{
			return '';
		}
		return Module_PM::instance()->php('cell_pmfromto.php', [
		    'field'=>$this, 'pm'=>$this->gdo]);
	}
	
	public function renderFilter(GDT_Filter $f) : string
	{
		return Module_PM::instance()->php('filter_pmfromto.php', [
		    'field' => $this, 'f' => $f]);
	}
	
}
