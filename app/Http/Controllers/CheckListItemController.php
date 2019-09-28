<?php

namespace Bhoechie\Checklist\Http\Controllers;

use Bhoechie\Checklist\Jobs\CheckListItem\CompletingCheckListItem;
use Bhoechie\Checklist\Jobs\CheckListItem\CreateCheckListItem;
use Bhoechie\Checklist\Jobs\CheckListItem\InCompletingCheckListItem;
use Bhoechie\Checklist\Jobs\CheckListItem\UpdateCheckListItem;
use Bhoechie\Checklist\Models\CheckList\CheckList;
use Bhoechie\Checklist\Models\CheckList\CheckListItem;
use Illuminate\Http\Request;

/**
 * CheckList Item controller.
 *
 * @author      bhoechie <septian.bhoechie@gmail.com>
 */
class CheckListItemController extends Controller
{

    /**
     * get checklist list
     * Route Path   : /api/checklists/items
     * Route Method : GET.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {

        $query = CheckListItem::query();

        if ($keyword = $request->input('filter', false)) {
            $query->where('name', 'like', "%{$keyword}%")
                ->orWhere('description', 'like', "%{$keyword}%");
        }

        return response()->json($query->paginate($request->input('limit', 10))->appends($request->except('page')));
    }

    /**
     * get checklist list items
     * Route Path   : /api/checklists{checklist_id}/items
     * Route Method : GET.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function items(Request $request, $checklistId)
    {

        $checkList = CheckList::with('items')->find($checklistId);

        if ($checkList instanceof CheckList === false) {
            abort(404);
        }

        return response()->json($checkList);
    }

    /**
     * create checklist Template
     * Route Path   : /api/checklists/{checklist_id}
     * Route Method : POST.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request, $checklistId)
    {
        $checkList = CheckList::find($checklistId);

        if ($checkList instanceof CheckList === false) {
            abort(404);
        }

        //validation request is compatible with json payload, because
        //this function was overide on base controller
        $this->validate($request, [
            'due' => 'required|date_format:Y-m-d H:i:s',
            'urgency' => 'required|numeric|min:1',
            'description' => 'required',
            // 'task_id' => 'required|numeric|min:1',
            'assignee_id' => 'required|exists:users,id',
        ]);

        $response = $this->dispatchNow(new CreateCheckListItem($checkList, $this->input()));

        return response()->json($response);
    }

    /**
     * update checklist
     * Route Path   : /api/checklists/{checklist_id}/items/{checklistItem_id}
     * Route Method : PATCH.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $checklistId, $checklistItemId)
    {
        $checkListItem = CheckListItem::where('checklist_id', $checklistId)
            ->find($checklistItemId);

        if ($checkListItem instanceof CheckListItem === false) {
            abort(404);
        }

        //validation request is compatible with json payload, because
        //this function was overide on base controller
        $this->validate($request, [
            'due' => 'required|date_format:Y-m-d H:i:s',
            'urgency' => 'required|numeric|min:1',
            'description' => 'required',
            // 'task_id' => 'required|numeric|min:1',
            'assignee_id' => 'required|exists:users,id',
        ]);

        $response = $this->dispatchNow(new UpdateCheckListItem($checkListItem, $this->input()));

        return response()->json($response);
    }

    /**
     * complete checklist
     * Route Path   : /api/checklists/complete
     * Route Method : PATCH.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function complete(Request $request)
    {
        //validation request is compatible with json payload, because
        //this function was overide on base controller
        $this->validate($request, [
            'data' => 'required|array',
            'data.*.item_id' => 'required|exists:checklist_items,id',
        ]);
        $response = $this->dispatchNow(new CompletingCheckListItem($this->input()));

        return response()->json($response);
    }

    /**
     * incomplete checklist
     * Route Path   : /api/checklists/incomplete
     * Route Method : PATCH.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function incomplete(Request $request)
    {
        //validation request is compatible with json payload, because
        //this function was overide on base controller
        $this->validate($request, [
            'data' => 'required|array',
            'data.*.item_id' => 'required|exists:checklist_items,id',
        ]);
        $response = $this->dispatchNow(new InCompletingCheckListItem($this->input()));

        return response()->json($response);
    }

    /**
     * show checklist
     * Route Path   : /api/checklists/{templateId}
     * Route Method : POST.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Request $request, $checklistId, $checklistItemId)
    {
        $checkListItem = CheckListItem::where('checklist_id', $checklistId)
            ->find($checklistItemId);

        if ($checkListItem instanceof CheckListItem === false) {
            abort(404);
        }

        return response()->json($checkListItem);
    }

    /**
     * delete checklist
     * Route Path   : /api/checklists/{checklistId}
     * Route Method : DELETE.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete(Request $request, $checklistId)
    {
        $checkList = CheckList::with('items')->find($checklistId);

        if ($checkList instanceof CheckList === false) {
            abort(404);
        }
        $deleted = $checkList->delete();

        return response()->json('delete-success', 204);
    }
}
