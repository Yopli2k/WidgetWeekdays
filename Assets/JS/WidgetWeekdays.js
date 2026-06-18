/**
 * This file is part of WidgetWeekdays plugin for FacturaScripts.
 * Copyright (C) 2026 Jose Antonio Cuello Principal <yopli2000@gmail.com>
 *
 * Vanilla JS handler for the base "weekdays" widget.
 * Each widget keeps a hidden input with a 7-char string (1/0) in order
 * monday -> sunday. Toggling a day checkbox (.fs-weekdays-day) just rebuilds
 * that hidden value so a single field is submitted.
 *
 * The preset buttons (workweek, all, none) are handled by WidgetWeekdaysfull.js,
 * which reuses fsWeekdaysUpdateHidden defined here.
 */
'use strict';

function fsWeekdaysUpdateHidden(container) {
    if (!container) {
        return;
    }
    const hidden = container.querySelector('input[type="hidden"]');
    const days = container.querySelectorAll('.fs-weekdays-day');
    let value = '';
    days.forEach(function (day) {
        value += day.checked ? '1' : '0';
    });
    if (hidden) {
        hidden.value = value;
    }
}

document.addEventListener('change', function (event) {
    if (event.target.classList.contains('fs-weekdays-day')) {
        fsWeekdaysUpdateHidden(event.target.closest('.fs-weekdays'));
    }
});
