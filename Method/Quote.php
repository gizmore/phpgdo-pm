<?php
namespace GDO\PM\Method;

/**
 * Inherits the reply method.
 * It does automatically create a quoted message body.
 *
 * @version 7.0.1
 * @since 7.0.l
 * @author gizmore
 * @see Reply
 * @see Write
 */
final class Quote extends Reply
{

	protected bool $quote = true;

	public function getMethodTitle(): string
	{
		return t('mt_pm_reply');
	}

}
