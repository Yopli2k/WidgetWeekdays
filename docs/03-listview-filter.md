# Filtro por días de la semana en listados

WidgetWeekdays incluye un filtro listo para usar, `WeekdaysFilter`, que permite filtrar un `ListView` por los días seleccionados en un campo `varchar(7)`. Es el equivalente, para este tipo de campo, a los filtros estándar de FacturaScripts (`addFilterSelect`, `addFilterCheckbox`, etc.).

El filtro muestra los siete días como botones y, al aplicar, devuelve los registros que tienen activos **todos** los días marcados (combinación **AND**). Si marcas un único día, equivale a "registros que tienen ese día activo".

## Cómo añadirlo a un listado

El sistema de filtros de FacturaScripts guarda las instancias en el array público `filters` de la vista. `WeekdaysFilter` no tiene un método ayudante (`addFilter...`) propio; se asigna directamente al array, igual que hace el plugin *ProductFamilyFilter*.

Hazlo desde `createViews()` de tu `ListController` (o desde una extensión del controlador), después de crear la vista de lista:

```php
use FacturaScripts\Dinamic\Lib\ListFilter\WeekdaysFilter;

protected function createViews()
{
    parent::createViews();

    // ... configuración de la vista de lista 'ListMiModelo' ...

    $this->views['ListMiModelo']->filters['weekdays'] =
        new WeekdaysFilter('weekdays', 'weekdays', 'weekdays');
}
```

### Argumentos del constructor

```php
new WeekdaysFilter(string $key, string $field, string $label);
```

| Argumento | Descripción |
|-----------|-------------|
| `$key`    | Clave única del filtro dentro de la vista (la del array `filters`). |
| `$field`  | Nombre de la columna `varchar(7)` por la que se filtra. |
| `$label`  | Clave de traducción que se muestra como título del filtro. |

En el ejemplo anterior los tres coinciden (`'weekdays'`), pero pueden ser distintos si tu columna o tu etiqueta tienen otro nombre:

```php
$this->views['ListReparto']->filters['dias'] =
    new WeekdaysFilter('dias', 'deliverydays', 'delivery-days');
```

## Cómo filtra (semántica AND)

Por cada día marcado, el filtro añade una condición sobre la posición correspondiente de la cadena. Como todas las condiciones se combinan con AND, el listado muestra solo los registros que tienen **todos** los días marcados activos a la vez.

Ejemplos sobre una columna con estos valores:

| Registro | Valor       | Lunes+Jueves marcados | Solo viernes |
|----------|-------------|:---------------------:|:------------:|
| A        | `1111100`   | ✅ (L y J activos)    | ✅           |
| B        | `1001000`   | ✅ (L y J activos)    | ❌           |
| C        | `0000011`   | ❌                    | ❌           |
| D        | `0001000`   | ❌ (falta lunes)      | ❌           |

Si no marcas ningún día, el filtro no aplica ninguna condición (no descarta registros).

## Notas

- El filtro reutiliza el JavaScript y el CSS del propio widget, así que **no necesitas añadir assets** adicionales: se cargan automáticamente cuando el filtro se renderiza.
- Importa siempre la clase desde el espacio de nombres `Dinamic` (`FacturaScripts\Dinamic\Lib\ListFilter\WeekdaysFilter`).
- Recuerda declarar `require = 'WidgetWeekdays'` en el `facturascripts.ini` de tu plugin para que la clase esté disponible.
