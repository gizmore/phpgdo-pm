<?php
namespace GDO\PM\Method;

use GDO\Core\GDT;
use GDO\Core\GDT_Tuple;
use GDO\Form\GDT_AntiCSRF;
use GDO\Form\GDT_Form;
use GDO\Form\GDT_Submit;
use GDO\Form\GDT_Validator;
use GDO\PM\GDO_PM;
use GDO\PM\GDT_PM;
use GDO\PM\Module_PM;
use GDO\UI\GDT_HTML;
use GDO\UI\GDT_Message;
use GDO\User\GDO_User;
use GDO\Util\Strings;

/**
 * Inherits the write method, but does not have a user field.
 * Instead it replies to an existing PM.
 *
 * @version 7.0.1
 * @since 7.0.l
 * @author gizmore
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

	protected function getParent(): ?GDO_PM
	{
		return $this->pm;
	}

	protected function getRecipient(): GDO_User
	{
		return $this->pm->getOtherUser(GDO_User::current());
	}

	protected function createForm(GDT_Form $form): void
	{
		$this->pm = $this->gdoParameterValue('to');
		[$title, $message] = $this->initialValues($form);
		$form->addFields(
			GDT_Validator::make()->validator($form, null, [$this, 'validateCanSend']),
			$this->pm->gdoColumnCopy('pm_title')->initial($title),
			$this->pm->gdoColumnCopy('pm_message')->initial($message),
			GDT_AntiCSRF::make(),
		);
		$form->actions()->addFields(
			GDT_Submit::make(),
			GDT_Submit::make('btn_preview')->onclick([$this, 'preview']),
		);
	}

	protected function initialValues(GDT_Form $form)
	{
		# Title
		$title = $this->pm->gdoVar('pm_title');
		$re = Module_PM::instance()->cfgRE();
		$title = trim($re) . ' ' . trim(Strings::substrFrom($title, $re, $title));

		# Message
		$message = null;
		if ($this->quote)
		{
			$by = $this->pm->getSender();
			$at = $this->pm->gdoVar('pm_sent_at');
			$msg = $this->pm->gdoColumn('pm_message')->getVarInput();
			$message = GDT_Message::quoteMessage($by, $at, $msg);
		}

		return [$title, $message];
	}

	##############
	### Action ###
	##############
	public function formValidated(GDT_Form $form): GDT
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
	public function renderPage(): GDT
	{
		$response = GDT_Tuple::make();
		$response->addField(GDT_HTML::make()->var($this->pm?->renderCard()));
		$response->addField(parent::renderPage());
		return $response;
	}

}
