<?php

namespace Bhoechie\Checklist\Jobs\Template;

use Bhoechie\Checklist\Models\Template\Template;

class CreateTemplate
{
    private $inputs;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($inputs = array())
    {
        $this->inputs = $inputs;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $payload = $this->inputs['checklist'];
        $payload['name'] = $this->inputs['name'];
        $template = Template::create($payload);
        foreach ($this->inputs['items'] as $payloadItem) {
            $template->items()->create($payloadItem);
        }

        $template->load('items');

        return $template;
    }
}
