# Sistema de Salidas ESG

## Tecnologías

- Laravel 12
- MySQL
- React
- TailwindCSS
- Capacitor

## Convenciones

- Todos los controladores usan Resource Controllers.
- Todas las respuestas son JSON.
- Los nombres de tablas son en plural.
- Los nombres de columnas están en snake_case.
- Todas las consultas usan Eloquent.
- No usar consultas SQL crudas salvo que sea necesario.

## API externa

Productos:
http://189.206.185.236/api/Mty/ComercialProductos

Existencias:
http://189.206.185.236/api/Mty/ComercialExistencia/{id}

Unidad de medida:
http://189.206.185.236/api/Mty/ComercialUnidadMedida

## Objetivo

El sistema NO administra inventario.

Toda la información proviene de CONTPAQi.

El sistema únicamente facilita el registro de salidas.

Después enviará la venta interna a CONTPAQi.