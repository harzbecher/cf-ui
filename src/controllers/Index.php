<?php

/**
 * Created by PhpStorm.
 * User: 204072257
 * Date: 4/11/2016
 * Time: 8:16 PM
 */

use Mapache\Controller;

class Index extends Controller
{
    public function IndexAction(){
        header("Location: home");
    }
}