<?php

namespace App\Services;

use Carbon\Carbon;

class NotificationBuilderService
{
    public function build($type,$model,$company,$tenant)
    {
        $unitName = $model?->unit_management?->unit_management_main->name ?? '-';

        $rent = number_format(
            $model->rent_amount ?? 0,
            $company->decimals ?? 2
        );

        $date = $model->period_from
            ? Carbon::parse($model->period_from)->format('Y-m-d')
            : '-';

        return [
            'title' => "New $type",
            'table' => [
                [
                    'unit' => $unitName,
                    'rent' => "$rent ({$company->currency_code})",
                    'date' => $date,
                ]
            ]
        ];
    }
}
