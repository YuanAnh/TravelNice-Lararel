<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminLog;
use Illuminate\Http\Request;

class LogController extends Controller
{
    public function index(Request $request)
    {
        $query = AdminLog::with('admin')->latest('created_at');

        if ($admin = $request->admin_id) {
            $query->where('admin_id', $admin);
        }
        if ($action = $request->action) {
            $query->where('action', $action);
        }
        if ($date = $request->date) {
            $query->whereDate('created_at', $date);
        }

        $logs    = $query->paginate(30);
        $actions = AdminLog::distinct()->pluck('action');
        $admins  = \App\Models\User::role('admin')->get();

        return view('admin.logs.index', compact('logs', 'actions', 'admins'));
    }
}