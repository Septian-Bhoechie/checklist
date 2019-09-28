<?php

namespace Bhoechie\Checklist\Http\Controllers;

use Bhoechie\Checklist\Jobs\CheckList\CreateCheckList;
use Bhoechie\Checklist\Jobs\CheckList\UpdateCheckList;
use Bhoechie\Checklist\Models\CheckList\CheckList;
use Illuminate\Http\Request;

/**
 * CheckList controller.
 *
 * @author      bhoechie <septian.bhoechie@gmail.com>
 */
class CheckListController extends Controller
{

    /**
     * get checklist list
     * Route Path   : /api/checklists
     * Route Method : GET.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {

        $query = CheckList::query()->with('items');

        if ($keyword = $request->input('filter', false)) {
            $query->where('name', 'like', "%{$keyword}%")
                ->orWhere('description', 'like', "%{$keyword}%");
        }

        return response()->json($query->paginate($request->input('limit', 10))->appends($request->except('page')));
    }

    /**
     * create checklist Template
     * Route Path   : /api/checklists
     * Route Method : POST.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        //validation request is compatible with json payload, because
        //this function was overide on base controller
        $this->validate($request, [
            'object_id' => 'required|numeric|min:1',
            'object_domain' => 'required',
            'due' => 'required|date_format:Y-m-d\TH:i:sP',
            'urgency' => 'required|numeric|min:1',
            'description' => 'required',
            'task_id' => 'required|numeric|min:1',
        ]);

        $response = $this->dispatchNow(new CreateCheckList($this->input()));

        return response()->json($response);
    }

    /**
     * update checklist
     * Route Path   : /api/checklists
     * Route Method : PATCH.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $checklistId)
    {
        $checkList = CheckList::with('items')->find($checklistId);

        if ($checkList instanceof CheckList === false) {
            abort(404);
        }

        //validation request is compatible with json payload, because
        //this function was overide on base controller
        $this->validate($request, [
            'object_id' => 'required|numeric|min:1',
            'object_domain' => 'required',
            'due' => 'required|date_format:Y-m-d\TH:i:sP',
            'urgency' => 'required|numeric|min:1',
            'description' => 'required',
            'task_id' => 'required|numeric|min:1',
        ]);

        $response = $this->dispatchNow(new UpdateCheckList($checkList, $this->input()));

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
    public function show(Request $request, $checklistId)
    {
        $checkList = CheckList::with('items')->find($checklistId);

        if ($checkList instanceof CheckList === false) {
            abort(404);
        }

        return response()->json($checkList);
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
