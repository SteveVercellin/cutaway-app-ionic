<?php

namespace Includes\Interfaces;

interface WordpressRestFunction
{
    public function registerRestApis();
    public function registerRestFunctions();
    public function registerTestRestFunctions();
}