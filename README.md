<p align="center"><a href="https://www.diagro.be" target="_blank"><img src="https://diagro.be/assets/img/diagro-logo.svg" width="400"></a></p>

<p align="center">
<img src="https://img.shields.io/badge/project-lib_laravel_event-yellowgreen" alt="Diagro event library">
<img src="https://img.shields.io/badge/type-library-informational" alt="Diagro service">
<img src="https://img.shields.io/badge/php-8.1-blueviolet" alt="PHP">
<img src="https://img.shields.io/badge/laravel-9.0-red" alt="Laravel framework">
</p>

##Composer

`diagro/lib_laravel_event: "^1.0"`

##app/Providers/BroadcastServiceProvider

```php
public function boot()
{
    Broadcast::routes([‘middleware’ => [‘web’, ‘broadcast’]);
    ...
}
```

##Example

```php

<?php
class EventResult implements ShouldBroadcast
{
  use BroadcastWhenOccupied;

  protected function channelName(): string
  {
    return 'Diagro.API.Async';
  }
}
```
