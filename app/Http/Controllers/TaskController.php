<?php

namespace App\Http\Controllers;

use App\Http\Requests\NewEntryRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class TaskController extends Controller
{
    public function store(NewEntryRequest $request) {
        $params = $request->all();
        unset($params['day']);
        DB::table('tasks')->insert($params);
        return response()->json(['success' => true, 'message' => 'Task created', 'data' => []], 201);
    }

    /**
     *
     */
    public function index(Request $request) {
        $day = Carbon::parse($request->day);
        $startFrom = $day->copy()->startOfDay();
        $endFrom = $day->copy()->endOfDay();
        $outstanding = DB::table('tasks')->where('completed', false)
            ->where('created_at', '<=', $endFrom)
            ->get();
        $completed = DB::table('tasks')->where('completed', true)
            ->whereBetween('created_at', [$startFrom, $endFrom])
            ->get();

        foreach($outstanding as $x) {
            $x->human_created_at = Carbon::parse($x->created_at)->setTimezone('Europe/London')->format('jS M Y \a\t h:i');
            $x->carried = Carbon::parse($x->created_at)->diffInDays($day) >= 1;
        }

        foreach($completed as $x) {
            $x->human_completed_on = Carbon::parse($x->completed_on)->setTimezone('Europe/London')->format('jS M Y \a\t h:i');
        }

        return response()->json([
            'success' => true,
            'message' => 'Tasks retrieved',
            'data' => [
                'entries' => [
                    'outstanding' => $outstanding,
                    'completed' => $completed
                ]
            ]
        ], 200);
    }

    /**
     *
     */
    public function destroy(Request $request) {
        DB::table('tasks')->where('id', $request->task)->delete();
        return response()->json(['success' => true, 'message' => 'Task trashed', 'data' => []], 203);
    }

    /**
     *
     */
    public function update(Request $request) {
        $task = DB::table('tasks')->where('id', $request->task)->first();
        if ($task->completed) {
            DB::table('tasks')->where('id', $request->task)->update([
                'completed' => false,
                'completed_on' => null
            ]);
        } else {
            DB::table('tasks')->where('id', $request->task)->update([
                'completed' => true,
                'completed_on' => now()
            ]);
        }
        return response()->json(['success' => true, 'message' => 'Task reverted', 'data' => []], 203);
    }
}