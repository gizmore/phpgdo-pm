<?php
namespace GDO\PM\Test;

use GDO\Tests\MethodTest;
use GDO\Tests\TestCase;
use GDO\PM\Method\Write;
use function PHPUnit\Framework\assertGreaterThanOrEqual;
use function PHPUnit\Framework\assertEquals;

final class PNTest extends TestCase
{
    public function testDefaultMethods()
    {
        MethodTest::make()->defaultMethod('PM', 'Folders');
        $this->assert200("Test PM::Folders");
        MethodTest::make()->defaultMethod('PM', 'Folder');
        $this->assert200("Test PM::Folder");
    }
    
    public function testPreview()
    {
        $p = ['pm_title' => 'TITLE', 'pm_message' => 'MESSAGE', 'pm_write_to' => '3'];
        $r = MethodTest::make()->method(Write::make())->parameters($p)->execute('btn_preview');
        $html = $r->render();
        
        $n = substr_count($html, 'MESSAGE');
        assertEquals(2, $n, 'Test if message is shown and kept in editor.');
    }
    
}
