<?php

use Bhoechie\Checklist\Models\CheckList\CheckList;
use Bhoechie\Checklist\Models\User;
use Laravel\Lumen\Testing\DatabaseTransactions;

class CheckListTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * A basic test.
     *
     * @return void
     */
    public function testValidation()
    {
        $user = factory(User::class)->create();

        //with no auth
        $this
            ->post('api/checklists');
        $this->assertEquals(
            401, $this->response->getStatusCode()
        );
        // with auth
        $this->actingAs($user)
            ->post('api/checklists');
        $this->assertEquals(
            422, $this->response->getStatusCode()
        );
    }

    /**
     * A basic test.
     *
     * @return void
     */
    public function testCreateAndUpdate()
    {
        $user = factory(User::class)->create();
        $payload = [
            "object_domain" => "contact",
            "object_id" => 1,
            "task_id" => 123,
            "due" => "2019-01-25T12:50:14+00:00",
            "urgency" => 1,
            "description" => "Need to verify this guy house.",
        ];
        // with auth
        $this->actingAs($user)
            ->post('api/checklists', $payload);
        $this->assertEquals(
            200, $this->response->getStatusCode()
        );

        $checkList = CheckList::orderBy('id', 'desc')->first();
        $this->seeJsonEquals([
            'data' => $checkList->toArray(),
        ]);

    }
}
