<?php

namespace App\Entity\Traits;

trait EntityCommonTrait
{
    /**
     * @return string
     */
    public function getClassName()
    {
        return (new \ReflectionClass($this))->getShortName();
    }
}
