<?php

namespace carlcs\uielementfields\fields;

use Craft;
use craft\base\ElementInterface;
use craft\helpers\Html;

abstract class BaseField extends craft\base\Field
{
    public static function hasContentColumn(): bool
    {
        return false;
    }

    protected function inputHtml(mixed $value, ElementInterface $element = null): string
    {
        return $this->_removeHeadingStyles() . $this->formHtml($element);
    }

    protected function formHtml(ElementInterface $element = null): string
    {
        return '';
    }

    private function _removeHeadingStyles(): string
    {
        $namespacedId =  Craft::$app->getView()->namespaceInputId($this->handle);
        $namespacedId = "$namespacedId-field";

        $css = <<<CSS
#$namespacedId .heading,
#$namespacedId .instructions {
    display: none;
}
CSS;

        return Html::style($css);
    }
}
