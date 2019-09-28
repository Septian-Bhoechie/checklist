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

        // dd($this->response->getContent());

        $checkList = CheckList::orderBy('id', 'desc')->first();
        $this->seeJsonEquals([
            'data' => $checkList->toArray(),
        ]);

        //show detail
        $this->actingAs($user)
            ->get("api/checklists/{$checkList->id}");
        $this->assertEquals(
            200, $this->response->getStatusCode()
        );

        //updating checklist
        $payload = [
            "object_domain" => "contact2",
            "object_id" => 1,
            "task_id" => 122,
            "due" => "2019-01-25T12:50:14+00:00",
            "urgency" => 1,
            "description" => "Need to verify this guy house2.",
        ];
        $this->actingAs($user)
            ->patch("api/checklists/{$checkList->id}", $payload);
        $checkList->refresh();
        $this->seeJsonEquals([
            'data' => $checkList->toArray(),
        ]);

        //check pagination
        $this->actingAs($user)
            ->get("api/checklists");
        $this->assertEquals(
            200, $this->response->getStatusCode()
        );

        $checkLists = CheckList::query()->with('items')->paginate(10);
        $response = [
            'meta' => [
                "count" => 10,
                "total" => $checkLists->total(),
            ],
            "links" => [
                "first" => $checkLists->url(1),
                "last" => $checkLists->url($checkLists->lastPage()),
                "next" => $checkLists->nextPageUrl(),
                "prev" => $checkLists->previousPageUrl(),
            ],
            "data" => $checkLists->items(),
        ];

        $this->seeJsonEquals($response);

        //delete
        $this->actingAs($user)
            ->delete("api/checklists/{$checkList->id}");
        $this->assertEquals(
            204, $this->response->getStatusCode()
        );
    }
}
