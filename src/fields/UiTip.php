<?php

namespace carlcs\uielementfields\fields;

use Craft;
use craft\base\ElementInterface;
use craft\helpers\Cp;
use craft\helpers\Html;
use yii\helpers\Markdown;

class UiTip extends BaseField
{
    const STYLE_TIP = 'tip';
    const STYLE_WARNING = 'warning';

    public string $tip;
    public string $style = self::STYLE_TIP;

    public static function displayName(): string
    {
        return Craft::t('app', 'UI Tip');
    }

    public function getSettingsHtml(): ?string
    {
        return Cp::textareaFieldHtml([
            'label' => $this->_isTip() ? Craft::t('app', 'Tip') : Craft::t('app', 'Warning'),
            'instructions' => Craft::t('app', 'Can contain Markdown formatting.'),
            'class' => 'nicetext',
            'id' => 'tip',
            'name' => 'tip',
            'value' => $this->tip,
        ]);
    }

    public function formHtml(ElementInterface $element = null): string
    {
        $noteClass = $this->_isTip() ? self::STYLE_TIP : self::STYLE_WARNING;
        $tip = Markdown::process(Html::encode(Craft::t('site', $this->tip)));

        $content = Html::tag('blockquote', $tip, [
            'class' => ['note', $noteClass],
        ]);

        return Html::tag('div', $content, [
            'class' => 'readable',
        ]);
    }

    private function _isTip(): bool
    {
        return $this->style !== self::STYLE_WARNING;
    }
}
