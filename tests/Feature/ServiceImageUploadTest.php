<?php

use App\Models\Service;
use App\Models\User;
use Database\Factories\ServiceFactory;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Exceptions;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

beforeEach(function (): void {
    Schema::create('customers', function (Blueprint $table): void {
        $table->id();
        $table->string('name');
        $table->string('email')->nullable();
        $table->string('phone')->nullable();
        $table->string('address')->nullable();
        $table->timestamps();
    });
    Schema::create('services', function (Blueprint $table): void {
        $table->id();
        $table->string('service_name');
        $table->decimal('price', 10, 2);
        $table->string('image')->nullable();
        $table->text('description')->nullable();
        $table->timestamps();
    });
    Schema::create('orders', function (Blueprint $table): void {
        $table->id();
        $table->foreignId('customer_id')->constrained();
        $table->foreignId('service_id')->constrained();
        $table->integer('quantity');
        $table->string('status')->default('Pending');
        $table->decimal('total_price', 10, 2)->default(0);
        $table->timestamps();
    });

    $this->artisan('migrate', ['--no-interaction' => true])->assertExitCode(0);
});

test('admin uploads supported images and sees them after reloading the list', function (string $extension): void {
    Storage::fake('public');
    $admin = User::factory()->create(['role' => 'admin']);
    $this->actingAs($admin)->get(route('admin.services.create'))
        ->assertSee('multipart/form-data')->assertSee('name="image"', false);

    $this->post(route('admin.services.store'), [
        'service_name' => 'Wash and Fold', 'price' => 150, 'description' => 'Fresh laundry',
        'image' => UploadedFile::fake()->image('laundry.'.$extension),
    ])->assertRedirect(route('admin.services.index'))->assertSessionHasNoErrors();

    $service = Service::sole();
    $this->assertDatabaseHas('services', ['id' => $service->id, 'service_name' => 'Wash and Fold', 'price' => 150]);
    expect($service->image)->toStartWith('services/');
    Storage::disk('public')->assertExists($service->image);
    $url = '/storage/'.$service->image;
    $this->get(route('admin.services.index'))->assertSee($url, false);
    $this->get(route('admin.services.index'))->assertSee($url, false);
    $this->get(route('admin.services.edit', $service))->assertSee($url, false);
})->with(['jpg', 'jpeg', 'png', 'webp']);

test('admin can create a service without an image', function (): void {
    Storage::fake('public');
    $this->actingAs(User::factory()->create(['role' => 'admin']))->post(route('admin.services.store'), [
        'service_name' => 'Ironing', 'price' => 50,
    ])->assertRedirect(route('admin.services.index'));
    $this->assertDatabaseHas('services', ['service_name' => 'Ironing', 'image' => null]);
    Storage::disk('public')->assertDirectoryEmpty('services');
    $this->get(route('admin.services.index'))->assertSee('No Image');
});

test('admin replaces an image only after the service is updated', function (): void {
    Storage::fake('public');
    Storage::disk('public')->put('services/old.png', 'old image');
    $service = ServiceFactory::new()->create(['image' => 'services/old.png']);
    $this->actingAs(User::factory()->create(['role' => 'admin']))->put(route('admin.services.update', $service), [
        'service_name' => 'Updated service', 'price' => 200,
        'image' => UploadedFile::fake()->image('replacement.webp'),
    ])->assertRedirect(route('admin.services.index'))->assertSessionHasNoErrors();
    $service->refresh();
    $this->assertDatabaseHas('services', ['id' => $service->id, 'service_name' => 'Updated service', 'price' => 200]);
    expect($service->image)->not->toBe('services/old.png');
    Storage::disk('public')->assertExists($service->image);
    Storage::disk('public')->assertMissing('services/old.png');
});

test('editing without a new image preserves the existing image', function (): void {
    Storage::fake('public');
    Storage::disk('public')->put('services/keep.png', 'keep image');
    $service = ServiceFactory::new()->create(['image' => 'services/keep.png']);
    $this->actingAs(User::factory()->create(['role' => 'admin']))->put(route('admin.services.update', $service), [
        'service_name' => 'Renamed service', 'price' => 175, 'description' => 'Updated description', 'image' => null,
    ])->assertRedirect(route('admin.services.index'));
    $this->assertDatabaseHas('services', ['id' => $service->id, 'image' => 'services/keep.png', 'price' => 175]);
    Storage::disk('public')->assertExists('services/keep.png');
});

test('deleting a service removes only an unshared image', function (bool $shared): void {
    Storage::fake('public');
    Storage::disk('public')->put('services/delete.png', 'image');
    $service = ServiceFactory::new()->create(['image' => 'services/delete.png']);
    if ($shared) {
        ServiceFactory::new()->create(['image' => 'services/delete.png']);
    }
    $this->actingAs(User::factory()->create(['role' => 'admin']))->delete(route('admin.services.destroy', $service))
        ->assertRedirect(route('admin.services.index'));
    $this->assertModelMissing($service);
    if ($shared) {
        Storage::disk('public')->assertExists('services/delete.png');
    } else {
        Storage::disk('public')->assertMissing('services/delete.png');
    }
})->with(['unshared' => false, 'shared' => true]);

test('replacement keeps an image referenced by another service', function (): void {
    Storage::fake('public');
    Storage::disk('public')->put('services/shared.png', 'shared image');
    $service = ServiceFactory::new()->create(['image' => 'services/shared.png']);
    ServiceFactory::new()->create(['image' => 'services/shared.png']);
    $this->actingAs(User::factory()->create(['role' => 'admin']))->put(route('admin.services.update', $service), [
        'service_name' => 'Replacement', 'price' => 80, 'image' => UploadedFile::fake()->image('new.png'),
    ])->assertRedirect(route('admin.services.index'));
    Storage::disk('public')->assertExists('services/shared.png');
    Storage::disk('public')->assertExists($service->fresh()->image);
});

test('invalid images leave the database and existing image unchanged', function (string $kind, string $method): void {
    Storage::fake('public');
    Storage::disk('public')->put('services/existing.png', 'existing image');
    $service = ServiceFactory::new()->create(['image' => 'services/existing.png']);
    $disguisedFile = tmpfile();
    fwrite($disguisedFile, 'This is not an image.');
    $image = match ($kind) {
        'oversized' => UploadedFile::fake()->image('large.png')->size(2049),
        'unsupported' => UploadedFile::fake()->image('animated.gif'),
        'disguised' => new UploadedFile(stream_get_meta_data($disguisedFile)['uri'], 'fake.jpg', 'image/jpeg', null, true),
    };
    $route = $method === 'post' ? route('admin.services.store') : route('admin.services.update', $service);
    $this->actingAs(User::factory()->create(['role' => 'admin']))->{$method}($route, [
        'service_name' => 'Invalid upload', 'price' => 80, 'image' => $image,
    ])->assertSessionHasErrors('image');
    $this->assertDatabaseCount('services', 1);
    $this->assertDatabaseHas('services', ['id' => $service->id, 'image' => 'services/existing.png', 'service_name' => $service->service_name]);
    Storage::disk('public')->assertExists('services/existing.png');
    expect(Storage::disk('public')->allFiles('services'))->toHaveCount(1);
})->with(['oversized', 'unsupported', 'disguised'])->with(['post', 'put']);

test('staff and customers cannot change service images', function (string $role, string $method): void {
    Storage::fake('public');
    Storage::disk('public')->put('services/protected.png', 'image');
    $service = ServiceFactory::new()->create(['image' => 'services/protected.png']);
    $route = $method === 'post' ? route('admin.services.store') : route('admin.services.'.($method === 'put' ? 'update' : 'destroy'), $service);
    $this->actingAs(User::factory()->create(['role' => $role]))->{$method}($route, [
        'service_name' => 'Unauthorized', 'price' => 1, 'image' => UploadedFile::fake()->image('new.png'),
    ])->assertRedirect(route($role === 'staff' ? 'staff.dashboard' : 'customer.dashboard'));
    $this->assertDatabaseCount('services', 1);
    $this->assertDatabaseHas('services', ['id' => $service->id, 'image' => 'services/protected.png', 'service_name' => $service->service_name]);
    Storage::disk('public')->assertExists('services/protected.png');
    expect(Storage::disk('public')->allFiles('services'))->toHaveCount(1);
})->with(['staff', 'customer', 'user'])->with(['post', 'put', 'delete']);

test('guests cannot upload service images', function (): void {
    Storage::fake('public');
    $this->post(route('admin.services.store'), [
        'service_name' => 'Guest', 'price' => 1, 'image' => UploadedFile::fake()->image('guest.png'),
    ])->assertRedirect(route('login'));
    $this->assertDatabaseCount('services', 0);
    Storage::disk('public')->assertDirectoryEmpty('services');
});

test('missing image files show the placeholder rather than a broken image', function (?string $path): void {
    Storage::fake('public');
    $service = ServiceFactory::new()->create(['image' => $path]);
    $this->actingAs(User::factory()->create(['role' => 'admin']))->get(route('admin.services.index'))
        ->assertSee('No Image')->assertDontSee('class="service-image"', false);
    expect($service->imageUrl())->toBeNull();
    Storage::disk('public')->assertDirectoryEmpty('services');
})->with([null, 'services/missing.png', 'missing.png', '../private.png']);

test('failed service updates preserve the old image and clean up the new upload', function (): void {
    Storage::fake('public');
    Storage::disk('public')->put('services/original.png', 'image');
    $service = ServiceFactory::new()->create(['image' => 'services/original.png']);
    Exceptions::fake();
    Service::updating(function (): void {
        throw new RuntimeException('Database save failed');
    });
    $this->actingAs(User::factory()->create(['role' => 'admin']))->put(route('admin.services.update', $service), [
        'service_name' => 'Failed change', 'price' => 90, 'image' => UploadedFile::fake()->image('new.png'),
    ])->assertServerError();
    $this->assertDatabaseHas('services', ['id' => $service->id, 'image' => 'services/original.png']);
    Storage::disk('public')->assertExists('services/original.png');
    expect(Storage::disk('public')->allFiles('services'))->toHaveCount(1);
    Exceptions::assertReported(RuntimeException::class);
});

test('service API retains its existing image path contract', function (): void {
    $service = ServiceFactory::new()->create(['image' => 'services/photo.png']);
    $this->getJson('/api/services')->assertOk()->assertJsonPath('0.image', 'services/photo.png')
        ->assertJsonPath('0.id', $service->id)->assertJsonMissingPath('0.image_url');
});

test('legacy uploaded images remain visible and are safely cleaned up', function (string $action, bool $shared): void {
    Storage::fake('public');
    $filename = 'lavea-test-'.bin2hex(random_bytes(12)).'.png';
    $path = public_path('uploads/services/'.$filename);
    File::ensureDirectoryExists(dirname($path));
    file_put_contents($path, base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8/x8AAwMCAO+aPlsAAAAASUVORK5CYII='));
    try {
        $service = ServiceFactory::new()->create(['image' => $filename]);
        if ($shared) {
            ServiceFactory::new()->create(['image' => $filename]);
        }
        $this->actingAs(User::factory()->create(['role' => 'admin']))->get(route('admin.services.index'))
            ->assertSee('/uploads/services/'.$filename, false);
        if ($action === 'replace') {
            $this->put(route('admin.services.update', $service), [
                'service_name' => 'Modern upload', 'price' => 100, 'image' => UploadedFile::fake()->image('new.png'),
            ])->assertRedirect(route('admin.services.index'));
            Storage::disk('public')->assertExists($service->fresh()->image);
        } else {
            $this->delete(route('admin.services.destroy', $service))->assertRedirect(route('admin.services.index'));
            $this->assertModelMissing($service);
            Storage::disk('public')->assertDirectoryEmpty('services');
        }
        expect(file_exists($path))->toBe($shared);
    } finally {
        if (file_exists($path)) {
            unlink($path);
        }
    }
})->with(['replace', 'delete'])->with(['unshared' => false, 'shared' => true]);

test('failed creation cleans up the uploaded image', function (): void {
    Storage::fake('public');
    Exceptions::fake();
    Service::creating(function (): void {
        throw new RuntimeException('Database insert failed');
    });
    $this->actingAs(User::factory()->create(['role' => 'admin']))->post(route('admin.services.store'), [
        'service_name' => 'Failed service', 'price' => 100, 'image' => UploadedFile::fake()->image('new.png'),
    ])->assertServerError();
    $this->assertDatabaseCount('services', 0);
    Storage::disk('public')->assertDirectoryEmpty('services');
    Exceptions::assertReported(RuntimeException::class);
});

test('staff and customers see public storage service images', function (string $role): void {
    Storage::fake('public');
    Storage::disk('public')->put('services/visible.png', 'image');
    ServiceFactory::new()->create(['image' => 'services/visible.png']);
    $this->actingAs(User::factory()->create(['role' => $role]))->get(route($role.'.services.index'))
        ->assertSee('/storage/services/visible.png', false);
    Storage::disk('public')->assertExists('services/visible.png');
})->with(['staff', 'customer']);

test('invalid database image paths cannot delete files outside service storage', function (): void {
    Storage::fake('public');
    Storage::disk('public')->put('private.png', 'protected');
    $service = ServiceFactory::new()->create(['image' => 'services/../private.png']);
    $this->actingAs(User::factory()->create(['role' => 'admin']))->delete(route('admin.services.destroy', $service))
        ->assertRedirect(route('admin.services.index'));
    $this->assertModelMissing($service);
    Storage::disk('public')->assertExists('private.png');
});
