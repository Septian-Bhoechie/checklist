<?php

namespace Bhoechie\Checklist\Jobs\Template;

use Bhoechie\Checklist\Models\CheckList\CheckList;
use Bhoechie\Checklist\Models\Template\Template;
use Carbon\Carbon;

class AssignTemplate
{
    private $template;
    private $checkList;
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
        $payload = $this->inputs['data'];
        $checkIds = [];
        foreach ($payload as $payloadItem) {
            $payload = $payloadItem['attributes'];

            $payload['description'] = $this->template->description;
            $timing = ucfirst($this->template->due_unit);
            $payload['due'] = Carbon::now()->{"add{$timing}s"}($this->template->due_interval);
            $payload['created_by'] = app('auth')->user()->id;

            $checklist = CheckList::create($payload);
            array_push($checkIds, $checklist->id);

            foreach ($this->template->items as $item) {
                $timing = ucfirst($item->due_unit);
                $payload = [
                    'description' => $item->description,
                    'urgency' => $item->urgency,
                    'due' => Carbon::now()->{"add{$timing}s"}($item->due_interval),
                    'created_by' => app('auth')->user()->id,
                ];
                $checklist->items()->create($payload);
            }
        }

        $this->checkList = CheckList::with('items')->whereIn('id', $checkIds)->paginate(10);

        return $this->checkList;
    }
}
