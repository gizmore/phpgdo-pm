<?php
namespace GDO\PM\tpl;
use GDO\PM\GDO_PM;
use GDO\Core\GDT_Template;
use GDO\UI\GDT_Icon;
/** @var $field GDT_Template **/
/** @var $pm GDO_PM **/
$pm = $field->gdo;
?>
<?php if (!$pm->isRead()) : ?>
<?= GDT_Icon::iconS('alert'); ?>
<?php endif; ?>

