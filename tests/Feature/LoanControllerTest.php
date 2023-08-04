<?php
declare(strict_types=1);

namespace Tests\Feature;

use App\Domain\Entities\Loan\Loan;
use App\Domain\Entities\Loan\ScheduledRepayment;
use App\Domain\Entities\User\Role;
use App\Domain\Entities\User\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoanControllerTest extends TestCase
{
    use RefreshDatabase;

    public function testStore()
    {
        $user = User::factory()->create();
        $data = [
            'amount' => 10000,
            'term' => 3,
            'request_date' => Carbon::parse('2022-02-07'),
        ];

        $response = $this->actingAs($user, 'sanctum')->postJson('/api/loans', $data);

        $response->assertStatus(201)
            ->assertJsonStructure(['amount', 'term', 'request_date', 'status', 'customer_id', 'scheduled_repayments']);

        // Ensure that the loan and scheduled repayments are created in the database
        $loanId = $response->json('id');
        $this->assertDatabaseHas('loans', [
            'id' => $loanId,
            'amount' => 10000,
            'term' => 3,
            'request_date' => Carbon::parse('2022-02-07')->toDateString(),
            'status' => 'PENDING',
            'customer_id' => $user->id,
        ]);
        $this->assertDatabaseCount('scheduled_repayments', 3);
        $this->assertDatabaseHas('scheduled_repayments', [
            'loan_id' => $loanId,
            'status' => 'PENDING',
        ]);

        // Test with invalid loan request data (missing fields)
        $invalidData = [
            'amount' => 5000,
        ];

        $response = $this->actingAs($user, 'sanctum')->postJson('/api/loans', $invalidData);
        $response->assertStatus(422);

        // Ensure that no loan or scheduled repayment is created in the database for the invalid request
        $this->assertDatabaseCount('loans', 1);
        $this->assertDatabaseCount('scheduled_repayments', 3);
    }

    public function testStoreWithWrongData()
    {
        /**
         * Test with invalid loan request data (amount is less than 1)
         */
        $user = User::factory()->create();
        $data = [
            'amount' => 0,
            'term' => 3,
            'request_date' => Carbon::parse('2022-02-07'),
        ];
        $response = $this->actingAs($user, 'sanctum')->postJson('/api/loans', $data);
        $response->assertStatus(422);

        /**
         * Test with invalid loan request data (amount is not an number)
         */
        $user = User::factory()->create();
        $data = [
            'amount' => 'abc',
            'term' => 3,
            'request_date' => Carbon::parse('2022-02-07'),
        ];
        $response = $this->actingAs($user, 'sanctum')->postJson('/api/loans', $data);
        $response->assertStatus(422);

        // Ensure that no loan or scheduled repayment is created in the database for the invalid request
        $this->assertDatabaseCount('loans', 0);
        $this->assertDatabaseCount('scheduled_repayments', 0);

        $user = User::factory()->create();
        $data = [
            'amount' => null,
            'term' => 3,
            'request_date' => Carbon::parse('2022-02-07'),
        ];
        $response = $this->actingAs($user, 'sanctum')->postJson('/api/loans', $data);
        $response->assertStatus(422);
        // Ensure that no loan or scheduled repayment is created in the database for the invalid request
        $this->assertDatabaseCount('loans', 0);
        $this->assertDatabaseCount('scheduled_repayments', 0);

        /**
         * Test with invalid loan request data (term is less than 1)
         */
        $user = User::factory()->create();
        $data = [
            'amount' => 10000,
            'term' => 0,
            'request_date' => Carbon::parse('2022-02-07'),
        ];
        $response = $this->actingAs($user, 'sanctum')->postJson('/api/loans', $data);
        $response->assertStatus(422);

        // Ensure that no loan or scheduled repayment is created in the database for the invalid request
        $this->assertDatabaseCount('loans', 0);
        $this->assertDatabaseCount('scheduled_repayments', 0);

        /**
         * Test with invalid loan request data (term is not an number)
         */
        $user = User::factory()->create();
        $data = [
            'amount' => 10000,
            'term' => null,
            'request_date' => Carbon::parse('2022-02-07'),
        ];
        $response = $this->actingAs($user, 'sanctum')->postJson('/api/loans', $data);
        $response->assertStatus(422);

        $data = [
            'amount' => 10000,
            'term' => 'acb',
            'request_date' => '2022-02-07',
        ];
        $response = $this->actingAs($user, 'sanctum')->postJson('/api/loans', $data);
        $response->assertStatus(422);

        // Ensure that no loan or scheduled repayment is created in the database for the invalid request
        $this->assertDatabaseCount('loans', 0);
        $this->assertDatabaseCount('scheduled_repayments', 0);

        /**
         * Test with invalid loan request data (invalid request date format)
         */
        $user = User::factory()->create();
        $data = [
            'amount' => 10000,
            'term' => 3,
            'request_date' => null,
        ];

        $response = $this->actingAs($user, 'sanctum')->postJson('/api/loans', $data);

        $response->assertStatus(422);

        // Ensure that no loan or scheduled repayment is created in the database for the invalid request
        $this->assertDatabaseCount('loans', 0);
        $this->assertDatabaseCount('scheduled_repayments', 0);
    }

    public function testApproveSuccess()
    {
        $user = User::factory()->create();
        $role = Role::factory()->create(['name' => 'admin']);
        $user->roles()->attach($role->id);
        $loan = Loan::factory()->create(['status' => Loan::STATUS_PENDING]);

        $response = $this->actingAs($user, 'sanctum')->putJson("/api/loans/{$loan->id}/approve");
        $response->assertStatus(200)
            ->assertJson(['message' => 'Loan approved successfully.']);

        // ensure that the loan status is updated to approved
        $this->assertDatabaseHas('loans', [
            'id' => $loan->id,
            'status' => Loan::STATUS_APPROVED
        ]);
    }

    public function testApproveFail()
    {
        /**
         * Test with approved loan
         */
        $user = User::factory()->create();
        $role = Role::factory()->create(['name' => 'admin']);
        $user->roles()->attach($role->id);
        $loan = Loan::factory()->create(['status' => Loan::STATUS_APPROVED]);

        $response = $this->actingAs($user, 'sanctum')->putJson("/api/loans/{$loan->id}/approve");
        $response->assertStatus(422)
            ->assertJson(['message' => 'Loan has already been approved.']);
        // ensure that the loan status still remains approved
        $this->assertDatabaseHas('loans', [
            'id' => $loan->id,
            'status' => Loan::STATUS_APPROVED
        ]);

        $loan = Loan::factory()->create(['status' => Loan::STATUS_PAID]);
        $response = $this->actingAs($user, 'sanctum')->putJson("/api/loans/{$loan->id}/approve");
        $response->assertStatus(422)
            ->assertJson(['message' => 'Loan has already been approved.']);

        // ensure that the loan status is not updated to approved
        $this->assertDatabaseHas('loans', [
            'id' => $loan->id,
            'status' => Loan::STATUS_PAID
        ]);

        /**
         * Test with non admin user
         */
        $user = User::factory()->create();
        $loan = Loan::factory()->create(['status' => Loan::STATUS_PENDING]);
        $response = $this->actingAs($user, 'sanctum')->putJson("/api/loans/{$loan->id}/approve");
        $response->assertStatus(403)
            ->assertJson(['message' => 'Only admins can approve loans.']);
    }

    public function testViewCustomerLoans()
    {
        $user = User::factory()->create();
        $loans = Loan::factory()->count(3)->create(['customer_id' => $user->id]);

        $response = $this->actingAs($user, 'sanctum')->getJson('/api/loans');

        $response->assertStatus(200)
            ->assertJsonCount(3)
            ->assertJsonStructure([
                ['amount', 'term', 'request_date', 'status', 'customer_id', 'scheduled_repayments']
            ]);

        // Ensure that the loans returned are for the authenticated user
        $response->assertJsonFragment(['customer_id' => $user->id]);
    }

    public function testShow()
    {
        $user = User::factory()->create();
        $loan = Loan::factory()->create(['customer_id' => $user->id]);

        $response = $this->actingAs($user, 'sanctum')->getJson("/api/loans/{$loan->id}");
        $response->assertStatus(200)
            ->assertJsonStructure(['amount', 'term', 'request_date', 'status', 'customer_id', 'scheduled_repayments']);

        // Ensure that the loan returned is correct
        $response->assertJsonFragment(['id' => $loan->id]);
    }

    public function testShowForAdmin()
    {
        $customer = User::factory()->create();
        $loan = Loan::factory()->create(['customer_id' => $customer->id]);

        $admin = User::factory()->create();
        $role = Role::factory()->create(['name' => Role::ADMIN]);
        $admin->roles()->attach($role->id);

        $response = $this->actingAs($admin, 'sanctum')->getJson("/api/loans/{$loan->id}");

        $response->assertStatus(200)
            ->assertJsonStructure(['amount', 'term', 'request_date', 'status', 'customer_id', 'scheduled_repayments']);
        // Ensure that the loan returned is correct
        $response->assertJsonFragment(['id' => $loan->id]);
    }

    public function testShowFailForNonExistentLoan()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user, 'sanctum')->getJson("/api/loans/1");

        $response->assertStatus(404)
            ->assertJson(['message' => 'Loan not found.']);
    }

    public function testShowFailForNotIsCustomerOfLoan()
    {
        $user = User::factory()->create();
        $loan = Loan::factory()->create();

        $response = $this->actingAs($user, 'sanctum')->getJson("/api/loans/{$loan->id}");

        $response->assertStatus(403)
            ->assertJson(['message' => 'This action is unauthorized.']);
    }

    public function testAddRepayment()
    {
        $user = User::factory()->create();
        $data = [
            'amount' => 10000,
            'term' => 3,
            'request_date' => Carbon::parse('2022-02-07'),
        ];

        $createLoanResponse = $this->actingAs($user, 'sanctum')->postJson('/api/loans', $data);
        $loan = $createLoanResponse->json();
        $scheduledRepayment = ScheduledRepayment::where('loan_id', $loan['id'])->first();
        $data = [
            'amount' => 10000,
        ];

        $response = $this->actingAs($user, 'sanctum')
            ->putJson("/api/loans/{$loan['id']}/scheduled-repayments/{$scheduledRepayment->id}/repayment", $data);
        $response->assertStatus(200)
            ->assertJson(['message' => 'Repayment added successfully.']);
    }

    public function testAddRepaymentTwice()
    {
        $user = User::factory()->create();
        $data = [
            'amount' => 10000,
            'term' => 3,
            'request_date' => Carbon::parse('2022-02-07'),
        ];

        $createLoanResponse = $this->actingAs($user, 'sanctum')->postJson('/api/loans', $data);
        $loan = $createLoanResponse->json();
        $scheduledRepayment = ScheduledRepayment::where('loan_id', $loan['id'])->first();
        $data = [
            'amount' => 10000,
        ];

        $response = $this->actingAs($user, 'sanctum')
            ->putJson("/api/loans/{$loan['id']}/scheduled-repayments/{$scheduledRepayment->id}/repayment", $data);
        $response->assertStatus(200)
            ->assertJson(['message' => 'Repayment added successfully.']);

        $response = $this->actingAs($user, 'sanctum')
            ->putJson("/api/loans/{$loan['id']}/scheduled-repayments/{$scheduledRepayment->id}/repayment", $data);
        $response->assertStatus(422)
            ->assertJson(['message' => 'Repayment has already been added.']);
    }

    public function testAddRepaymentForNotCustomer()
    {
        $user = User::factory()->create();
        $data = [
            'amount' => 10000,
            'term' => 3,
            'request_date' => Carbon::parse('2022-02-07'),
        ];

        $createLoanResponse = $this->actingAs($user, 'sanctum')->postJson('/api/loans', $data);
        $loan = $createLoanResponse->json();
        $scheduledRepayment = ScheduledRepayment::where('loan_id', $loan['id'])->first();
        $data = [
            'amount' => 10000,
        ];

        $user2 = User::factory()->create();
        $response = $this->actingAs($user2, 'sanctum')
            ->putJson("/api/loans/{$loan['id']}/scheduled-repayments/{$scheduledRepayment->id}/repayment", $data);
        $response->assertStatus(403)
            ->assertJson(['message' => 'This action is unauthorized.']);
    }
}
