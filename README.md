# Otomaties WordPress Iframemanager

This WordPress plugin is an implementation of [orestbida/iframemanager](https://github.com/orestbida/iframemanager). This will automatically replace iframes in the following blocks:
- core/embed (YouTube & Vimeo)
- core/html (YouTube, Vimeo & Google Maps)

## Installation

`composer require tombroucke/otomaties-wordpress-iframemanager`

## Documentation

[orestbida/iframemanager](https://github.com/orestbida/iframemanager)

## Customize color scheme
```scss
@use 'sass:color';

:root {
  --im-primary-color: #fff;
  --im-link-color: #{$primary};
  --im-spinner-color: #fff;
  --im-btn-primary-bg: #{$primary};
  --im-btn-primary-hover-bg: #{color.adjust($primary, $lightness: 5%)};
  --im-btn-primary-border-color: transparent;
  --im-btn-secondary-bg: #{color.adjust($light, $alpha: -0.3)};
  --im-btn-secondary-hover-bg: #{color.adjust($light, $lightness: 15%, $alpha: -0.2)};
  --im-btn-secondary-active-bg: #{color.adjust($light, $lightness: 15%, $alpha: -0.2)};
  --im-btn-primary-color: #fff;
  --im-service-bg: #151515;
  --im-service-overlay-bg: rgb(60 60 60);
  --im-service-overlay-bg-gradient: linear-gradient(14deg, rgb(50 50 50 / 100%) 0%, rgb(215 215 215 / 11.8%) 100%);
}
```
