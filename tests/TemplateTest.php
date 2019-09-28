<?php

use Bhoechie\Checklist\Models\Template\Template;
use Bhoechie\Checklist\Models\User;
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
    public function testCreateAndGet()
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
        $payload['id'] = $idTemplate;
        $this->seeJsonEquals([
            'data' => [
                'attributes' => $payload,
            ],
        ]);

        $this->actingAs($user)
            ->get('api/checklists/templates');

        $this->assertEquals(
            200, $this->response->getStatusCode()
        );

        $paginate = Template::query()->with('items')->paginate(10);
        $response = [
            'meta' => [
                "count" => 10,
                "total" => $paginate->total(),
            ],
            "links" => [
                "first" => $paginate->url(1),
                "last" => $paginate->url($paginate->lastPage()),
                "next" => $paginate->nextPageUrl(),
                "prev" => $paginate->previousPageUrl(),
            ],
            "data" => $paginate->items(),
        ];

        $this->seeJsonEquals($response);

        $this->actingAs($user)
            ->get("api/checklists/templates/{$paginate->items()->first()->id}");

        $this->assertEquals(
            200, $this->response->getStatusCode()
        );
    }
}
