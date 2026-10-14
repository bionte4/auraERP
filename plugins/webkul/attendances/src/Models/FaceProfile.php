<?php

namespace Webkul\Attendance\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Webkul\Employee\Models\Employee;
use Webkul\Support\Traits\BelongsToCompany;

class FaceProfile extends Model
{
    use BelongsToCompany;

    protected $table = 'attendances_face_profiles';

    protected $fillable = [
        'company_id',
        'employee_id',
        'descriptor',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'descriptor' => 'array',
        ];
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }
}
