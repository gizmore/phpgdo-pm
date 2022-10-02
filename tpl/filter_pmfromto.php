<?php
namespace GDO\PM\tpl;
/** @var $field \GDO\PM\GDT_PMFromTo **/
/** @var $f \GDO\Table\GDT_Filter **/
?>
<input
 name="f[<?= $field->getName()?>]"
 type="text"
 value="<?= html($field->filterVar($f)); ?>" />
