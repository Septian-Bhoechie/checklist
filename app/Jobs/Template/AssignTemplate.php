<?php

namespace Bhoechie\Checklist\Jobs\Template;

use Bhoechie\Checklist\Models\Template\Template;

class AssignTemplate
{
    private $template;
    private $inputs;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($template, $inputs = array())
    {
        $this->template = $template;
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
        $this->template->update($payload);

        if (count($this->inputs['items']) > 0) {
            //deleting existing items when there are items on request payload
            $this->template->items()->delete();
        }

        foreach ($this->inputs['items'] as $payloadItem) {
            $this->template->items()->create($payloadItem);
        }

        $this->template->load('items');

        return $this->template;
    }
}
