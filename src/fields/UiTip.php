<?php

namespace carlcs\uielementfields\fields;

use Craft;
use craft\base\ElementInterface;
use craft\helpers\Html;
use yii\helpers\Markdown;

class UiTip extends BaseField
{
    const STYLE_TIP = 'tip';
    const STYLE_WARNING = 'warning';

    /**
     * @var string
     */
    public $tip;

    /**
     * @var string
     */
    public $style = self::STYLE_TIP;

    /**
     * @inheritdoc
     */
    public static function displayName(): string
    {
        return Craft::t('app', 'UI Tip');
    }

    /**
     * @inheritdoc
     */
    public function getSettingsHtml(): string
    {
        return Craft::$app->getView()->renderTemplateMacro('_includes/forms', 'textareaField', [
            [
                'label' => $this->_isTip() ? Craft::t('app', 'Tip') : Craft::t('app', 'Warning'),
                'instructions' => Craft::t('app', 'Can contain Markdown formatting.'),
                'class' => 'nicetext',
                'id' => 'tip',
                'name' => 'tip',
                'value' => $this->tip,
            ]
        ]);
    }

    /**
     * @inheritdoc
     */
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

    /**
     * @return bool
     */
    private function _isTip(): bool
    {
        return $this->style !== self::STYLE_WARNING;
    }
}
