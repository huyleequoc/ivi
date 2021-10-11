<?php
namespace WPQuadsPro\Forms\Elements;

use WPQuadsPro\Forms\Elements;

/**
 * Class Email
 * @package WPQuadsPro\Forms\Elements
 */
class Email extends Elements
{

    /**
     * @return string
     */
    protected function prepareOutput()
    {
        return "<input id='{$this->getId()}' name='{$this->getName()}' type='email' {$this->prepareAttributes()} value='{$this->default}' />";
    }

    /**
     * @return string
     */
    public function render()
    {
        return ($this->renderFile) ? @file_get_contents($this->renderFile) : $this->prepareOutput();
    }
}