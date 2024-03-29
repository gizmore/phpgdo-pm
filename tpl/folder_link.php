<?php
namespace GDO\PM\tpl;

/**
 * Render a PM Folder link to open the folder.
 * @var GDT_Template $field  *
 */

use GDO\Core\GDT_Template;
use GDO\UI\GDT_Link;

echo GDT_Link::make()->textRaw($field->gdo->renderName(), true)
    ->href(href('PM', 'Overview', "&folder={$field->gdo->getID()}"))
    ->render();
