<?php /** @var $field \GDO\Core\GDT_Template **/
use GDO\UI\GDT_Link;
$gdo = $field->gdo;

echo GDT_Link::make()->label($gdo->getName())->href(href('PM', 'Overview', '&folder='.$gdo->getID()))->render();
