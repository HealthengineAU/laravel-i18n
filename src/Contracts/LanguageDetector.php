<?php

namespace Healthengine\I18n\Contracts;

use Illuminate\Http\Request;

interface LanguageDetector
{
    public function detect(Request $request): ?string;
}
