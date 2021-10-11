<?php
namespace WPQuadsPro\Forms\Elements;

use WPQuadsPro\Forms\Elements;

/**
 * Class File
 * @package WPQuadsPro\Forms\Elements
 */
class File extends Elements
{

    /**
     * @return string
     */
    protected function prepareOutput()
    {
        return "<input id='{$this->getId()}' name='{$this->getName()}' type='file' {$this->prepareAttributes()} value='{$this->default}' />";
    }

    /**
     * @return string
     */
    public function render()
    {
        return ($this->renderFile) ? @file_get_contents($this->renderFile) : $this->prepareOutput();
    }
}