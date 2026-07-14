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

use FacturaScripts\Core\Tools;
use FacturaScripts\Dinamic\Lib\AssetManager;

/**
 * Versión "full" del widget weekdays (type="weekdaysfull").
 *
 * Hereda toda la funcionalidad básica de WidgetWeekdays (selección de los siete
 * días, almacenamiento en varchar(7), vista de solo lectura y exportaciones) y
 * añade los botones de selección rápida: días laborables (lunes a viernes),
 * todos y ninguno.
 *
 * @author Jose Antonio Cuello Principal <yopli2000@gmail.com>
 */
class WidgetWeekdaysfull extends WidgetWeekdays
{
    /**
     * Añade los recursos del widget base más el JS específico de los presets.
     */
    protected function assets(): void
    {
        parent::assets();
        $route = Tools::config('route');
        AssetManager::addJs($route . '/Dinamic/Assets/JS/WidgetWeekdaysfull.js');
    }

    /**
     * Botón de selección rápida (preset).
     *
     * @param string $preset
     * @param string $label
     * @return string
     */
    protected function presetButton(string $preset, string $label): string
    {
        return '<button type="button" class="btn btn-outline-secondary fs-weekdays-preset"'
            . ' data-preset="' . $preset . '">' . $label . '</button>';
    }

    /**
     * HTML de los botones de selección rápida: laborables, todos y ninguno.
     *
     * @return string
     */
    protected function presetsHtml(): string
    {
        if ($this->readonly()) {
            return '';
        }

        return '<div class="btn-group ms-md-2 mt-2 mt-md-0 fs-weekdays-presets" role="group">'
            . $this->presetButton('workweek', Tools::trans('workweek'))
            . $this->presetButton('all', Tools::trans('all'))
            . $this->presetButton('none', Tools::trans('none'))
            . '</div>';
    }
}
