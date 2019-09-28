<?php

namespace Bhoechie\Checklist\Jobs\CheckList;

use Bhoechie\Checklist\Models\CheckList\CheckList;

class UpdateCheckList
{
    private $inputs;
    private $checkList;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(CheckList $checkList, $inputs = array())
    {
        $this->inputs = $inputs;
        $this->checkList = $checkList;
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
        $this->checkList->update($payload);

        $this->checkList->refresh();

        return $this->checkList;
    }
}
