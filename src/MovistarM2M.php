<?php
namespace BionConnection\MovistarM2M;
use Illuminate\Support\ServiceProvider;




class MovistarM2M extends ServiceProvider
{

    protected $movistar;
    
    
    public function __construct(Api $movistar)
    {
        $this->movistar = $movistar;
        
    }
    
    
    public function echoThis($string)
    {
        return $this->request('flickr.test.echo', ['this' => $string]);
    }
    
    
}




/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

