<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IncidentReport extends Model
{
    protected $fillable = [
        'full_name',
        'employee_id',
        'email',
        'mobile',
        'department',
        'incident_date',
        'incident_time',
        'incident_type',
        'assets_affected',
        'severity',
        'description',
        'actions_taken',
        'attachments',
        'status',
        'admin_note',
    ];

    protected $casts = [
        'incident_date' => 'date',
        'attachments'   => 'array',
    ];

    public function getSeverityColorClass(): string
    {
        return match ($this->severity) {
            'critical' => 'bg-red-100 text-red-700',
            'high'     => 'bg-orange-100 text-orange-700',
            'medium'   => 'bg-yellow-100 text-yellow-700',
            'low'      => 'bg-green-100 text-green-700',
            default    => 'bg-gray-100 text-gray-700',
        };
    }

    public function getStatusColorClass(): string
    {
        return match ($this->status) {
            'new'          => 'bg-blue-100 text-blue-700',
            'in_review'    => 'bg-purple-100 text-purple-700',
            'investigating'=> 'bg-orange-100 text-orange-700',
            'resolved'     => 'bg-green-100 text-green-700',
            'closed'       => 'bg-gray-100 text-gray-700',
            default        => 'bg-gray-100 text-gray-700',
        };
    }
}
