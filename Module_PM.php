<?php
namespace GDO\PM;

use GDO\Core\GDO_Module;
use GDO\Date\GDT_Duration;
use GDO\PM\Method\Write;
use GDO\Register\GDO_UserActivation;
use GDO\Core\GDT_Checkbox;
use GDO\Core\GDT_Int;
use GDO\UI\GDT_Message;
use GDO\Core\GDT_Name;
use GDO\Core\GDT_String;
use GDO\User\GDT_Level;
use GDO\User\GDT_User;
use GDO\User\GDT_Username;
use GDO\User\GDO_User;
use GDO\UI\GDT_Card;
use GDO\UI\GDT_Link;
use GDO\UI\GDT_Page;

/**
 * Private messaging module.
 * 
 * - WelcomePM
 * - EmailOnPM
 * - Folders
 * 
 * @author gizmore
 * @version 7.0.1
 * @since 6.4.0
 */
final class Module_PM extends GDO_Module
{
	##############
	### Module ###
	##############
	public function getClasses() : array { return [GDO_PMFolder::class, GDO_PM::class]; }
	public function onLoadLanguage() : void { $this->loadLanguage('lang/pm'); }
	public function onInstall() : void { PMInstall::install($this); }
	public function getDependencies() : array { return ['Account']; }
	
	##############
	### Config ###
	##############
	public function getConfig() : array
	{
		return [
			GDT_String::make('pm_re')->initial('RE: '),
			GDT_Int::make('pm_limit')->initial('5')->unsigned()->min(0)->max(10000),
			GDT_Duration::make('pm_limit_timeout')->initial('16h'),
			GDT_Int::make('pm_max_folders')->initial('0')->unsigned(),
			GDT_Checkbox::make('pm_for_guests')->initial('1'),
			GDT_Checkbox::make('pm_captcha')->initial('0'),
			GDT_Checkbox::make('pm_causes_mail')->initial(module_enabled('Mail')?'1':'0'),
			GDT_User::make('pm_bot_uid')->label('pm_bot_uid')->writeable(false),
			GDT_Checkbox::make('pm_own_bot'),
			GDT_Int::make('pm_per_page')->initial('20')->unsigned(),
			GDT_Checkbox::make('pm_welcome')->initial('0'),
			GDT_Int::make('pm_sig_len')->initial('255')->max(1024)->unsigned(),
			GDT_Int::make('pm_msg_len')->initial('2048')->max(65535)->unsigned(),
			GDT_Int::make('pm_title_len')->initial('64')->max(255)->unsigned(),
			GDT_Int::make('pm_fname_len')->initial(GDT_Username::LENGTH)->max(GDT_Name::LENGTH),
			GDT_Checkbox::make('pm_delete')->initial('1'),
			GDT_Int::make('pm_limit_per_level')->initial('1000000')->unsigned(),
		    GDT_Checkbox::make('hook_sidebar')->initial('1'),
		];
	}
	public function cfgRE() { return $this->getConfigValue('pm_re'); }
	public function cfgIsPMLimited() { return $this->cfgLimitTimeout() >= 0; }
	public function cfgPMLimit() { return $this->getConfigValue('pm_limit'); }
	public function cfgLimitTimeout() { return $this->getConfigValue('pm_limit_timeout'); }
	public function cfgMaxFolders() { return $this->getConfigValue('pm_maxfolders'); }
	public function cfgAllowOwnFolders() { return $this->cfgMaxFolders() > 0; }
	public function cfgGuestPMs() { return $this->getConfigValue('pm_for_guests'); }
	public function cfgGuestCaptcha() { return $this->getConfigValue('pm_captcha'); }
	public function cfgEmailOnPM() { return $this->getConfigValue('pm_causes_mail'); }
	public function cfgEmailSender() { return $this->getConfigValue('pm_mail_sender'); }
	public function cfgBotUserID() { return $this->getConfigVar('pm_bot_uid'); }
	public function cfgBotUser() { return $this->getConfigValue('pm_bot_uid'); }
	public function cfgOwnBot() { return $this->getConfigValue('pm_own_bot'); }
	public function cfgPMPerPage() { return $this->getConfigValue('pm_per_page'); }
	public function cfgWelcomePM() { return $this->getConfigValue('pm_welcome'); }
	public function cfgMaxSigLen() { return $this->getConfigValue('pm_sig_len'); }
	public function cfgMaxMsgLen() { return $this->getConfigValue('pm_msg_len'); }
	public function cfgMaxTitleLen() { return $this->getConfigValue('pm_title_len'); }
	public function cfgMaxFolderNameLen() { return $this->getConfigValue('pm_fname_len'); }
	public function cfgAllowDelete() { return $this->getConfigValue('pm_delete'); }
	public function cfgLimitPerLevel() { return $this->getConfigValue('pm_limit_per_level'); }
	public function cfgLimitForUser(GDO_User $user)
	{
		$min = $this->cfgPMLimit();
		$level = $user->getLevel();
		return $min + floor($level / $this->cfgLimitPerLevel());
	}
	public function cfgRightBar() { return $this->getConfigValue('hook_sidebar'); }
	
	################
	### Settings ###
	################
	public function getUserSettings()
	{
	    return [
	        GDT_Link::make('link_pm_center')->href(href('PM', 'Overview'))->noacl(),
	        GDT_Level::make('pm_level')->initial('0')->notNull()->label('pm_level'),
	        GDT_Checkbox::make('pm_email')->initial('1'),
	        GDT_Checkbox::make('pm_guests')->initial('0'),
	    ];
	}
	
	public function getUserSettingBlobs()
	{
	    return [
	        GDT_Message::make('signature')->max(4096)->label('signature')->noacl(),
	    ];
	}
	
	#############
	### Hooks ###
	#############
	public function hookProfileCard(GDO_User $user, GDT_Card $card)
	{
		$linkPM = GDT_Link::make()->href(href('PM', 'Write', '&username='.$user->renderUserName()))->label(t('link_write_pm'));
		$card->actions()->addField($linkPM);
	}
	
	public function hookUserActivated(GDO_User $user, GDO_UserActivation $activation=null)
	{
		if ($this->cfgWelcomePM())
		{
			if ($bot = $this->cfgBotUser())
			{
				$this->sendWelcomePM(method('PM', 'Write'), $bot, $user);
			}
		}
	}
	
	private function sendWelcomePM(Write $method, GDO_User $from, GDO_User $to)
	{
		$title = t('pm_welcome_title', [sitename()]);
		$message = t('pm_welcome_message', [$to->displayName(), sitename()]);
		$method->deliver($from, $to, $title, $message);
	}

	##############
	### Navbar ###
	##############
	public function onInitSidebar() : void
	{
	    if ($this->cfgRightBar())
	    {
    		if (GDO_User::current()->isAuthenticated())
    		{
    		    $user = GDO_User::current();
    		    $count = GDO_PM::countUnread($user);
    		    $button = GDT_Link::make('btn_pm')->href(href('PM', 'Overview'));
    		    if ($count > 0)
    		    {
    		        $button->label('btn_pm_unread', [$count]);
    		    }
    		    GDT_Page::instance()->rightBar()->addField($button);
    		}
	    }
	}
	
}
