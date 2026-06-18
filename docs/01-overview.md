# Presentación de WidgetWeekdays

**WidgetWeekdays** añade a FacturaScripts dos tipos de widget, `weekdays` y `weekdaysfull`, para seleccionar los días de la semana en cualquier combinación y guardarlos en un único campo `varchar(7)`.

Está pensado para cualquier funcionalidad que dependa de "en qué días ocurre algo": tareas recurrentes, horarios de apertura, días de reparto, turnos, recordatorios, etc. En lugar de crear siete campos booleanos (uno por día), guardas toda la selección en una sola columna y manipulas el valor con una librería de utilidades incluida en el plugin.

## Qué aporta

- Dos **widgets visuales** con un botón por día: `type="weekdays"` (base, solo los días) y `type="weekdaysfull"` (hereda del base y añade botones de selección rápida: laborables, todos y ninguno).
- Una **clase de utilidades** (`Weekdays`) con métodos estáticos para leer y manipular el valor desde tu código PHP sin pelearte con la cadena de caracteres.
- Un **filtro para listados** (`WeekdaysFilter`) para filtrar un `ListView` por los días seleccionados.

## Cómo se guardan los datos

El valor se almacena en un campo `varchar(7)`: una cadena de exactamente 7 caracteres, donde cada posición es un día de la semana y vale `1` (día seleccionado) o `0` (no seleccionado).

El orden de las posiciones sigue el estándar **ISO 8601**, el mismo que usan `date('N')` de PHP y `DateTimeTools::dayOfWeek()` del propio FacturaScripts:

| Índice | 0 | 1 | 2 | 3 | 4 | 5 | 6 |
|--------|---|---|---|---|---|---|---|
| Día    | Lunes | Martes | Miércoles | Jueves | Viernes | Sábado | Domingo |

Ejemplos de valores:

| Valor       | Significado            |
|-------------|------------------------|
| `1111100`   | De lunes a viernes     |
| `0000011`   | Fin de semana          |
| `1111111`   | Todos los días         |
| `1001000`   | Lunes y jueves         |
| `0000000`   | Ningún día             |
| `null` o `""` | Ningún día (equivale a `0000000`) |

Un valor nulo, vacío o con una longitud distinta de 7 se interpreta siempre como "ningún día". La librería del plugin se encarga de normalizar estos casos por ti, de modo que nunca tendrás que comprobar la longitud de la cadena a mano.

> Para obtener el índice de una fecha cualquiera basta con `date('N', $timestamp) - 1` (lunes = 0 … domingo = 6). La clase `Weekdays` ya hace este cálculo internamente en sus métodos basados en fechas.

## Compatibilidad

- FacturaScripts 2026 o superior.
- PHP 8.0 o superior.
