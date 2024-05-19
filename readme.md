# OctopusFlexiTracker

<div align="center">
	<img src="https://i.imgur.com/GGnBEFv.png">
</div>

This project is neither official nor linked to Octopus Energy.

The repository consists of simple code written in PHP. The file download_prices.php is in charge of downloading the file with the prices of the day, while the file update_prices.php is in charge of updating it during the day.

## How to configure it?

The project works with PhpSpreadsheet, so you will need to install it.

For this you will need to have PHP and Composer installed on the machine where you are going to run it.

Once composer is installed you will need to launch:

```bash
composer require phpoffice/phpspreadsheet
```

Once installed, you will have to modify the crontab of the machine so that these PHP are executed in the following way.
```bash
crontab -e
```

Add the following lines:
```bash
59 23 * * * /usr/bin/php /path/to/file/download_prices.php
0 * * * * /usr/bin/php /path/to/file/update_prices.php
```

The update_prices.php file will generate the JSON file with the current prices in the path set in the config.ini file. It is necessary that this path is public to be able to consult the file!

## How to integrate it with home assistant?

An example of how to use this code would be to query the data from a system such as home assistant.

If you want to use it, you would have to do it this way.

Add a sensor in the configuration.yaml file.

```bash
sensor:
  - platform: rest
    name: Octopus Flexi
    resource: URL/prices_day.json
    unit_of_measurement: "EUR/kWh"
    value_template: "{{ value_json[0]['Total'] | float }}"
```

Create an automation so that this sensor is updated every hour getting the new data from the JSON.

```bash
- alias: Updates the Octopus Flexi tariff data every hour
  description: ''
  trigger:
  - platform: time_pattern
    minutes: '0'
    seconds: '10'
  condition: []
  action:
  - service: homeassistant.update_entity
    entity_id: sensor.octopus_flexi

```

## Note

If you appreciate my work and want to help me, I invite you to register in Octopus Energy with my link, we will both earn 50€ discount on the following invoices.

https://share.octopusenergy.es/new-crick-47
