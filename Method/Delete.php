<?php
namespace GDO\PM\Method;

use GDO\Core\GDT;
use GDO\Core\GDT_Response;
use GDO\Core\Method;
use GDO\PM\PMMethod;

/**
 * Delete a PM via tokenhash from mail.
 *
 * @author gizmore
 */
final class Delete extends Method
{

	use PMMethod;

	public function execute(): GDT
	{
		return GDT_Response::make();
	}

}
