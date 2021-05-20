<?php

namespace carlcs\uielementfields\fields;

use Craft;

class UiWarning extends UiTip
{
    /**
     * inheritdoc
     */
    public $style = self::STYLE_WARNING;

    /**
     * @inheritdoc
     */
    public static function displayName(): string
    {
        return Craft::t('app', 'UI Warning');
    }
}
