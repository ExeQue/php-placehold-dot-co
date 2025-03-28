<?php

declare(strict_types=1);

namespace Tests\Concerns\Builder;

use ExeQue\PlaceholdDotCo\Data\Font;
use Tests\Fixtures\InteractsWithFontImplementation;

it('is immutable', function () {
    $original = new InteractsWithFontImplementation();

    foreach (Font::cases() as $font) {
        $mutated = $original->font($font);
        expect($mutated)->not->toBe($original);
    }

    $mutated = $original->lato();
    expect($mutated)->not->toBe($original);

    $mutated = $original->lora();
    expect($mutated)->not->toBe($original);

    $mutated = $original->montserrat();
    expect($mutated)->not->toBe($original);

    $mutated = $original->notoSans();
    expect($mutated)->not->toBe($original);

    $mutated = $original->openSans();
    expect($mutated)->not->toBe($original);

    $mutated = $original->oswald();
    expect($mutated)->not->toBe($original);

    $mutated = $original->playfairDisplay();
    expect($mutated)->not->toBe($original);

    $mutated = $original->poppins();
    expect($mutated)->not->toBe($original);

    $mutated = $original->ptSans();
    expect($mutated)->not->toBe($original);

    $mutated = $original->raleway();
    expect($mutated)->not->toBe($original);

    $mutated = $original->roboto();
    expect($mutated)->not->toBe($original);

    $mutated = $original->sourceSansPro();
    expect($mutated)->not->toBe($original);
});

it('can set the font', function () {
    $builder = new InteractsWithFontImplementation();

    foreach (Font::cases() as $font) {
        $mutated = $builder->font($font);

        expect($mutated)->not->toBe($builder)
            ->and($mutated->renderFont())->toBe($font->value);
    }
});

it('sets the font correctly using specific font methods', function () {
    $builder = new InteractsWithFontImplementation(true);

    $builder = $builder->lato();
    expect($builder->renderFont())->toBe(Font::Lato->value);

    $builder = $builder->lora();
    expect($builder->renderFont())->toBe(Font::Lora->value);

    $builder = $builder->montserrat();
    expect($builder->renderFont())->toBe(Font::Montserrat->value);

    $builder = $builder->notoSans();
    expect($builder->renderFont())->toBe(Font::NotoSans->value);

    $builder = $builder->openSans();
    expect($builder->renderFont())->toBe(Font::OpenSans->value);

    $builder = $builder->oswald();
    expect($builder->renderFont())->toBe(Font::Oswald->value);

    $builder = $builder->playfairDisplay();
    expect($builder->renderFont())->toBe(Font::PlayfairDisplay->value);

    $builder = $builder->poppins();
    expect($builder->renderFont())->toBe(Font::Poppins->value);

    $builder = $builder->ptSans();
    expect($builder->renderFont())->toBe(Font::PTSans->value);

    $builder = $builder->raleway();
    expect($builder->renderFont())->toBe(Font::Raleway->value);

    $builder = $builder->roboto();
    expect($builder->renderFont())->toBe(Font::Roboto->value);

    $builder = $builder->sourceSansPro();
    expect($builder->renderFont())->toBe(Font::SourceSansPro->value);
});

it('does not render font if it has no text to render', function () {
    $builder = new InteractsWithFontImplementation(false);

    foreach (Font::cases() as $font) {
        $mutated = $builder->font($font);

        expect($mutated)->not->toBe($builder)
            ->and($mutated->renderFont())->toBeNull();
    }
});
