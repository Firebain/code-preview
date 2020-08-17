<?php

namespace Tests\Unit;

use App\Models\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserTest extends TestCase
{
    private $lines;
    private $transaction;

    public function setUp(): void {
        parent::setUp();

        $this->artisan('migrate:fresh');

        $this->seed();

        $createUser = function ($referral) {
            return factory(User::class)->create([
                'referral' => $referral
            ]);
        };

        $lines[5] = $createUser(1);
        $lines[4] = $createUser($lines[5]->id);
        $lines[3] = $createUser($lines[4]->id);
        $lines[2] = $createUser($lines[3]->id);
        $lines[1] = $createUser($lines[2]->id);

        $lines['root'] = $createUser($lines[1]->id);

        $this->lines = collect($lines);

        $this->transaction = $lines['root']->transactions()->create([
            'sum' => 100,
            'main_service_id' => 1
        ]);
    }

    public function testAddPercentAllWithSubscribtion()
    {
        $this->lines->each(function ($user) {
            $user->subscriptions()->create([
                'transaction_id' => $this->transaction->id,
                'ends_at' => now()->addMonth()
            ]);
        });

        $this->lines['root']->addFollowersPercent(100);

        $this->refreshLines();

        $this->assertEquals(20, $this->lines[1]->current_amount);
        $this->assertEquals(5, $this->lines[2]->current_amount);
        $this->assertEquals(3, $this->lines[3]->current_amount);
        $this->assertEquals(3, $this->lines[4]->current_amount);
        $this->assertEquals(0, $this->lines[5]->current_amount);
    }

    public function testAddPercentOneWithoutSubscribtion()
    {
        $this->lines->each(function ($user) {
            $user->subscriptions()->create([
                'transaction_id' => $this->transaction->id,
                'ends_at' => now()->addMonth()
            ]);
        });

        $this->lines[3]->subscriptions()->first()->delete();

        $this->lines['root']->addFollowersPercent(100);

        $this->refreshLines();

        $this->assertEquals(20, $this->lines[1]->current_amount);
        $this->assertEquals(5, $this->lines[2]->current_amount);
        $this->assertEquals(0, $this->lines[3]->current_amount);
        $this->assertEquals(3, $this->lines[4]->current_amount);
        $this->assertEquals(0, $this->lines[5]->current_amount);

        $this->assertTrue(true);
    }

    private function refreshLines()
    {
        $this->lines->each(function ($line) { $line->refresh(); });
    }
}
