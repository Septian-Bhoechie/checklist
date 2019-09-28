<?php

use Bhoechie\Checklist\Models\CheckList\CheckList;
use Bhoechie\Checklist\Models\Template\Template;
use Bhoechie\Checklist\Models\User;
use Illuminate\Support\Arr;
use Laravel\Lumen\Testing\DatabaseTransactions;

class TemplateTest extends TestCase
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
            ->post('api/checklists/templates');
        $this->assertEquals(
            401, $this->response->getStatusCode()
        );
        // with auth
        $this->actingAs($user)
            ->post('api/checklists/templates');
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
            'name' => "foo template",
            "checklist" => [
                "description" => "my checklist",
                "due_interval" => 3,
                "due_unit" => "hour",
            ],
            "items" => [
                [
                    "description" => "my foo item",
                    "urgency" => 2,
                    "due_interval" => 40,
                    "due_unit" => "minute",
                ],
                [
                    "description" => "my bar item",
                    "urgency" => 3,
                    "due_interval" => 30,
                    "due_unit" => "minute",
                ],
            ],
        ];
        // with auth
        $this->actingAs($user)
            ->post('api/checklists/templates', $payload);
        $this->assertEquals(
            200, $this->response->getStatusCode()
        );

        $idTemplate = Template::max('id');
        $this->seeJsonEquals([
            'data' => [
                'id' => $idTemplate,
                'type' => 'templates',
                'attributes' => $payload,
            ],
        ]);

        $this->actingAs($user)
            ->get('api/checklists/templates');

        $this->assertEquals(
            200, $this->response->getStatusCode()
        );

        $templates = Template::query()->with('items')->paginate(10);
        $response = [
            'meta' => [
                "count" => 10,
                "total" => $templates->total(),
            ],
            "links" => [
                "first" => $templates->url(1),
                "last" => $templates->url($templates->lastPage()),
                "next" => $templates->nextPageUrl(),
                "prev" => $templates->previousPageUrl(),
            ],
            "data" => $templates->items(),
        ];

        $this->seeJsonEquals($response);

        $this->actingAs($user)
            ->get("api/checklists/templates/{$templates->first()->id}");

        $this->assertEquals(
            200, $this->response->getStatusCode()
        );
        $response = [
            "data" => [
                'id' => $templates->first()->id,
                'type' => 'templates',
                'attributes' => Arr::except($templates->first()->attributes, ['id']),
            ],
        ];
        $this->seeJsonEquals($response);

        //update template
        $payload = [
            'name' => "foo template2",
            "checklist" => [
                "description" => "my checklist2",
                "due_interval" => 2,
                "due_unit" => "hour",
            ],
            "items" => [
                [
                    "description" => "my foo item2",
                    "urgency" => 1,
                    "due_interval" => 20,
                    "due_unit" => "minute",
                ],
                [
                    "description" => "my bar item2",
                    "urgency" => 2,
                    "due_interval" => 20,
                    "due_unit" => "minute",
                ],
            ],
        ];
        $this->actingAs($user)
            ->patch("api/checklists/templates/{$templates->first()->id}", $payload);
        $this->assertEquals(
            200, $this->response->getStatusCode()
        );
        $response = [
            "data" => [
                'id' => $templates->first()->id,
                'type' => 'templates',
                'attributes' => $payload,
            ],
        ];
        $this->seeJsonEquals($response);

        //assign template
        $payload = [
            'data' => [
                [
                    "attributes" => [
                        "object_id" => 1,
                        "object_domain" => "deals",
                    ],
                ],
                [
                    "attributes" => [
                        "object_id" => 2,
                        "object_domain" => "deals",
                    ],
                ],
                [
                    "attributes" => [
                        "object_id" => 3,
                        "object_domain" => "deals",
                    ],
                ],
            ],
        ];

        $this->actingAs($user)
            ->post("api/checklists/templates/{$templates->first()->id}/assigns", $payload);
        $this->assertEquals(
            200, $this->response->getStatusCode()
        );

        $checklists = CheckList::with('items')->paginate(10);
        $response = [
            'meta' => [
                "count" => 10,
                "total" => $checklists->total(),
            ],
            "links" => [
                "first" => $checklists->url(1),
                "last" => $checklists->url($templates->lastPage()),
                "next" => $checklists->nextPageUrl(),
                "prev" => $checklists->previousPageUrl(),
            ],
            "data" => $checklists->items(),
        ];
        $this->seeJsonEquals($response);

        //deleting template
        $this->actingAs($user)
            ->delete("api/checklists/templates/{$templates->first()->id}");
        $this->assertEquals(
            204, $this->response->getStatusCode()
        );

    }
}
