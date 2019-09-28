<?php

namespace Bhoechie\Checklist\Jobs\CheckListItem;

use Bhoechie\Checklist\Models\CheckList\CheckList;
use Bhoechie\Checklist\Models\CheckList\CheckListItem;

class UpdateCheckListItem
{
    private $inputs;
    private $checkListItem;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(CheckListItem $checkListItem, $inputs = array())
    {
        $this->inputs = $inputs;
        $this->checkListItem = $checkListItem;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $payload = $this->inputs;
        $payload['updated_by'] = app('auth')->user()->id;
        $this->checkListItem->update($payload);

        $this->checkListItem->refresh();

        return $this->checkListItem;
    }
}
