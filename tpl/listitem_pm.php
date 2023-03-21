<?php
namespace GDO\PM\tpl;

use GDO\PM\GDO_PM;
use GDO\PM\GDT_PMFromTo;
use GDO\Table\GDT_ListItem;
use GDO\UI\GDT_Action;
use GDO\UI\GDT_Button;
use GDO\User\GDO_User;

/** @var $pm GDO_PM * */

# Gather data
$id = $pm->getID();
$user = GDO_User::current();
$otherUser = $pm->getOtherUser($user);
$href = href('PM', 'Read', '&id=' . $id);
$hrefDelete = $pm->href_delete();

# Build LI
$li = GDT_ListItem::make('pm-' . $id)->gdo($pm);
$li->avatarUser($otherUser, 48);
// $fromto = $pm->getSender() === $user ? 'pm_fromto_to' : 'pm_fromto_from';
// $li->subtitle(GDT_Headline::make()->level(5)->text($fromto, [$otherUser->renderUserName()]));
$li->subtitleRaw(GDT_PMFromTo::make()->gdo($pm)->render());
$li->actions()->addFields(
	GDT_Button::make()->href($href)->icon('view')->label('btn_view'),
	GDT_Action::make()->href($hrefDelete)->icon('delete')->label('btn_delete'),
);
$li->addClass($pm->isRead() ? 'pm-read' : 'unread pm-unread');
$li->titleRaw($pm->displayTitle());

# Render
echo $li->render();
