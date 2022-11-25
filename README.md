# PNGs to JPGs for Kirby 3

Automatically convert pngs to jpgs on upload. Make the life of your editors easier while keeping your website fast 😊

## Options

```php
return [
  'mrflix.pngs-to-jpgs.background' => 'white',
  'mrflix.pngs-to-jpgs.quality' => 90,
  'mrflix.pngs-to-jpgs.excludeTemplates' => [
    'home',
    'project'
  ],
  'mrflix.pngs-to-jpgs.excludePages' => [
    'projects/project-a'
  ],
];
```

## Installation

Put the repository folder into your `site/plugins` folder

or use Composer: `composer require mrflix/pngs-to-jpgs`
