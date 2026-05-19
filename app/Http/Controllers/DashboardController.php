<?php

namespace App\Http\Controllers;

use App\Services\CalendarService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
     public function __invoke(Request $request): Response
     {
         $focusMonth =
$this->getFocusMonth($request->query('timestamp'), $request->query('next'));
         $buildFromMonth =
$focusMonth->copy()->subMonths(1)->startOfMonth();
         $calendar = new CalendarService($buildFromMonth);
         $calendar->calculateDisplayDates();



        $tasks = DB::table('tasks')->whereBetween(
            'created_at',
            [
                Carbon::parse($calendar->daysArray[0]['day']),
                Carbon::parse($calendar->daysArray[count($calendar->daysArray) - 1]['day'])->endOfDay()
            ]
        )->get();



            foreach($calendar->daysArray as &$x) {
                $x['hasCompleted'] = false;
                $x['hasOutstanding'] = false;
            }

        foreach($tasks as $t) {
            foreach($calendar->daysArray as &$x) {
                if ($t->completed) {
                    $taskTime = Carbon::parse($t->completed_on)->toDateString();
                    if ($taskTime == $x['day']) {
                        $x['hasCompleted'] = true;
                    }
                }

                if (!$t->completed) {

                    $taskTime = Carbon::parse($t->created_at)->toDateString();
                    if ($taskTime == $x['day']) {
                        $x['hasOutstanding'] = true;
                    }
                }
            }
        }

         return Inertia::render('Welcome', [
             'timestamp' => $focusMonth->startOfMonth()->timestamp,
             'human_date' => $focusMonth->format('M Y'),
             'days' => $calendar->daysArray
         ]);
     }

     /**
      *
      */
     private function getFocusMonth(int|null $timestamp, string|null $forward): object
     {
         $focusMonth = Carbon::now();
         if ($timestamp) {
             $focusMonth = Carbon::parse((int)$timestamp);
             $forward == '1' ? $focusMonth->addMonths(1) :
$focusMonth->subMonths(1);
         }
         return $focusMonth;
     }
}