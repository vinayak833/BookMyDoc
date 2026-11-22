<?php

namespace App\Services;

use App\Models\Doctor;
use App\Models\TimeSlot;
use Illuminate\Support\Carbon;

class SlotGenerator
{
    /**
     * Create slots for a doctor between two dates, every $minutes minutes
     * from $start to $end (H:i). Existing slots are skipped, never duplicated.
     * Returns how many new slots were created.
     */
    public function generate(Doctor $doctor, Carbon $from, Carbon $to, string $start, string $end, int $minutes): int
    {
        $created = 0;

        for ($day = $from->copy()->startOfDay(); $day->lte($to); $day->addDay()) {
            $cursor = $day->copy()->setTimeFromTimeString($start);
            $limit = $day->copy()->setTimeFromTimeString($end);

            while ($cursor->copy()->addMinutes($minutes)->lte($limit)) {
                $slot = TimeSlot::firstOrCreate(
                    ['doctor_id' => $doctor->id, 'slot_start' => $cursor->copy()],
                    ['slot_end' => $cursor->copy()->addMinutes($minutes), 'is_booked' => false]
                );

                if ($slot->wasRecentlyCreated) {
                    $created++;
                }

                $cursor->addMinutes($minutes);
            }
        }

        return $created;
    }
}
