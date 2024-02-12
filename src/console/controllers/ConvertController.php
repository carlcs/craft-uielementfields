<?php

namespace carlcs\uielementfields\console\controllers;

use carlcs\uielementfields\fields\UiLineBreak;
use carlcs\uielementfields\fields\UiTemplate;
use carlcs\uielementfields\fields\UiTip;
use carlcs\uielementfields\fields\UiWarning;
use Craft;
use craft\console\Controller;
use craft\fieldlayoutelements\LineBreak;
use craft\fieldlayoutelements\Template;
use craft\fieldlayoutelements\Tip;
use craft\helpers\Console;
use craft\services\ProjectConfig;
use yii\console\ExitCode;

/**
 * Converts UI Element fields to core UI Elements
 */
class ConvertController extends Controller
{
    public $defaultAction = 'index';

    /**
     * Converts UI Element fields to core UI Elements
     */
    public function actionIndex(): int
    {
        $projectConfig = Craft::$app->getProjectConfig();

        $fieldConfigs = null;
        $this->do('Looking for UI Element fields in the project config', function () use (&$fieldConfigs, $projectConfig) {
            $fieldConfigs = array_filter(
                $projectConfig->get(ProjectConfig::PATH_FIELDS) ?? [],
                fn ($config) => in_array(($config['type'] ?? null), [
                    UiTip::class,
                    UiWarning::class,
                    UiTemplate::class,
                    UiLineBreak::class,
                ], true),
            );
        });

        if (empty($fieldConfigs)) {
            $this->stdout("   No UI Element fields found.\n", Console::FG_YELLOW);
            return ExitCode::OK;
        }

        $this->stdout(' → ', Console::FG_GREY);
        $this->stdout('Looking for entry types where UI Element fields are used');
        $this->stdout(" …\n", Console::FG_GREY);

        $entryTypeConfigs = $projectConfig->get(ProjectConfig::PATH_ENTRY_TYPES) ?? [];
        $modifiedAny = false;

        foreach ($entryTypeConfigs as $entryTypeUid => $entryTypeConfig) {
            $modified = false;

            if (isset($entryTypeConfig['fieldLayouts']) && is_array($entryTypeConfig['fieldLayouts'])) {
                foreach ($entryTypeConfig['fieldLayouts'] as &$fieldLayoutConfig) {
                    if (isset($fieldLayoutConfig['tabs']) && is_array($fieldLayoutConfig['tabs'])) {
                        foreach ($fieldLayoutConfig['tabs'] as &$tabConfig) {
                            if (isset($tabConfig['elements']) && is_array($tabConfig['elements'])) {
                                foreach ($tabConfig['elements'] as &$elementConfig) {
                                    $fieldConfig = $fieldConfigs[$elementConfig['fieldUid'] ?? null] ?? null;
                                    if (!$fieldConfig) {
                                        continue;
                                    }

                                    $elementConfig = $this->convertFieldLayoutElementConfig($elementConfig, $fieldConfig);
                                    $modified = $modifiedAny = true;
                                }
                            }
                        }
                    }
                }
            }

            if ($modified) {
                $this->stdout('   ');
                $this->do(
                    $this->markdownToAnsi(sprintf('Updating `%s` (`%s`)', $entryTypeUid, $entryTypeConfig['handle'])),
                    function () use ($entryTypeUid, $entryTypeConfig, $projectConfig) {
                        $entryTypePath = sprintf('%s.%s', ProjectConfig::PATH_ENTRY_TYPES, $entryTypeUid);
                        $projectConfig->set($entryTypePath, $entryTypeConfig);
                    },
                );
            }
        }

        if (!$modifiedAny) {
            $this->stdout("   No entry types found.\n", Console::FG_YELLOW);
        } else {
            $this->stdout(" ✓ Finished updating entry types\n", Console::FG_GREEN);
        }

        $this->stdout(' → ', Console::FG_GREY);
        $this->stdout('Deleting UI Element fields');
        $this->stdout(" …\n", Console::FG_GREY);

        foreach ($fieldConfigs as $fieldUid => $fieldConfig) {
            $this->stdout('   ');
            $this->do(
                $this->markdownToAnsi(sprintf('Deleting `%s` (`%s`)', $fieldUid, $fieldConfig['handle'])),
                function () use ($fieldUid, $projectConfig) {
                    $fieldPath = sprintf('%s.%s', ProjectConfig::PATH_FIELDS, $fieldUid);
                    $projectConfig->remove($fieldPath);
                },
            );
        }

        $this->stdout(" ✓ Finished deleting fields\n", Console::FG_GREEN);
        $this->stdout(" ✓ Finished converting UI Element fields.\n", Console::FG_GREEN, Console::BOLD);

        return ExitCode::OK;
    }

    private function convertFieldLayoutElementConfig(array $elementConfig, array $fieldConfig): array
    {
        $config = [
            'uid' => $elementConfig['uid'],
            'userCondition' => $elementConfig['userCondition'],
            'elementCondition' => $elementConfig['elementCondition'],
        ];

        switch ($fieldConfig['type']) {
            case UiTip::class:
            case UiWarning::class:
                $config['type'] = Tip::class;
                $config['tip'] = $fieldConfig['settings']['tip'];
                $config['style'] = $fieldConfig['settings']['style'];
                $config['dismissible'] = false;
                break;
            case UiTemplate::class:
                $config['type'] = Template::class;
                $config['template'] = $fieldConfig['settings']['template'];
                $config['width'] = $elementConfig['width'];
                break;
            case UiLineBreak::class:
                $config['type'] = LineBreak::class;
                break;
        }

        return $config;
    }
}
