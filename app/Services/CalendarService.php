<?php

namespace App\Services;

use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class CalendarService
{
     public array $daysArray = [];
     private int $daysInPreviousMonth = 0;
     private int $daysInNextMonth = 0;
     private int $focusMonthStartsOnInt = 0;
     private int $focusMonthEndsOnInt = 0;
     private int $focusMonthInt = 0;

     public function __construct(public object $date)
     {
         for ($x = 0; $x <= 2; $x += 1) {
             switch ($x) {
                 case 0:
                     $this->daysInPreviousMonth = $this->date->daysInMonth;
                     break;
                 case 1:
                     $this->focusMonthInt = $this->date->month;
                     $this->focusMonthStartsOnInt =
$this->date->copy()->firstOfMonth()->dayOfWeek;
                     $this->focusMonthEndsOnInt =
$this->date->copy()->LastOfMonth()->dayOfWeek;
                     break;
                 case 2:
                     $this->daysInNextMonth = $this->date->daysInMonth;
                     break;
             }
             $this->buildDates();
             $this->date->addMonths(1);
         }
     }

     /**
      *
      */
     public function buildDates(): void
     {
         $days = $this->date->daysInMonth;
         $day = Carbon::parse($this->date);
         for ($x = 1; $x <= $days; $x += 1) {
             $this->daysArray[] = $this->buildArrayItem($day);
             $day->addDays(1);
         }
     }

     /**
      *
      */
     private function calculateStartFromIndex(): int
     {
         $startFrom = $this->daysInPreviousMonth -
$this->focusMonthStartsOnInt;
         //Go back a week if focus month starts on sunday
         if ($this->focusMonthStartsOnInt == 0) {
             $startFrom -= 7;
         }
         return $startFrom;
     }

     /**
      *
      */
     private function calculateEndFromIndex(): int
     {
         $endAt = $this->daysInNextMonth - (6 - $this->focusMonthEndsOnInt);
         //Add a week if focus month ends on saturday
         if ($this->focusMonthEndsOnInt == 6) {
             $endAt -= 7;
         }
         return $endAt;
     }

     /**
      *
      */
     public function calculateDisplayDates(): void
     {
         $this->daysArray = array_slice($this->daysArray,
$this->calculateStartFromIndex(), -$this->calculateEndFromIndex());
     }

     /**
      *
      */
     private function buildArrayItem(object $day): array
     {
        return [
            'id' => Str::random(8),
            'is_focus_month' => $this->focusMonthInt == $day->month,
            'is_today' => $day == Carbon::today(),
            'day' => $day->toDateString(),
            'day_int' => $day->format('d'),
            'weekday_int' => $day->dayOfWeek(),
            'weekday_string' => $day->format('D'),
            'month_int' => $day->format('m'),
            'month_string' => $day->format('M'),
            'human_date' => $day->format('jS M Y'),
            'is_weekend' => in_array($day->dayOfWeek(), ['0', '6'])
        ];
    }
}