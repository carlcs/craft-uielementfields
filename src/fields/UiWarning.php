<?php

namespace carlcs\uielementfields\fields;

use Craft;

class UiWarning extends UiTip
{
    public string $style = self::STYLE_WARNING;

    public static function displayName(): string
    {
        return Craft::t('app', 'UI Warning');
    }
}
