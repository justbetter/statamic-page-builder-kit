# Statamic Page Builder

> This addon adds a page builder fieldset with some components.

## Features

The page builder contains a:

- Hero Banner Component
- Text Component
- USP Component

## Install

``` bash
composer require justbetter/statamic-page-builder
```

## How to Use

Simply install the addon and add the `component_page_builder` fieldset to a blueprint.
You can use the page builder by including it like this:

``` blade
@include('statamic-page-builder::page_builder')
```

## Todo

- Add Pages Collection
- Add Form Component
- Add image slider Component
- Add image + text Component
- Add CTA blocks Component
- Add accordeon Component
- Add Blog collection + category taxonomy + Component (Add-on)
- Add FAQ collection + FAQ category taxonomy + Component (Add-on)
