<?php

namespace HealthEngine\I18n\Contracts;

use Illuminate\Contracts\Translation\Translator;

interface I18nTranslator extends Translator
{
    /**
     * @return string The content direction (e.g. "ltr" or "rtl")
     */
    public function direction(): string;
}
