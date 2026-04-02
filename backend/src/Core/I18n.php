<?php

declare(strict_types=1);

namespace App\Core;

final class I18n
{
    private static string $locale = 'nl';

    /** @var array<string, array<string, string>> */
    private static array $messages = [
        'nl' => [
            'error.not_found' => 'Niet gevonden.',
            'error.server' => 'Serverfout.',
            'auth.invalid_login_data' => 'Ongeldige login gegevens.',
            'auth.invalid_credentials' => 'Verkeerde login gegevens.',
            'auth.invalid_registration_data' => 'Ongeldige registratiegegevens.',
            'auth.email_exists' => 'Email bestaat al.',
            'auth.token_missing' => 'Token ontbreekt.',
            'auth.token_invalid_or_expired' => 'Token ongeldig of verlopen.',
            'exercise.required_fields' => 'Naam, beschrijving en afbeelding zijn verplicht.',
            'exercise.invalid_sets_or_reps' => 'Sets en reps moeten groter zijn dan 0.',
            'exercise.invalid_muscle_groups' => 'Ongeldige spiergroepen.',
            'exercise.invalid_start_weight' => 'Startgewicht mag niet negatief zijn.',
        ],
        'en' => [
            'error.not_found' => 'Not found.',
            'error.server' => 'Server error.',
            'auth.invalid_login_data' => 'Invalid login data.',
            'auth.invalid_credentials' => 'Invalid credentials.',
            'auth.invalid_registration_data' => 'Invalid registration data.',
            'auth.email_exists' => 'Email already exists.',
            'auth.token_missing' => 'Token is missing.',
            'auth.token_invalid_or_expired' => 'Token is invalid or expired.',
            'exercise.required_fields' => 'Name, description and image are required.',
            'exercise.invalid_sets_or_reps' => 'Sets and reps must be greater than 0.',
            'exercise.invalid_muscle_groups' => 'Invalid muscle groups.',
            'exercise.invalid_start_weight' => 'Start weight cannot be negative.',
        ],
    ];

    public static function detectLocaleFromRequest(): string
    {
        $forced = strtolower(trim((string) ($_SERVER['HTTP_X_LOCALE'] ?? '')));
        if ($forced === 'nl' || $forced === 'en') {
            return $forced;
        }

        $acceptLanguage = strtolower((string) ($_SERVER['HTTP_ACCEPT_LANGUAGE'] ?? ''));
        if (preg_match('/(^|,|\s)en(-|;|,|$)/', $acceptLanguage) === 1) {
            return 'en';
        }

        return 'nl';
    }

    public static function setLocale(string $locale): void
    {
        self::$locale = ($locale === 'en') ? 'en' : 'nl';
    }

    public static function t(string $key): string
    {
        return self::$messages[self::$locale][$key]
            ?? self::$messages['en'][$key]
            ?? $key;
    }
}
