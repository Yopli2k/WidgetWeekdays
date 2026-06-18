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
namespace FacturaScripts\Plugins\WidgetWeekdays\Lib\ListFilter;

use FacturaScripts\Core\Base\DataBase\DataBaseWhere;
use FacturaScripts\Core\Lib\ListFilter\BaseFilter;
use FacturaScripts\Core\Tools;
use FacturaScripts\Dinamic\Lib\AssetManager;
use FacturaScripts\Dinamic\Lib\Weekdays;

/**
 * Filtro para listados sobre un campo varchar(7) gestionado por el widget "weekdays".
 *
 * Muestra los 7 días de la semana como botones y filtra los registros que tienen
 * activos TODOS los días marcados (combinación AND). El valor del filtro es una
 * cadena de 7 caracteres de 1 y 0 en orden lunes→domingo, igual que el campo.
 *
 * Uso (normalmente desde una extensión del ListController):
 *   $view->filters['weekdays'] = new WeekdaysFilter('weekdays', 'weekdays', 'weekdays');
 *
 * @author Jose Antonio Cuello Principal <yopli2000@gmail.com>
 */
class WeekdaysFilter extends BaseFilter
{
    /**
     * Claves de traducción de las abreviaturas de los días, en orden lunes→domingo.
     *
     * @var string[]
     */
    protected static $abbr = [
        'weekday-mon-abbr',
        'weekday-tue-abbr',
        'weekday-wed-abbr',
        'weekday-thu-abbr',
        'weekday-fri-abbr',
        'weekday-sat-abbr',
        'weekday-sun-abbr',
    ];

    /**
     * Añade una condición al where por cada día seleccionado.
     * Para comprobar que el carácter de la posición del día vale '1' se usa un
     * patrón LIKE con guiones bajos (un carácter cualquiera por posición), p.ej.
     * el jueves (índice 3) genera el patrón '___1%'. El '%' final es necesario
     * para que DataBaseWhere use el patrón tal cual y no lo envuelva en '%...%'.
     *
     * @param array $where
     * @return bool
     */
    public function getDataBaseWhere(array &$where): bool
    {
        if (Weekdays::isEmpty($this->value)) {
            return false;
        }

        foreach (Weekdays::selectedIndexes($this->value) as $index) {
            $pattern = str_repeat('_', $index) . '1%';
            $where[] = new DataBaseWhere($this->field, $pattern, 'LIKE');
        }

        return true;
    }

    /**
     * @return string
     */
    public function render(): string
    {
        $selected = Weekdays::toArray($this->value);

        $daysHtml = '';
        foreach (static::$abbr as $i => $key) {
            $id = 'filter_' . $this->name() . '_' . $i;
            $checked = $selected[$i] ? ' checked=""' : '';
            $daysHtml .= '<input type="checkbox" class="btn-check fs-weekdays-day" id="' . $id . '"'
                . ' autocomplete="off"' . $checked . $this->readonly() . '/>'
                . '<label class="btn btn-outline-primary" for="' . $id . '">' . Tools::trans($key) . '</label>';
        }

        return '<div class="col-sm-auto">'
            . '<div class="mb-3">'
            . '<div class="small mb-1">' . Tools::trans($this->label) . '</div>'
            . '<div class="fs-weekdays" data-field="' . $this->field . '">'
            . '<input type="hidden" name="' . $this->name() . '" value="' . implode('', $selected) . '"/>'
            . '<div class="btn-group btn-group-sm flex-wrap fs-weekdays-days" role="group">' . $daysHtml . '</div>'
            . '</div>'
            . '</div>'
            . '</div>';
    }

    /**
     * Carga los recursos del widget (mismo JS/CSS) para que los botones funcionen
     * también en la zona de filtros del listado.
     */
    protected function assets(): void
    {
        $route = Tools::config('route');
        AssetManager::addCss($route . '/Dinamic/Assets/CSS/WidgetWeekdays.css');
        AssetManager::addJs($route . '/Dinamic/Assets/JS/WidgetWeekdays.js');
    }
}
