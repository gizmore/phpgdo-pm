<?php
namespace GDO\PM;
use GDO\Core\GDT;
use GDO\UI\WithLabel;

final class GDT_PMFromTo extends GDT
{
	use WithLabel;
	
	public function displayHeaderLabel() { return ''; }
	
	public function renderCell() : string
	{
		return Module_PM::instance()->php('cell_pmfromto.php', [
		    'field'=>$this, 'pm'=>$this->gdo]);
	}
	
	public function renderFilter($f)
	{
		return Module_PM::instance()->php('filter_pmfromto.php', [
		    'field' => $this, 'pm' => $this->gdo, 'f' => $f]);
	}
	
}
