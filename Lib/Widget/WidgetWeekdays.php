<?php
/**
 * This file is part of WidgetWeekdays plugin for FacturaScripts.
 * FacturaScripts Copyright (C) 2015-2026 Carlos Garcia Gomez <carlos@facturascripts.com>
 * WidgetWeekdays Copyright (C) 2026 Jose Antonio Cuello Principal <yopli2000@gmail.com>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */
namespace FacturaScripts\Plugins\WidgetWeekdays\Lib\Widget;

use FacturaScripts\Core\Lib\Widget\BaseWidget;
use FacturaScripts\Core\Request;
use FacturaScripts\Core\Tools;
use FacturaScripts\Dinamic\Lib\AssetManager;
use FacturaScripts\Dinamic\Lib\Weekdays;

/**
 * Widget to select the days of the week (any combination) and store them
 * into a varchar(7) field as a string of 1 (selected) and 0 (not selected).
 *
 * This is the base widget: it only renders the seven day toggles (monday →
 * sunday). The quick-select presets (workweek, all, none) are provided by the
 * child widget WidgetWeekdaysfull (type="weekdaysfull").
 *
 * The position of each character follows the ISO 8601 order used by the rest
 * of FacturaScripts (DateTimeTools::dayOfWeek):
 *   index 0 = Monday (ISO 1) ... index 6 = Sunday (ISO 7)
 *
 * A null or empty value is treated as all zeros ('0000000').
 *
 * @author Jose Antonio Cuello Principal <yopli2000@gmail.com>
 */
class WidgetWeekdays extends BaseWidget
{
    /**
     * Definición de los días en orden lunes→domingo (ISO 8601).
     * - name: clave de traducción del nombre completo (existe en el Core).
     * - abbr: clave de traducción de la abreviatura (botón de edición).
     * - letter: clave de traducción de la letra (vista de solo lectura).
     *
     * @var array
     */
    protected static $days = [
        ['name' => 'monday', 'abbr' => 'weekday-mon-abbr', 'letter' => 'weekday-mon-letter'],
        ['name' => 'tuesday', 'abbr' => 'weekday-tue-abbr', 'letter' => 'weekday-tue-letter'],
        ['name' => 'wednesday', 'abbr' => 'weekday-wed-abbr', 'letter' => 'weekday-wed-letter'],
        ['name' => 'thursday', 'abbr' => 'weekday-thu-abbr', 'letter' => 'weekday-thu-letter'],
        ['name' => 'friday', 'abbr' => 'weekday-fri-abbr', 'letter' => 'weekday-fri-letter'],
        ['name' => 'saturday', 'abbr' => 'weekday-sat-abbr', 'letter' => 'weekday-sat-letter'],
        ['name' => 'sunday', 'abbr' => 'weekday-sun-abbr', 'letter' => 'weekday-sun-letter'],
    ];

    /**
     * Representación en texto plano (exportaciones, PDF...).
     *
     * @param object $model
     * @return string
     */
    public function plainText($model)
    {
        $this->setValue($model);
        $selected = Weekdays::toArray($this->value);
        $names = [];
        foreach (static::$days as $i => $day) {
            if ($selected[$i]) {
                $names[] = Tools::trans($day['name']);
            }
        }

        return empty($names) ? '-' : implode(', ', $names);
    }

    /**
     * @param object $model
     * @param Request $request
     */
    public function processFormData(&$model, $request)
    {
        $value = $request->request->get($this->fieldname);
        $model->{$this->fieldname} = Weekdays::normalize($value);
    }

    /**
     * Añade los recursos necesarios al gestor de assets.
     */
    protected function assets(): void
    {
        $route = Tools::config('route');
        AssetManager::addCss($route . '/Dinamic/Assets/CSS/WidgetWeekdays.css');
        AssetManager::addJs($route . '/Dinamic/Assets/JS/WidgetWeekdays.js');
    }

    /**
     * Devuelve el HTML del control en modo edición.
     *
     * @param string $type
     * @param string $extraClass
     * @return string
     */
    protected function inputHtml($type = 'text', $extraClass = ''): string
    {
        $selected = Weekdays::toArray($this->value);
        $uid = $this->getUniqueId();
        $disabled = $this->readonly() ? ' disabled=""' : '';

        $daysHtml = '';
        foreach (static::$days as $i => $day) {
            $id = 'wd_' . $uid . '_' . $i;
            $checked = $selected[$i] ? ' checked=""' : '';
            $daysHtml .= '<input type="checkbox" class="btn-check fs-weekdays-day" id="' . $id . '"'
                . ' autocomplete="off"' . $checked . $disabled . '/>'
                . '<label class="btn btn-outline-primary" for="' . $id . '">'
                . Tools::trans($day['abbr']) . '</label>';
        }

        return '<div class="fs-weekdays d-md-flex align-items-center flex-wrap" data-field="' . $this->fieldname . '">'
            . '<input type="hidden" name="' . $this->fieldname . '" value="' . implode('', $selected) . '"/>'
            . '<div class="btn-group fs-weekdays-days" role="group">' . $daysHtml . '</div>'
            . $this->presetsHtml()
            . '</div>';
    }

    /**
     * HTML de los botones de selección rápida (presets). En el widget base no
     * hay presets; el widget hijo (WidgetWeekdaysfull) sobrescribe este método.
     *
     * @return string
     */
    protected function presetsHtml(): string
    {
        return '';
    }

    /**
     * Representación visual del valor en tablas/listados.
     *
     * @return string
     */
    protected function show(): string
    {
        if (Weekdays::isEmpty($this->value)) {
            return '-';
        }

        $selected = Weekdays::toArray($this->value);
        $html = '<span class="fs-weekdays-show">';
        foreach (static::$days as $i => $day) {
            $class = $selected[$i] ? 'badge bg-primary' : 'badge bg-light text-muted';
            $html .= '<span class="' . $class . ' me-1">' . Tools::trans($day['letter']) . '</span>';
        }

        return $html . '</span>';
    }
}
