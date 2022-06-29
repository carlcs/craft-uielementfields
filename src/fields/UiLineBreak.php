<?php

namespace carlcs\uielementfields\fields;

use Craft;
use craft\base\ElementInterface;

class UiLineBreak extends BaseField
{
    public static function displayName(): string
    {
        return Craft::t('app', 'UI Line Break');
    }

    public function formHtml(ElementInterface $element = null): string
    {
        return '';
    }
}
