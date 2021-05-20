<?php

namespace carlcs\uielementfields\fields;

use Craft;
use craft\base\ElementInterface;
use craft\helpers\Html;

abstract class BaseField extends craft\base\Field
{
    /**
     * @inheritdoc
     */
    public static function hasContentColumn(): bool
    {
        return false;
    }

    /**
     * @inheritdoc
     */
    protected function inputHtml($value, ElementInterface $element = null): string
    {
        return $this->_removeHeadingStyles() . $this->formHtml($element);
    }

    /**
     * @param ElementInterface|null $element
     * @return string
     */
    protected function formHtml(ElementInterface $element = null): string
    {
        return '';
    }

    /**
     * @return string
     */
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
