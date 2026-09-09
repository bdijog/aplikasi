<?php

namespace Tests\Feature;

use BezhanSalleh\LanguageSwitch\Http\Livewire\LanguageSwitchComponent;
use Livewire\Livewire;
use Tests\TestCase;

class AdminLoginLanguageSwitchTest extends TestCase
{
    /**
     * Test that the admin login page renders the language switch component.
     */
    public function test_admin_login_page_renders_language_switch(): void
    {
        $response = $this->get(route('filament.admin.auth.login'));

        $response->assertSuccessful();
        $response->assertSeeLivewire(LanguageSwitchComponent::class);
    }

    /**
     * Test that changing the locale via the language switch component updates the session.
     */
    public function test_language_switch_changes_locale_in_session(): void
    {
        Livewire::test(LanguageSwitchComponent::class)
            ->call('changeLocale', 'en')
            ->assertSessionHas('locale', 'en');

        Livewire::test(LanguageSwitchComponent::class)
            ->call('changeLocale', 'id')
            ->assertSessionHas('locale', 'id');
    }
}
