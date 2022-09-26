<?php
namespace GDO\PM\tpl;
/** @var $field \GDO\Core\GDT_Template **/
use GDO\UI\GDT_Link;
$id = $field->getVar();

echo GDT_Link::make()->label($field->labelKey)->href(href('PM', 'Overview', '&folder='.$id))->render();
