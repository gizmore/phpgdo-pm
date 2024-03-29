<?php
namespace GDO\PM\tpl;

use GDO\PM\GDO_PM;
use GDO\PM\GDT_PMFromTo;
use GDO\UI\GDT_Button;
use GDO\UI\GDT_Card;
use GDO\UI\GDT_HR;
use GDO\UI\GDT_HTML;
use GDO\User\GDO_User;

/** @var $pm GDO_PM * */
/** @var $noactions bool * */

$card = GDT_Card::make('pm-' . $pm->getID());
$card->gdo($pm);
$user = GDO_User::current();
$otherUser = $pm->getOtherUser($user);

// $card->creatorHeader($user ===  $pm->getSender() ? 'pm_to' : 'pm_from', 'pm_sent_at');
$card->avatarUser($otherUser, 48);
$card->titleRaw($pm->displayTitle());
$card->subtitleRaw(GDT_PMFromTo::make()->gdo($pm)->render());

$hr = GDT_HR::make()->renderHTML();

$html = <<<EOT
<div>
  <div>{$pm->displayMessage()}</div>
  $hr
  <div>{$pm->displaySignature()}</div>
</div>
EOT;
$card->content(GDT_HTML::make()->var($html));

if (!isset($noactions))
{
	$card->actions()->addFields(
		GDT_Button::make('quote')->gdo($pm)->icon('quote'),
		GDT_Button::make('reply')->gdo($pm)->icon('reply'),
		GDT_Button::make('delete')->gdo($pm)->icon('delete'),
	);
}

echo $card->renderHTML();
