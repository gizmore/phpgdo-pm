<?php
namespace GDO\PM\Test;

use GDO\PM\GDO_PM;
use GDO\PM\Method\Folder;
use GDO\PM\Method\Folders;
use GDO\PM\Method\Write;
use GDO\Tests\GDT_MethodTest;
use GDO\Tests\TestCase;
use function PHPUnit\Framework\assertEquals;
use function PHPUnit\Framework\assertGreaterThanOrEqual;

/**
 * Private message module tests.
 * Sends a PM so automated tests have something to play with.
 *
 * @version 7.0.1
 * @since 6.10.0
 * @author gizmore
 */
final class PNTest extends TestCase
{

	public function testDefaultMethods()
	{
		GDT_MethodTest::make()->method(Folders::make())->execute();
		$this->assert200('Test PM::Folders');
		GDT_MethodTest::make()->method(Folder::make())->execute();
		$this->assert200('Test PM::Folder');
	}

	public function testPreview()
	{
		$p = ['pm_title' => 'TITLE', 'pm_message' => 'MESSAGE', 'to' => '3'];
		$r = GDT_MethodTest::make()->method(Write::make())->inputs($p)->execute('btn_preview');
		$html = $r->renderHTML();
		$n = substr_count($html, 'MESSAGE');
		assertEquals(2, $n, 'Test if message is shown and kept in editor.');
	}

	public function testSend()
	{
		$p = ['pm_title' => 'TITLE', 'pm_message' => 'MESSAGE', 'to' => '3'];
		$m = GDT_MethodTest::make()->method(Write::make())->inputs($p);
		$m->execute();
		$n = GDO_PM::table()->countWhere();
		assertGreaterThanOrEqual(2, $n, 'Test if PM can be sent from giz to 3.');
	}

}
