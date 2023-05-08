<?php
declare(strict_types=1);
namespace GDO\PM\Method;

use GDO\Core\Application;
use GDO\Core\GDO_DBException;
use GDO\Core\GDT;
use GDO\Core\GDT_Hook;
use GDO\Core\GDT_Tuple;
use GDO\Date\Time;
use GDO\Form\GDT_AntiCSRF;
use GDO\Form\GDT_Form;
use GDO\Form\GDT_Submit;
use GDO\Form\GDT_Validator;
use GDO\Form\MethodForm;
use GDO\PM\EMailOnPM;
use GDO\PM\GDO_PM;
use GDO\PM\GDO_PMFolder;
use GDO\PM\Module_PM;
use GDO\PM\PMMethod;
use GDO\UI\GDT_Headline;
use GDO\UI\GDT_Page;
use GDO\User\GDO_User;
use GDO\User\GDT_User;

/**
 * Write a PM to a user.
 *
 * @version 7.0.3
 * @since 3.3.0
 * @author gizmore
 */
class Write extends MethodForm
{

	use PMMethod;

	protected GDO_PM $pmTo;

	/**
	 * @throws GDO_DBException
	 */
	public function validateCanSend(GDT_Form $form, GDT $field, $value = null): bool
	{
		$user = GDO_User::current();

		# We validate the user field.
		if ($value instanceof GDO_User)
		{
			if ($value->getID() === $user->getID())
			{
				if (!Module_PM::instance()->cfgPMSelf())
				{
					return $field->error('err_no_pm_self');
				}
			}
			if (!$value->isUser())
			{
				return $field->error('err_only_pm_users');
			}
		}

		# non admins may have a send limit
		if (!$user->isAdmin())
		{
			$module = Module_PM::instance();
			if ($module->cfgIsPMLimited())
			{
				$limit = $module->cfgLimitForUser($user);
				$cutTime = Application::$TIME - $module->cfgLimitTimeout();
				$cut = Time::getDate($cutTime);
				$uid = $user->getID();
				$sent = GDO_PM::table()->countWhere("pm_owner!={$uid} AND pm_from={$uid} and pm_sent_at>'$cut'");
				if ($sent >= $limit)
				{
					return $field->error('err_pm_limit_reached', [$limit, Time::displayAgeTS($cutTime)]);
				}
			}
		}

		return true;
	}

	public function preview(): GDT
	{
		$form = $this->getForm();
		$parent = $this->getParent();
		$from = GDO_User::current();
		$to = $this->getRecipient();
		$title = $form->getFormVar('pm_title');
		$message = $form->getFormVar('pm_message');
		$pm = GDO_PM::blank([
			'pm_parent' => $parent ? $parent->getPMFor($to)->getID() : null,
			'pm_owner' => $to->getID(),
			'pm_from' => $from->getID(),
			'pm_to' => $to->getID(),
			'pm_folder' => GDO_PMFolder::INBOX,
			'pm_title' => $title,
			'pm_message' => $message,
		]);
		$card = $this->templatePHP('card_pm.php', ['pm' => $pm, 'noactions' => true]);
		$result = GDT_Tuple::make();
		$result->addField(GDT_Headline::make()->level(2)->text('btn_preview'));
		$result->addField($card);
		$result->addField($this->renderPage());
		return $result;
	}

	protected function getParent(): ?GDO_PM
	{
		return null;
	}

	protected function getRecipient(): GDO_User
	{
		return $this->getForm()->getFormValue('to');
	}

	protected function createForm(GDT_Form $form): void
	{
		[$username, $title, $message] = $this->initialValues($form);
		$table = GDO_PM::table();
		$to = GDT_User::make('to')->notNull()->initial($username)->withCompletion();
		$form->addFields(
			$to,
			GDT_Validator::make()->validator($form, $to, [$this, 'validateCanSend']),
			$table->gdoColumnCopy('pm_title')->initial($title),
			$table->gdoColumnCopy('pm_message')->initial($message),
			GDT_AntiCSRF::make(),
		);
		$form->actions()->addFields(
			GDT_Submit::make(),
			GDT_Submit::make('btn_preview')->onclick([$this, 'preview']),
		);
	} # this is the sent pm. we might act with an email.

	protected function initialValues(GDT_Form $form): array
	{
		$username = null;
		$title = null;
		$message = null;
		return [$username, $title, $message];
	}

	###############
	### Preview ###
	###############

	public function formValidated(GDT_Form $form): GDT
	{
		$this->deliver(GDO_User::current(), $form->getFormValue('to'),
			$form->getFormVar('pm_title'), $form->getFormVar('pm_message'));
		return $this->redirectMessage('msg_pm_sent', null, href('PM', 'Overview'));
	}

	public function deliver(GDO_User $from, GDO_User $to, string $title, string $message, GDO_PM $parent = null)
	{
		$from->persistent();
		$to->persistent();
		$pmFrom = GDO_PM::blank([
			'pm_parent' => $parent?->getPMFor($from)->getID(),
			'pm_read_at' => Time::getDate(),
			'pm_owner' => $from->getID(),
			'pm_from' => $from->getID(),
			'pm_to' => $to->getID(),
			'pm_folder' => GDO_PMFolder::OUTBOX,
			'pm_title' => $title,
			'pm_message' => $message,
		])->insert();
		$pmTo = GDO_PM::blank([
			'pm_parent' => $parent?->getPMFor($to)->getID(),
			'pm_owner' => $to->getID(),
			'pm_from' => $from->getID(),
			'pm_to' => $to->getID(),
			'pm_folder' => GDO_PMFolder::INBOX,
			'pm_title' => $title,
			'pm_message' => $message,
			'pm_other' => $pmFrom->getID(),
			'pm_other_read_at' => Time::getDate(),
		])->insert();
		$pmFrom->saveVar('pm_other', $pmTo->getID());
		$to->tempUnset('gdo_pm_unread');

		# Copy to next func
		$this->pmTo = $pmTo;
	}

public function afterExecute(): void
	{
		if ($this->pressedButton === 'submit')
		{
			if (isset($this->pmTo))
			{
				$pmTo = $this->pmTo;
				$response = EMailOnPM::deliver($pmTo);
				if ($response)
				{
					GDT_Page::instance()->topResponse()->addField($response);
				}
				GDT_Hook::callWithIPC('PMSent', $pmTo);
			}
		}
	}

}
