<?php

namespace App\Observers;

use App\Models\MedicalRecord;
use Illuminate\Support\Facades\DB;

class MedicalRecordObserver
{
    public function updating(MedicalRecord $record): void
    {
        $changes = [];
        foreach (['diagnosis','prescription'] as $field) {
            if ($record->isDirty($field)) $changes[$field] = ['old' => $record->getRawOriginal($field), 'new' => $record->getAttribute($field)];
        }
        $record->setRelation('_pendingAuditChanges', $changes);
    }

    public function updated(MedicalRecord $record): void
    {
        foreach ($record->getRelation('_pendingAuditChanges') ?? [] as $field => $change) {
            DB::table('audit_logs')->insert([
                'auditable_type' => $record->getMorphClass(), 'auditable_id' => $record->id,
                'user_id' => auth()->id(), 'event' => 'updated',
                'old_values' => json_encode([$field => $change['old']], JSON_THROW_ON_ERROR),
                'new_values' => json_encode([$field => $change['new']], JSON_THROW_ON_ERROR),
                'created_at' => now(), 'updated_at' => now(),
            ]);
        }
        $record->unsetRelation('_pendingAuditChanges');
    }
}
