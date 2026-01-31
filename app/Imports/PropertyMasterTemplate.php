<?php
 

namespace App\Imports;

use App\Models\Block;
use App\Models\BlockManagement;
use App\Models\Floor;
use App\Models\FloorManagement;
use App\Models\PropertyManagement;
use App\Models\PropertyType;
use App\Models\Unit;
use App\Models\UnitCondition;
use App\Models\UnitDescription;
use App\Models\UnitManagement;
use App\Models\View;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class PropertyMasterTemplate implements ToCollection, WithHeadingRow
{
    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {

            // ---------------------
            // Property Type
            // ---------------------
            $propertyType = PropertyType::firstOrCreate([
                'name' => trim($row['property_type']),
            ]);

            // ---------------------
            // Property
            // ---------------------
            $property = PropertyManagement::firstOrCreate(
                ['name' => trim($row['property_name'])],
                ['code' => $row['property_code'] ?? null]
            );

            $property->property_types()
                ->syncWithoutDetaching([$propertyType->id]);

            // ---------------------
            // Block (NO DUPLICATE)
            // ---------------------
            $blockName = Block::firstOrCreate([
                'name' => trim($row['block_name']),
            ]);

            $block = BlockManagement::firstOrCreate([
                'block_id'               => $blockName->id,
                'property_management_id' => $property->id,
            ]);

            // ---------------------
            // Floor (NO DUPLICATE)
            // ---------------------
            $floorName = Floor::firstOrCreate([
                'name' => trim($row['floor_name']),
            ]);

            $floor = FloorManagement::firstOrCreate([
                'floor_id'               => $floorName->id,
                'block_management_id'    => $block->id,
                'property_management_id' => $property->id,
            ]);

            // ---------------------
            // Unit Name (NO DUPLICATE)
            // ---------------------
            $unitValue = $row['unit_no'] ?: $row['unit_name'];

            $unitName = $unitValue
                ? Unit::firstOrCreate([
                    'name' => trim($unitValue),
                ])
                : null;

            // ---------------------
            // Unit Description
            // ---------------------
            $unitDescription = !empty($row['unit_type'])
                ? UnitDescription::firstOrCreate(['name' => trim($row['unit_type'])])
                : null;

            // ---------------------
            // Unit Condition
            // ---------------------
            $unitCondition = !empty($row['unit_condition'])
                ? UnitCondition::firstOrCreate(['name' => trim($row['unit_condition'])])
                : null;

            // ---------------------
            // View
            // ---------------------
            $view = !empty($row['view'])
                ? View::firstOrCreate(['name' => trim($row['view'])])
                : null;

            // ---------------------
            // Unit Management (NO DUPLICATE)
            // ---------------------
            if ($unitName) {
                UnitManagement::updateOrCreate(
                    [
                        'unit_id'                => $unitName->id,
                        'property_management_id' => $property->id,
                        'block_management_id'    => $block->id,
                        'floor_management_id'    => $floor->id,
                    ],
                    [
                        'unit_description_id' => $unitDescription->id ?? null,
                        'unit_condition_id'   => $unitCondition->id ?? null,
                        'view_id'             => $view->id ?? null,
                    ]
                );
            }
        }
    }
}
