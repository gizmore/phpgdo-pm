<?php
namespace GDO\PM\tpl;

use GDO\Core\GDT_Response;

/** @var $folder GDT_Response * */
/** @var $folders GDT_Response * */
?>
<div>
	<div><?=$folders->renderHTML()?></div>
	<div><?=$folder->renderHTML()?></div>
</div>
