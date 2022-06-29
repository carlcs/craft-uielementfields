<?php

namespace carlcs\uielementfields;

use carlcs\uielementfields\fields\UiLineBreak;
use carlcs\uielementfields\fields\UiTemplate;
use carlcs\uielementfields\fields\UiTip;
use carlcs\uielementfields\fields\UiWarning;
use craft\events\RegisterComponentTypesEvent;
use craft\services\Fields;
use yii\base\Event;

/**
 * @method static Plugin getInstance()
 */
class Plugin extends \craft\base\Plugin
{
    public function init()
    {
        parent::init();

        Event::on(Fields::class, Fields::EVENT_REGISTER_FIELD_TYPES, function(RegisterComponentTypesEvent $event) {
            $event->types[] = UiTip::class;
            $event->types[] = UiWarning::class;
            $event->types[] = UiTemplate::class;
            $event->types[] = UiLineBreak::class;
        });
    }
}
