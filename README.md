# Statamic Page Builder Kit

> This addon adds a page builder fieldset with some components.

## Features

This addon adds the following features:

- Pages Collection
- Hero Banner Component
- Text Component
- Image + text Component
- USP Component
- Form Component

## Install

``` bash
composer require justbetter/statamic-page-builder-kit
```

## How to Use

When making use of the collections provided by this addon, the page builder will already be generated for you.
If you want to add the page builder to an existing blueprint, you can do so by adding the `statamic-page-builder-kit::page_builder` fieldset to the blueprint.
You can use the page builder in your templates by including it like this:

``` blade
@include('statamic-page-builder-kit::page_builder')
```

## Todo

- Add image slider Component
- Add CTA blocks Component
- Add accordeon Component
- Add Blog collection + category taxonomy + Component (Add-on)
- Add FAQ collection + FAQ category taxonomy + Component (Add-on)
