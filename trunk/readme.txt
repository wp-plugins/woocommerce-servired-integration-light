=== Woocommerce Servired Light ===
Contributors: PluginTPV
Donate link: http://www.plugintpv.com/
Tags: woocommerce, servired, credit card, martercard, visa
Requires at least: 3.2
Tested up to: 3.4
Stable tag: 1.5
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Integración para Woocommerce del sistema de pago por tarjeta Servired.

== Description ==

Añada pago por tarjeta de crédito con Servired a Woocommerce.
Permita a sus usuarios pagar con Martercard y Visa.
<p>La versión 'light' actualiza las notas de pedido con el resultado del pago.</p>
<p>Use la <a href="http://plugintpv.com/plugins/servired-integracion-woocommerce/">versión PRO</a> para actualizar además el estado del pedido de forma automática.</p>

Web oficial: [plugintpv](http://plugintpv.com/plugins/servired-woocommerce-integration-light/)

== Installation ==

Puede instalarlo automáticamente desde Wordpress, o manualmente:

1. Descomprima el archivo, y copie la carpeta 'woocommerce-servired-integration-light' en su carpeta de plugins del servidor (/wp-content/plugins/).
1. Active el plugin desde el menú de Plugins.

== Preguntas frecuentes (FAQs) ==

= ¿ Donde consigo los datos de configuración ? =

Su entidad bancaria debe proporcionarle los datos de acceso. Una vez que los tenga, basta con añadirlos al plugin, y ya lo tendrá funcionando.

= ¿ Donde se configura el plugin ? =

En el menú de Woocommerce->Ajustes
En la pestaña de "Pasarelas de Pago"

== Screenshots ==

1. Admin - página de configuración.
2. Tienda - Formas de pago.


== Changelog ==

= 1.5 =
* Compatible con Woocommerce 2.3.x
* Corregido error con https
* Añadidos filtros: wooservired_light_title, wooservired_light_description, wooservired_light_param_urlOK, and wooservired_light_param_KO

= 1.4 =
* Añadida selección entre url de Sermepa o RedSys.
* Comprobada compatibilidad con Wordpress hasta 3.9
* Comprobada compatibilidad con Woocommerce hasta 2.1.8
= 1.3.1 =
* Corregido error Pedido Duplicado
* Al terminar transacción correctamente muestra resumen del pedido.

= 1.3 =
* Corregido error específico La Caixa.

= 1.2 =
* Ampliado formato de firma y corregido error existente en ella.

= 1.1.1 =
* Soporte multilenguaje. Actualmente inglés y español.
* Corregido: fallo al activar el plugin sin tener instalado Woocommerce.

= 1.1.0 = 
* Corregido fallo nombre de plugin. No cargaba logotipo correctamente.

= 1.0.0 =
* Versión inicial.
