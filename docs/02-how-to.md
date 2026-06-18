# Usar WidgetWeekdays en tu plugin

Esta guía explica, paso a paso, cómo incorporar el widget de días de la semana a tu propio plugin: declarar la dependencia, crear el campo, mostrarlo en tus vistas y trabajar con el valor desde PHP.

## 1. Declarar la dependencia

WidgetWeekdays es un plugin independiente. Para que el widget y sus clases estén disponibles, añade la dependencia en el `facturascripts.ini` de tu plugin:

```ini
name = 'MiPlugin'
version = 1.0
...
require = 'WidgetWeekdays'
```

De este modo FacturaScripts garantiza que WidgetWeekdays esté instalado y activo antes que el tuyo, y podrás usar las clases del plugin sin comprobaciones defensivas.

## 2. Crear el campo en la tabla

Añade una columna `varchar(7)` a la tabla de tu modelo, en el XML de `Table/`. La columna puede ser **nullable**: un valor nulo se interpreta como "ningún día seleccionado".

```xml
<?xml version="1.0" encoding="UTF-8"?>
<table>
    <column>
        <name>weekdays</name>
        <type>character varying(7)</type>
    </column>
    <!-- resto de columnas... -->
</table>
```

> Puedes nombrar la columna como quieras (`weekdays`, `dias`, `deliverydays`...). En los ejemplos usamos `weekdays`.

## 3. Usar el widget en una vista XML

En el `XMLView/` de tu controlador, define la columna usando `type="weekdays"` y apuntando con `fieldname` al campo de la tabla:

```xml
<column name="weekdays" numcolumns="12" order="100">
    <widget type="weekdays" fieldname="weekdays"/>
</column>
```

El widget se encarga de todo:

- **En modo edición** muestra un botón por día (Lun, Mar, Mié…) más botones de selección rápida: **laborables**, **fin de semana**, **todos** y **ninguno**.
- **En modo lectura / listados** muestra una fila de etiquetas (L M X J V S D) resaltando los días activos.
- **En exportaciones y PDF** muestra los nombres de los días seleccionados separados por comas.

Toda la interacción del lado del cliente es JavaScript *vanilla* y viaja en un único campo oculto con la cadena de 7 caracteres, así que no necesitas añadir scripts ni estilos: el widget carga sus propios *assets*.

## 4. Normalizar el valor al guardar (recomendado)

El widget ya normaliza el valor que llega del formulario, pero si tu modelo se puede rellenar por otras vías (importaciones, API, código propio) conviene normalizar también en el método `test()` del modelo. Así te aseguras de que la columna siempre contiene una cadena válida de 7 caracteres, o `null` si no hay ningún día.

```php
use FacturaScripts\Dinamic\Lib\Weekdays;

public function test(): bool
{
    // deja la columna con 7 caracteres exactos, o null si no hay días seleccionados.
    $this->weekdays = Weekdays::normalizeOrNull($this->weekdays);

    return parent::test();
}
```

> Importa siempre las clases del plugin desde el espacio de nombres `Dinamic` (`FacturaScripts\Dinamic\Lib\Weekdays`), no desde `Plugins\WidgetWeekdays\...`. Así respetas el sistema de regeneración de clases de FacturaScripts y permites que otros plugins extiendan la clase.

## 5. Trabajar con el valor desde PHP

La clase `Weekdays` (`FacturaScripts\Dinamic\Lib\Weekdays`) reúne todas las utilidades para leer y manipular el campo sin tocar la cadena directamente. Todos los métodos son estáticos y aceptan valores nulos, vacíos o mal formados (los normalizan internamente).

### Consultar

```php
use FacturaScripts\Dinamic\Lib\Weekdays;

// ¿Hay algún día seleccionado?
if (Weekdays::isEmpty($model->weekdays)) {
    // ningún día
}

// ¿Está seleccionado un día concreto? (índice base 0: lunes=0 ... domingo=6)
$esLunes = Weekdays::isSelected($model->weekdays, Weekdays::MONDAY);

// ¿El día de la semana de una fecha está seleccionado?
$hoyToca = Weekdays::isSelectedOnDate($model->weekdays);              // hoy
$tocaEseDia = Weekdays::isSelectedOnDate($model->weekdays, '2026-06-18');

// ¿Cuántos días hay seleccionados?
$total = Weekdays::count($model->weekdays);
```

### Obtener listas

```php
// Índices base 0 de los días seleccionados: [0, 3] para lunes y jueves.
$indices = Weekdays::selectedIndexes($model->weekdays);

// Días en formato ISO 8601 (lunes=1 ... domingo=7): [1, 4].
$isoDays = Weekdays::selectedIsoDays($model->weekdays);

// Array de 7 enteros 0/1 indexado de lunes (0) a domingo (6): [1,0,0,1,0,0,0].
$array = Weekdays::toArray($model->weekdays);
```

### Construir y modificar valores

```php
// Desde índices base 0 (lunes=0).
$model->weekdays = Weekdays::fromIndexes([Weekdays::MONDAY, Weekdays::THURSDAY]); // '1001000'

// Desde días ISO 8601 (lunes=1 ... domingo=7).
$model->weekdays = Weekdays::fromIsoDays([1, 4]); // '1001000'

// Activar o desactivar un día puntual (devuelve una nueva cadena).
$model->weekdays = Weekdays::setDay($model->weekdays, Weekdays::SUNDAY, true);

// Presets habituales.
$model->weekdays = Weekdays::workweek(); // '1111100' (lunes a viernes)
$model->weekdays = Weekdays::weekend();  // '0000011' (sábado y domingo)
$model->weekdays = Weekdays::all();      // '1111111'
$model->weekdays = Weekdays::none();     // '0000000'

// Normalizar manualmente.
$valor = Weekdays::normalize($entrada);       // siempre 7 caracteres
$valor = Weekdays::normalizeOrNull($entrada); // 7 caracteres o null si no hay días
```

### Constantes de índice

Para no usar números mágicos, la clase expone una constante por día (índice base 0):

```php
Weekdays::MONDAY    // 0
Weekdays::TUESDAY   // 1
Weekdays::WEDNESDAY // 2
Weekdays::THURSDAY  // 3
Weekdays::FRIDAY    // 4
Weekdays::SATURDAY  // 5
Weekdays::SUNDAY    // 6
Weekdays::LENGTH    // 7 (longitud de la cadena)
```

## Ejemplo completo

Supón un modelo `Reparto` con un campo `weekdays` que indica los días en los que se realiza una entrega, y quieres saber si una entrega corresponde a una fecha concreta:

```php
use FacturaScripts\Dinamic\Lib\Weekdays;

$reparto = new Reparto();
$reparto->load($id);

// Marca lunes, miércoles y viernes como días de reparto.
$reparto->weekdays = Weekdays::fromIndexes([
    Weekdays::MONDAY,
    Weekdays::WEDNESDAY,
    Weekdays::FRIDAY,
]);
$reparto->save();

// Más adelante, para una fecha dada:
if (Weekdays::isSelectedOnDate($reparto->weekdays, '2026-06-19')) {
    // ese viernes hay reparto
}
```
