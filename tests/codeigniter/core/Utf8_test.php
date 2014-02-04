<?php

class Utf8_test extends CI_TestCase
{
    public function set_up()
    {
        $this->utf8 = new Mock_Core_Utf8();
    }

    // --------------------------------------------------------------------

    public function test_convert_to_utf8()
    {
        $this->assertEquals('тест', $this->utf8->convert_to_utf8('����', 'WINDOWS-1251'));
    }

    // --------------------------------------------------------------------

    public function test_is_ascii()
    {
        $this->assertTrue($this->utf8->is_ascii_test('foo bar'));
        $this->assertFalse($this->utf8->is_ascii_test('тест'));
    }

}
