# Clavel CMS Package Time Tracker
Paquete de Seguimiento de horas de trabajo

# TODO
* Quitar en timesheet el radio de activo. No sirve para nada
# Depende del paquete


## Instalación
# Instalacion directa con script
```
COMPOSER_MEMORY_LIMIT=-1 composer update
php artisan migrate:fresh --seed
./packages/clavel/timetracker/tools/install.sh
```


# Añadir en el composer.json
```
"require": {
    "php": ">=7.1.0",   
    "laravel/framework": "5.7.*",
    ...
    "clavel/timetracker": "@dev"
  },
```

y

```
,
  "repositories": [
    {
      "type": "path",
      "url": "./packages/clavel/timetracker/"

    }
  ],
```

si no esta ponemos

```
"minimum-stability": "dev",
```

Llamamos a composer para que reconozca el paquete

```
composer update
```

## Publicación de los contenidos


```
$ php artisan vendor:publish --provider="Clavel\TimeTracker\TimeTrackerServiceProvider"
```

Creamos las tablas de la base de datos 
```
php artisan migrate
```

y añadimos datos

para ello primero lanzamos un 
```
composer dumpauto
```
para que encuentre las clases de seed añadidas. Y luego ejecutamos.

Obligatorios
```
php artisan db:seed --class=TimeTrackerPermissionSeeder
```


### Configuración
En /config/general.php ponemos
```
'only_backoffice' => true,
```


### Migracion hacia nuevo cms
delete from `clavel-v2`.tt_config;
insert INTO `clavel-v2`.tt_config select * from timetracker.tt_config;

delete from `clavel-v2`.users;
insert INTO `clavel-v2`.users  (`id`,
	`username`,
	`email`,
	`email_verified_at`,
	`password`,
	`confirmed`,
	`active`,
	`remember_token` ,
	`created_at`,
	`updated_at`)  SELECT `id`,
	`username`,
	`email`,
	`email_verified_at`,
	`password`,
	`confirmed`,
	`active`,
	`remember_token` ,
	`created_at`,
	`updated_at` from timetracker.users;

delete from `clavel-v2`.user_profiles;
insert INTO `clavel-v2`.user_profiles (
	`id`,
	`user_id`,
	`first_name`,
	`last_name`,
	`gender`,
	`photo`,
	`phone`,
	`mobile`,
	`user_lang` ,
	`created_at` ,
	`updated_at` 
)

 select 	`id`,
	`user_id`,
	`first_name`,
	`last_name`,
	`gender`,
	`photo`,
	`phone`,
	`mobile`,
	`user_lang` ,
	`created_at` ,
	`updated_at` 
	 from timetracker.user_profiles;

delete from `clavel-v2`.activities;
insert INTO `clavel-v2`.activities select * from timetracker.activities;

delete from `clavel-v2`.customers;
insert INTO `clavel-v2`.customers select * from timetracker.customers;


delete from `clavel-v2`.invoiced_states;
insert INTO `clavel-v2`.invoiced_states select * from timetracker.invoiced_states;
delete from `clavel-v2`.invoiced_state_translations;
insert INTO `clavel-v2`.invoiced_state_translations select * from timetracker.invoiced_state_translations;

delete from `clavel-v2`.project_states;
insert INTO `clavel-v2`.project_states select * from timetracker.project_states;
delete from `clavel-v2`.project_state_translations;
insert INTO `clavel-v2`.project_state_translations select * from timetracker.project_state_translations;

delete from `clavel-v2`.project_types;
insert INTO `clavel-v2`.project_types select * from timetracker.project_types;
delete from `clavel-v2`.project_type_translations;
insert INTO `clavel-v2`.project_type_translations select * from timetracker.project_type_translations;

delete from `clavel-v2`.projects;
delete from `clavel-v2`.projects;
insert INTO `clavel-v2`.projects (
	`id`,
	`customer_id`,
	`name`,
	`order_number`,
	`customer_number`,
	`budget_number`,
	`description`,
	`active`,
	`budget`,
	`fixed_rate`,
	`hourly_rate`,
	`created_at`,
	`updated_at`,
	`customer_final_id`,
	`vat`,
	`total`,
	`slug_state`,
	`bill_info`,
	`invoiced`,
	`color`,
	`historified`,
	`project_type_id`,
	`expire_at`,
	`closed_at`,
	`invoice_number`,
	`responsable_id`,
	`work_hours`
) 
SELECT 
`id`,
	`customer_id`,
	`name`,
	`order_number`,
	`customer_number`,
	`budget_number`,
	`description`,
	`active`,
	`budget`,
	`fixed_rate`,
	`hourly_rate`,
	`created_at`,
	`updated_at`,
	`customer_final_id`,
	`vat`,
	`total`,
	`slug_state`,
	`bill_info`,
	`invoiced`,
	`color`,
	`historified`,
	`project_type_id`,
	`expire_at`,
	`closed_at`,
	`invoice_number`,
	`responsable_id`,
	`work_hours`
from timetracker.projects;


delete from `clavel-v2`.timesheet;
insert INTO `clavel-v2`.timesheet select * from timetracker.timesheet;
