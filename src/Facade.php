<?php namespace Socialgrid\SnsSender;
class Facade extends \Illuminate\Support\Facades\Facade
{
    /**
     * {@inheritDoc}
     */
    protected static function getFacadeAccessor()
    {
        return 'snssender';
    }
}
