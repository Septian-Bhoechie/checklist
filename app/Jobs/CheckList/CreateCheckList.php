<?php

namespace Bhoechie\Checklist\Jobs\CheckList;

use Bhoechie\Checklist\Models\CheckList\CheckList;

class CreateCheckList
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
        $payload = $this->inputs;
        $payload['created_by'] = app('auth')->user()->id;
        $checkList = CheckList::create($payload);

        $checkList->refresh();

        return $checkList;
    }
}
