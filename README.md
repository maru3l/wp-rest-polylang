# wp-rest-polylang

## description

Adds value `lang` and `translations` to WP REST api response for each Post and Page request for site running the Polylang plugin.

## Values

### lang
The locale value of the post
```
{
  [...]
  "lang": "en"
  [...]
}
```

### translations
List of translation for the post
```
{
  [...]
  "translations": {
    "en": 18,
    "fr": 16
  },
  [...]
}
```
