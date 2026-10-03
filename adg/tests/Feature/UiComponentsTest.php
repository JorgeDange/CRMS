<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UiComponentsTest extends TestCase
{
    use RefreshDatabase;

    public function test_component_gallery_renders_prototype_markup(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/ui');

        $response
            ->assertOk()
            ->assertSee('page-header')
            ->assertSee('btn btn-primary')
            ->assertSee('btn btn-light-brand')
            ->assertSee('btn btn-primary btn-sm')
            ->assertSee('btn btn-light-brand btn-icon')
            ->assertSee('feather-save')
            ->assertSee('badge bg-soft-primary text-primary')
            ->assertSee('badge bg-soft-danger text-danger')
            ->assertSee('badge small bg-gray-200 rounded ms-1 text-dark')
            ->assertSee('badge nxl-h-badge bg-success')
            ->assertSee('card stretch stretch-full')
            ->assertSee('avatar-text avatar-lg bg-gray-200')
            ->assertSee('progress-bar bg-warning')
            ->assertSee('form-label')
            ->assertSee('form-control')
            ->assertSee('invalid-feedback', false)
            ->assertSee('modal fade')
            ->assertSee('id="demoModal"', false)
            ->assertSee('modal-footer')
            ->assertSee('table table-hover')
            ->assertSee('table-responsive');
    }

    public function test_component_gallery_requires_authentication(): void
    {
        $this->get('/ui')->assertRedirect('/login');
    }
}
