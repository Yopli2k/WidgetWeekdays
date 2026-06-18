/**
 * This file is part of WidgetWeekdays plugin for FacturaScripts.
 * Copyright (C) 2026 Jose Antonio Cuello Principal <yopli2000@gmail.com>
 *
 * Vanilla JS handler for the "weekdays" widget.
 * Each widget keeps a hidden input with a 7-char string (1/0) in order
 * monday -> sunday. The day checkboxes (.btn-check) and the preset buttons
 * just rebuild that hidden value so a single field is submitted.
 */
'use strict';

// presets en orden lunes -> domingo (índice 0..6)
const fsWeekdaysPresets = {
    workweek: [1, 1, 1, 1, 1, 0, 0],
    weekend: [0, 0, 0, 0, 0, 1, 1],
    all: [1, 1, 1, 1, 1, 1, 1],
    none: [0, 0, 0, 0, 0, 0, 0]
};

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

document.addEventListener('change', function (event) {
    if (event.target.classList.contains('fs-weekdays-day')) {
        fsWeekdaysUpdateHidden(event.target.closest('.fs-weekdays'));
    }
});

document.addEventListener('click', function (event) {
    const button = event.target.closest('.fs-weekdays-preset');
    if (button) {
        event.preventDefault();
        fsWeekdaysApplyPreset(button.closest('.fs-weekdays'), button.dataset.preset);
    }
});
