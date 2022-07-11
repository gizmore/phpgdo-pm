<?php
namespace GDO\PM;

use GDO\Core\Application;
use GDO\UI\GDT_Page;
use GDO\UI\GDT_Bar;
use GDO\UI\GDT_Link;

/**
 * PM Methods draw a navbar.
 * 
 * @author gizmore
 * @version 6.10.1
 * @since 6.2.0
 */
trait PMMethod
{
    public function beforeExecute() : void
    {
        if (Application::instance()->isHTML())
        {
            $navbar = GDT_Bar::make()->horizontal();
            $navbar->addFields([
                GDT_Link::make('btn_overview')->href(href('PM', 'Overview'))->icon('table'),
                GDT_Link::make('link_settings')->href(href('Account', 'Settings', '&module=PM'))->icon('settings'),
                GDT_Link::make('link_trashcan')->href(href('PM', 'Trashcan'))->icon('delete'),
                GDT_Link::make('link_write_pm')->href(href('PM', 'Write'))->icon('create'),
            ]);
            GDT_Page::$INSTANCE->topBar()->addField($navbar);
        }
    }
    
}
