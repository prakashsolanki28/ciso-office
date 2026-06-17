<?php

use App\Models\User;

function admin(): User
{
    return User::factory()->create(['role' => 'admin']);
}

it('lets an admin create a pre-verified, active employee', function () {
    $this->actingAs(admin())
        ->post(route('admin.employees.store'), [
            'name'  => 'Jane Employee',
            'email' => 'jane@corp.com',
            'password' => 'secret123',
        ])
        ->assertRedirect(route('admin.employees.index'));

    $employee = User::where('email', 'jane@corp.com')->sole();
    expect($employee->role)->toBe('employee');
    expect($employee->is_active)->toBeTrue();
    expect($employee->email_verified_at)->not->toBeNull();
});

it('forbids non-admins from managing employees', function () {
    $emp = User::factory()->create(['role' => 'employee']);

    $this->actingAs($emp)->get(route('admin.employees.index'))->assertForbidden();
});

it('deactivates and reactivates an employee', function () {
    $employee = User::factory()->create(['role' => 'employee', 'is_active' => true]);

    $this->actingAs(admin())->patch(route('admin.employees.deactivate', $employee));
    expect($employee->fresh()->is_active)->toBeFalse();

    $this->actingAs(admin())->patch(route('admin.employees.deactivate', $employee));
    expect($employee->fresh()->is_active)->toBeTrue();
});

it('resets an employee password', function () {
    $employee = User::factory()->create(['role' => 'employee']);
    $oldHash = $employee->password;

    $this->actingAs(admin())->patch(route('admin.employees.reset-password', $employee), [
        'password' => 'brandnew123',
    ])->assertSessionHas('success');

    expect($employee->fresh()->password)->not->toBe($oldHash);
});

it('does not let an admin edit a non-employee via the employee routes', function () {
    $other = User::factory()->create(['role' => 'admin']);

    $this->actingAs(admin())->get(route('admin.employees.edit', $other))->assertNotFound();
});
