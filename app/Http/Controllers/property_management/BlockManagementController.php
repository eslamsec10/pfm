<?php
namespace App\Http\Controllers\property_management;

use App\Http\Controllers\Controller;
use App\Models\Block;
use App\Models\BlockManagement;
use App\Models\PropertyManagement;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class BlockManagementController extends Controller
{
    public function index(Request $request)
    {
        // $this->authorize('block_management');
        $ids              = $request->bulk_ids;
        $search           = $request['search'];
        $query_param      = $search ? ['search' => $request['search']] : '';
        $block_management = (new BlockManagement())->setConnection('tenant')->join('blocks', 'block_management.block_id', '=', 'blocks.id')->when($request['search'], function ($q) use ($request) {
            $key = explode(' ', $request['search']);
            foreach ($key as $value) {
                $q->Where('blocks.name', 'like', "%{$value}%")
                    ->orWhere('block_management.id', $value);
            }
        })
            ->select('block_management.*', 'blocks.name as block_name')
            ->latest()->paginate()->appends($query_param);

        $data = [
            'main'   => $block_management,
            'search' => $search,

        ];
        return view("admin-views.property_management.block_management.index", $data);
    }
    public function create()
    {
        // $this->authorize('create_block');

        $property_managements = (new PropertyManagement())->setConnection('tenant')->forUser()->select('id', 'name', 'code')->get();
        $blocks               = (new Block())->setConnection('tenant')->select('id', 'name', 'code')->get();
        $data                 = [
            "property_managements" => $property_managements,
            "blocks"               => $blocks,

        ];
        return view("admin-views.property_management.block_management.create", $data);
    }
    public function edit($id)
    {
        // $this->authorize('create_block');

        $main_block           = (new BlockManagement())->setConnection('tenant')->with('block')->findOrFail($id);
        $property_managements = (new PropertyManagement())->setConnection('tenant')->forUser()->get();
        $block_management     = (new BlockManagement())->setConnection('tenant')->pluck('block_id');
        // $block_management = $block_management->except($main_block->block->id);
        $blocks = Block::whereNotIn('id', $block_management)->get();
        // $blocks = $blocks->merge($main_block->block);
        // dd($blocks);

        $data = [
            "property_managements" => $property_managements,
            "blocks"               => $blocks,
            "main_block"           => $main_block,

        ];
        //
        return view("admin-views.property_management.block_management.edit", $data);

    }

    public function store(Request $request)
    {
        $request->validate([
            'block_id'               => 'required',
            'property_management_id' => 'required',
        ]);
        $request->validate([
            'block_id'               => [
                'required',
                Rule::unique('block_management')
                    ->where(function ($query) use ($request) {
                        return $query->where('property_management_id', $request->property_management_id);
                    }),
            ],
            'property_management_id' => 'required',
        ]);
        try {
            foreach ($request->block_id as $id) {
                (new BlockManagement())->setConnection('tenant')->create([
                    "block_id"               => $id,
                    "property_management_id" => $request->property_management_id,
                ]);
            }
            return redirect()->route("block_management.index")->with("success", __('property_master.added_successfully'));
        } catch (\Exception $e) {
            return redirect()->back()->with("error", $e->getMessage());
        }
    }
    public function update(Request $request, $id)
    {
        // $this->authorize('create_block');
        $request->validate([
            'block_id'               => 'required',
            'property_management_id' => 'required',
        ]);
        try {
            $main_block = (new BlockManagement())->setConnection('tenant')->findOrFail($id);
            $main_block->update([
                "block_id"               => $request->block_id,
                "property_management_id" => $request->property_management_id,
            ]);

            return redirect()->route("block_management.index")->with("success", __('property_master.updated_successfully'));
        } catch (\Exception $e) {
            return redirect()->back()->with("error", $e->getMessage());
        }
    }

    public function delete(Request $request)
    {
        // $this->authorize('show_block');

        $property = (new BlockManagement())->setConnection('tenant')->findOrFail($request->id);
        $property->delete();
        return redirect()->back()->with('success', __('general.deleted_successfully'));
    }
    public function show($id)
    {
        $this->authorize('show_block');

        $property = (new BlockManagement())->setConnection('tenant')->findOrFail($id);
        $data     = [
            'property' => $property,
        ];
        return view('admin-views.property_management.block_management.show', $data);
    }

    public function view_image($id)
    {
        // $this->authorize('show_image_block');

        $block    = (new BlockManagement())->setConnection('tenant')->findOrFail($id);
        $property = (new PropertyManagement())->setConnection('tenant')->forUser()->where('id', $block->property_management_id)->with('blocks_management_child', 'blocks_management_child.block'
            , 'blocks_management_child.floors_management_child', 'blocks_management_child.floors_management_child.floor_management_main',
            'blocks_management_child.floors_management_child.unit_management_child', 'blocks_management_child.floors_management_child.unit_management_child.unit_management_main'
        )->first();
        $data = [
            'property'   => $property,
            'main_block' => $block,
        ];
        return view('admin-views.property_management.block_management.view_image', $data);
    }

    public function list_view($id)
    {
        // $this->authorize('show_image_block');

        $block    = (new BlockManagement())->setConnection('tenant')->findOrFail($id);
        $property = (new PropertyManagement())->setConnection('tenant')->forUser()->where('id', $block->property_management_id)->with('blocks_management_child', 'blocks_management_child.block'
            , 'blocks_management_child.floors_management_child', 'blocks_management_child.floors_management_child.floor_management_main',
            'blocks_management_child.floors_management_child.unit_management_child', 'blocks_management_child.floors_management_child.unit_management_child.unit_management_main'
        )->first();
        $data = [
            'property'   => $property,
            'main_block' => $block,
        ];
        return view('admin-views.property_management.block_management.list_view', $data);
    }

    public function get_blocks_by_property(Request $request)
    {
        $block_management = (new BlockManagement())->setConnection('tenant')->where('property_management_id', $request->property_management_id)->pluck('block_id');

        $blocks = (new Block())->setConnection('tenant')->whereNotIn('id', $block_management)->get();
        return json_encode($blocks);
    }
}
