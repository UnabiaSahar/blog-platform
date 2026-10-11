<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    /**
     * Seed the application's users, including representative billing states.
     */
    public function run(): void
    {
        User::factory()->admin()->create([
            'name' => 'Platform Owner',
            'email' => 'admin@example.com',
        ]);

        User::factory()->author()->create([
            'name' => 'Test Author',
            'email' => 'author@example.com',
        ]);

        User::factory()->create([
            'name' => 'Free Reader',
            'email' => 'reader@example.com',
        ]);

        $this->subscribe(User::factory()->create([
            'name' => 'Active Subscriber',
            'email' => 'subscriber@example.com',
        ]));

        $this->subscribe(
            User::factory()->create([
                'name' => 'Trial Subscriber',
                'email' => 'trialing@example.com',
            ]),
            [
                'stripe_status' => 'trialing',
                'trial_ends_at' => now()->addDays(config('billing.trial_days')),
            ],
        );

        $this->subscribe(
            User::factory()->create([
                'name' => 'Lapsed Subscriber',
                'email' => 'lapsed@example.com',
            ]),
            [
                'ends_at' => now()->subDay(),
            ],
        );
    }

    /**
     * Attach a fake (non-Stripe) subscription to the given user.
     *
     * @param  array<string, mixed>  $attributes
     */
    private function subscribe(User $user, array $attributes = []): void
    {
        $user->forceFill(['stripe_id' => 'cus_'.Str::random(24)])->save();

        $user->subscriptions()->create(array_merge([
            'type' => 'default',
            'stripe_id' => 'sub_'.Str::random(24),
            'stripe_status' => 'active',
            'stripe_price' => config('billing.plans.monthly.stripe_price') ?: 'price_monthly_demo',
            'quantity' => 1,
        ], $attributes));
    }
}
