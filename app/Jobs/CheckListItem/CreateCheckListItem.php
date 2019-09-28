<?php

namespace Bhoechie\Checklist\Jobs\CheckListItem;

use Bhoechie\Checklist\Models\CheckList\CheckList;
use Bhoechie\Checklist\Models\CheckList\CheckListItem;

class CreateCheckListItem
{
    private $inputs;
    private $checklist;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(CheckList $checklist, $inputs = array())
    {
        $this->inputs = $inputs;
        $this->checklist = $checklist;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $payload = $this->inputs;
        $payload['created_by'] = app('auth')->user()->id;
        $checkListItem = $this->checklist->items()->create($payload);

        $checkListItem->refresh();

        return $checkListItem;
    }
}
