<?php

namespace App\Http\Controllers\hierarchy;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\general\Groups;
use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Country;

class GroupController extends Controller
{
    public function index(Request $request)
    {
        // $this->authorize('complaints');
        $ids         = $request->bulk_ids;
        $search      = $request['search'];
        $query_param = $search ? ['search' => $request['search']] : '';
        $groups = (new Groups())->setConnection('tenant')->query()
            // ->when(empty($request['search']), function ($q) {
            //     $q->parent();  
            // })
            ->when($request['search'], function ($q) use ($request) {
                $key = explode(' ', $request['search']);
                foreach ($key as $value) {
                    $q->where('name', 'like', "%{$value}%")
                        ->orWhere('id', $value);
                }
            })
            ->latest()
            ->paginate()
            ->appends($query_param);
        $all_groups      = (new Groups())->setConnection('tenant')->get();
        $company = (new Company())->setConnection('tenant')->first();
        $data = [
            'company' => $company,
            'main'   => $groups,
            'search' => $search,
            'all_groups' => $all_groups,

        ];
        return view("admin-views.hierarchy.groups.groups_list", $data);
    }

    public function store(Request $request)
    {
        // dd($request->all());
        $request->validate([
            'name'         => 'required|string|max:255',
            'code'         => 'required|string|max:255',
            'display_name' => 'required|string|max:255',
            'group_id'     => 'required',
            // 'nature'       => 'required',

        ]);
        try {
            $vat_applicable_from = $request->vat_applicable_from
                ? Carbon::createFromFormat('d/m/Y', $request->vat_applicable_from)->format('Y-m-d')
                : null;
            $group = (new Groups())->setConnection('tenant')->create([
                'code'                     => $request->code,
                'name'                     => $request->name,
                'display_name'             => $request->display_name,
                'result'                   => $request->result,
                'nature'                   => $request->nature,
                'group_id'                 => $request->group_id,
                'is_projects_parent_group' => $request->is_projects_parent_group ?: 0,
                'enable_auto_code'         => ($request->status == 'yes') ? 1 : 0,
                'status'                   => $request->main_status,
                'tax_applicable'            => $request->tax_applicable ?: 0,
                'is_taxable'                => $request->is_taxable ?: 0,
                'vat_applicable_from'       => $vat_applicable_from,
                'tax_rate'                  => $request->tax_rate ?: 0,
            ]);

            return redirect()->route("groups.index")->with("success", __('property_master.added_successfully'));
        } catch (\Throwable $th) {
            return redirect()->back()->with("error", $th->getMessage());
        }
    }
    public function edit($id)
    {
        $group = (new Groups())->setConnection('tenant')->findOrFail($id);
        $main  = (new Groups())->setConnection('tenant')->where('id', '!=', $id)->get();

        $data = [
            'group' => $group,
            'main'  => $main,
        ];
        return view("admin-views.hierarchy.groups.edit", $data);
    }
    public function show($id)
    {
        $group = (new Groups())->setConnection('tenant')->findOrFail($id);
        $sub_groups = (new Groups())->setConnection('tenant')->where('group_id', $id)->get();
        $countries = (new Country())->setConnection('tenant')->get();
        $parent_group = (new Groups())->setConnection('tenant')->where('id', $group->group_id)->first();
        $data = [
            'parent_group' => $parent_group,
            'group' => $group,
            'sub_groups' => $sub_groups,
            'countries' => $countries,
        ];
        return view("admin-views.hierarchy.groups.show", $data);
    }

    public function update(Request $request, $id)
    {
        $group = (new Groups())->setConnection('tenant')->findOrFail($id);
        $request->validate([
            'name'         => 'required|string|max:255',
            'code'         => 'required|string|max:255',
            'display_name' => 'required|string|max:255',
            'group_id'     => 'required',
            // 'nature'       => 'required',

        ]);
        try {
            $vat_applicable_from = $request->vat_applicable_from
                ? Carbon::createFromFormat('d/m/Y', $request->vat_applicable_from)->format('Y-m-d')
                : null;
            $group->update([
                'code'                     => $request->code ?? $group->code,
                'name'                     => $request->name ?? $group->name,
                'display_name'             => $request->display_name ?? $group->display_name,
                'result'                   => $request->result ?? $group->result,
                'nature'                   => $request->nature ?? $group->nature,
                'group_id'                 => $request->group_id ?? $group->group_id,
                'tax_applicable'            => $request->tax_applicable ?: 0,
                'is_taxable'                => $request->is_taxable ?: 0,
                'vat_applicable_from'       => $vat_applicable_from,
                'tax_rate'                  => $request->tax_rate ?: 0,
                'is_projects_parent_group' => $request->is_projects_parent_group ?: $group->is_projects_parent_group,
                'enable_auto_code'         => ($request->status == 'yes') ? 1 : 0,
                'status'                   => $request->main_status ?? $group->status,
            ]);

            return redirect()->route("groups.index")->with("success", __('general.updated_successfully'));
        } catch (\Throwable $th) {
            return redirect()->back()->with("error", $th->getMessage());
        }
    }
    public function delete(Request $request)
    {
        $group = (new Groups())->setConnection('tenant')->findOrFail($request->id);
        $this->deleteSubGroups($group);
        $group->delete();
        return redirect()->route("groups.index")->with("success", __('general.deleted_successfully'));
    }
    private function deleteSubGroups($group)
    {
        $group->load('sub_groups');

        foreach ($group->sub_groups as $subGroup) {
            $this->deleteSubGroups($subGroup);
            $subGroup->delete();
        }
    }

    public function get_group_by_id($id)
    {
        $group = (new Groups())->setConnection('tenant')->find($id);
        if ($group) {
            return response()->json([
                'status' => 200,
                "group" => $group,
                "date" => ($group->vat_applicable_from) ? (Carbon::createFromFormat('Y-m-d', $group->vat_applicable_from)->format('d/m/Y')) : '',
            ]);
        } else {
            return response()->json([
                'status' => 404,
                "message" => "Group Not Found",
            ]);
        }
    }

    public function chart_of_account()
    {
        $groups = (new Groups())->setConnection('tenant')->where('group_id', 0)->with('ledgers', 'sub_groups')->get();

        $company = (new Company())->setConnection('tenant')->first();
        $all_groups      = (new Groups())->setConnection('tenant')->get();

        $data = [
            'company' => $company,
            'groups' => $groups,
            'all_groups' => $all_groups,


        ];
        return view("admin-views.hierarchy.groups.chart_of_account", $data);
    }
}
