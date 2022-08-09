<?php
namespace GDO\PM\tpl;
/** @var $field \GDO\PM\GDT_PMFromTo **/
?>
<input
 name="f[<?= $field->getName()?>]"
 type="text"
 value="<?= html($field->filterVar()); ?>" />
