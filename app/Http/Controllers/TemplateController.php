<?php

namespace Bhoechie\Checklist\Http\Controllers;

use Bhoechie\Checklist\Jobs\Template\CreateTemplate;
use Bhoechie\Checklist\Models\Template\Template;
use Bhoechie\Checklist\Models\User;
use Illuminate\Http\Request;

/**
 * Template controller.
 *
 * @author      bhoechie <septian.bhoechie@gmail.com>
 */
class TemplateController extends Controller
{
    private $dueUnit = ['second', 'minute', 'hour', 'day', 'month', 'year'];

    /**
     * get checklist list Template
     * Route Path   : /api/checklists/templates
     * Route Method : GET.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {

        $query = Template::query()->with('items');

        if ($keyword = $request->input('filter', false)) {
            $query->where('name', 'like', "%{$keyword}%")
                ->orWhere('description', 'like', "%{$keyword}%");
        }

        return response()->json($query->paginate($request->input('limit', 10))->appends($request->except('page')));
    }

    /**
     * create checklist Template
     * Route Path   : /api/checklists/templates
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
            'name' => 'required',
            'checklist' => 'required|array',
            'checklist.description' => 'required',
            'checklist.due_interval' => 'required|numeric|min:1',
            'checklist.due_unit' => 'required|in:' . implode(',', $this->dueUnit),
            'items' => 'required|array',
            'items.*.description' => 'required',
            'items.*.urgency' => 'required|numeric|min:1',
            'items.*.due_interval' => 'required|numeric|min:1',
            'items.*.due_unit' => 'required|in:' . implode(',', $this->dueUnit),
        ]);

        $response = $this->dispatchNow(new CreateTemplate($this->input()));

        return response()->json($response);
    }

    /**
     * show template
     * Route Path   : /api/user/show
     * Route Method : POST.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Request $request, $templateId)
    {
        $template = Template::find($templateId);

        if ($template instanceof Template === false) {
            abort(404);
        }

        return response()->json($template);
    }
}
