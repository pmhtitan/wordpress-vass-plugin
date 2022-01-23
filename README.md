"# wordpress-vass-plugin" 

# Countries Shortcode JR (WordPress)

## Comentario sobre requerimientos - api countries endpoint
Tras revisar el endpoint propuesto (https://restcountries.eu/rest/v2/all), el dominio no se puede acceder.

* En su lugar haré uso de [https://restcountries.com/v3.1/region/europe](https://restcountries.com/v3.1/region/europe/?fields=name,capital,population). 

## Comentarios generales
* También he preferido usar el endpoint filtrando por name, capital y population, para que el JSON fuera más ligero y hubieran mejores tiempos de respuesta.

* No requería maquetación ni includes, he omitido la estructura de carpetas.