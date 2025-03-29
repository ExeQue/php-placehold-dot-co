# Placehold.co

Wrapper for [placehold.co](https://placehold.co/) to generate placeholder images.

It supports the full API of [placehold.co](https://placehold.co/) and provides a fluent interface to build the images.

## Installation

You can install this package via composer:

```bash
composer require exeque/placehold-dot-co
```

## Usage

### The Image Object

The image object contains the following properties:
- `size`: The size of the image in bytes.
- `contents`: The contents of the image as a string.
- `mime`: The mime type of the image.
- `uri`: The URI of the image.

The image is stored using a `php://temp` resource to reduce memory usage.
The resource is automatically closed when the object is destroyed.

### Basic Usage

Images can be generated using the `builder()` method.

The `Builder` class provides an immutable fluent interface to build the image.

```php
use ExeQue\PlaceholdDotCo\Data\Format;
use ExeQue\PlaceholdDotCo\Data\Image;
use ExeQue\PlaceholdDotCo\Placehold;

$placehold = new Placehold();

/** @var Image $image */
$image = $placehold->builder()
    ->size(1920, 1080)
    ->color('black', 'white')
    ->format(Format::JPEG)
    ->text('Hello World')
    ->get();

$image->size; // Byte size
$image->contents; // Image contents as string
$image->mime; // Image mime type
$image->uri; // Image URI
$image->detach(); // Detach the data stream from the object
```

### Size

By default, the images are created as 300x300, but you can change the size.

```php
use ExeQue\PlaceholdDotCo\Placehold;

$placehold = new Placehold();

$builder = $placehold->builder();

$builder->size(1920, 1080);
$builder->width(1920);
$builder->height(1080);
$builder->square(1000);
```

It is also possible to change the orientation of the image by using the `landscape()` and `portrait()` methods.

```php
use ExeQue\PlaceholdDotCo\Placehold;

$placehold = new Placehold();

$landscape = $placehold->builder()->size(1920, 1080); // landscape 1920x1080
$portrait = $placehold->builder()->size(1080, 1920); // portrait 1080x1920

$landscape->portrait(); // landscape 1920x1080 -> portrait 1080x1920
$portrait->landscape(); // portrait 1080x1920 -> landscape 1920x1080
```

#### Retina

You can also create retina images by using the `retina()` method.

Only supported for:
- JPEG
- PNG
- GIF
- WEBP
- AVIF

```php
use ExeQue\PlaceholdDotCo\Placehold;

$builder = $placehold->builder();

$builder->x1(); // no retina / reset
$builder->x2(); // retina 2x
$builder->x3(); // retina 3x
```

### Format

You can change the image format by using the `format()` or format specific methods.

```php
use ExeQue\PlaceholdDotCo\Data\Format;
use ExeQue\PlaceholdDotCo\Placehold;

$placehold = new Placehold();

$builder = $placehold->builder();

// JPEG
$builder->format(Format::JPEG);
$builder->jpeg();

// PNG
$builder->format(Format::PNG);
$builder->png();

// GIF
$builder->format(Format::GIF);
$builder->gif();

// WEBP
$builder->format(Format::WEBP);
$builder->webp();

// SVG
$builder->format(Format::SVG);
$builder->svg();

// AVIF
$builder->format(Format::AVIF);
$builder->avif();
```

### Color

You can change the background and text color by using the `color()`, `background()` or `foreground()` methods.

Note: If you're using the `foreground()` method without a background color set it will set the background color to white.

The methods support CSS colors, hex colors (without #), and transparency (via the `transparent` color).

```php
use ExeQue\PlaceholdDotCo\Data\Format;
use ExeQue\PlaceholdDotCo\Placehold;

$placehold = new Placehold();

$builder = $placehold->builder();

$builder->color('black', 'white'); // background black, text white
$builder->color('F00', 'FFF'); // background #F00, text #FFF
$builder->color('FF0000', 'FFFFFF'); // background #FF0000, text #FFFFFF
$builder->color('transparent', 'black'); // background transparent, text black

$builder->background('black'); // background black
$builder->foreground('black'); // text black (background white)
```

### Text

You can change the text by using the `text()` method.
Existing text will be overwritten - Existing text can be removed by using the `noText()` method.

Multiline text is supported and lines are separated by `\n`.

```php
use ExeQue\PlaceholdDotCo\Placehold;

$placehold = new Placehold();

$builder = $placehold->builder();

$builder->text('Hello World'); // Single line "Hello World"
$builder->text("Hello\nWorld"); // Multi line text "Hello\nWorld"

$builder->noText(); // remove text
```

### Font

You can change the font by using the `font()` or specific font methods.

```php
use ExeQue\PlaceholdDotCo\Data\Font;
use ExeQue\PlaceholdDotCo\Placehold;

$placehold = new Placehold();

$builder = $placehold->builder();

// Lato
$builder->font(Font::Lato);
$builder->lato();

// Lora
$builder->font(Font::Lora);
$builder->lora();

// Montserrat
$builder->font(Font::Montserrat);
$builder->montserrat();

// Noto Sans
$builder->font(Font::NotoSans);
$builder->notoSans();

// Open Sans
$builder->font(Font::OpenSans);
$builder->openSans();

// Oswald
$builder->font(Font::Oswald);
$builder->oswald();

// Playfair Display
$builder->font(Font::PlayfairDisplay);
$builder->playfairDisplay();

// Poppins
$builder->font(Font::Poppins);
$builder->poppins();

// PT Sans
$builder->font(Font::PTSans);
$builder->ptSans();

// Raleway
$builder->font(Font::Raleway);
$builder->raleway();

// Roboto
$builder->font(Font::Roboto);
$builder->roboto();

// Source Sans Pro
$builder->font(Font::SourceSansPro);
$builder->sourceSansPro();
```

### Conditional Methods

If you want to use the builder methods conditionally, you can use the `when()` method.

```php
use ExeQue\PlaceholdDotCo\Builder;
use ExeQue\PlaceholdDotCo\Placehold;

$placehold = new Placehold();

$builder = $placehold->builder();

$condition = true;
$callableCondition = fn() => true;

$builder
    ->when($condition, fn(Builder $builder) => $builder->square(400))
    ->when($callableCondition, fn(Builder $builder) => $builder->square(400));

```

## URL Generation

You can also generate the URL for the image by using the `url()` method.

```php
use ExeQue\PlaceholdDotCo\Placehold;

$builder = $placehold->builder();

$builder->uri(); // URI of the image as a PSR-7 compatible UriInterface
```

## Advanced Usage

### Batching

The library supports fetching multiple images at once using the `batch()` method by leveraging the `Guzzle` library's async requests.

You are not guaranteed to get the images in the same order as you requested them, so you should use the keys to identify the images if order matters.

```php
use ExeQue\PlaceholdDotCo\Placehold;

$placehold = new Placehold();

$builder = $placehold->builder()->size(1920, 1080)->png();

// The batch method returns a generator function that can be used to fetch the images. Any indices can be used as keys.
$generator = $placehold->batch([
    'first' => $builder->text('First Image'),
    'second' => $builder->text('Second Image'),
]);

foreach ($generator as $index => $image) {
    // Do something with the image
}

// The generator function will return the images as they are fetched.
$images = iterator_to_array($generator);

$images['first']; // URI of the first image
$images['second']; // URI of the second image

```

## FakerPHP Provider

This library also provides a FakerPHP provider to generate placeholder images.

Most, but not all, features are available in the `Faker` provider.

Check the phpdoc for the `ImageProvider` class for more information.

```php

use ExeQue\PlaceholdDotCo\Faker\ImageProvider;
use ExeQue\PlaceholdDotCo\Placehold;
use Faker\Factory;
use Faker\Generator;

$provider = new ImageProvider(
    new Placehold() // [Optional] Can be configured with a Placehold instance
); 

/** @var Generator|ImageProvider $faker */
$faker = Factory::create();

$faker->addProvider($provider);

$faker->placeholdCoUrl();       // Create a url for a placeholder image
$faker->placeholdCoImage();     // Create a placeholder image as a string
$faker->placeholdCoResource();  // Create a placeholder image as a resource

```

## Testing

You can run the tests using the following command:

```bash
composer test           # run unit tests
composer test:coverage  # run unit tests with coverage
composer test:types     # run type coverage tests
composer test:mutation  # run mutation tests

composer test:all       # run all tests
```

## License

This library is open-sourced software licensed under the [MIT license](LICENSE.md).
