<?php

class HomeController
{

    public function index()
    {
        $VIEW = './view/index.phtml';
        require './layout/app.phtml';
    }

}