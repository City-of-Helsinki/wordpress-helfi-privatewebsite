<?php

declare(strict_types = 1);

namespace CityOfHelsinki\WordPress\PrivateWebsite\Integrations\WPHelfiCookieConsent\Cookies;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use CityOfHelsinki\WordPress\CookieConsent\Features\Interfaces\Known_Cookie_Data;

final class Helsinki_Login_Redirect implements Known_Cookie_Data
{
	public function issuer(): string
	{
		return __( 'Helsinki Private Website', 'helsinki-privatewebsite' );
	}

	public function name(): string
	{
		return 'helsinki-login-redirect';
	}

	public function label(): string
	{
		return 'helsinki-login-redirect';
	}

	public function descriptionTranslations(): array
	{
		return array(
			'fi' => 'Sivusto käyttää tätä tietuetta tietojen tallentamiseen siitä, mihin käyttäjä tulee uudelleenohjata kirjautumisen jälkeen.',
			'sv' => 'Webbplatsen använder den här posten för att lagra information om vart användaren ska omdirigeras efter inloggning.',
			'en' => 'The site uses this record to store information about where the user should be redirected after logging in.'
		);
	}

	public function retentionTranslations(): array
	{
		return array(
			'fi' => 'Istunto',
			'sv' => 'Session',
			'en' => 'Session'
		);
	}

	public function type(): string
	{
		return 'sessionstorage';
	}

	public function category(): string
	{
		return 'functional';
	}
}
