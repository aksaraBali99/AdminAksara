<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * The Gates in AppServiceProvider (manage-clients, manage-invoices, etc.)
 * were only ever used to hide sidebar links - no route enforced them, so
 * any authenticated user of any role could reach any URL directly. These
 * tests lock in the fix: each route group must reject roles the Gate
 * denies and allow roles it permits.
 */
class RouteAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    private function userWithRole(string $role): User
    {
        return User::factory()->create(['role' => $role]);
    }

    public function test_hr_cannot_access_client_management(): void
    {
        $hr = $this->userWithRole('hr');

        $this->actingAs($hr)->get(route('clients.index'))->assertForbidden();
    }

    public function test_owner_and_finance_can_access_client_management(): void
    {
        foreach (['owner', 'finance'] as $role) {
            $this->actingAs($this->userWithRole($role))
                ->get(route('clients.index'))
                ->assertOk();
        }
    }

    public function test_hr_cannot_access_invoices(): void
    {
        $hr = $this->userWithRole('hr');

        $this->actingAs($hr)->get(route('invoices.index'))->assertForbidden();
    }

    public function test_owner_and_finance_can_access_invoices(): void
    {
        foreach (['owner', 'finance'] as $role) {
            $this->actingAs($this->userWithRole($role))
                ->get(route('invoices.index'))
                ->assertOk();
        }
    }

    public function test_hr_cannot_access_finance_area(): void
    {
        $hr = $this->userWithRole('hr');

        foreach ([
            route('finance.index'),
            route('incomes.index'),
            route('expenses.index'),
            route('expense-categories.index'),
            route('reports.index'),
        ] as $url) {
            $this->actingAs($hr)->get($url)->assertForbidden();
        }
    }

    public function test_owner_and_finance_can_access_finance_area(): void
    {
        foreach (['owner', 'finance'] as $role) {
            $user = $this->userWithRole($role);

            foreach ([
                route('finance.index'),
                route('incomes.index'),
                route('expenses.index'),
                route('expense-categories.index'),
            ] as $url) {
                $this->actingAs($user)->get($url)->assertOk();
            }

            // reports.index is excluded from the assertOk() list above: it
            // has a pre-existing, unrelated bug where its
            // withSum(...)->having(...) query fails on SQLite (works on
            // MySQL, which is what production actually runs). That's a
            // separate issue from authorization - here we only need to
            // confirm the Gate let the request through (i.e. it did NOT
            // get rejected with 403 before ever reaching the controller).
            $response = $this->actingAs($user)->get(route('reports.index'));
            $this->assertNotEquals(403, $response->getStatusCode());
        }
    }

    public function test_all_roles_can_access_hr_area(): void
    {
        foreach (['owner', 'finance', 'hr'] as $role) {
            $user = $this->userWithRole($role);

            $this->actingAs($user)->get(route('employees.index'))->assertOk();
            $this->actingAs($user)->get(route('payslips.index'))->assertOk();
        }
    }

    public function test_only_owner_can_access_user_management(): void
    {
        $owner = $this->userWithRole('owner');
        $this->actingAs($owner)->get(route('users.index'))->assertOk();

        foreach (['finance', 'hr'] as $role) {
            $this->actingAs($this->userWithRole($role))
                ->get(route('users.index'))
                ->assertForbidden();
        }
    }

    public function test_non_owner_cannot_bypass_authorization_by_posting_directly(): void
    {
        // Even without ever seeing the "Users" link in the nav, a direct
        // POST to create a user must be blocked for non-owners - this is
        // the exact privilege-escalation path the missing route
        // middleware allowed (an hr/finance user could self-promote to
        // owner by hitting the endpoint directly).
        $hr = $this->userWithRole('hr');

        $response = $this->actingAs($hr)->post(route('users.store'), [
            'name' => 'Escalated User',
            'username' => 'escalated',
            'password' => 'password',
            'password_confirmation' => 'password',
            'role' => 'owner',
        ]);

        $response->assertForbidden();
        $this->assertDatabaseMissing('users', ['username' => 'escalated']);
    }

    public function test_every_authenticated_role_can_manage_their_own_profile(): void
    {
        foreach (['owner', 'finance', 'hr'] as $role) {
            $this->actingAs($this->userWithRole($role))
                ->get(route('profile.edit'))
                ->assertOk();
        }
    }

    public function test_every_authenticated_role_can_see_the_dashboard(): void
    {
        foreach (['owner', 'finance', 'hr'] as $role) {
            $this->actingAs($this->userWithRole($role))
                ->get(route('dashboard'))
                ->assertOk();
        }
    }
}
