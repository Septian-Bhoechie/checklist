<?php

use Bhoechie\Checklist\Models\CheckList\CheckList;
use Bhoechie\Checklist\Models\CheckList\CheckListItem;
use Bhoechie\Checklist\Models\User;
use Laravel\Lumen\Testing\DatabaseTransactions;

class CheckListItemTest extends TestCase
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
        $checkList = factory(CheckList::class)->create([
            'created_by' => $user->id,
        ]);

        //with no auth
        $this
            ->post("api/checklists/{$checkList->id}/items");
        $this->assertEquals(
            401, $this->response->getStatusCode()
        );
        // with auth
        $this->actingAs($user)
            ->post("api/checklists/{$checkList->id}/items");
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
        $checkList = factory(CheckList::class)->create([
            'created_by' => $user->id,
        ]);

        //create items
        $payload = [
            "description" => "Need to verify this guy house.",
            "due" => "2019-01-19 18:34:51",
            "urgency" => 2,
            "assignee_id" => $user->id,
        ];
        $this->actingAs($user)
            ->post("api/checklists/{$checkList->id}/items", $payload);
        // dd($this->response->getContent());
        $this->assertEquals(
            200, $this->response->getStatusCode()
        );
        $checkListItem = CheckListItem::orderBy('id', 'desc')->first();
        $this->seeJsonEquals([
            'data' => $checkListItem->toArray(),
        ]);

        //show detail
        $this->actingAs($user)
            ->get("api/checklists/{$checkList->id}/items/{$checkListItem->id}", $payload);
        $this->assertEquals(
            200, $this->response->getStatusCode()
        );
        $this->seeJsonEquals([
            'data' => $checkListItem->toArray(),
        ]);

        //update data
        $payload = [
            "description" => "Need to verify this guy house2.",
            "due" => "2019-01-19 19:34:51",
            "urgency" => 1,
            "assignee_id" => $user->id,
        ];
        $this->actingAs($user)
            ->patch("api/checklists/{$checkList->id}/items/{$checkListItem->id}", $payload);
        $this->assertEquals(
            200, $this->response->getStatusCode()
        );
        $checkListItem->refresh();
        $this->seeJsonEquals([
            'data' => $checkListItem->toArray(),
        ]);

        //get paginate checklist with items
        $this->actingAs($user)
            ->get("api/checklists/{$checkList->id}/items", $payload);
        $this->assertEquals(
            200, $this->response->getStatusCode()
        );
        $checkList->refresh()->load('items');
        $this->seeJsonEquals([
            'data' => $checkList->toArray(),
        ]);

        //get paginate checklist items
        $this->actingAs($user)
            ->get("api/checklists/items");
        $this->assertEquals(
            200, $this->response->getStatusCode()
        );
        $checkListItems = CheckListItem::paginate(10);
        $response = [
            'meta' => [
                "count" => 10,
                "total" => $checkListItems->total(),
            ],
            "links" => [
                "first" => $checkListItems->url(1),
                "last" => $checkListItems->url($checkListItems->lastPage()),
                "next" => $checkListItems->nextPageUrl(),
                "prev" => $checkListItems->previousPageUrl(),
            ],
            "data" => $checkListItems->items(),
        ];

        $this->seeJsonEquals($response);

        //completing items
        $payload = [
            "data" => [],
        ];
        foreach ($checkListItems as $item) {
            array_push($payload['data'], ['item_id' => $item->id]);
        }
        $this->actingAs($user)
            ->post("api/checklists/complete", $payload);

        $this->assertEquals(
            200, $this->response->getStatusCode()
        );
    }
}
