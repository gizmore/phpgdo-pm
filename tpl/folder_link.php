<?php
namespace GDO\PM\tpl;

/** @var $field GDT_Template * */

use GDO\Core\GDT_Template;
use GDO\UI\GDT_Link;

$id = $field->getVar();

echo GDT_Link::make()->text($field->labelKey)->href(href('PM', 'Overview', '&folder=' . $id))->render();
