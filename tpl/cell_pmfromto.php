<?php 
namespace GDO\PM\tpl;
use GDO\PM\GDT_PMFromTo;
use GDO\PM\GDO_PM;
use GDO\User\GDO_User;
use GDO\User\GDT_ProfileLink;
/** @var $field GDT_PMFromTo **/
/** @var $pm GDO_PM **/
$user = GDO_User::current();

if ($pm->isFrom($user))
{
    $other = $pm->getReceiver();
    $tkey = 'pm_to';
}
else
{
    $other = $pm->getSender();
    $tkey = 'pm_by';
}

$link = GDT_ProfileLink::make()->user($other)->nickname()->level()->render();

$p = $pm->getPMFor($user);

$otherPM = $p ? $p->getOtherPM() : $pm->getOtherPM();

if ($otherPM)
{
	$otherReadState = $otherPM->isRead() ?
		t('pm_read', [$otherPM->displayReadAgo()]) :
		t('pm_unread');
}
else
{
	$otherReadState = t('pm_unread');
}

if ($pm->isFrom($user))
{
	echo t($tkey, [$link, $pm->displayAge(), $otherReadState]);
}
else
{
	echo t('pm_from', [$link, $pm->displayAge()]);
}

