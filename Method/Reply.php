<?php
namespace GDO\PM\Method;

use GDO\Form\GDT_AntiCSRF;
use GDO\Form\GDT_Form;
use GDO\Form\GDT_Submit;
use GDO\Form\GDT_Validator;
use GDO\PM\GDO_PM;
use GDO\PM\Module_PM;
use GDO\UI\GDT_Message;
use GDO\Util\Strings;
use GDO\PM\GDT_PM;
use GDO\Core\GDT;
use GDO\Core\GDT_Tuple;
use GDO\UI\GDT_CardView;
use GDO\User\GDO_User;

/**
 * Inherits the write method, but does not have a user field.
 * Instead it replies to an existing PM.
 * 
 * @author gizmore
 * @version 7.0.1
 * @since 7.0.l
 */
class Reply extends Write
{
	protected GDO_PM $pm;
	
	protected bool $quote = false;

	public function gdoParameters(): array
	{
		return [
			GDT_PM::make('to')->notNull(),
		];
	}

	public function createForm(GDT_Form $form): void
	{
		$this->pm = $this->gdoParameterValue('to');
		list($title, $message) = $this->initialValues($form);
		$table = GDO_PM::table();
		$form->addFields(
			GDT_Validator::make()->validator($form, null, [$this, 'validateCanSend']),
			$table->gdoColumn('pm_title')->initial($title),
			$table->gdoColumn('pm_message')->initial($message),
			GDT_AntiCSRF::make(),
		);
		$form->actions()->addFields(
			GDT_Submit::make(),
			GDT_Submit::make('btn_preview'),
		);
	}

	protected function initialValues(GDT_Form $form)
	{
		# Title
		$title = $this->pm->gdoVar('pm_title');
		$re = Module_PM::instance()->cfgRE();
		$title = trim($re) . ' ' . trim(Strings::substrFrom($title, $re, $title));

		# Message
		$message = '';
		if ($this->quote)
		{
			$by = $this->pm->getSender();
			$at = $this->pm->gdoVar('pm_sent_at');
			$msg = $this->pm->getMessage();
			$message = GDT_Message::quoteMessage($by, $at, $msg);
		}
		
		return [$title, $message];
	}
	
	##############
	### Action ###
	##############
	public function formValidated(GDT_Form $form)
	{
		$from = GDO_User::current();
		$to = $this->pm->getOtherUser($from);
		$this->deliver($from, $to,
			$form->getFormVar('pm_title'),
			$form->getFormVar('pm_message'),
			$this->pm);
		return $this->redirectMessage('msg_pm_sent', null, href('PM', 'Overview'));
	}
	
	##############
	### Render ###
	##############
	public function renderPage() : GDT
	{
		$response = GDT_Tuple::make();
		$response->addField(GDT_CardView::make()->gdo($this->pm));
		$response->addField(parent::renderPage());
		return $response;
	}

}
