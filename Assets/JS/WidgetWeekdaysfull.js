/**
 * This file is part of WidgetWeekdays plugin for FacturaScripts.
 * Copyright (C) 2026 Jose Antonio Cuello Principal <yopli2000@gmail.com>
 *
 * Vanilla JS handler for the preset buttons of the "weekdaysfull" widget.
 * Depends on fsWeekdaysUpdateHidden from WidgetWeekdays.js (always loaded
 * first by WidgetWeekdaysfull::assets()).
 */
'use strict';

// presets en orden lunes -> domingo (índice 0..6)
const fsWeekdaysPresets = {
    workweek: [1, 1, 1, 1, 1, 0, 0],
    all: [1, 1, 1, 1, 1, 1, 1],
    none: [0, 0, 0, 0, 0, 0, 0]
};

function fsWeekdaysApplyPreset(container, preset) {
    const pattern = fsWeekdaysPresets[preset];
    if (!container || !pattern) {
        return;
    }
    const days = container.querySelectorAll('.fs-weekdays-day');
    days.forEach(function (day, index) {
        day.checked = pattern[index] === 1;
    });
    fsWeekdaysUpdateHidden(container);
}

document.addEventListener('click', function (event) {
    const button = event.target.closest('.fs-weekdays-preset');
    if (button) {
        event.preventDefault();
        fsWeekdaysApplyPreset(button.closest('.fs-weekdays'), button.dataset.preset);
    }
});
