const monthNames = [
    'فروردین',
    'اردیبهشت',
    'خرداد',
    'تیر',
    'مرداد',
    'شهریور',
    'مهر',
    'آبان',
    'آذر',
    'دی',
    'بهمن',
    'اسفند',
];

const weekDays = ['ش', 'ی', 'د', 'س', 'چ', 'پ', 'ج'];
let activePicker = null;

function normalizeDigits(value = '') {
    return String(value)
        .replace(/[۰-۹]/g, (digit) => '۰۱۲۳۴۵۶۷۸۹'.indexOf(digit))
        .replace(/[٠-٩]/g, (digit) => '٠١٢٣٤٥٦٧٨٩'.indexOf(digit));
}

function formatMoneyValue(value = '') {
    const digits = normalizeDigits(value).replace(/[^\d-]+/g, '');

    if (digits === '' || digits === '-') {
        return '';
    }

    return Number(digits).toLocaleString('en-US');
}

function notifyInputChanged(input) {
    if (!input) {
        return;
    }

    input.dispatchEvent(new Event('input', { bubbles: true }));
    input.dispatchEvent(new Event('change', { bubbles: true }));
}

function bindMoneyInputs() {
    document.querySelectorAll('[data-money-input]').forEach((input) => {
        const hidden = document.getElementById(input.dataset.targetInput);

        if (!hidden || input.dataset.bound === 'true') {
            return;
        }

        const sync = () => {
            const raw = normalizeDigits(input.value).replace(/[^\d-]+/g, '');
            hidden.value = raw;
            input.value = formatMoneyValue(raw);
            notifyInputChanged(hidden);
        };

        input.addEventListener('input', sync);
        input.addEventListener('blur', sync);
        input.dataset.bound = 'true';
        sync();
    });
}

function div(a, b) {
    return ~~(a / b);
}

function mod(a, b) {
    return a - ~~(a / b) * b;
}

function jalCal(jy, withoutLeap) {
    const breaks = [-61, 9, 38, 199, 426, 686, 756, 818, 1111, 1181, 1210, 1635, 2060, 2097, 2192, 2262, 2324, 2394, 2456, 3178];
    const bl = breaks.length;
    const gy = jy + 621;
    let leapJ = -14;
    let jp = breaks[0];
    let jm;
    let jump;
    let leap;
    let leapG;
    let march;
    let n;
    let i;

    if (jy < jp || jy >= breaks[bl - 1]) {
        throw new Error(`Invalid Jalaali year ${jy}`);
    }

    for (i = 1; i < bl; i += 1) {
        jm = breaks[i];
        jump = jm - jp;

        if (jy < jm) {
            break;
        }

        leapJ += div(jump, 33) * 8 + div(mod(jump, 33), 4);
        jp = jm;
    }

    n = jy - jp;
    leapJ += div(n, 33) * 8 + div(mod(n, 33) + 3, 4);

    if (mod(jump, 33) === 4 && jump - n === 4) {
        leapJ += 1;
    }

    leapG = div(gy, 4) - div((div(gy, 100) + 1) * 3, 4) - 150;
    march = 20 + leapJ - leapG;

    if (withoutLeap) {
        return { gy, march };
    }

    if (jump - n < 6) {
        n = n - jump + div(jump + 4, 33) * 33;
    }

    leap = mod(mod(n + 1, 33) - 1, 4);
    if (leap === -1) {
        leap = 4;
    }

    return { leap, gy, march };
}

function g2d(gy, gm, gd) {
    let d =
        div((gy + div(gm - 8, 6) + 100100) * 1461, 4) +
        div(153 * mod(gm + 9, 12) + 2, 5) +
        gd -
        34840408;

    d = d - div(div(gy + 100100 + div(gm - 8, 6), 100) * 3, 4) + 752;

    return d;
}

function d2g(jdn) {
    let j = 4 * jdn + 139361631;

    j = j + div(div(4 * jdn + 183187720, 146097) * 3, 4) * 4 - 3908;

    const i = div(mod(j, 1461), 4) * 5 + 308;
    const gd = div(mod(i, 153), 5) + 1;
    const gm = mod(div(i, 153), 12) + 1;
    const gy = div(j, 1461) - 100100 + div(8 - gm, 6);

    return { gy, gm, gd };
}

function j2d(jy, jm, jd) {
    const r = jalCal(jy, true);

    return g2d(r.gy, 3, r.march) + (jm <= 7 ? (jm - 1) * 31 : (jm - 7) * 30 + 186) + (jd - 1);
}

function d2j(jdn) {
    const g = d2g(jdn);
    let jy = g.gy - 621;
    const r = jalCal(jy, false);
    const jdn1f = g2d(g.gy, 3, r.march);
    let jd;
    let jm;
    let k = jdn - jdn1f;

    if (k >= 0) {
        if (k <= 185) {
            jm = 1 + div(k, 31);
            jd = mod(k, 31) + 1;
            return { jy, jm, jd };
        }

        k -= 186;
    } else {
        jy -= 1;
        k += 179;

        if (r.leap === 1) {
            k += 1;
        }
    }

    jm = 7 + div(k, 30);
    jd = mod(k, 30) + 1;

    return { jy, jm, jd };
}

function toJalaali(gy, gm, gd) {
    return d2j(g2d(gy, gm, gd));
}

function toGregorian(jy, jm, jd) {
    return d2g(j2d(jy, jm, jd));
}

function isLeapJalaaliYear(jy) {
    return jalCal(jy, false).leap === 0;
}

function jalaaliMonthLength(jy, jm) {
    if (jm <= 6) {
        return 31;
    }

    if (jm <= 11) {
        return 30;
    }

    return isLeapJalaaliYear(jy) ? 30 : 29;
}

function gregorianStringToJalaali(value) {
    if (!value) {
        return null;
    }

    const normalized = normalizeDigits(value).replace('T', ' ');
    const [datePart, timePart = ''] = normalized.split(' ');
    const [gy, gm, gd] = datePart.split('-').map(Number);

    if (!gy || !gm || !gd) {
        return null;
    }

    const jDate = toJalaali(gy, gm, gd);
    const date = `${jDate.jy}/${String(jDate.jm).padStart(2, '0')}/${String(jDate.jd).padStart(2, '0')}`;

    return timePart ? `${date} ${timePart.slice(0, 5)}` : date;
}

function jalaaliStringToGregorian(value, type = 'date') {
    const normalized = normalizeDigits(value).trim().replace(/-/g, '/');
    const [datePart, timePart = '00:00'] = normalized.split(' ');
    const match = datePart.match(/^(\d{4})\/(\d{1,2})\/(\d{1,2})$/);

    if (!match) {
        return null;
    }

    const [, jy, jm, jd] = match.map(Number);

    if (jm < 1 || jm > 12 || jd < 1 || jd > jalaaliMonthLength(jy, jm)) {
        return null;
    }

    const gDate = toGregorian(jy, jm, jd);
    const date = `${gDate.gy}-${String(gDate.gm).padStart(2, '0')}-${String(gDate.gd).padStart(2, '0')}`;

    if (type !== 'datetime-local') {
        return date;
    }

    const [hour = '00', minute = '00'] = timePart.split(':');

    return `${date}T${String(hour).padStart(2, '0')}:${String(minute).padStart(2, '0')}`;
}

function extractDateState(input) {
    const hidden = document.getElementById(input.dataset.targetInput);
    const type = input.dataset.dateType || 'date';
    const displayValue = input.value.trim();
    const hiddenValue = hidden?.value?.trim() || '';
    const current = displayValue || gregorianStringToJalaali(hiddenValue) || gregorianStringToJalaali(new Date().toISOString().slice(0, 16));
    const normalized = normalizeDigits(current);
    const match = normalized.match(/^(\d{4})\/(\d{1,2})\/(\d{1,2})(?:\s+(\d{1,2}):(\d{1,2}))?$/);

    if (match) {
        return {
            type,
            hidden,
            jy: Number(match[1]),
            jm: Number(match[2]),
            jd: Number(match[3]),
            time: match[4] ? `${String(match[4]).padStart(2, '0')}:${String(match[5] || '00').padStart(2, '0')}` : '00:00',
        };
    }

    const now = new Date();
    const today = toJalaali(now.getFullYear(), now.getMonth() + 1, now.getDate());

    return {
        type,
        hidden,
        jy: today.jy,
        jm: today.jm,
        jd: today.jd,
        time: `${String(now.getHours()).padStart(2, '0')}:${String(now.getMinutes()).padStart(2, '0')}`,
    };
}

function isBeforeMin(input, gregorianValue) {
    const sourceName = input.dataset.minSource;

    if (!sourceName) {
        return false;
    }

    const minTarget = document.getElementById(sourceName);
    const minValue = minTarget?.value;

    if (!minValue || !gregorianValue) {
        return false;
    }

    return new Date(gregorianValue) < new Date(minValue);
}

function updateJalaliValue(input, state) {
    const gregorianValue = jalaaliStringToGregorian(
        `${state.jy}/${String(state.jm).padStart(2, '0')}/${String(state.jd).padStart(2, '0')}${state.type === 'datetime-local' ? ` ${state.time}` : ''}`,
        state.type,
    );

    if (!gregorianValue || isBeforeMin(input, gregorianValue)) {
        input.setCustomValidity('تاریخ انتخاب شده معتبر نیست.');
        return;
    }

    input.setCustomValidity('');
    input.value = state.type === 'datetime-local'
        ? `${state.jy}/${String(state.jm).padStart(2, '0')}/${String(state.jd).padStart(2, '0')} ${state.time}`
        : `${state.jy}/${String(state.jm).padStart(2, '0')}/${String(state.jd).padStart(2, '0')}`;

    if (state.hidden) {
        state.hidden.value = gregorianValue;
        notifyInputChanged(state.hidden);
    }
}

function closePicker() {
    if (activePicker) {
        activePicker.remove();
        activePicker = null;
    }
}

function renderPicker(input) {
    closePicker();

    const state = extractDateState(input);
    let viewYear = state.jy;
    let viewMonth = state.jm;
    const picker = document.createElement('div');
    picker.className = 'admin-jalali-picker';

    function positionPicker() {
        const rect = input.getBoundingClientRect();
        picker.style.top = `${window.scrollY + rect.bottom + 8}px`;
        picker.style.left = `${window.scrollX + rect.left}px`;
    }

    function renderCalendar() {
        const monthLength = jalaaliMonthLength(viewYear, viewMonth);
        const firstGregorian = toGregorian(viewYear, viewMonth, 1);
        const firstWeekday = (new Date(firstGregorian.gy, firstGregorian.gm - 1, firstGregorian.gd).getDay() + 1) % 7;
        const days = [];

        for (let i = 0; i < firstWeekday; i += 1) {
            days.push('<button type="button" class="admin-jalali-picker__day is-empty" disabled></button>');
        }

        for (let day = 1; day <= monthLength; day += 1) {
            const isSelected = state.jy === viewYear && state.jm === viewMonth && state.jd === day;
            days.push(`
                <button type="button" class="admin-jalali-picker__day ${isSelected ? 'is-selected' : ''}" data-day="${day}">
                    ${day}
                </button>
            `);
        }

        picker.innerHTML = `
            <div class="admin-jalali-picker__header">
                <button type="button" class="admin-jalali-picker__nav" data-nav="next">‹</button>
                <strong>${monthNames[viewMonth - 1]} ${viewYear}</strong>
                <button type="button" class="admin-jalali-picker__nav" data-nav="prev">›</button>
            </div>
            <div class="admin-jalali-picker__weekdays">
                ${weekDays.map((day) => `<span>${day}</span>`).join('')}
            </div>
            <div class="admin-jalali-picker__days">
                ${days.join('')}
            </div>
            ${state.type === 'datetime-local' ? `
                <div class="admin-jalali-picker__time">
                    <label>ساعت</label>
                    <input type="time" value="${state.time}" data-time>
                </div>
            ` : ''}
        `;

        picker.querySelectorAll('[data-day]').forEach((dayButton) => {
            dayButton.addEventListener('click', () => {
                state.jy = viewYear;
                state.jm = viewMonth;
                state.jd = Number(dayButton.dataset.day);

                const timeInput = picker.querySelector('[data-time]');
                if (timeInput) {
                    state.time = timeInput.value || '00:00';
                }

                updateJalaliValue(input, state);
                closePicker();
            });
        });

        picker.querySelectorAll('[data-nav]').forEach((navButton) => {
            navButton.addEventListener('click', () => {
                if (navButton.dataset.nav === 'prev') {
                    viewMonth -= 1;
                    if (viewMonth < 1) {
                        viewMonth = 12;
                        viewYear -= 1;
                    }
                } else {
                    viewMonth += 1;
                    if (viewMonth > 12) {
                        viewMonth = 1;
                        viewYear += 1;
                    }
                }

                renderCalendar();
            });
        });

        const timeInput = picker.querySelector('[data-time]');
        if (timeInput) {
            timeInput.addEventListener('input', () => {
                state.time = timeInput.value || '00:00';
            });
        }
    }

    document.body.appendChild(picker);
    positionPicker();
    renderCalendar();
    activePicker = picker;
}

function bindJalaliInputs() {
    document.querySelectorAll('[data-jalali-input]').forEach((input) => {
        if (input.dataset.bound === 'true') {
            return;
        }

        input.addEventListener('focus', () => renderPicker(input));
        input.addEventListener('click', () => renderPicker(input));
        input.addEventListener('blur', () => {
            const parsed = jalaaliStringToGregorian(input.value, input.dataset.dateType || 'date');
            const hidden = document.getElementById(input.dataset.targetInput);

            if (!input.value.trim()) {
                input.setCustomValidity('');
                if (hidden) {
                    hidden.value = '';
                    notifyInputChanged(hidden);
                }
                return;
            }

            if (!parsed || isBeforeMin(input, parsed)) {
                input.setCustomValidity('تاریخ وارد شده معتبر نیست.');
                return;
            }

            input.setCustomValidity('');
            if (hidden) {
                hidden.value = parsed;
                notifyInputChanged(hidden);
            }
        });

        const hidden = document.getElementById(input.dataset.targetInput);
        if (hidden?.value && !input.value) {
            input.value = gregorianStringToJalaali(hidden.value) || '';
        }

        input.dataset.bound = 'true';
    });
}

function setupGlobalPickerEvents() {
    document.addEventListener('click', (event) => {
        if (activePicker && !activePicker.contains(event.target) && !event.target.closest('[data-jalali-input]')) {
            closePicker();
        }
    });

    window.addEventListener('resize', closePicker);
    window.addEventListener('scroll', closePicker, true);
}

export function initAdminFormTools() {
    bindMoneyInputs();
    bindJalaliInputs();
    setupGlobalPickerEvents();
}

document.addEventListener('DOMContentLoaded', initAdminFormTools);
