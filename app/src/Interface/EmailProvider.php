<?php

namespace LetsCo\Interface;

interface EmailProvider
{
    public function send($to, $templateID, $params);
}
