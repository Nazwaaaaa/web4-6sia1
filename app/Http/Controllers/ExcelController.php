<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Exports\UsersExport;
use Maatwebsite\Excel\Facades\Excel;

class ExcelController extends Controller
{
    public function exportUsers() 
    {
        return Excel::download(new UsersExport, 'DATA_USER_' . time() . '.xlsx');
    }
}
