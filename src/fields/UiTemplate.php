<?php

namespace carlcs\uielementfields\fields;

use Craft;
use craft\base\ElementInterface;
use craft\helpers\Html;
use craft\web\View;

class UiTemplate extends BaseField
{
    /**
     * @var string
     */
    public $template;

    /**
     * @inheritdoc
     */
    public static function displayName(): string
    {
        return Craft::t('app', 'UI Template');
    }

    /**
     * @inheritdoc
     */
    public function getSettingsHtml()
    {
        return Craft::$app->getView()->renderTemplateMacro('_includes/forms', 'textField', [
            [
                'label' => Craft::t('app', 'Template'),
                'instructions' => Craft::t('app', 'The path to a template file within your `templates/` folder.'),
                'tip' => Craft::t('app', 'The template will be rendered with an `element` variable.'),
                'class' => 'code',
                'id' => 'template',
                'name' => 'template',
                'value' => $this->template,
            ]
        ]);
    }

    /**
     * @inheritdoc
     */
    public function formHtml(ElementInterface $element = null): string
    {
        if (!$this->template) {
            return $this->_error(Craft::t('app', 'No template path has been chosen yet.'), 'warning');
        }

        try {
            $content = trim(Craft::$app->getView()->renderTemplate($this->template, [
                'element' => $element,
            ], View::TEMPLATE_MODE_SITE));
        } catch (\Throwable $e) {
            return $this->_error($e->getMessage(), 'error');
        }

        return Html::tag('div', $content);
    }

    /**
     * @param string $error
     * @param string $errorClass
     * @return string
     */
    private function _error(string $error, string $errorClass): string
    {
        $icon = Html::tag('span', '', [
            'data' => ['icon' => 'alert'],
        ]);
        $content = Html::tag('p', $icon . ' ' . Html::encode($error), [
            'class' => $errorClass,
        ]);

        return Html::tag('div', $content, [
            'class' => 'pane',
        ]);
    }
}
