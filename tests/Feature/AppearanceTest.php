<?php

use App\Models\User;

test('browser theme cookies select dark mode and restore light mode', function (): void {
    $this->withUnencryptedCookie('lavea_theme', 'dark')->get('/')
        ->assertOk()->assertSee('css/lavea-theme.css', false);

    $this->withUnencryptedCookie('lavea_theme', 'light')->get('/')
        ->assertOk()->assertDontSee('css/lavea-theme.css', false);
});

test('appearance settings save a cookie that the browser toggle can replace', function (string $theme): void {
    $admin = User::factory()->make(['id' => 1, 'role' => 'admin']);

    $response = $this->actingAs($admin)->post(route('admin.settings.appearance'), ['theme' => $theme]);

    $response->assertRedirect(route('admin.settings'))
        ->assertSessionHas('theme_success', 'Appearance setting saved.')
        ->assertPlainCookie('lavea_theme', $theme);

    expect($response->getCookie('lavea_theme', false)->isHttpOnly())->toBeFalse();

    $page = $this->withUnencryptedCookie('lavea_theme', $theme)->get('/');
    $page->assertOk();

    if ($theme === 'dark') {
        $page->assertSee('css/lavea-theme.css', false);
    } else {
        $page->assertDontSee('css/lavea-theme.css', false);
    }
})->with(['light', 'dark']);
