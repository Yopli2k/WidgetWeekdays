# WidgetWeekdays

Plugin para FacturaScripts que añade un nuevo widget (`type="weekdays"`) para seleccionar
los días de la semana en cualquier combinación, pensado para acciones que deben ejecutarse
en determinados días (tareas recurrentes, horarios, etc.).

## Almacenamiento

El valor se guarda en un campo `varchar(7)` como una cadena de `1` (día seleccionado) y
`0` (no seleccionado). El orden de los caracteres sigue el estándar **ISO 8601**
(el mismo que usa `DateTimeTools::dayOfWeek()` del ecosistema FacturaScripts):

| índice | 0 | 1 | 2 | 3 | 4 | 5 | 6 |
|--------|---|---|---|---|---|---|---|
| día    | Lunes | Martes | Miércoles | Jueves | Viernes | Sábado | Domingo |

Ejemplos:

- `1111100` → de lunes a viernes
- `0000011` → fin de semana
- `1111111` → todos los días
- `null`, `""` o `0000000` → ningún día (un valor nulo o vacío equivale a todo ceros)

Para saber el índice de una fecha basta con `DateTimeTools::dayOfWeek($fecha) - 1`.

## Uso en una vista XML

1. Define el campo en el `Table/` del modelo como `varchar` de longitud 7.
2. En el `XMLView/` usa el widget con `type="weekdays"`:

```xml
<column name="days" order="100">
    <widget type="weekdays" fieldname="days"/>
</column>
```

## Selección visual

- Botones tipo *toggle* de Bootstrap 5 (`.btn-check`), uno por día.
- Botones de selección rápida: **Lun-Vie**, **Fin de semana**, **Todos** y **Ninguno**.

Toda la lógica de cliente es JavaScript vanilla; un único campo oculto con la cadena de
7 caracteres es lo que se envía en el formulario.

## Filtro en listados (ListView)

El plugin incluye `Lib/ListFilter/WeekdaysFilter` para filtrar un listado por el campo
varchar(7). Se añade como cualquier otro filtro, normalmente desde una extensión del
`ListController` (igual que hace el plugin ProductFamilyFilter), asignándolo al array
`filters` de la vista:

```php
use FacturaScripts\Dinamic\Lib\ListFilter\WeekdaysFilter;

// dentro de createViews() (o de una extensión del controlador):
$this->views['MiListView']->filters['weekdays'] =
    new WeekdaysFilter('weekdays', 'weekdays', 'weekdays');
// argumentos: (key, fieldname, label)
```

El filtro muestra los 7 días como botones y devuelve los registros que tienen activos
**todos** los días marcados (combinación AND). Para un único día seleccionado equivale a
"registros con ese día activo". Reutiliza el JS/CSS del propio widget, por lo que no
requiere assets adicionales.

## Más información
<ul>
    <li>General info: https://www.facturascripts.com</li>
    <li>Plugin info:  https://www.facturascripts.com/plugins/widgetrichtext</li>
</ul>


## Documentación / Issues / Feedback
https://www.facturascripts.com

## Enlaces de interés
- [Cómo instalar plugins en FacturaScripts](https://facturascripts.com/publicaciones/como-instalar-un-plugin-en-facturascripts)
- [Programa para hacer facturas gratis](https://facturascripts.com/programa-para-hacer-facturas)
- [Cómo instalar FacturaScripts en Windows](https://facturascripts.com/instalar-windows)

### Otros plugins del mismo autor
- [Amortización de Inmovilizados](https://facturascripts.com/plugins/amortizaciones)
- [Documentos Recurrentes](https://facturascripts.com/plugins/documentosrecurrentes)
- [Recursos Humanos](https://facturascripts.com/plugins/humanresources)
- [Producción](https://facturascripts.com/plugins/produccion)
- [Producto Pack](https://facturascripts.com/plugins/productopack)
- [Pagos Múltiples](https://facturascripts.com/plugins/pagosmultiples)
- LawFirm: Solución sectorial para despachos de abogacía
- CourseManagement: Solución sectorial para gestión de cursos de formación subvencionados</li>
- GestionVeterinaria: Solución sectorial para administración de clínicas veterinarias</li>
