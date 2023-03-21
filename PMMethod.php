<?php
namespace GDO\PM;

use GDO\UI\GDT_Bar;
use GDO\UI\GDT_Link;
use GDO\UI\GDT_Page;

/**
 * PM Methods draw a PM navbar.
 *
 * @version 7.0.1
 * @since 6.2.0
 * @author gizmore
 */
trait PMMethod
{

	public function onRenderTabs(): void
	{
		$navbar = GDT_Bar::make()->horizontal();
		$navbar->addFields(GDT_Link::make('btn_overview')->href(href('PM', 'Overview'))
			->icon('table'),
			GDT_Link::make('link_settings')->href(href('Account', 'Settings', '&module=PM&opened=1'))
				->icon('settings'), GDT_Link::make('link_trashcan')->href(href('PM', 'Trashcan'))
				->icon('delete'), GDT_Link::make('link_write_pm')->href(href('PM', 'Write'))
				->icon('create'),);
		GDT_Page::instance()->topResponse()->addField($navbar);
	}

}
