<?php
use GDO\PM\GDO_PM;
use GDO\UI\GDT_Button;
use GDO\UI\GDT_HTML;
use GDO\UI\GDT_Card;
use GDO\UI\GDT_Title;
use GDO\UI\GDT_HR;

/** @var $pm GDO_PM **/
/** @var $noactions bool **/

// $creator = $pm->getSender();

$card = GDT_Card::make('pm-'.$pm->getID());
$card->gdo($pm);

$card->creatorHeader(GDT_Title::make()->titleEscaped(false)->titleRaw($pm->displayTitle()), 'pm_from');

$hr = GDT_HR::make()->renderCell();

$html = <<<EOT
<div>
  <div>{$pm->displayMessage()}</div>
  $hr
  <div>{$pm->displaySignature()}</div>
</div>
EOT;
$card->content(GDT_HTML::withHTML($html));

if (!isset($noactions))
{
    $card->actions()->addFields([
    	GDT_Button::make('quote')->gdo($pm)->icon('quote'),
    	GDT_Button::make('reply')->gdo($pm)->icon('reply'),
    	GDT_Button::make('delete')->gdo($pm)->icon('delete'),
    ]);
}

echo $card->renderCell();
