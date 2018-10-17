<?php

namespace BionConnection\MovistarM2M\Facades;

use Illuminate\Support\Facades\Facade;

class MovistarM2M extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    
    protected static function getFacadeAccessor()
    {
        return 'MovistarM2M';
    }
}
