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
namespace FacturaScripts\Plugins\WidgetWeekdays\Lib;

/**
 * Utilidades para trabajar con el campo varchar(7) del widget "weekdays".
 *
 * El valor es una cadena de 7 caracteres de 1 (día seleccionado) y 0 (no seleccionado).
 * El orden sigue el estándar ISO 8601 (el mismo de date('N') y DateTimeTools::dayOfWeek):
 *   índice 0 = Lunes (ISO 1) ... índice 6 = Domingo (ISO 7).
 *
 * Un valor nulo, vacío o con longitud distinta de 7 se normaliza a '0000000'.
 *
 * @author Jose Antonio Cuello Principal <yopli2000@gmail.com>
 */
class Weekdays
{
    /** @var int Índice del viernes. */
    const FRIDAY = 4;

    /** @var int Número de días de la semana (longitud de la cadena). */
    const LENGTH = 7;

    /** @var int Índice del lunes. */
    const MONDAY = 0;

    /** @var int Índice del sábado. */
    const SATURDAY = 5;

    /** @var int Índice del domingo. */
    const SUNDAY = 6;

    /** @var int Índice del jueves. */
    const THURSDAY = 3;

    /** @var int Índice del martes. */
    const TUESDAY = 1;

    /** @var int Índice del miércoles. */
    const WEDNESDAY = 2;

    /**
     * Cadena con todos los días seleccionados.
     *
     * @return string
     */
    public static function all(): string
    {
        return str_repeat('1', self::LENGTH);
    }

    /**
     * Número de días seleccionados.
     *
     * @param mixed $value
     * @return int
     */
    public static function count($value): int
    {
        return substr_count(self::normalize($value), '1');
    }

    /**
     * Construye la cadena a partir de una lista de índices (base 0, lunes=0).
     *
     * @param int[] $indexes
     * @return string
     */
    public static function fromIndexes(array $indexes): string
    {
        $chars = array_fill(0, self::LENGTH, '0');
        foreach ($indexes as $index) {
            if ($index >= 0 && $index < self::LENGTH) {
                $chars[$index] = '1';
            }
        }

        return implode('', $chars);
    }

    /**
     * Construye la cadena a partir de una lista de días ISO 8601 (lunes=1 ... domingo=7).
     *
     * @param int[] $isoDays
     * @return string
     */
    public static function fromIsoDays(array $isoDays): string
    {
        return self::fromIndexes(array_map(static function ($iso) {
            return (int)$iso - 1;
        }, $isoDays));
    }

    /**
     * Indica si no hay ningún día seleccionado (null, vacío o '0000000').
     *
     * @param mixed $value
     * @return bool
     */
    public static function isEmpty($value): bool
    {
        return false === strpos(self::normalize($value), '1');
    }

    /**
     * Indica si el día indicado (índice base 0, lunes=0) está seleccionado.
     *
     * @param mixed $value
     * @param int $index
     * @return bool
     */
    public static function isSelected($value, int $index): bool
    {
        if ($index < 0 || $index >= self::LENGTH) {
            return false;
        }

        return self::normalize($value)[$index] === '1';
    }

    /**
     * Indica si el día de la semana de una fecha está seleccionado.
     * Si no se indica fecha, usa el día actual.
     *
     * @param mixed $value
     * @param string $date Fecha en cualquier formato reconocido por strtotime.
     * @return bool
     */
    public static function isSelectedOnDate($value, string $date = ''): bool
    {
        $timestamp = empty($date) ? time() : strtotime($date);
        if (false === $timestamp) {
            return false;
        }

        // date('N') devuelve 1 (lunes) ... 7 (domingo); el índice es ese valor menos 1.
        $index = (int)date('N', $timestamp) - 1;
        return self::isSelected($value, $index);
    }

    /**
     * Cadena sin ningún día seleccionado.
     *
     * @return string
     */
    public static function none(): string
    {
        return str_repeat('0', self::LENGTH);
    }

    /**
     * Normaliza cualquier valor a una cadena de exactamente 7 caracteres de 1 y 0.
     * Rellena con 0 si es más corta, recorta si es más larga y trata cualquier
     * carácter distinto de '1' como '0'.
     *
     * @param mixed $value
     * @return string
     */
    public static function normalize($value): string
    {
        $value = (string)$value;
        $out = '';
        for ($i = 0; $i < self::LENGTH; $i++) {
            $out .= (isset($value[$i]) && $value[$i] === '1') ? '1' : '0';
        }

        return $out;
    }

    /**
     * Como normalize(), pero devuelve null cuando no hay ningún día seleccionado.
     * Útil para guardar el campo en columnas nullable manteniendo la BD limpia.
     *
     * @param mixed $value
     * @return string|null
     */
    public static function normalizeOrNull($value): ?string
    {
        return self::isEmpty($value) ? null : self::normalize($value);
    }

    /**
     * Lista de índices (base 0, lunes=0) de los días seleccionados.
     *
     * @param mixed $value
     * @return int[]
     */
    public static function selectedIndexes($value): array
    {
        $result = [];
        $string = self::normalize($value);
        for ($i = 0; $i < self::LENGTH; $i++) {
            if ($string[$i] === '1') {
                $result[] = $i;
            }
        }

        return $result;
    }

    /**
     * Lista de días ISO 8601 (lunes=1 ... domingo=7) seleccionados.
     *
     * @param mixed $value
     * @return int[]
     */
    public static function selectedIsoDays($value): array
    {
        return array_map(static function ($index) {
            return $index + 1;
        }, self::selectedIndexes($value));
    }

    /**
     * Devuelve una nueva cadena con el día indicado activado o desactivado.
     *
     * @param mixed $value
     * @param int $index Índice base 0 (lunes=0).
     * @param bool $selected
     * @return string
     */
    public static function setDay($value, int $index, bool $selected): string
    {
        $string = self::normalize($value);
        if ($index >= 0 && $index < self::LENGTH) {
            $string[$index] = $selected ? '1' : '0';
        }

        return $string;
    }

    /**
     * Convierte el valor en un array de 7 enteros (0/1) indexado de lunes (0) a domingo (6).
     *
     * @param mixed $value
     * @return int[]
     */
    public static function toArray($value): array
    {
        $string = self::normalize($value);
        $result = [];
        for ($i = 0; $i < self::LENGTH; $i++) {
            $result[] = $string[$i] === '1' ? 1 : 0;
        }

        return $result;
    }

    /**
     * Cadena con el fin de semana seleccionado (sábado y domingo).
     *
     * @return string
     */
    public static function weekend(): string
    {
        return self::fromIndexes([self::SATURDAY, self::SUNDAY]);
    }

    /**
     * Cadena con los días laborables seleccionados (lunes a viernes).
     *
     * @return string
     */
    public static function workweek(): string
    {
        return self::fromIndexes([self::MONDAY, self::TUESDAY, self::WEDNESDAY, self::THURSDAY, self::FRIDAY]);
    }
}
