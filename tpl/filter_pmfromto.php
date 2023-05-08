<?php
namespace GDO\PM\tpl;

use GDO\PM\GDT_PMFromTo;
use GDO\Table\GDT_Filter;

/** @var $field GDT_PMFromTo * */
/** @var $f GDT_Filter * */
?>
<input
	name="f[<?=$field->getName()?>]"
	type="text"
	value="<?=html($field->filterVar($f));?>"/>
