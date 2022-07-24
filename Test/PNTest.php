<?php
namespace GDO\PM\Test;

use GDO\Tests\GDT_MethodTest;
use GDO\Tests\TestCase;
use GDO\PM\Method\Write;
use function PHPUnit\Framework\assertGreaterThanOrEqual;
use function PHPUnit\Framework\assertEquals;
use GDO\PM\Method\Folder;
use GDO\PM\Method\Folders;

final class PNTest extends TestCase
{
    public function testDefaultMethods()
    {
    	GDT_MethodTest::make()->method(Folders::make())->execute();
        $this->assert200("Test PM::Folders");
        GDT_MethodTest::make()->method(Folder::make())->execute();
        $this->assert200("Test PM::Folder");
    }
    
    public function testPreview()
    {
        $p = ['pm_title' => 'TITLE', 'pm_message' => 'MESSAGE', 'pm_write_to' => '3'];
        $r = GDT_MethodTest::make()->method(Write::make())->inputs($p)->execute('btn_preview');
        $html = $r->render();
        
        $n = substr_count($html, 'MESSAGE');
        assertEquals(2, $n, 'Test if message is shown and kept in editor.');
    }
    
}
