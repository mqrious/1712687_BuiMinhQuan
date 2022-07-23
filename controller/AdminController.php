<?php

class AdminController
{
    public function index()
    {
        $VIEW = './view/admin/index.phtml';
        require './layout/app.phtml';
    }
}