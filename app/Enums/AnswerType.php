<?php

namespace App\Enums;

enum AnswerType: string
{
    case String = 'string';
    case Boolean = 'boolean';
    case Single = 'single';
    case Multiple = 'multiple';
}
