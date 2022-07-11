<?php
namespace GDO\PM\Method;

use GDO\Core\Application;
use GDO\Core\GDT_Hook;
use GDO\Date\Time;
use GDO\Form\GDT_AntiCSRF;
use GDO\Form\GDT_Form;
use GDO\Form\GDT_Submit;
use GDO\Form\MethodForm;
use GDO\PM\EMailOnPM;
use GDO\PM\Module_PM;
use GDO\PM\GDO_PM;
use GDO\PM\GDO_PMFolder;
use GDO\PM\PMMethod;
use GDO\User\GDT_User;
use GDO\User\GDO_User;
use GDO\Util\Common;
use GDO\Util\Strings;
use GDO\Form\GDT_Validator;
use GDO\Core\Website;
use GDO\UI\GDT_Message;

final class Write extends MethodForm
{
	use PMMethod;
	
	private $reply;
	
	public function execute()
	{
		$user = GDO_User::current();
		$module = Module_PM::instance();

		# Get in reply to
		if ($this->reply = GDO_PM::table()->find(Common::getRequestString('reply'), false))
		{
			if ($this->reply->getOwnerID() !== $user->getID())
			{
				$this->reply = null;
			}
		}
		
		if ($module->cfgIsPMLimited())
		{
			$limit = $module->cfgLimitForUser($user);
			$cutTime = Application::$TIME - $module->cfgLimitTimeout();
			$cut = Time::getDate($cutTime);
			$uid = $user->getID();
			$sent = GDO_PM::table()->countWhere("pm_owner!={$uid} AND pm_from={$uid} and pm_sent_at>'$cut'");
			if ($sent >= $limit)
			{
			    return $this->error('err_pm_limit_reached', [$limit, Time::displayAgeTS($cutTime)]);
			}
		}
		
		if ($this->reply)
		{
			return Read::make()->pmRead($this->reply)->
			addField(parent::execute());
		}
		
		return parent::execute();
	}
	
	public function createForm(GDT_Form $form) : void
	{
		list($username, $title, $message) = $this->initialValues($form);
		$table = GDO_PM::table();
		$to = GDT_User::make('pm_write_to')->notNull()->initial($username);
		$form->addFields(array(
			$to,
			GDT_Validator::make()->validator($form, $to, [$this, 'validateCanSend']),
			$table->gdoColumnCopy('pm_title')->initial($title),
			$table->gdoColumnCopy('pm_message')->initial($message),
			GDT_AntiCSRF::make(),
		));
		$form->actions()->addFields([
			GDT_Submit::make(),
		    GDT_Submit::make('btn_preview')
		]);
	}
	
	private function initialValues(GDT_Form $form)
	{
		$username = null; $title = null; $message = null;
		if ($this->reply)
		{
			# Recipient
			$username = $this->reply->getOtherUser(GDO_User::current())->getID();
			# Message
			$message = '';
			# Title
			$title = $this->reply->gdoVar('pm_title');
			$re = Module_PM::instance()->cfgRE();
			$title = $re . ' ' . trim(Strings::substrFrom($title, $re, $title));
		}
		
		if (isset($_REQUEST['quote']))
		{
			$by = $this->reply->getSender();
			$at = $this->reply->gdoVar('pm_sent_at');
			$msg = $this->reply->getMessage();
			$message = GDT_Message::quoteMessage($by, $at, $msg);
		}
		
		if (isset($_REQUEST['username']))
		{
		    if ($user = GDO_User::getByName($_REQUEST['username']))
		    {
		        $username = $user->getID();
		    }
		}
		
		return [$username, $title, $message];
	}
	
	public function validateCanSend(GDT_Form $form, GDT_User $user, GDO_User $value=null)
	{
	    if ($value)
	    {
    		if ($value->getID() === GDO_User::current()->getID())
    		{
    		    return $user->error('err_no_pm_self');
    		}
    		if (!$value->isUser())
    		{
    		    return $user->error('err_only_pm_users');
    		}
	    }
		return true;
	}
	
	public function formValidated(GDT_Form $form)
	{
		$this->deliver(GDO_User::current(), $form->getFormValue('pm_write_to'), $form->getFormVar('pm_title'), $form->getFormVar('pm_message'), $this->reply);
		return Website::redirectMessage('msg_pm_sent', null, href('PM', 'Overview'));
	}
	
	public function deliver(GDO_User $from, GDO_User $to, $title, $message, GDO_PM $parent=null)
	{
		$pmFrom = GDO_PM::blank(array(
				'pm_parent' => $parent ? $parent->getPMFor($from)->getID() : null,
				'pm_read_at' => Time::getDate(),
				'pm_owner' => $from->getID(),
				'pm_from' => $from->getID(),
				'pm_to' => $to->getID(),
		   		'pm_folder' => GDO_PMFolder::OUTBOX,
				'pm_title' => $title,
				'pm_message' => $message,
		))->insert();
		$pmTo = GDO_PM::blank(array(
				'pm_parent' => $parent ? $parent->getPMFor($to)->getID() : null,
				'pm_owner' => $to->getID(),
				'pm_from' => $from->getID(),
				'pm_to' => $to->getID(),
				'pm_folder' => GDO_PMFolder::INBOX,
				'pm_title' => $title,
				'pm_message' => $message,
				'pm_other' => $pmFrom->getID(),
				'pm_other_read_at' => Time::getDate(),
		))->insert();
		$pmFrom->saveVar('pm_other', $pmTo->getID());
		$to->tempUnset('gdo_pm_unread');
		$to->recache();
		
		# Copy to next func
		$this->pmTo = $pmTo;
	}
	
	/**
	 * @var GDO_PM
	 */
	private $pmTo;
	public function afterExecute()
	{
	    if ($this->pressedButton === 'submit')
	    {
    		if ($this->pmTo)
    		{
    			$pmTo = $this->pmTo;
    			$response = EMailOnPM::deliver($pmTo);
    			GDT_Hook::callWithIPC('PMSent', $pmTo);
    			return $response;
    		}
	    }
	}
	
	###############
	### Preview ###
	###############
	public function onSubmit_btn_preview(GDT_Form $form)
	{
	    $parent = $this->reply;
	    $from = GDO_User::current();
	    $to = $form->getFormValue('pm_write_to');
	    $title = $form->getFormVar('pm_title');
	    $message = $form->getFormVar('pm_message');
	    $pm = GDO_PM::blank(array(
	        'pm_parent' => $parent ? $parent->getPMFor($to)->getID() : null,
	        'pm_owner' => $to->getID(),
	        'pm_from' => $from->getID(),
	        'pm_to' => $to->getID(),
	        'pm_folder' => GDO_PMFolder::INBOX,
	        'pm_title' => $title,
	        'pm_message' => $message,
	    ));
	    $card = $this->templatePHP('card_pm.php', ['pm' => $pm, 'noactions' => true]);
	    
	    return parent::renderPage()->addField($card);
	}

}
