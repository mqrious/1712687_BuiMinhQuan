<?php

class HomeController
{

    public function index()
    {
        $view = './view/index.phtml';
        require './layout/app.phtml';
    }

}