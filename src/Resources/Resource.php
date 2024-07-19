<?php

namespace Filament\Core\Resources;

use Closure;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\Field;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Forms\Form;
use Filament\Resources\Resource as FilamentResource;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Resource extends FilamentResource
{

    public static function form(Form $form): Form
    {
        return $form->columns(3)->schema(
            [
                Forms\Components\Group::make()
                    ->statePath('resourceTranslations')
                    ->schema([
                        Tabs::make('Translations')->schema(
                            collect(config('palindroma.supported_locales'))
                                ->map(fn($locale) => static::getTranslatableFields($locale))
                                ->toArray()
                        ),
                    ])
                    ->columnSpan(2),
                Forms\Components\Group::make()->schema([
                        static::getMetaFields(),
                    ]
                )->columnSpan(1),
                ...static::getExtraFields(),
            ]);
    }

    public static function getExtraFields(): array
    {
        return [];
    }

    public static function getTranslatableFields($locale): Tab
    {
        $tab = static::translatableFields(Tab::make(Str::of($locale.' '.config('app.locale_labels')[$locale])), $locale);

        $tab->schema([
            ...$tab->getChildComponents(),
            ...static::defaultTranslatableFields(),
        ]);

        collect($tab->getChildComponents())->each(function (Component $component) use ($locale) {
            if ($component instanceof Field) {
                $component->hint("Translatable")->hintIcon('gmdi-translate-o');
            }
        });

        return $tab;
    }

    public static function defaultTranslatableFields(): array
    {
        return [];
    }

    public static function getMetaFields(): Section
    {
        $section = static::metaFields(Section::make());
        $section->schema([
            ...$section->getChildComponents(),
            ...static::defaultMetaFields(),
        ]);

        return $section;
    }

    public static function translatableFields(Tab $tab, $locale): Tab
    {
        return $tab->schema([

        ]);
    }

    public static function defaultMetaFields(): array
    {
        return [
            Forms\Components\Placeholder::make('created_at')
                ->label('Created Date')
                ->content(fn(?Model $record): string => $record?->created_at?->diffForHumans() ?? '-'),

            Forms\Components\Placeholder::make('updated_at')
                ->label('Last Modified Date')
                ->content(fn(?Model $record): string => $record?->updated_at?->diffForHumans() ?? '-'),
        ];
    }

    public static function metaFields(Section $section): Section
    {
        return $section->schema([
        ]);
    }
}
