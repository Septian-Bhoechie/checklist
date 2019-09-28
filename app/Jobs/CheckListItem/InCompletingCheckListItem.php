<?php

namespace Bhoechie\Checklist\Jobs\CheckListItem;

use Bhoechie\Checklist\Models\CheckList\CheckList;
use Bhoechie\Checklist\Models\CheckList\CheckListItem;

class InCompletingCheckListItem
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
        $result = [];
        $ids = [];
        foreach ($this->inputs['data'] as $key => $value) {
            array_push($ids, $value['item_id']);
        }
        $items = CheckListItem::whereIn('id', $ids)->get();

        foreach ($items as $item) {
            $item->update([
                'is_completed' => true,
            ]);
            array_push($result, [
                "id" => $item->id,
                "item_id" => $item->id,
                "is_completed" => false,
                "checklist_id" => $item->checklist_id,
            ]);
        }

        return $result;
    }
}
