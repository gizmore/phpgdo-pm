<?php 
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
    $tkey = 'pm_from';
}
else
{
    $other = $pm->getSender();
    $tkey = 'pm_to';
}

if (module_enabled('Profile'))
{
    $link = GDT_ProfileLink::make()->forUser($other)->
        withAvatar()->withNickname()->render();
}
else
{
    $link = $other->renderUserName();
}

echo t($tkey, [$link]);
